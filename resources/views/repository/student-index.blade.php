@extends('layouts.app')
@section('title', 'Research Repository — Student Catalog')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    .admin-repo-minimal {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #1e293b;
    }

    /* Fixed Non-Moving Repository Header (Sticky Top Bar) */
    .fixed-admin-repo-header {
        position: sticky;
        top: 0;
        z-index: 50;
        margin-top: -36px;
        margin-left: -40px;
        margin-right: -40px;
        padding: 16px 40px 14px 40px;
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.03);
    }

    @media (max-width: 1023px) {
        .fixed-admin-repo-header {
            margin-top: -24px;
            margin-left: -20px;
            margin-right: -20px;
            padding: 14px 20px;
        }
    }

    /* Minimal Search Box */
    .min-search-box {
        display: flex;
        align-items: center;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 3px 4px 3px 12px;
        transition: all 0.2s ease;
    }

    .min-search-box:focus-within {
        background: #ffffff;
        border-color: #0c4233;
        box-shadow: 0 0 0 3px rgba(12, 66, 51, 0.08);
    }

    .min-search-input {
        background: transparent;
        border: none;
        color: #0f172a;
        font-weight: 500;
        font-size: 0.8125rem;
        width: 100%;
        outline: none;
    }

    .min-search-input::placeholder {
        color: #94a3b8;
    }

    .min-search-btn {
        background: #0c4233;
        color: #ffffff;
        padding: 6px 14px;
        border-radius: 9px;
        font-weight: 600;
        font-size: 0.75rem;
        border: none;
        cursor: pointer;
        transition: all 0.15s ease;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .min-search-btn:hover {
        background: #082d23;
    }

    /* Minimal Category Dropdown */
    .min-cat-selector {
        position: relative;
        min-width: 210px;
    }

    .min-cat-btn {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 6px 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #334155;
        transition: all 0.15s ease;
        height: 38px;
    }

    .min-cat-btn:hover {
        background: #ffffff;
        border-color: #cbd5e1;
    }

    .min-cat-dropdown {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 4px;
        z-index: 60;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-4px);
        transition: all 0.15s ease;
        max-height: 280px;
        overflow-y: auto;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
    }

    .min-cat-dropdown.open {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .min-cat-option {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 7px 10px;
        border-radius: 8px;
        text-decoration: none;
        color: #475569;
        font-size: 0.8125rem;
        font-weight: 500;
        transition: background 0.15s ease;
    }

    .min-cat-option:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    .min-cat-option.active {
        background: #ecfdf5;
        color: #065f46;
        font-weight: 700;
    }

    /* Minimal Card Arrangement */
    .min-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .min-card:hover {
        border-color: rgba(16, 185, 129, 0.4);
        box-shadow: 0 8px 20px -4px rgba(12, 66, 51, 0.06);
        transform: translateY(-2px);
    }

    /* Theme Adaptations: Dark, OLED, Sepia */
    html[data-guest-theme="dark"] .admin-repo-minimal,
    html[data-guest-theme="oled"] .admin-repo-minimal,
    html[data-student-theme="dark"] .admin-repo-minimal,
    html[data-student-theme="oled"] .admin-repo-minimal {
        color: #f8fafc;
    }

    html[data-guest-theme="dark"] .fixed-admin-repo-header,
    html[data-guest-theme="oled"] .fixed-admin-repo-header,
    html[data-student-theme="dark"] .fixed-admin-repo-header,
    html[data-student-theme="oled"] .fixed-admin-repo-header {
        background: #111827 !important;
        border-bottom-color: #374151 !important;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.5) !important;
    }

    html[data-guest-theme="dark"] .fixed-admin-repo-header h1,
    html[data-guest-theme="oled"] .fixed-admin-repo-header h1,
    html[data-student-theme="dark"] .fixed-admin-repo-header h1,
    html[data-student-theme="oled"] .fixed-admin-repo-header h1 {
        color: #f8fafc !important;
    }

    html[data-guest-theme="dark"] .fixed-admin-repo-header .border-slate-100,
    html[data-guest-theme="oled"] .fixed-admin-repo-header .border-slate-100,
    html[data-student-theme="dark"] .fixed-admin-repo-header .border-slate-100,
    html[data-student-theme="oled"] .fixed-admin-repo-header .border-slate-100 {
        border-color: #374151 !important;
    }

    html[data-guest-theme="dark"] .min-search-box,
    html[data-guest-theme="oled"] .min-search-box,
    html[data-student-theme="dark"] .min-search-box,
    html[data-student-theme="oled"] .min-search-box {
        background: #1f2937 !important;
        border-color: #374151 !important;
    }

    html[data-guest-theme="dark"] .min-search-input,
    html[data-guest-theme="oled"] .min-search-input,
    html[data-student-theme="dark"] .min-search-input,
    html[data-student-theme="oled"] .min-search-input {
        color: #f8fafc !important;
    }

    html[data-guest-theme="dark"] .min-cat-btn,
    html[data-guest-theme="oled"] .min-cat-btn,
    html[data-student-theme="dark"] .min-cat-btn,
    html[data-student-theme="oled"] .min-cat-btn {
        background: #1f2937 !important;
        border-color: #374151 !important;
        color: #f8fafc !important;
    }

    html[data-guest-theme="dark"] .min-cat-dropdown,
    html[data-guest-theme="oled"] .min-cat-dropdown,
    html[data-student-theme="dark"] .min-cat-dropdown,
    html[data-student-theme="oled"] .min-cat-dropdown {
        background: #1f2937 !important;
        border-color: #374151 !important;
    }

    html[data-guest-theme="dark"] .min-cat-option,
    html[data-guest-theme="oled"] .min-cat-option,
    html[data-student-theme="dark"] .min-cat-option,
    html[data-student-theme="oled"] .min-cat-option {
        color: #cbd5e1 !important;
    }

    html[data-guest-theme="dark"] .min-cat-option:hover,
    html[data-guest-theme="oled"] .min-cat-option:hover,
    html[data-student-theme="dark"] .min-cat-option:hover,
    html[data-student-theme="oled"] .min-cat-option:hover {
        background: #374151 !important;
        color: #ffffff !important;
    }

    html[data-guest-theme="dark"] .min-card,
    html[data-guest-theme="oled"] .min-card,
    html[data-student-theme="dark"] .min-card,
    html[data-student-theme="oled"] .min-card {
        background: #111827 !important;
        border-color: #1f2937 !important;
    }

    html[data-guest-theme="dark"] .min-card h3,
    html[data-guest-theme="oled"] .min-card h3,
    html[data-student-theme="dark"] .min-card h3,
    html[data-student-theme="oled"] .min-card h3 {
        color: #f8fafc !important;
    }

    html[data-guest-theme="dark"] .min-card p,
    html[data-guest-theme="oled"] .min-card p,
    html[data-student-theme="dark"] .min-card p,
    html[data-student-theme="oled"] .min-card p {
        color: #94a3b8 !important;
    }

    html[data-guest-theme="dark"] .min-card .border-slate-100,
    html[data-guest-theme="oled"] .min-card .border-slate-100,
    html[data-student-theme="dark"] .min-card .border-slate-100,
    html[data-student-theme="oled"] .min-card .border-slate-100 {
        border-color: #1f2937 !important;
    }

    html[data-guest-theme="dark"] .min-card .text-slate-700,
    html[data-guest-theme="oled"] .min-card .text-slate-700,
    html[data-student-theme="dark"] .min-card .text-slate-700,
    html[data-student-theme="oled"] .min-card .text-slate-700 {
        color: #cbd5e1 !important;
    }

    html[data-guest-theme="dark"] .bg-white.border-dashed,
    html[data-guest-theme="oled"] .bg-white.border-dashed,
    html[data-student-theme="dark"] .bg-white.border-dashed,
    html[data-student-theme="oled"] .bg-white.border-dashed {
        background: #111827 !important;
        border-color: #374151 !important;
    }
</style>

<div class="admin-repo-minimal">
    {{-- COMPLETELY FIXED / STATIONARY TOP HEADER BAR --}}
    <div class="fixed-admin-repo-header">
        <div class="max-w-7xl mx-auto space-y-3">
            {{-- Header Top Bar: Title & Primary Actions --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2.5 border-b border-slate-100">
                <div class="space-y-0.5">
                    <div class="flex items-center gap-2">
                        <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">
                            Research Repository
                        </h1>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-200/80 rounded-md">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                            Student Catalog
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 font-medium">
                        Browse, search, and inspect all approved capstone manuscripts in the catalog.
                    </p>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-xs font-semibold text-slate-500 bg-slate-100/80 border border-slate-200/60 px-3 py-1.5 rounded-xl">
                        {{ \App\Models\Research::where('status', 'approved')->count() }} total approved
                    </span>
                    <a href="{{ route('similarity-checker') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold text-white bg-[#0c4233] hover:bg-[#082d23] rounded-xl transition shadow-xs">
                        <span>⚡</span> Similarity Checker ➔
                    </a>
                </div>
            </div>

            {{-- Search & Category Filter Row --}}
            <form action="{{ route('repository.index') }}" method="GET" class="flex flex-col md:flex-row items-stretch md:items-center gap-2.5">
                {{-- Search Box --}}
                <div class="min-search-box flex-1">
                    <div class="text-slate-400 mr-2 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="query" value="{{ $query }}"
                           placeholder="Search by topic, concept, author, or keywords…"
                           class="min-search-input" autocomplete="off">
                    @if($categoryId)
                        <input type="hidden" name="category" value="{{ $categoryId }}">
                    @endif
                    <button type="submit" class="min-search-btn">
                        Search
                    </button>
                </div>

                {{-- Category Filter --}}
                @php $selectedCategory = $categories->firstWhere('id', $categoryId); @endphp
                <div class="min-cat-selector" id="catSelector">
                    <button type="button" class="min-cat-btn w-full" onclick="toggleCategoryDropdown()">
                        <span class="flex items-center gap-1.5 truncate">
                            <span class="text-slate-400 text-xs">📁</span>
                            <span class="truncate">{{ $selectedCategory->name ?? 'All Categories' }}</span>
                        </span>
                        <svg id="catArrow" class="w-3.5 h-3.5 text-slate-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div class="min-cat-dropdown" id="catDropdown">
                        <a href="{{ route('repository.index', array_filter(['query' => $query])) }}"
                           class="min-cat-option {{ !$categoryId ? 'active' : '' }}">
                            <span>All Categories</span>
                            <span class="text-[10px] text-slate-400 font-bold bg-slate-100 px-1.5 py-0.5 rounded">{{ \App\Models\Research::where('status', 'approved')->count() }}</span>
                        </a>
                        <div class="h-px bg-slate-100 my-1"></div>
                        @foreach($categories as $category)
                            <a href="{{ route('repository.index', array_filter(['category' => $category->id, 'query' => $query])) }}"
                               class="min-cat-option {{ $categoryId == $category->id ? 'active' : '' }}">
                                <span class="truncate">{{ $category->name }}</span>
                                <span class="text-[10px] text-slate-400 font-bold bg-slate-100 px-1.5 py-0.5 rounded">{{ $category->classified_researches_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </form>

            {{-- Active Filters Indicator --}}
            @if($query || $categoryId)
                <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100 text-xs">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-slate-500 font-semibold text-[11px]">Filtered by:</span>
                        @if($query)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-slate-100 border border-slate-200 rounded-lg text-slate-800 font-medium text-[11px]">
                                <span>"{{ $query }}"</span>
                                @if(isset($searchMode) && $searchMode === 'semantic')
                                    <span class="text-[9px] text-purple-700 font-bold bg-purple-50 px-1.5 py-0.2 rounded border border-purple-200">Semantic</span>
                                @endif
                            </span>
                        @endif
                        @if($selectedCategory)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800 font-medium text-[11px]">
                                📂 {{ $selectedCategory->name }}
                            </span>
                        @endif
                    </div>

                    <a href="{{ route('repository.index') }}" class="text-[11px] font-bold text-slate-500 hover:text-rose-600 transition">
                        Reset filters ✕
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- MAIN RESEARCH CARDS CANVAS --}}
    <div class="max-w-7xl mx-auto pt-6 space-y-4">
        @php
            $items = $query ? $researches : ($researches instanceof \Illuminate\Pagination\LengthAwarePaginator ? $researches->items() : $researches);
        @endphp

        {{-- Results Summary Bar --}}
        <div class="flex items-center justify-between text-xs text-slate-500 pb-1">
            <p class="font-medium">
                Showing <strong class="text-slate-800 font-bold">{{ count($items) }}</strong> published {{ count($items) === 1 ? 'manuscript' : 'manuscripts' }}
            </p>
            <span class="text-[11px] text-slate-400 font-medium">Verified Academic Catalog</span>
        </div>

        @if(count($items) === 0)
            <div class="bg-white border border-dashed border-slate-200 rounded-2xl p-12 text-center space-y-2">
                <span class="text-3xl block">🔍</span>
                <h3 class="text-base font-bold text-slate-800">No researches found</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">
                    {{ $query ? 'No approved capstone papers matched your search query. Try different terms or clear filters.' : 'There are no approved capstone papers in this category yet.' }}
                </p>
                @if($query || $categoryId)
                    <div class="pt-2">
                        <a href="{{ route('repository.index') }}" class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition">
                            Reset Filters
                        </a>
                    </div>
                @endif
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($items as $index => $item)
                    @php
                        $research = is_array($item) ? $item['research'] : $item;
                        $similarity = is_array($item) && isset($item['similarity']) ? $item['similarity'] : null;
                    @endphp
                    <div class="min-card group">
                        <div class="space-y-3">
                            {{-- Top Header Row: Category Badge & Views/Match --}}
                            <div class="flex items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-100 rounded-lg truncate max-w-[170px]" title="{{ $research->category->name ?? 'Uncategorized' }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 shrink-0"></span>
                                    <span class="truncate">{{ $research->category->name ?? 'Uncategorized' }}</span>
                                </span>

                                <div class="flex items-center gap-2 text-[11px] font-medium shrink-0">
                                    @if($similarity !== null)
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-100">
                                            ⚡ {{ $similarity }}% Match
                                        </span>
                                    @endif
                                    <span class="text-slate-400 font-medium">
                                        👁️ {{ number_format($research->views_count ?? 0) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Title --}}
                            <a href="{{ route('repository.show', $research->id) }}" class="block">
                                <h3 class="text-[15px] font-bold text-slate-900 group-hover:text-emerald-800 transition-colors leading-snug line-clamp-2">
                                    {{ $research->title }}
                                </h3>
                            </a>

                            {{-- Project Context Snippet --}}
                            <p class="text-xs text-slate-500 leading-relaxed line-clamp-3 font-normal">
                                {{ $research->project_context ?? $research->objectives ?? $research->abstract }}
                            </p>
                        </div>

                        {{-- Card Bottom Footer: Authors, Year & Direct Link --}}
                        <div class="pt-3.5 mt-4 border-t border-slate-100 flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0 flex items-center gap-1.5 text-slate-600 truncate" title="{{ $research->authors }}">
                                <span class="text-slate-400">👤</span>
                                <span class="font-semibold text-slate-700 truncate text-[11px] max-w-[150px] sm:max-w-[180px]">{{ $research->authors }}</span>
                                <span class="text-slate-400 text-[10px] shrink-0 font-medium">· {{ $research->year }}</span>
                            </div>

                            <a href="{{ route('repository.show', $research->id) }}"
                               class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-800 hover:text-emerald-950 bg-emerald-50/70 hover:bg-emerald-100 border border-emerald-200/60 px-2.5 py-1 rounded-lg transition shrink-0">
                                <span>View</span>
                                <span>➔</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- PAGINATION --}}
        @if(!$query && $researches instanceof \Illuminate\Pagination\LengthAwarePaginator && $researches->hasPages())
            <div class="pt-6 pb-8 flex justify-center">
                {{ $researches->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    function toggleCategoryDropdown() {
        const dd = document.getElementById('catDropdown');
        const arrow = document.getElementById('catArrow');
        if (dd) dd.classList.toggle('open');
        if (arrow) arrow.classList.toggle('rotate-180');
    }

    document.addEventListener('click', function(e) {
        const sel = document.getElementById('catSelector');
        if (sel && !sel.contains(e.target)) {
            const dd = document.getElementById('catDropdown');
            const arrow = document.getElementById('catArrow');
            if (dd) dd.classList.remove('open');
            if (arrow) arrow.classList.remove('rotate-180');
        }
    });
</script>
@endsection
