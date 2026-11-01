<?php

namespace App\Http\Controllers;

use App\Models\Research;
use App\Models\ResearchCategory;
use App\Models\SearchLog;
use App\Services\AI\AIService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Dedicated search results page for homepage and global search.
     */
    public function index(Request $request, AIService $ai)
    {
        session()->forget('current_viewed_research_id');
        $query = trim($request->input('q') ?? $request->input('query') ?? '');
        $categoryId = $request->integer('category') ?: null;
        $field = $request->input('field', 'any');

        $categories = ResearchCategory::withCount([
            'classifiedResearches as classified_researches_count' => fn ($researches) => $researches->where('status', 'approved'),
        ])->orderBy('name', 'asc')->get();

        if ($categoryId && ! $categories->contains('id', $categoryId)) {
            $categoryId = null;
        }

        $results = collect();
        $searchMode = 'none';

        if ($query !== '') {
            // Log search term
            SearchLog::create([
                'user_id'              => auth()->id(),
                'query'                => $query,
                'research_category_id' => $categoryId,
            ]);

            $searchMode = 'semantic';

            try {
                $queryEmbedding = $ai->generateEmbedding($query);

                if (!empty($queryEmbedding['embedding'])) {
                    $dbQuery = Research::where('status', 'approved')
                        ->with(['embedding', 'category']);

                    if ($categoryId) {
                        $dbQuery->whereHas('categories', fn ($cat) => $cat->whereKey($categoryId));
                    }

                    $allResearches = $dbQuery->get();

                    foreach ($allResearches as $research) {
                        if (!$research->embedding || !$research->embedding->content_embedding) {
                            continue;
                        }

                        $storedEmbedding = json_decode($research->embedding->content_embedding, true);

                        $score = $ai->compare($queryEmbedding['embedding'], $storedEmbedding);
                        $similarity = $score['similarity'] ?? 0;

                        if ($similarity >= config('similarity.minimum_score', 55)) {
                            $results->push([
                                'research'   => $research,
                                'similarity' => round($similarity, 1),
                            ]);
                        }
                    }

                    $results = $results->sortByDesc('similarity')->values();
                }
            } catch (\Throwable $e) {
                // If AI is offline, fallback gracefully to keyword search
                $searchMode = 'keyword';
            }

            // Keyword variants to handle diacritics/accents (e.g. Estano <=> Estaño)
            $queryVariants = [$query];
            $queryVariants[] = str_replace(['ñ', 'Ñ'], ['n', 'N'], $query);
            $queryVariants[] = preg_replace('/n/i', 'ñ', $query);
            $queryVariants = array_values(array_unique(array_filter($queryVariants)));

            // Direct keyword matching for authors, title, abstract
            $keywordDbQuery = Research::where('status', 'approved')
                ->with('category')
                ->where(function ($q) use ($queryVariants, $field) {
                    foreach ($queryVariants as $qv) {
                        if ($field === 'title') {
                            $q->orWhere('title', 'like', "%{$qv}%");
                        } elseif ($field === 'authors') {
                            $q->orWhere('authors', 'like', "%{$qv}%");
                        } elseif ($field === 'advisor') {
                            $q->orWhere('authors', 'like', "%{$qv}%")
                              ->orWhere('abstract', 'like', "%{$qv}%");
                        } else {
                            $q->orWhere('title', 'like', "%{$qv}%")
                              ->orWhere('abstract', 'like', "%{$qv}%")
                              ->orWhere('keywords', 'like', "%{$qv}%")
                              ->orWhere('authors', 'like', "%{$qv}%")
                              ->orWhere('objectives', 'like', "%{$qv}%");
                        }
                    }
                });

            if ($categoryId) {
                $keywordDbQuery->whereHas('categories', fn ($cat) => $cat->whereKey($categoryId));
            }

            $keywordResults = $keywordDbQuery->latest('approved_at')->get();

            // Keyword fallback if AI gave no results or is offline
            if ($searchMode === 'keyword' || $results->isEmpty()) {
                $searchMode = 'keyword';
                $results = $keywordResults->map(fn($r) => [
                    'research'   => $r,
                    'similarity' => null,
                ]);
            } else {
                // Merge direct author/title matches to the top so author searches are never omitted
                $existingIds = $results->pluck('research.id')->all();
                $additionalMatches = $keywordResults->reject(fn($r) => in_array($r->id, $existingIds))
                    ->map(fn($r) => [
                        'research'   => $r,
                        'similarity' => 100,
                    ]);
                $results = $additionalMatches->concat($results);
            }
        }

        return view('search.index', [
            'results'    => $results,
            'query'      => $query,
            'categoryId' => $categoryId,
            'categories' => $categories,
            'searchMode' => $searchMode,
            'field'      => $field,
        ]);
    }
}
