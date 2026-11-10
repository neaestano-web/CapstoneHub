<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Research;
use App\Services\AI\AIService;
use Illuminate\Support\Str;

class SimilarityController extends Controller
{
    public function index()
    {
        return view('similarity-checker.index');
    }

    /**
     * Proactively warm up / ping the Python AI service in the background.
     */
    public function warmup()
    {
        $aiUrl = rtrim((string) config('services.ai.url', env('AI_SERVICE_URL', 'https://capstone-ai-fqz3.onrender.com')), '/');

        try {
            \Illuminate\Support\Facades\Http::timeout(2)
                ->connectTimeout(2)
                ->get($aiUrl . '/');
        } catch (\Throwable $e) {
            // Ignore timeout — incoming connection triggers Render wake-up cycle
        }

        return response()->json(['status' => 'waking', 'url' => $aiUrl]);
    }

    /**
     * Run cross-field similarity check combining Textual and Semantic similarity.
     * Retains single-input field: compares query across Title, Context, General Objectives, and Specific Objectives.
     */
    public function check(
        Request $request,
        AIService $ai
    )
    {
        try {
            // Support unified single input 'query', while remaining backwards-compatible with 'title' or 'objectives'
            $query = trim((string) $request->input('query', $request->input('title', $request->input('objectives', ''))));

            if ($query === '') {
                return back()
                    ->withErrors(['input' => 'Please enter your proposed research title, context, general objective, or specific objectives.'])
                    ->withInput();
            }

            // 1. Generate semantic embedding vector for the unified input text via Sentence Transformer
            $queryEmbeddingData = [];
            try {
                $queryEmbeddingData = $ai->generateEmbedding($query);
            } catch (\Throwable $e) {
                \Log::warning('AI Service embedding generation failed: ' . $e->getMessage());
            }

            $hasAiEmbedding = !empty($queryEmbeddingData) && !empty($queryEmbeddingData['embedding']);
            $inputEmbedding = $hasAiEmbedding ? $queryEmbeddingData['embedding'] : null;

            // 2. Extract distinctive content keywords and bigrams from the user input for textual analysis
            $inputKeywords = $this->extractKeywords($query);
            $inputBigrams = $this->extractBigrams($query);

            $researches = Research::where('status', 'approved')
                ->with(['embedding', 'category'])
                ->get();

            $results = [];

            $minScoreThreshold = (float) config('similarity.minimum_score', 60.0);
            $semanticWeight = (float) config('similarity.semantic_weight', 0.60);
            $textualWeight = (float) config('similarity.textual_weight', 0.40);
            $primaryFieldWeight = (float) config('similarity.primary_field_weight', 0.85);
            $supportiveFieldWeight = (float) config('similarity.supportive_field_weight', 0.15);

            foreach ($researches as $research) {
                // Parse and prepare the 4 distinct fields
                $titleText = trim((string) ($research->title ?? ''));
                $contextText = trim((string) ($research->project_context ?? ''));
                $objectivesParsed = $this->parseObjectives($research->objectives);
                $generalObjText = $objectivesParsed['general'];
                $specificObjText = $objectivesParsed['specific'];

                $fields = [
                    'title' => [
                        'name' => 'Research Title',
                        'text' => $titleText,
                        'embedding' => $this->getFieldEmbedding($research, 'title', $titleText, $ai),
                    ],
                    'context' => [
                        'name' => 'Project Context',
                        'text' => $contextText,
                        'embedding' => $contextText !== '' ? $this->getFieldEmbedding($research, 'context', $contextText, $ai) : null,
                    ],
                    'general_objectives' => [
                        'name' => 'General Objectives',
                        'text' => $generalObjText,
                        'embedding' => $generalObjText !== '' ? $this->getFieldEmbedding($research, 'general_objectives', $generalObjText, $ai) : null,
                    ],
                    'specific_objectives' => [
                        'name' => 'Specific Objectives',
                        'text' => $specificObjText,
                        'embedding' => $specificObjText !== '' ? $this->getFieldEmbedding($research, 'specific_objectives', $specificObjText, $ai) : null,
                    ],
                ];

                $fieldEvaluations = [];
                $allSharedKeywords = [];

                foreach ($fields as $fieldKey => $fieldData) {
                    $fieldText = $fieldData['text'];

                    if ($fieldText === '') {
                        continue;
                    }

                    // A. Textual Similarity Detection
                    $textualResult = $this->computeTextualSimilarity($query, $fieldText, $inputKeywords, $inputBigrams);
                    $textualScore = $textualResult['score'];
                    if (!empty($textualResult['shared_keywords'])) {
                        $allSharedKeywords = array_unique(array_merge($allSharedKeywords, $textualResult['shared_keywords']));
                    }

                    // B. Semantic Similarity Detection (Sentence Transformer Cosine)
                    $semanticScore = 0.0;
                    if ($hasAiEmbedding && !empty($fieldData['embedding'])) {
                        $simRes = $ai->compare($inputEmbedding, $fieldData['embedding']);
                        $semanticScore = (float) ($simRes['similarity'] ?? 0.0);
                    } else {
                        // Fallback when embedding is temporarily unavailable
                        $semanticScore = $textualScore;
                    }

                    // C. Calibrated Combined Field Score
                    $combinedFieldScore = $this->calibrateCombinedScore($semanticScore, $textualScore, $semanticWeight, $textualWeight, count($textualResult['shared_keywords']));

                    $fieldEvaluations[$fieldKey] = [
                        'name' => $fieldData['name'],
                        'combined' => round($combinedFieldScore, 1),
                        'semantic' => round($semanticScore, 1),
                        'textual' => round($textualScore, 1),
                        'shared_keywords' => $textualResult['shared_keywords'],
                        'shared_phrases' => $textualResult['shared_phrases'],
                    ];
                }

                if (empty($fieldEvaluations)) {
                    continue;
                }

                // 3. Identify the Best-Matching Research Field
                $bestFieldKey = null;
                $highestFieldScore = -1.0;

                foreach ($fieldEvaluations as $k => $eval) {
                    if ($eval['combined'] > $highestFieldScore) {
                        $highestFieldScore = $eval['combined'];
                        $bestFieldKey = $k;
                    }
                }

                $bestFieldName = $fieldEvaluations[$bestFieldKey]['name'];

                // 4. Combined Similarity Evaluation across all fields
                // Anchor by the highest field score (so single-field matches are never unfairly diluted),
                // supplemented with secondary correlation from other relevant fields.
                $otherScores = [];
                foreach ($fieldEvaluations as $k => $eval) {
                    if ($k !== $bestFieldKey && $eval['combined'] >= 25.0) {
                        $otherScores[] = $eval['combined'];
                    }
                }

                $supportiveScore = !empty($otherScores) ? (array_sum($otherScores) / count($otherScores)) : 0.0;

                if ($supportiveScore > 0) {
                    $overallSimilarity = ($highestFieldScore * $primaryFieldWeight) + ($supportiveScore * $supportiveFieldWeight);
                } else {
                    $overallSimilarity = $highestFieldScore;
                }

                $overallSimilarity = round(min(100.0, max(0.0, $overallSimilarity)), 1);

                // Filter by minimum score threshold to eliminate false positives
                if ($overallSimilarity >= $minScoreThreshold) {
                    // 5. Generate Explanation of Similarities and Important Differences
                    $similarityExplanation = $this->generateSimilarityExplanation(
                        $query,
                        $research,
                        $bestFieldName,
                        $fieldEvaluations[$bestFieldKey],
                        $allSharedKeywords
                    );

                    $differencesExplanation = $this->generateDifferencesExplanation(
                        $query,
                        $research,
                        $bestFieldName,
                        $fieldEvaluations[$bestFieldKey],
                        $inputKeywords,
                        $allSharedKeywords
                    );

                    $results[] = [
                        'research' => $research,
                        'overall_similarity' => $overallSimilarity,
                        'best_matching_field' => $bestFieldName,
                        'best_field_key' => $bestFieldKey,
                        'best_field_score' => $highestFieldScore,
                        'field_evaluations' => $fieldEvaluations,
                        'similarity_explanation' => $similarityExplanation,
                        'differences_explanation' => $differencesExplanation,
                        'shared_keywords' => array_slice(array_values($allSharedKeywords), 0, 6),
                    ];
                }
            }

            // 6. Rank the Top 5 most relevant research projects
            $maxResults = (int) config('similarity.max_results', 5);
            $results = collect($results)
                ->sortByDesc('overall_similarity')
                ->take($maxResults);

            return view('similarity-checker.index', [
                'results' => $results,
                'inputQuery' => $query,
                'inputTitle' => $query,
                'inputObjectives' => $query,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Similarity Check Fatal Error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['input' => 'An unexpected error occurred during similarity screening: ' . $e->getMessage()]);
        }
    }

    /**
     * Retrieve or dynamically compute field embedding vector (384-dimensions).
     */
    private function getFieldEmbedding(Research $research, string $fieldKey, string $text, AIService $ai): ?array
    {
        if ($research->embedding) {
            if ($fieldKey === 'title' && !empty($research->embedding->title_embedding)) {
                $decoded = json_decode($research->embedding->title_embedding, true);
                if (is_array($decoded) && !empty($decoded)) return $decoded;
            }

            if ($fieldKey === 'context' && !empty($research->embedding->context_embedding)) {
                $decoded = json_decode($research->embedding->context_embedding, true);
                if (is_array($decoded) && !empty($decoded)) return $decoded;
            }

            if ($fieldKey === 'specific_objectives' && !empty($research->embedding->objective_embedding)) {
                $decoded = json_decode($research->embedding->objective_embedding, true);
                if (is_array($decoded) && !empty($decoded)) return $decoded;
            }
        }

        // On-the-fly generation via FastEmbed ONNX (cached automatically for 24 hours in AIService)
        try {
            $embData = $ai->generateEmbedding($text);
            if (!empty($embData['embedding']) && is_array($embData['embedding'])) {
                return $embData['embedding'];
            }
        } catch (\Throwable $e) {
            // Fail safe
        }

        return null;
    }

    /**
     * Separate research objectives into General Objective and Specific Objectives.
     */
    private function parseObjectives(?string $objectives): array
    {
        $text = trim((string) $objectives);
        if ($text === '') {
            return ['general' => '', 'specific' => ''];
        }

        // Check for explicit headers like "General Objective(s):" and "Specific Objective(s):"
        if (preg_match('/(?:general\s+objective[s]?|general\s+aim[s]?|main\s+objective[s]?):?\s*(.*?)(?=(?:specific\s+objective[s]?|specific\s+aim[s]?):|$)/is', $text, $gMatch)) {
            $general = trim($gMatch[1]);
            $specific = '';
            if (preg_match('/(?:specific\s+objective[s]?|specific\s+aim[s]?):?\s*(.*)/is', $text, $sMatch)) {
                $specific = trim($sMatch[1]);
            }
            if ($general !== '' || $specific !== '') {
                return [
                    'general' => $general ?: $text,
                    'specific' => $specific ?: $text,
                ];
            }
        }

        // Split into introductory goal vs numbered/bulleted specific points
        $lines = preg_split('/\r\n|\r|\n/', $text);
        $generalLines = [];
        $specificLines = [];
        $foundBullet = false;

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') continue;

            if (preg_match('/^([•\-\*]|\d+[\.\)])\s+/u', $trimmed) || preg_match('/^(specifically|specific objectives)/i', $trimmed)) {
                $foundBullet = true;
            }

            if ($foundBullet) {
                $specificLines[] = $trimmed;
            } else {
                $generalLines[] = $trimmed;
            }
        }

        $general = trim(implode(' ', $generalLines));
        $specific = trim(implode("\n", $specificLines));

        return [
            'general' => $general !== '' ? $general : $text,
            'specific' => $specific !== '' ? $specific : $text,
        ];
    }

