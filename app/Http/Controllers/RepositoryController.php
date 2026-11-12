<?php

namespace App\Http\Controllers;

use App\Models\Research;
use App\Models\ResearchCategory;
use App\Models\SearchLog;
use App\Services\AI\AIService;
use Illuminate\Http\Request;

class RepositoryController extends Controller
{
    /**
     * Resolve the correct repository view based on the auth user's role.
     */
    private function resolveView(): string
    {
        if (auth()->check()) {
            $role = auth()->user()->role;
            if ($role === 'admin')  return 'repository.admin-index';
            if ($role === 'student') return 'repository.student-index';
        }
        return 'repository.index';
    }

    public function index(Request $request, AIService $ai)
    {
        // Reset currently viewed research session when user exits to the catalog
        session()->forget('current_viewed_research_id');

        // Semantic search is exclusively available to authenticated Students and Admins.
        // For guest/public visitors, any search query input is strictly ignored.
        $isAuth = auth()->check();
        $query  = $isAuth ? trim($request->input('query') ?? $request->input('search') ?? '') : '';

        $categoryId = $request->integer('category') ?: null;
        $categories = ResearchCategory::where(function ($q) {
            $q->whereHas('classifiedResearches', fn ($r) => $r->where('status', 'approved'))
              ->orWhereHas('researches', fn ($r) => $r->where('status', 'approved'));
        })
        ->withCount([
            'classifiedResearches as classified_researches_count' => fn ($researches) => $researches->where('status', 'approved'),
            'researches as direct_researches_count' => fn ($researches) => $researches->where('status', 'approved'),
        ])->orderBy('name', 'asc')->get();

        foreach ($categories as $cat) {
            $cat->classified_researches_count = max(
                (int) ($cat->classified_researches_count ?? 0),
                (int) ($cat->direct_researches_count ?? 0)
            );
        }

        if ($categoryId && ! $categories->contains('id', $categoryId)) {
            $categoryId = null;
        }

        // ── Semantic Search for Authenticated Users ──
        if ($isAuth && $query !== '') {
            try {
                SearchLog::create([
                    'user_id'              => auth()->id(),
                    'query'                => mb_substr($query, 0, 250),
                    'research_category_id' => $categoryId,
                ]);
            } catch (\Throwable $e) {
                \Log::warning('SearchLog insert skipped: ' . $e->getMessage());
            }

            $searchMode = 'semantic';
            $results    = collect();

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

                        if ($similarity >= config('similarity.minimum_score', 60)) {
                            $results->push([
                                'research'   => $research,
                                'similarity' => round($similarity, 1),
                            ]);
                        }
                    }

                    $results = $results->sortByDesc('similarity')->values();
                }
            } catch (\Throwable $e) {
                // AI service unavailable – fall back to keyword search
                $searchMode = 'keyword';
            }

            // ── Keyword fallback (AI down OR no embedding results) ────────────
            if ($searchMode === 'keyword' || $results->isEmpty()) {
                $searchMode = 'keyword';

                $dbQuery = Research::where('status', 'approved')
                    ->with('category')
                    ->where(function ($q) use ($query) {
                        $q->where('title', 'like', "%{$query}%")
                          ->orWhere('abstract', 'like', "%{$query}%")
                          ->orWhere('project_context', 'like', "%{$query}%")
                          ->orWhere('keywords', 'like', "%{$query}%")
                          ->orWhere('authors', 'like', "%{$query}%")
                          ->orWhere('objectives', 'like', "%{$query}%");
                    });

                if ($categoryId) {
                    $dbQuery->whereHas('categories', fn ($cat) => $cat->whereKey($categoryId));
                }

                $keywordResults = $dbQuery->latest('approved_at')->get();

                $results = $keywordResults->map(fn($r) => [
                    'research'   => $r,
                    'similarity' => null,
                ]);
            }

            $selectedCategories = $categoryId ? [$categoryId] : [];
            $selectedYears      = [];
            $selectedAuthors    = [];
            $sort               = $request->input('sort', 'newest');
            $authorsList        = $this->getAuthorsList();
            $yearsList          = $this->getYearsList();
            $recommendations    = collect();

            return view($this->resolveView(), [
                'researches'         => $results,
                'categories'         => $categories,
                'query'              => $query,
                'searchMode'         => $searchMode,
                'recommendations'    => $recommendations,
                'categoryId'         => $categoryId,
                'selectedCategories' => $selectedCategories,
                'selectedYears'      => $selectedYears,
                'selectedAuthors'    => $selectedAuthors,
                'selectedSort'       => $sort,
                'authorsList'        => $authorsList,
                'yearsList'          => $yearsList,
            ]);
        }

        // ── Browse Mode (Guest visitors or Authenticated users without query) ──
        $dbQuery = Research::where('status', 'approved')
            ->with(['category', 'categories'])
            ->withCount(['downloadRequests as approved_downloads_count' => function ($q) {
                $q->where('status', 'approved');
            }]);

        // Multi-category or single category filter
        $selectedCategories = $request->input('categories');
        if (!is_array($selectedCategories)) {
            $selectedCategories = $categoryId ? [$categoryId] : [];
        }
        $selectedCategories = array_filter(array_map('intval', $selectedCategories));

        if (!empty($selectedCategories)) {
            $dbQuery->where(function ($q) use ($selectedCategories) {
                $q->whereIn('research_category_id', $selectedCategories)
                  ->orWhereHas('categories', fn ($cat) => $cat->whereIn('research_categories.id', $selectedCategories));
            });
        }

        // Year filter
        $selectedYears = (array) $request->input('years', []);
        $selectedYears = array_filter($selectedYears);
        if (!empty($selectedYears)) {
            $dbQuery->where(function ($q) use ($selectedYears) {
                foreach ($selectedYears as $yr) {
                    if ($yr === 'older' || $yr === 'Older') {
                        $q->orWhereYear('approved_at', '<', 2022)
                          ->orWhere('year', '<', 2022);
                    } else {
                        $q->orWhereYear('approved_at', $yr)
                          ->orWhere('year', $yr);
                    }
                }
            });
        }

        // Author filter with accent/diacritic tolerance (e.g. Estano <=> Estaño)
        $selectedAuthors = (array) $request->input('authors', []);
        $selectedAuthors = array_filter($selectedAuthors);
        if (!empty($selectedAuthors)) {
            $dbQuery->where(function ($q) use ($selectedAuthors) {
                foreach ($selectedAuthors as $author) {
                    $variants = [$author];
                    $variants[] = str_replace(['ñ', 'Ñ'], ['n', 'N'], $author);
                    $variants[] = preg_replace('/n/i', 'ñ', $author);
                    $variants = array_values(array_unique(array_filter($variants)));
                    foreach ($variants as $v) {
                        $q->orWhere('authors', 'like', "%{$v}%");
                    }
                }
            });
        }

        // Keyword filter for guest search (title, authors, abstract, keywords)
        $keyword = trim($request->input('keyword') ?? $request->input('search') ?? $request->input('q') ?? '');
        if ($keyword !== '') {
            $keywordVariants = [$keyword];
            $keywordVariants[] = str_replace(['ñ', 'Ñ'], ['n', 'N'], $keyword);
            $keywordVariants[] = preg_replace('/n/i', 'ñ', $keyword);
            $keywordVariants = array_values(array_unique(array_filter($keywordVariants)));

            $dbQuery->where(function ($q) use ($keywordVariants) {
                foreach ($keywordVariants as $kw) {
                    $q->orWhere('title', 'like', "%{$kw}%")
                      ->orWhere('authors', 'like', "%{$kw}%")
                      ->orWhere('abstract', 'like', "%{$kw}%")
                      ->orWhere('keywords', 'like', "%{$kw}%");
                }
            });
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $dbQuery->oldest('approved_at');
                break;
            case 'views':
                $dbQuery->orderByDesc('views_count');
                break;
            case 'title':
                $dbQuery->orderBy('title', 'asc');
                break;
            case 'newest':
            default:
                $sort = 'newest';
                $dbQuery->latest('approved_at');
                break;
        }

        $researches = $dbQuery->paginate(9)->withQueryString();

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Infinite-Scroll')) {
            $html = view('repository.partials.card-items', compact('researches'))->render();
            return response()->json([
                'html' => $html,
                'hasMore' => $researches->hasMorePages(),
                'nextPageUrl' => $researches->nextPageUrl(),
                'currentPage' => $researches->currentPage(),
                'total' => $researches->total(),
            ]);
        }

        // ── Dynamic Authors & Years List for Filter ─────────────────────
        $authorsList = $this->getAuthorsList();
        $yearsList   = $this->getYearsList();

        // ── Recommendations: latest 3 from other categories ──────────────────
        $recommendations = Research::where('status', 'approved')
            ->with('category')
            ->when(!empty($selectedCategories), fn ($q) => $q->whereDoesntHave('categories', fn ($cat) => $cat->whereIn('research_categories.id', $selectedCategories)))
            ->latest('approved_at')
            ->take(3)
            ->get();

        return view($this->resolveView(), [
            'researches'         => $researches,
            'categories'         => $categories,
            'selectedCategories' => $selectedCategories,
            'selectedYears'      => $selectedYears,
            'selectedAuthors'    => $selectedAuthors,
            'selectedSort'       => $sort,
            'authorsList'        => $authorsList,
            'yearsList'          => $yearsList,
            'query'              => '',
            'searchMode'         => 'browse',
            'recommendations'    => $recommendations,
            'categoryId'         => count($selectedCategories) === 1 ? reset($selectedCategories) : null,
        ]);
    }

    public function show(Research $research, AIService $ai)
    {
        if ($research->status !== 'approved') {
            abort(404);
        }

        if (request()->routeIs('repository.full')) {
            return redirect()->route('repository.show', $research)->with('show_pdf_modal', true);
        }

        // Increment view count only when entering or re-entering the project after exiting.
        // If the user is currently viewing this project and refreshes the browser, do NOT increment.
        if (session('current_viewed_research_id') !== $research->id) {
            $research->increment('views_count');
            session(['current_viewed_research_id' => $research->id]);
        }

        $related = collect([]);

        if ($research->embedding && $research->embedding->content_embedding) {
            try {
                $currentEmbedding = json_decode(
                    $research->embedding->content_embedding,
                    true
                );

                $researches = Research::where('id', '!=', $research->id)
                    ->where('status', 'approved')
                    ->with('embedding')
                    ->get();

                $scores = [];

                foreach ($researches as $item) {
                    if (!$item->embedding || !$item->embedding->content_embedding) {
                        continue;
                    }

                    $storedEmbedding = json_decode(
                        $item->embedding->content_embedding,
                        true
                    );

                    $result = $ai->compare(
                        $currentEmbedding,
                        $storedEmbedding
                    );

                    $similarity = $result['similarity'] ?? 0;

                    if ($similarity >= config('similarity.minimum_score', 60)) {
                        $scores[] = [
                            'research'   => $item,
                            'similarity' => $similarity
                        ];
                    }
                }

                $related = collect($scores)
                    ->sortByDesc('similarity')
                    ->take(5);
            } catch (\Throwable $e) {
                // AI service unavailable or offline: continue rendering research details gracefully
                $related = collect([]);
            }
        }

        return view('repository.show', [
            'research' => $research,
            'related'  => $related
        ]);
    }

    /**
     * Get dynamic list of approved authors for repository filtering.
     */
    private function getAuthorsList()
    {
        $rawAuthorsList = Research::where('status', 'approved')
            ->whereNotNull('authors')
            ->pluck('authors');

        $authorsList = collect();
        foreach ($rawAuthorsList as $rawStr) {
            $rawStr = trim($rawStr);
            if (empty($rawStr)) continue;

            if (str_contains($rawStr, ';')) {
                $parts = explode(';', $rawStr);
            } elseif (str_contains($rawStr, "\n")) {
                $parts = explode("\n", $rawStr);
            } else {
                $commaParts = array_map('trim', explode(',', $rawStr));
                if (count($commaParts) === 2 && !str_contains($commaParts[0], ' ') && !str_contains($commaParts[1], ' and ')) {
                    $parts = [$rawStr];
                } else {
                    $parts = $commaParts;
                }
            }

            foreach ($parts as $p) {
                $clean = trim($p);
                if (!empty($clean) && strlen($clean) >= 2) {
                    $authorsList->push($clean);
                }
            }
        }

        $authorsList = $authorsList->unique(fn ($a) => mb_strtolower($a))->sort(SORT_NATURAL | SORT_FLAG_CASE)->values();

        return $authorsList;
    }

    /**
     * Get dynamic list of distinct years for approved researches.
     */
    private function getYearsList()
    {
        $approvedResearches = Research::where('status', 'approved')
            ->get(['year', 'approved_at']);

        $years = collect();
        foreach ($approvedResearches as $research) {
            if (!empty($research->year)) {
                $years->push((string) $research->year);
            } elseif ($research->approved_at) {
                $years->push((string) $research->approved_at->year);
            }
        }

        return $years->filter()->unique()->sortDesc()->values();
    }
}
