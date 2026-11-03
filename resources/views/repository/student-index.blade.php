@extends('layouts.app')
@section('title', 'Browse Repository — Student Portal')

@section('content')
<style>
    .sd-card {
        background-color: var(--guest-bg-surface, #ffffff);
        border: 1px solid var(--guest-border, #e2e8f0);
        border-radius: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }
    .sd-card-subtle {
        background-color: var(--guest-bg-input, #f8fafc);
        border: 1px solid var(--guest-border, #e2e8f0);
        border-radius: 0.75rem;
    }
    .sd-title {
        color: var(--guest-text-headings, #0f172a) !important;
    }
    .sd-text {
        color: var(--guest-text-main, #334155) !important;
    }
    .sd-muted {
        color: var(--guest-text-muted, #64748b) !important;
    }
    .sd-border {
        border-color: var(--guest-border, #e2e8f0) !important;
    }
    .sd-accent {
        color: var(--guest-accent, #059669) !important;
    }
    .sd-divider {
        border-bottom: 1px solid var(--guest-border, #f1f5f9);
    }

    /* Minimal Search Box */
    .browse-min-search {
        background-color: var(--guest-bg-surface, #ffffff);
        border: 1.5px solid var(--guest-border, #cbd5e1);
        border-radius: 1rem;
        transition: all 0.2s ease;
    }
    .browse-min-search:focus-within {
        border-color: #0c4233;
        box-shadow: 0 0 0 3px rgba(12, 66, 51, 0.1);
    }

    /* Filter Projects Dropdown Panel */
    .filter-panel-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        width: 380px;
        max-width: calc(100vw - 32px);
        background-color: var(--guest-bg-surface, #ffffff);
        border: 1px solid var(--guest-border, #cbd5e1);
        border-radius: 1rem;
        box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.15), 0 10px 15px -5px rgba(0, 0, 0, 0.04);
        z-index: 50;
        display: none;
        flex-direction: column;
        max-height: 82vh;
        overflow: hidden;
        animation: panelFadeSlide 0.18s ease-out;
    }
    .filter-panel-dropdown.open {
        display: flex;
    }

    @keyframes panelFadeSlide {
        from { opacity: 0; transform: translateY(-6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Custom Accent Checkboxes */
    .custom-repo-cb {
        accent-color: #0c4233;
        width: 15px;
        height: 15px;
        border-radius: 4px;
        cursor: pointer;
    }

    /* Chevron rotation */
    .filter-section-chevron {
        transition: transform 0.2s ease;
    }
    .filter-section.collapsed .filter-section-chevron {
        transform: rotate(-90deg);
    }
    .filter-section.collapsed .filter-section-body {
        display: none;
    }

    .filter-trigger-btn.active .trigger-chevron {
        transform: rotate(180deg);
    }
    .trigger-chevron {
        transition: transform 0.2s ease;
    }
</style>

<div class="space-y-6 max-w-7xl mx-auto pb-16">
    {{-- ================= TOP HERO & SEARCH BAR ================= --}}
    <div class="sd-card p-6 sm:p-8">
        <div class="max-w-3xl">
            <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider sd-muted">Student Portal</span>
                <span class="sd-muted opacity-40">/</span>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Browse Repository</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight sd-title">
                Institutional Capstone Repository
            </h1>
            <p class="text-xs sm:text-sm sd-muted mt-1 leading-relaxed">
                Discover peer-reviewed undergraduate theses, capstone projects, and software systems with AI-driven semantic similarity discovery.
            </p>

            {{-- Stat Pills Row --}}
            <div class="flex items-center gap-3 mt-4 flex-wrap">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 sd-card-subtle text-xs font-semibold sd-text">
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold font-mono">{{ \App\Models\Research::where('status', 'approved')->count() }}+</span> Published Studies
                </div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 sd-card-subtle text-xs font-semibold sd-text">
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold font-mono">{{ $categories->count() }}</span> Disciplines
                </div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 sd-card-subtle text-xs font-semibold sd-text">
                    <span class="text-purple-600 dark:text-purple-400 font-bold">AI Vector</span> Semantic Matching
                </div>
            </div>
        </div>

        {{-- Minimal Search Form --}}
        <form action="{{ route('repository.index') }}" method="GET" class="mt-6">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            @if(!empty($selectedCategories))
                @foreach($selectedCategories as $sc)
                    <input type="hidden" name="categories[]" value="{{ $sc }}">
                @endforeach
            @endif

            <div class="browse-min-search flex items-center p-1.5 gap-2">
                <div class="pl-3 sd-muted">
                    <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </div>

                <input 
                    type="text" 
                    name="query" 
                    id="mainSearchQueryInput"
                    value="{{ $query }}" 
                    placeholder="Search by research title, keywords, authors, concept, or abstract..."
                    class="w-full bg-transparent border-0 outline-none px-2 py-2 text-xs sm:text-sm font-medium sd-title placeholder:sd-muted"
                    autocomplete="off"
                >

                @if($query)
                    <a href="{{ route('repository.index', array_filter(['categories' => $selectedCategories])) }}" 
                       class="px-2 py-1 text-xs font-bold sd-muted hover:text-rose-500 transition" 
                       title="Clear search query">
                        ✕
                    </a>
                @endif

                <button type="submit" class="px-4 sm:px-5 py-2.5 bg-[#0c4233] text-white hover:bg-[#115e47] rounded-xl text-xs font-bold transition flex items-center gap-1.5 shrink-0 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Search</span>
                </button>
            </div>
        </form>
    </div>

    @php
        $selectedCategories = $selectedCategories ?? ($categoryId ? [$categoryId] : []);
        $selectedYears      = $selectedYears ?? [];
        $selectedAuthors    = $selectedAuthors ?? [];
        $selectedSort       = $selectedSort ?? 'newest';
        $authorsList        = $authorsList ?? collect();
        $keywordFilter      = request('keyword', request('search', request('q', '')));

        $activeFiltersCount = count($selectedCategories) + count($selectedYears) + count($selectedAuthors) + ($keywordFilter !== '' ? 1 : 0);
        $items = $query ? $researches : ($researches instanceof \Illuminate\Pagination\LengthAwarePaginator ? $researches->items() : $researches);
        $totalCount = $query ? count($researches) : ($researches instanceof \Illuminate\Pagination\LengthAwarePaginator ? $researches->total() : count($researches));
    @endphp

    {{-- ================= SECTION HEADER: TITLE, COUNT, FILTER & SORT ================= --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pt-1">
        <div>
            <h2 class="text-lg sm:text-xl font-bold tracking-tight sd-title">
                Capstone Projects
            </h2>
            <p class="text-xs sd-muted mt-0.5">
                @if($query)
                    Found <span class="font-bold sd-title">{{ $totalCount }}</span> result(s) for "<span class="font-semibold sd-title">{{ $query }}</span>"
                    @if(isset($searchMode) && $searchMode === 'semantic')
                        <span class="inline-block ml-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-500/10 text-purple-700 dark:text-purple-300 border border-purple-500/20">AI Vector Match</span>
                    @endif
                @else
                    Showing <span class="font-bold sd-title">{{ number_format($totalCount) }}</span> approved research studies
                @endif
            </p>
        </div>

        {{-- Controls: Filter Button & Sort Select --}}
        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
            <!-- Filter Projects Dropdown Button -->
            <div class="relative" id="filterDropdownWrapper">
                <button type="button" 
                        id="filterDropdownToggle" 
                        onclick="toggleFilterDropdown()"
                        class="filter-trigger-btn sd-card px-3.5 py-2 text-xs font-bold sd-title inline-flex items-center gap-2 hover:border-[#0c4233] transition cursor-pointer {{ $activeFiltersCount > 0 ? 'border-[#0c4233]' : '' }}">
                    <svg class="w-3.5 h-3.5 sd-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <line x1="4" y1="21" x2="4" y2="14"></line>
                        <line x1="4" y1="10" x2="4" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12" y2="3"></line>
                        <line x1="20" y1="21" x2="20" y2="16"></line>
                        <line x1="20" y1="12" x2="20" y2="3"></line>
                        <line x1="1" y1="14" x2="7" y2="14"></line>
                        <line x1="9" y1="8" x2="15" y2="8"></line>
                        <line x1="17" y1="16" x2="23" y2="16"></line>
                    </svg>
                    <span>Filters</span>
                    @if($activeFiltersCount > 0)
                        <span class="bg-[#0c4233] text-white text-[10px] font-extrabold px-1.5 py-0.2 rounded-full">
                            {{ $activeFiltersCount }}
                        </span>
                    @endif
                    <svg class="trigger-chevron w-3 h-3 sd-muted ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>

                <!-- Filter Panel Popup -->
                <div class="filter-panel-dropdown" id="filterDropdownPanel">
                    <form action="{{ route('repository.index') }}" method="GET" id="browseFilterForm">
                        @if($query)
                            <input type="hidden" name="query" value="{{ $query }}">
                        @endif
                        <input type="hidden" name="sort" id="filterSortHidden" value="{{ $selectedSort }}">

                        <!-- Panel Header -->
                        <div class="p-4 border-b sd-border bg-slate-50/50 dark:bg-white/[0.02]">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold uppercase tracking-wider sd-title">Filter Projects</span>
                                    @if($activeFiltersCount > 0)
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20">
                                            {{ $activeFiltersCount }} active
                                        </span>
                                    @endif
                                </div>
                                <button type="button" onclick="closeFilterDropdown()" class="w-6 h-6 rounded-full sd-card-subtle flex items-center justify-center text-xs sd-muted hover:text-rose-500 transition" title="Close">
                                    ✕
                                </button>
                            </div>

                            <!-- Real-time keyword filter input -->
                            <div class="mt-3">
                                <input 
                                    type="text" 
                                    name="keyword" 
                                    id="filterKeywordInput" 
                                    class="w-full sd-card-subtle px-3 py-1.5 text-xs sd-title outline-none rounded-lg focus:border-[#0c4233] transition" 
                                    placeholder="Filter by author or keyword..." 
                                    autocomplete="off"
                                    value="{{ $keywordFilter }}"
                                >
                            </div>
                        </div>

                        <!-- Scrollable Filter Sections -->
                        <div class="overflow-y-auto max-h-72 p-4 space-y-4">
                            <!-- Category Filter -->
                            <div class="filter-section" id="sectionCategory">
                                <div class="flex items-center justify-between text-xs font-bold uppercase tracking-wider sd-muted cursor-pointer select-none mb-2" onclick="toggleFilterSection('sectionCategory')">
                                    <span>Category ({{ $categories->count() }})</span>
                                    <svg class="filter-section-chevron w-3.5 h-3.5 sd-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </div>
                                <div class="filter-section-body space-y-1.5 pt-1">
                                    @foreach($categories as $cat)
                                        <label class="flex items-center justify-between text-xs sd-text hover:sd-title cursor-pointer select-none py-0.5">
                                            <div class="flex items-center gap-2 truncate">
                                                <input 
                                                    type="checkbox" 
                                                    name="categories[]" 
                                                    value="{{ $cat->id }}" 
                                                    class="custom-repo-cb filter-category-cb"
                                                    {{ in_array($cat->id, $selectedCategories) ? 'checked' : '' }}
                                                >
                                                <span class="truncate">{{ $cat->name }}</span>
                                            </div>
                                            <span class="text-[10px] sd-muted font-mono ml-2">({{ $cat->classified_researches_count }})</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Author Filter -->
                            <div class="filter-section" id="sectionAuthor">
                                <div class="flex items-center justify-between text-xs font-bold uppercase tracking-wider sd-muted cursor-pointer select-none mb-2" onclick="toggleFilterSection('sectionAuthor')">
                                    <span>Author ({{ $authorsList->count() }})</span>
                                    <svg class="filter-section-chevron w-3.5 h-3.5 sd-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </div>
                                <div class="filter-section-body pt-1">
                                    @php
                                        $authorsToShow = $authorsList->take(6);
                                        $hiddenAuthors = $authorsList->skip(6);
                                        $hasHiddenSelected = !empty($selectedAuthors) && $hiddenAuthors->intersect($selectedAuthors)->isNotEmpty();
                                    @endphp

                                    <div class="space-y-1.5">
                                        @foreach($authorsToShow as $author)
                                            <label class="flex items-center gap-2 text-xs sd-text hover:sd-title cursor-pointer select-none py-0.5">
                                                <input 
                                                    type="checkbox" 
                                                    name="authors[]" 
                                                    value="{{ $author }}" 
                                                    class="custom-repo-cb filter-author-cb"
                                                    {{ in_array($author, $selectedAuthors) ? 'checked' : '' }}
                                                >
                                                <span class="truncate">{{ $author }}</span>
                                            </label>
                                        @endforeach
                                    </div>

                                    @if($authorsList->count() > 6)
                                        <div class="space-y-1.5 mt-1.5" id="moreAuthorsList" style="display: {{ $hasHiddenSelected ? 'block' : 'none' }};">
                                            @foreach($hiddenAuthors as $author)
                                                <label class="flex items-center gap-2 text-xs sd-text hover:sd-title cursor-pointer select-none py-0.5">
                                                    <input 
                                                        type="checkbox" 
                                                        name="authors[]" 
                                                        value="{{ $author }}" 
                                                        class="custom-repo-cb filter-author-cb"
                                                        {{ in_array($author, $selectedAuthors) ? 'checked' : '' }}
                                                    >
                                                    <span class="truncate">{{ $author }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <button type="button" class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 mt-2 block" id="showMoreAuthorsBtn" onclick="toggleMoreAuthors()">
                                            {{ $hasHiddenSelected ? '- Show Less' : '+ Show More (' . ($authorsList->count() - 6) . ' more)' }}
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Year Filter -->
                            <div class="filter-section" id="sectionYear">
                                <div class="flex items-center justify-between text-xs font-bold uppercase tracking-wider sd-muted cursor-pointer select-none mb-2" onclick="toggleFilterSection('sectionYear')">
                                    <span>Publication Year</span>
                                    <svg class="filter-section-chevron w-3.5 h-3.5 sd-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </div>
                                <div class="filter-section-body grid grid-cols-2 gap-2 pt-1">
                                    @foreach(['2026', '2025', '2024', '2023', '2022', 'Older'] as $yr)
                                        <label class="flex items-center gap-2 text-xs sd-text hover:sd-title cursor-pointer select-none">
                                            <input 
                                                type="checkbox" 
                                                name="years[]" 
                                                value="{{ strtolower($yr) }}" 
                                                class="custom-repo-cb filter-year-cb"
                                                {{ in_array(strtolower($yr), array_map('strtolower', $selectedYears)) ? 'checked' : '' }}
                                            >
                                            <span>{{ $yr }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Panel Footer -->
                        <div class="p-3.5 border-t sd-border bg-slate-50/50 dark:bg-white/[0.02] flex items-center justify-between gap-3">
                            @if($activeFiltersCount > 0)
                                <a href="{{ route('repository.index', array_filter(['query' => $query])) }}" class="text-xs font-bold text-rose-600 dark:text-rose-400 hover:underline">
                                    ✕ Reset All
                                </a>
                            @else
                                <span></span>
                            @endif

                            <button type="submit" class="px-4 py-1.5 bg-[#0c4233] text-white hover:bg-[#115e47] rounded-lg text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                                <span>Apply Filters</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sort By Select Pill -->
            <div class="sd-card px-2.5 py-1 flex items-center gap-1.5 text-xs">
                <span class="sd-muted font-medium text-[11px] hidden sm:inline">Sort:</span>
                <select class="bg-transparent border-0 outline-none text-xs font-bold sd-title cursor-pointer pr-1" onchange="applySort(this.value)">
                    <option value="newest" {{ ($selectedSort ?? 'newest') === 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="oldest" {{ ($selectedSort ?? '') === 'oldest' ? 'selected' : '' }}>Oldest</option>
                    <option value="views" {{ ($selectedSort ?? '') === 'views' ? 'selected' : '' }}>Most Viewed</option>
                    <option value="title" {{ ($selectedSort ?? '') === 'title' ? 'selected' : '' }}>Title (A-Z)</option>
                </select>
            </div>
        </div>
    </div>

    {{-- ================= ACTIVE FILTER CHIPS BAR ================= --}}
    @if($activeFiltersCount > 0 || $query)
        <div class="sd-card-subtle p-2.5 flex items-center gap-2 flex-wrap text-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider sd-muted mr-1">Active:</span>

            @if($query)
                <a href="{{ route('repository.index', request()->except('query')) }}" 
                   class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full sd-card text-xs font-semibold sd-title hover:border-rose-400 transition" 
                   title="Remove search query">
                    <span>Query: "{{ $query }}"</span>
                    <span class="text-rose-500 font-bold text-[10px]">✕</span>
                </a>
            @endif

            @foreach($categories->whereIn('id', $selectedCategories) as $c)
                <a href="{{ route('repository.index', array_merge(request()->except('categories'), ['categories' => array_diff($selectedCategories, [$c->id])])) }}" 
                   class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full sd-card text-xs font-semibold sd-title hover:border-rose-400 transition" 
                   title="Remove {{ $c->name }}">
                    <span>{{ $c->name }}</span>
                    <span class="text-rose-500 font-bold text-[10px]">✕</span>
                </a>
            @endforeach

            @foreach($selectedAuthors as $a)
                <a href="{{ route('repository.index', array_merge(request()->except('authors'), ['authors' => array_diff($selectedAuthors, [$a])])) }}" 
                   class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full sd-card text-xs font-semibold sd-title hover:border-rose-400 transition" 
                   title="Remove author {{ $a }}">
                    <span>{{ $a }}</span>
                    <span class="text-rose-500 font-bold text-[10px]">✕</span>
                </a>
            @endforeach

            @foreach($selectedYears as $y)
                <a href="{{ route('repository.index', array_merge(request()->except('years'), ['years' => array_diff($selectedYears, [$y])])) }}" 
                   class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full sd-card text-xs font-semibold sd-title hover:border-rose-400 transition" 
                   title="Remove year {{ $y }}">
                    <span>Year: {{ ucfirst($y) }}</span>
                    <span class="text-rose-500 font-bold text-[10px]">✕</span>
                </a>
            @endforeach

            @if($keywordFilter !== '')
                <a href="{{ route('repository.index', request()->except(['keyword', 'search', 'q'])) }}" 
                   class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full sd-card text-xs font-semibold sd-title hover:border-rose-400 transition" 
                   title="Remove keyword filter">
                    <span>Keyword: "{{ $keywordFilter }}"</span>
                    <span class="text-rose-500 font-bold text-[10px]">✕</span>
                </a>
            @endif

            <a href="{{ route('repository.index') }}" class="text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:underline ml-auto">
                Clear All
            </a>
        </div>
    @endif

    {{-- ================= CAPSTONE CARDS GRID ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="capstoneCardsGrid">
        @if(count($items) === 0)
            <div class="col-span-full sd-card border-dashed p-12 text-center">
                <div class="w-12 h-12 mx-auto rounded-full sd-card-subtle flex items-center justify-center text-xl mb-3">
                    🔍
                </div>
                <h3 class="text-base font-bold sd-title">
                    {{ $query ? 'No Capstone Projects Match Your Query' : 'No Capstone Projects Match Your Criteria' }}
                </h3>
                <p class="text-xs sd-muted max-w-md mx-auto mt-1 mb-5">
                    {{ $query ? 'Try searching with different concepts or clear your filters to browse more projects.' : 'Try adjusting your category, author, or year filters to browse more archived research works.' }}
                </p>
                <a href="{{ route('repository.index') }}" class="px-4 py-2 bg-[#0c4233] text-white hover:bg-[#115e47] rounded-xl text-xs font-bold transition inline-flex items-center gap-2 shadow-sm">
                    Browse All Projects
                </a>
            </div>
        @else
            <!-- Client-Side Search Empty State -->
            <div class="col-span-full sd-card border-dashed p-10 text-center" id="clientSearchNoResults" style="display: none;">
                <div class="w-10 h-10 mx-auto rounded-full sd-card-subtle flex items-center justify-center text-lg mb-2">
                    🔎
                </div>
                <h3 class="text-sm font-bold sd-title">No Capstone Projects Match Your Search</h3>
                <p class="text-xs sd-muted mt-1 mb-4" id="clientSearchNoResultsDesc">
                    No projects match the keyword.
                </p>
                <button type="button" onclick="clearFilterSearch()" class="px-3.5 py-1.5 sd-card text-xs font-bold sd-title hover:border-[#0c4233] transition">
                    Clear Filter
                </button>
            </div>

            @foreach($items as $index => $item)
                @php
                    $research = is_array($item) ? $item['research'] : $item;
                    $similarity = is_array($item) && isset($item['similarity']) ? $item['similarity'] : null;
                @endphp
                <div class="sd-card p-5 flex flex-col justify-between transition-all duration-200 hover:shadow-md hover:-translate-y-0.5 project-card-item group" 
                     data-title="{{ strtolower($research->title) }}" 
                     data-authors="{{ strtolower($research->authors ?? '') }}"
                     data-abstract="{{ strtolower($research->abstract ?? '') }}"
                     data-category="{{ strtolower($research->category->name ?? '') }}">
                    
                    <div>
                        <!-- Top Meta Row: Category Tag, Semantic Match & Year -->
                        <div class="flex items-center justify-between gap-2 flex-wrap mb-2.5">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20 truncate max-w-[170px]">
                                    {{ $research->category->name ?? 'Uncategorized' }}
                                </span>
                                @if($similarity !== null)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-500/10 text-purple-700 dark:text-purple-300 border border-purple-500/20" title="Semantic Match Score">
                                        🎯 {{ $similarity }}% match
                                    </span>
                                @endif
                            </div>
                            <span class="text-[11px] font-mono font-semibold sd-muted">
                                {{ $research->year ?: ($research->approved_at ? $research->approved_at->format('Y') : ($research->created_at ? $research->created_at->format('Y') : '2026')) }}
                            </span>
                        </div>

                        <!-- Title Link -->
                        <a href="{{ route('repository.show', $research->id) }}" class="text-sm font-bold sd-title group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition line-clamp-2 leading-snug">
                            {{ $research->title }}
                        </a>

                        <!-- Author Line -->
                        <div class="flex items-center gap-1.5 text-xs sd-muted mt-2">
                            <svg class="w-3.5 h-3.5 opacity-60 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="truncate" title="{{ $research->authors ?? 'Student Researcher' }}">
                                {{ $research->authors ?? 'SCS Researchers' }}
                            </span>
                        </div>

                        <!-- Abstract Snippet -->
                        <p class="text-xs sd-text line-clamp-3 mt-2.5 leading-relaxed opacity-85">
                            {{ $research->abstract ?? $research->objectives ?? 'This capstone focuses on developing modern software solutions and technological architectures for institutional automation.' }}
                        </p>
                    </div>

                    <!-- Footer Row: Stats + Action Button -->
                    <div class="pt-3.5 mt-4 border-t sd-border flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 text-xs sd-muted font-mono">
                            <span class="inline-flex items-center gap-1" title="Views">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                {{ number_format($research->views_count ?? 0) }}
                            </span>
                            <span class="inline-flex items-center gap-1" title="Downloads">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                {{ number_format($research->approved_downloads_count ?? 0) }}
                            </span>
                        </div>

                        <a href="{{ route('repository.show', $research->id) }}" class="px-3 py-1.5 bg-[#0c4233] text-white hover:bg-[#115e47] rounded-lg text-xs font-bold transition inline-flex items-center gap-1.5 shadow-sm">
                            <span>View Study</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- ================= CLEAN MINIMAL PAGINATION ================= --}}
    @if(!$query && $researches instanceof \Illuminate\Pagination\LengthAwarePaginator && $researches->hasPages())
        <div class="pt-6 flex justify-center">
            <div class="sd-card px-2 py-1.5 inline-flex items-center gap-1 text-xs">
                @if($researches->onFirstPage())
                    <span class="px-2.5 py-1 sd-muted opacity-40 cursor-not-allowed">‹ Previous</span>
                @else
                    <a href="{{ $researches->previousPageUrl() }}" class="px-2.5 py-1 sd-title font-semibold hover:text-emerald-600 transition">‹ Previous</a>
                @endif

                @foreach(range(1, min(5, $researches->lastPage())) as $page)
                    @if($page == $researches->currentPage())
                        <span class="px-2.5 py-1 bg-[#0c4233] text-white font-bold rounded-lg">{{ $page }}</span>
                    @else
                        <a href="{{ $researches->url($page) }}" class="px-2.5 py-1 sd-title font-semibold hover:bg-slate-100 dark:hover:bg-white/5 rounded-lg transition">{{ $page }}</a>
                    @endif
                @endforeach

                @if($researches->lastPage() > 5)
                    <span class="px-1 sd-muted font-bold">...</span>
                    <a href="{{ $researches->url($researches->lastPage()) }}" class="px-2.5 py-1 sd-title font-semibold hover:bg-slate-100 dark:hover:bg-white/5 rounded-lg transition">{{ $researches->lastPage() }}</a>
                @endif

                @if($researches->hasMorePages())
                    <a href="{{ $researches->nextPageUrl() }}" class="px-2.5 py-1 sd-title font-semibold hover:text-emerald-600 transition">Next ›</a>
                @else
                    <span class="px-2.5 py-1 sd-muted opacity-40 cursor-not-allowed">Next ›</span>
                @endif
            </div>
        </div>
    @endif
</div>

<!-- Filter Interactions & Realtime Search Scripts -->
<script>
    function toggleFilterDropdown() {
        const panel = document.getElementById('filterDropdownPanel');
        const btn = document.getElementById('filterDropdownToggle');
        if (panel) {
            panel.classList.toggle('open');
            if (btn) btn.classList.toggle('active');
            if (panel.classList.contains('open')) {
                const kw = document.getElementById('filterKeywordInput');
                if (kw) kw.focus();
            }
        }
    }

    function closeFilterDropdown() {
        const panel = document.getElementById('filterDropdownPanel');
        const btn = document.getElementById('filterDropdownToggle');
        if (panel) panel.classList.remove('open');
        if (btn) btn.classList.remove('active');
    }

    // Close dropdown on outside click
    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('filterDropdownWrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            closeFilterDropdown();
        }
    });

    function toggleFilterSection(sectionId) {
        const el = document.getElementById(sectionId);
        if (el) el.classList.toggle('collapsed');
    }

    function toggleMoreAuthors() {
        const moreList = document.getElementById('moreAuthorsList');
        const btn = document.getElementById('showMoreAuthorsBtn');
        if (moreList) {
            if (moreList.style.display === 'none' || moreList.style.display === '') {
                moreList.style.display = 'block';
                if (btn) btn.innerText = '- Show Less';
            } else {
                moreList.style.display = 'none';
                if (btn) btn.innerText = '+ Show More';
            }
        }
    }

    function applySort(sortVal) {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', sortVal);
        window.location.href = url.toString();
    }

    // Client-side real-time filtering from dropdown keyword input
    const filterKeywordInput = document.getElementById('filterKeywordInput');
    const projectCards = document.querySelectorAll('.project-card-item');

    function normalizeSearchText(str) {
        return (str || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s]/gi, ' ')
            .replace(/\s+/g, ' ')
            .trim();
    }

    window.clearFilterSearch = function() {
        if (filterKeywordInput) {
            filterKeywordInput.value = '';
            filterKeywordInput.dispatchEvent(new Event('input'));
            filterKeywordInput.focus();
        }
    };

    if (filterKeywordInput && projectCards.length > 0) {
        filterKeywordInput.addEventListener('input', function() {
            const rawVal = this.value;
            const term = normalizeSearchText(rawVal);
            let matchCount = 0;

            projectCards.forEach(function(card) {
                if (!term) {
                    card.style.display = 'flex';
                    matchCount++;
                    return;
                }

                const title = normalizeSearchText(card.getAttribute('data-title'));
                const authors = normalizeSearchText(card.getAttribute('data-authors'));
                const abstract = normalizeSearchText(card.getAttribute('data-abstract'));
                const category = normalizeSearchText(card.getAttribute('data-category'));

                const matches = title.includes(term) || authors.includes(term) || abstract.includes(term) || category.includes(term);

                if (matches) {
                    card.style.display = 'flex';
                    matchCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            const clientSearchEmpty = document.getElementById('clientSearchNoResults');
            const clientSearchDesc = document.getElementById('clientSearchNoResultsDesc');

            if (clientSearchEmpty) {
                if (matchCount === 0 && term) {
                    clientSearchEmpty.style.display = 'block';
                    if (clientSearchDesc) {
                        clientSearchDesc.textContent = 'No projects match the keyword "' + rawVal + '".';
                    }
                } else {
                    clientSearchEmpty.style.display = 'none';
                }
            }
        });
    }
</script>
@endsection