    /**
     * Measure textual overlap, matching keywords, and phrase sequences between input and field.
     */
    private function computeTextualSimilarity(string $query, string $field, array $inputKeywords, array $inputBigrams): array
    {
        if (empty($inputKeywords) || trim($field) === '') {
            return [
                'score' => 0.0,
                'shared_keywords' => [],
                'shared_phrases' => [],
            ];
        }

        $fieldKeywords = $this->extractKeywords($field);
        $fieldBigrams = $this->extractBigrams($field);

        if (empty($fieldKeywords)) {
            return [
                'score' => 0.0,
                'shared_keywords' => [],
                'shared_phrases' => [],
            ];
        }

        $sharedKeywords = array_values(array_intersect($inputKeywords, $fieldKeywords));
        $sharedPhrases = array_values(array_intersect($inputBigrams, $fieldBigrams));

        $sharedCount = count($sharedKeywords);
        $phraseCount = count($sharedPhrases);

        // Guard: if fewer than 2 distinctive keywords match and no multi-word phrase matches,
        // prevent common isolated words from registering as similarity.
        if ($sharedCount === 0 && $phraseCount === 0) {
            return [
                'score' => 0.0,
                'shared_keywords' => [],
                'shared_phrases' => [],
            ];
        }

        if ($sharedCount === 1 && $phraseCount === 0 && count($inputKeywords) > 1) {
            return [
                'score' => 15.0,
                'shared_keywords' => $sharedKeywords,
                'shared_phrases' => [],
            ];
        }

        $inputCount = count($inputKeywords);
        $fieldCount = count($fieldKeywords);

        // 1. Asymmetric Overlap / Containment (crucial when input is concise and target field is long)
        $containment = ($sharedCount / max(1, $inputCount)) * 100.0;

        // 2. Jaccard Index
        $unionCount = count(array_unique(array_merge($inputKeywords, $fieldKeywords)));
        $jaccard = ($sharedCount / max(1, $unionCount)) * 100.0;

        // 3. Dice Coefficient
        $dice = (2.0 * $sharedCount / max(1, $inputCount + $fieldCount)) * 100.0;

        // 4. Exact multi-word domain phrase bonus (e.g. "flood warning", "decision support")
        $phraseBonus = min(20.0, $phraseCount * 8.0);

        // Weighted textual score
        $rawScore = ($containment * 0.50) + ($dice * 0.30) + ($jaccard * 0.20) + $phraseBonus;

        return [
            'score' => round(min(100.0, max(0.0, $rawScore)), 1),
            'shared_keywords' => $sharedKeywords,
            'shared_phrases' => $sharedPhrases,
        ];
    }

