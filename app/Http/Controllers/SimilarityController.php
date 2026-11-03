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
        } catch (\Throwable $e) {
            \Log::warning('AI Service embedding generation failed: ' . $e->getMessage());
        }

        $hasAiEmbedding = !empty($queryEmbeddingData) && !empty($queryEmbeddingData['embedding']);
        $inputEmbedding = $hasAiEmbedding ? $queryEmbeddingData['embedding'] : null;

        $researches = Research::where('status', 'approved')
            ->with('embedding')
            ->get();

        $results = [];

        foreach ($researches as $research)
        {
            $aiTitleScore = null;
            $aiObjectiveScore = null;
            $aiContextScore = null;
            $aiContentScore = null;

            // 1. Semantic Embedding Comparison (AI Vectors)
            if ($hasAiEmbedding && $research->embedding) {
                if (!empty($research->embedding->title_embedding)) {
                    $decoded = json_decode($research->embedding->title_embedding, true);
                    if (is_array($decoded)) {
                        $res = $ai->compare($inputEmbedding, $decoded);
                        $aiTitleScore = $res['similarity'] ?? null;
                    }
                }

                if (!empty($research->embedding->objective_embedding)) {
                    $decoded = json_decode($research->embedding->objective_embedding, true);
                    if (is_array($decoded)) {
                        $res = $ai->compare($inputEmbedding, $decoded);
                        $aiObjectiveScore = $res['similarity'] ?? null;
                    }
                }

                if (!empty($research->embedding->context_embedding)) {
                    $decoded = json_decode($research->embedding->context_embedding, true);
                    if (is_array($decoded)) {
                        $res = $ai->compare($inputEmbedding, $decoded);
                        $aiContextScore = $res['similarity'] ?? null;
                    }
                }

                if (!empty($research->embedding->content_embedding)) {
                    $decoded = json_decode($research->embedding->content_embedding, true);
                    if (is_array($decoded)) {
                        $res = $ai->compare($inputEmbedding, $decoded);
                        $aiContentScore = $res['similarity'] ?? null;
                    }
                }
            }

            // 2. High-Accuracy Lexical & Phrase Matching
            $lexTitleScore = $this->computeLexicalScore($query, (string)$research->title);

            $lexObjectiveScore = !empty($research->objectives)
                ? $this->computeLexicalScore($query, (string)$research->objectives)
                : null;

            $lexContextScore = !empty($research->project_context)
                ? $this->computeLexicalScore($query, (string)$research->project_context)
                : null;

            $fullContent = trim($research->title . ' ' . $research->project_context . ' ' . $research->abstract . ' ' . $research->objectives);
            $lexContentScore = $this->computeLexicalScore($query, $fullContent);

            // 3. Hybrid Blending (Vector Semantic + Lexical Match)
            // Evaluates both AI semantic alignment and exact keyword/phrase containment
            $titleScore = $this->blendScores($aiTitleScore, $lexTitleScore);
            $objectiveScore = $this->blendScores($aiObjectiveScore, $lexObjectiveScore);
            $contextScore = $this->blendScores($aiContextScore, $lexContextScore);
            $contentScore = $this->blendScores($aiContentScore, $lexContentScore);

            // Collect all non-null scores
            $availableScores = array_filter([
                'title'     => $titleScore,
                'objective' => $objectiveScore,
                'context'   => $contextScore,
                'content'   => $contentScore
            ], fn($s) => $s !== null);

            if (empty($availableScores)) {
                continue;
            }

            // Strongest match dimension
            $maxScore = max($availableScores);
            $primaryMatch = 'Overall Topic';

            if ($titleScore !== null && abs($maxScore - $titleScore) < 0.001) {
                $primaryMatch = 'Title Match';
            } elseif ($objectiveScore !== null && abs($maxScore - $objectiveScore) < 0.001) {
                $primaryMatch = 'Objectives Match';
            } elseif ($contextScore !== null && abs($maxScore - $contextScore) < 0.001) {
                $primaryMatch = 'Context Match';
            } else {
                $primaryMatch = 'Topic / Abstract Match';
            }

            $overall = round($maxScore, 1);

            // Filter by minimum score threshold
            if ($overall >= config('similarity.minimum_score', 50)) {
                $results[] = [
                    'research'             => $research,
                    'title_similarity'     => $titleScore !== null ? round($titleScore, 1) : null,
                    'objective_similarity' => $objectiveScore !== null ? round($objectiveScore, 1) : null,
                    'context_similarity'   => $contextScore !== null ? round($contextScore, 1) : null,
                    'content_similarity'   => $contentScore !== null ? round($contentScore, 1) : null,
                    'primary_match'        => $primaryMatch,
                    'overall_similarity'   => $overall
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
     * Blend semantic AI vector similarity with lexical/keyword similarity.
     */
    private function blendScores(?float $aiScore, ?float $lexScore): ?float
    {
        if ($aiScore === null && $lexScore === null) {
            return null;
        }
        if ($aiScore === null) {
            return $lexScore;
        }
        if ($lexScore === null || $lexScore <= 0.0) {
            return $aiScore;
        }

        // When both are present: if lexical match is strong (e.g. keywords in title),
        // provide a balanced blend that preserves high semantic or high lexical matches
        return max($aiScore, (0.5 * $aiScore) + (0.5 * $lexScore));
    }

    /**
     * Helper to extract clean word tokens for string comparison.
     */
    private function tokenize(string $text): array
    {
        $text = strtolower(strip_tags($text));
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
        return array_values(array_filter(explode(' ', $text), fn($w) => strlen($w) > 2));
    }

    /**
     * Compute comprehensive lexical score using Containment, Dice coefficient, and Phrase Matching.
     * Prevents short queries (e.g. "booking system") from being artificially penalized when matched
     * against realistic academic titles.
     */
    private function computeLexicalScore(string $query, string $target): float
    {
        $queryClean = strtolower(trim(strip_tags($query)));
        $targetClean = strtolower(trim(strip_tags($target)));

        if ($queryClean === '' || $targetClean === '') {
            return 0.0;
        }

        // Exact full string match
        if ($queryClean === $targetClean) {
            return 100.0;
        }

        // Contiguous phrase match (e.g. "booking system" appearing verbatim in target title)
        $phraseBonus = 0.0;
        if (str_contains($targetClean, $queryClean) || (strlen($queryClean) > 10 && str_contains($queryClean, $targetClean))) {
            $phraseBonus = 82.0;
        }

        $qTokens = $this->tokenize($queryClean);
        $tTokens = $this->tokenize($targetClean);

        if (empty($qTokens) || empty($tTokens)) {
            return $phraseBonus;
        }

        $intersection = count(array_intersect($qTokens, $tTokens));
        if ($intersection === 0) {
            return 0.0;
        }

        $minCount = min(count($qTokens), count($tTokens));
        $containment = ($intersection / $minCount) * 100.0;
        $dice = ((2.0 * $intersection) / (count($qTokens) + count($tTokens))) * 100.0;

        // Base guarantee: if all query tokens are present in target, guarantee high score
        $baseScore = ($intersection >= count($qTokens)) ? 75.0 : 0.0;

        $lexical = (0.6 * $containment) + (0.4 * $dice);

        return round(max($phraseBonus, $baseScore, $lexical), 1);
    }
}
