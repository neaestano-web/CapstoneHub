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

    public function check(
        Request $request,
        AIService $ai
    )
    {
        // Support unified single input 'query', while remaining backwards-compatible with 'title' or 'objectives'
        $query = trim($request->input('query', $request->input('title', $request->input('objectives', ''))));

        if ($query === '') {
            return back()
                ->withErrors(['input' => 'Please enter your proposed research title, objectives, or topic summary.'])
                ->withInput();
        }

        // Generate embedding vector for the unified input text
        $queryEmbeddingData = [];
        try {
            $queryEmbeddingData = $ai->generateEmbedding($query);
        } catch (\Exception $e) {
            \Log::warning('AI Service embedding generation failed: ' . $e->getMessage());
        }

        $hasAiEmbedding = !empty($queryEmbeddingData) && !empty($queryEmbeddingData['embedding']);
        $inputEmbedding = $hasAiEmbedding ? $queryEmbeddingData['embedding'] : null;

        $researches = Research::where('status', 'approved')
            ->with('embedding')
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
                // High-fidelity fallback token similarity if AI embeddings are not present
                $titleScore = $this->computeTokenSimilarity($inputTokens, $this->tokenize($research->title));
                $objectiveScore = !empty($research->objectives) 
                    ? $this->computeTokenSimilarity($inputTokens, $this->tokenize($research->objectives)) 
                    : null;
                $contextScore = !empty($research->project_context)
                    ? $this->computeTokenSimilarity($inputTokens, $this->tokenize($research->project_context))
                    : null;
                $contentScore = $this->computeTokenSimilarity(
                    $inputTokens, 
                    $this->tokenize($research->title . ' ' . $research->project_context . ' ' . $research->abstract . ' ' . $research->objectives)
                );
            }

            // Fallback for context score if existing AI embedding row has not yet computed context_embedding
            if ($contextScore === null && !empty($research->project_context)) {
                $contextScore = $this->computeTokenSimilarity($inputTokens, $this->tokenize($research->project_context));
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

            // Filter by minimum score threshold
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
    }

    /**
     * Helper to extract clean word tokens for fallback comparison.
     */
    private function tokenize(string $text): array
    {
        $text = strtolower(strip_tags($text));
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
        return array_values(array_filter(explode(' ', $text), fn($w) => strlen($w) > 2));
    }

    /**
     * Compute Jaccard/Dice token similarity percentage.
     */
    private function computeTokenSimilarity(array $tokens1, array $tokens2): float
    {
        if (empty($tokens1) || empty($tokens2)) {
            return 0.0;
        }
        $intersection = count(array_intersect($tokens1, $tokens2));
        $union = count(array_unique(array_merge($tokens1, $tokens2)));
        if ($union === 0) return 0.0;
        return round(($intersection / $union) * 100, 1);
    }
}