    /**
     * Calibrate semantic and textual scores with mutual reinforcement and generic-word suppression.
     */
    private function calibrateCombinedScore(float $semantic, float $textual, float $semWeight, float $txtWeight, int $sharedKeywordCount): float
    {
        // Case 1: High semantic similarity with different wording (paraphrasing / conceptual match)
        if ($semantic >= 65.0 && $textual < 35.0) {
            return ($semantic * 0.85) + ($textual * 0.15);
        }

        // Case 2: Mutual high reinforcement (both semantic and textual are strong)
        if ($semantic >= 55.0 && $textual >= 50.0) {
            $boost = min(10.0, ($sharedKeywordCount * 1.5));
            return min(100.0, ($semantic * $semWeight) + ($textual * $txtWeight) + $boost);
        }

        // Case 3: Textual score appears high but semantic score is very low (false positive from shared generic terms)
        if ($textual >= 45.0 && $semantic < 40.0) {
            // Dampen textual score so generic words do not dominate
            $dampenedTextual = $textual * ($semantic / 50.0);
            return ($semantic * 0.70) + ($dampenedTextual * 0.30);
        }

        // Case 4: Standard calibrated blend
        return ($semantic * $semWeight) + ($textual * $txtWeight);
    }

    /**
     * Extract clean, meaningful domain keywords (stripping English stopwords, academic filler, and institutional noise).
     */
    private function extractKeywords(?string $text): array
    {
        if ($text === null || trim($text) === '') {
            return [];
        }

        $clean = strtolower(strip_tags($text));
        $clean = preg_replace('/[^a-z0-9\s]/', ' ', $clean);
        $rawWords = array_values(array_filter(explode(' ', $clean), fn($w) => strlen(trim($w)) > 2));

        $stopWords = [
            // Standard English grammatical stopwords
            'the', 'and', 'for', 'with', 'that', 'this', 'from', 'into', 'upon', 'about', 'after',
            'before', 'between', 'through', 'under', 'above', 'where', 'when', 'which', 'while',
            'what', 'will', 'would', 'could', 'should', 'have', 'been', 'being', 'having', 'were',
            'they', 'them', 'their', 'there', 'than', 'then', 'also', 'both', 'each', 'most', 'some',
            'such', 'very', 'only', 'same', 'more', 'other', 'here', 'your', 'ours', 'whose',

            // Generic academic boilerplate, institutional prefixes, and empty research fillers
            'jhcsc', 'jh', 'cerilles', 'state', 'college', 'dumingag', 'mati', 'pagadian', 'canuto',
            'main', 'campus', 'school', 'department', 'faculty', 'student', 'students',
            'repository', 'capstone', 'study', 'project', 'system', 'systems', 'proposed', 'develop',
            'development', 'developed', 'developing', 'design', 'designed', 'designing', 'implement',
            'implementation', 'implemented', 'evaluate', 'evaluation', 'evaluated', 'specifically',
            'following', 'determine', 'conducted', 'aim', 'aims', 'aimed', 'objective', 'objectives',
            'general', 'specific', 'provide', 'providing', 'based', 'using', 'used', 'towards',
            'toward', 'via', 'into', 'paper', 'research', 'thesis', 'present', 'presents', 'presented',
            'users', 'user', 'allow', 'allows', 'allowed', 'enable', 'enables', 'help', 'helps',
            'order', 'effective', 'efficient', 'level', 'process', 'management',
            'easy', 'improve', 'improving', 'improved',
            'academic', 'semester', 'device', 'devices', 'interface', 'responsive'
        ];

        $stopMap = array_flip($stopWords);
        $filtered = [];

        foreach ($rawWords as $word) {
            $w = trim($word);
            if (!isset($stopMap[$w]) && !is_numeric($w)) {
                $filtered[] = $w;
            }
        }

        return array_values(array_unique($filtered));
    }

