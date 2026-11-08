<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Research;
use App\Services\AI\AIService;

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
                    ->withErrors(['input' => 'Please enter your proposed research title, objectives, or topic summary.'])
                    ->withInput();
            }

            // Generate embedding vector for the unified input text
            $queryEmbeddingData = [];
            try {
                $queryEmbeddingData = $ai->generateEmbedding($query);
            } catch (\Throwable $e) {
                \Log::warning('AI Service embedding generation failed: ' . $e->getMessage());
            }

            $hasAiEmbedding = !empty($queryEmbeddingData) && !empty($queryEmbeddingData['embedding']);
            $inputEmbedding = $hasAiEmbedding ? $queryEmbeddingData['embedding'] : null;

            $researches = Research::where('status', 'approved')
                ->with(['embedding', 'category'])
                ->get();

            $results = [];

            // Pre-tokenize input for fallback string comparison if AI service/embeddings are unavailable
            $inputTokens = $this->tokenize($query);

            foreach ($researches as $research)
            {
                $titleScore = null;
                $objectiveScore = null;
                $contextScore = null;
                $contentScore = null;
                $primaryMatch = 'Overall Topic';

                $titleStr = (string) ($research->title ?? '');
                $objStr = (string) ($research->objectives ?? '');
                $ctxStr = (string) ($research->project_context ?? '');
                $absStr = (string) ($research->abstract ?? '');

                if ($hasAiEmbedding && $research->embedding) {
                    // 1. Compare against Title Vector (if input is a title, this score will be high)
                    if (!empty($research->embedding->title_embedding)) {
                        $decoded = json_decode($research->embedding->title_embedding, true);
                        if (is_array($decoded)) {
                            $res = $ai->compare($inputEmbedding, $decoded);
                            $titleScore = $res['similarity'] ?? 0;
                        }
                    }

                    // 2. Compare against Objectives Vector (if input is an objective, this score will be high)
                    if (!empty($research->embedding->objective_embedding)) {
                        $decoded = json_decode($research->embedding->objective_embedding, true);
                        if (is_array($decoded)) {
                            $res = $ai->compare($inputEmbedding, $decoded);
                            $objectiveScore = $res['similarity'] ?? 0;
                        }
                    }

                    // 3. Compare against Context Vector (if input is a project context, this score will be high)
                    if (!empty($research->embedding->context_embedding)) {
                        $decoded = json_decode($research->embedding->context_embedding, true);
                        if (is_array($decoded)) {
                            $res = $ai->compare($inputEmbedding, $decoded);
                            $contextScore = $res['similarity'] ?? 0;
                        }
                    }

                    // 4. Compare against Combined Content / Abstract Vector
                    if (!empty($research->embedding->content_embedding)) {
                        $decoded = json_decode($research->embedding->content_embedding, true);
                        if (is_array($decoded)) {
                            $res = $ai->compare($inputEmbedding, $decoded);
                            $contentScore = $res['similarity'] ?? 0;
                        }
                    }
                } else {
                    // Fallback token similarity only if AI embeddings are not present
                    $titleScore = $this->computeTokenSimilarity($inputTokens, $this->tokenize($titleStr));
                    $objectiveScore = $objStr !== '' 
                        ? $this->computeTokenSimilarity($inputTokens, $this->tokenize($objStr)) 
                        : null;
                    $contextScore = $ctxStr !== ''
                        ? $this->computeTokenSimilarity($inputTokens, $this->tokenize($ctxStr))
                        : null;
                    $contentScore = $this->computeTokenSimilarity(
                        $inputTokens, 
                        $this->tokenize($titleStr . ' ' . $ctxStr . ' ' . $absStr . ' ' . $objStr)
                    );
                }

                // Fallback for context score if existing AI embedding row has not yet computed context_embedding
                if ($contextScore === null && $ctxStr !== '') {
                    $contextScore = $this->computeTokenSimilarity($inputTokens, $this->tokenize($ctxStr));
                }

                // Collect all available scores
                $availableScores = array_filter([
                    'title' => $titleScore,
                    'objective' => $objectiveScore,
                    'context' => $contextScore,
                    'content' => $contentScore
                ], fn($s) => $s !== null);

                if (empty($availableScores)) {
                    continue;
                }

                // Identify the strongest match dimension (Title, Objectives, Context, or Overall Content)
                $maxScore = max($availableScores);

                if ($titleScore !== null && abs($maxScore - $titleScore) < 0.001) {
                    $primaryMatch = 'Title Match';
                } elseif ($objectiveScore !== null && abs($maxScore - $objectiveScore) < 0.001) {
                    $primaryMatch = 'Objectives Match';
                } elseif ($contextScore !== null && abs($maxScore - $contextScore) < 0.001) {
                    $primaryMatch = 'Context Match';
                } else {
                    $primaryMatch = 'Topic / Abstract Match';
                }

                // The overall similarity accurately reflects the highest semantic alignment
                $overall = round($maxScore, 1);

                // Filter strictly by minimum score threshold (60% and above)
                if ($overall >= config('similarity.minimum_score', 60)) {
                    $results[] = [
                        'research' => $research,
                        'title_similarity' => $titleScore !== null ? round($titleScore, 1) : null,
                        'objective_similarity' => $objectiveScore !== null ? round($objectiveScore, 1) : null,
                        'context_similarity' => $contextScore !== null ? round($contextScore, 1) : null,
                        'content_similarity' => $contentScore !== null ? round($contentScore, 1) : null,
                        'primary_match' => $primaryMatch,
                        'overall_similarity' => $overall
                    ];
                }
            }

            $results = collect($results)
                ->sortByDesc('overall_similarity')
                ->take(5);

            return view('similarity-checker.index', [
                'results' => $results,
                'inputQuery' => $query,
                'inputTitle' => $query,
                'inputObjectives' => $query
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
     * Helper to extract clean word tokens for fallback comparison.
     */
    private function tokenize(?string $text): array
    {
        if ($text === null || trim($text) === '') {
            return [];
        }

        $text = strtolower(strip_tags($text));
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
        return array_values(array_filter(explode(' ', $text), fn($w) => strlen($w) > 2));
    }

    /**
     * Compute hybrid token similarity incorporating Jaccard, Overlap/Containment, and String similarity.
     * Prevents false negatives when school prefixes (e.g. 'jhcsc') or minor stop-words skew set union sizes.
     */
    private function computeTokenSimilarity(array $tokens1, array $tokens2): float
    {
        if (empty($tokens1) || empty($tokens2)) {
            return 0.0;
        }

        // Filter out common noise / institutional prefix tokens like 'jhcsc'
        $noiseWords = ['jhcsc', 'for', 'and', 'the', 'with', 'using', 'based', 'toward', 'towards'];
        $filtered1 = array_values(array_filter($tokens1, fn($t) => !in_array($t, $noiseWords)));
        $filtered2 = array_values(array_filter($tokens2, fn($t) => !in_array($t, $noiseWords)));

        $t1 = !empty($filtered1) ? $filtered1 : $tokens1;
        $t2 = !empty($filtered2) ? $filtered2 : $tokens2;

        if (empty($t1) || empty($t2)) {
            return 0.0;
        }

        $intersection = count(array_intersect($t1, $t2));
        if ($intersection === 0) {
            return 0.0;
        }

        // 1. Jaccard Index
        $union = count(array_unique(array_merge($t1, $t2)));
        $jaccard = $union > 0 ? ($intersection / $union) * 100.0 : 0.0;

        // 2. Overlap / Containment Coefficient (vital for shared core topics)
        $minCount = min(count($t1), count($t2));
        $overlap = $minCount > 0 ? ($intersection / $minCount) * 100.0 : 0.0;

        // 3. Dice Coefficient
        $sumCount = count($t1) + count($t2);
        $dice = $sumCount > 0 ? (2.0 * $intersection / $sumCount) * 100.0 : 0.0;

        // Weighted hybrid score: strong containment represents real subject duplication
        $score = ($overlap * 0.45) + ($dice * 0.35) + ($jaccard * 0.20);

        return round(min(100.0, max($score, $overlap * 0.90)), 1);
    }
}