    /**
     * Extract meaningful domain bigrams (2-word phrases) after filtering out stopwords.
     */
    private function extractBigrams(?string $text): array
    {
        if ($text === null || trim($text) === '') {
            return [];
        }

        $keywords = $this->extractKeywords($text);
        $count = count($keywords);
        $bigrams = [];

        for ($i = 0; $i < $count - 1; $i++) {
            $bigrams[] = $keywords[$i] . ' ' . $keywords[$i + 1];
        }

        return array_values(array_unique($bigrams));
    }

    /**
     * Generate clear, human-readable explanation of detected similarities.
     */
    private function generateSimilarityExplanation(string $query, Research $research, string $bestFieldName, array $bestEval, array $sharedKeywords): string
    {
        $semantic = $bestEval['semantic'];
        $textual = $bestEval['textual'];

        $parts = [];

        $parts[] = "Strongest correlation identified in **{$bestFieldName}** (Semantic: {$semantic}%, Textual: {$textual}%).";

        if (!empty($sharedKeywords)) {
            $previewKw = array_slice($sharedKeywords, 0, 5);
            $parts[] = "Shared technical concepts and keywords: *" . implode(', ', $previewKw) . "*.";
        }

        if ($semantic >= 75.0 && $textual < 40.0) {
            $parts[] = "High semantic alignment detected: the submitted input shares core thematic purpose and intent with this manuscript despite variations in vocabulary or phrasing.";
        } elseif ($semantic >= 70.0 && $textual >= 60.0) {
            $parts[] = "Both direct terminology and underlying objective scope exhibit significant overlap with this existing study.";
        } else {
            $parts[] = "The submitted text addresses a problem context and technical scope comparable to this approved research.";
        }

        return implode(' ', $parts);
    }

    /**
     * Generate clear, human-readable explanation of important differences.
     */
    private function generateDifferencesExplanation(string $query, Research $research, string $bestFieldName, array $bestEval, array $inputKeywords, array $sharedKeywords): string
    {
        $sharedMap = array_flip($sharedKeywords);
        $distinctiveInput = array_values(array_filter($inputKeywords, fn($k) => !isset($sharedMap[$k])));

        $parts = [];

        if (!empty($distinctiveInput)) {
            $previewDistinctive = array_slice($distinctiveInput, 0, 4);
            $parts[] = "The submitted input introduces distinct focal elements not emphasized here: *" . implode(', ', $previewDistinctive) . "*.";
        }

        // Highlight category or contextual distinctiveness
        if ($research->category && !empty($research->category->name)) {
            $parts[] = "This manuscript is categorized under **{$research->category->name}**.";
        }

        $combined = $bestEval['combined'];
        if ($combined >= 85.0) {
            $parts[] = "Note: Due to high conceptual resemblance, ensure your proposed methodology, target beneficiaries, or experimental setting provide clear academic differentiation.";
        } else {
            $parts[] = "Key differences remain in target implementation details, specific feature sets, and functional scope.";
        }

        return implode(' ', $parts);
    }
}
