@extends('layouts.public')

@section('title', ($query ? 'Search: ' . $query . ' — ' : '') . 'capstoneHub Search')

@section('content')
<style>
    .search-page-container {
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        color: var(--guest-text-main, #f8fafc);
        max-width: 1200px;
        margin: 0 auto;
        padding: 1.5rem 1.5rem 5rem;
    }

    /* ===== BREADCRUMB ===== */
    .search-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--guest-text-muted, #94a3b8);
        margin-bottom: 1.8rem;
    }

    .search-breadcrumb a {
        color: var(--guest-accent, #00f5d4);
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .search-breadcrumb a:hover {
        filter: brightness(1.2);
        text-decoration: underline;
    }

    .search-breadcrumb-sep {
        opacity: 0.4;
    }

    .search-breadcrumb-current {
        color: var(--guest-text-headings, #ffffff);
    }

    /* ===== HERO SEARCH CONSOLE ===== */
    .search-hero-card {
        background: linear-gradient(var(--guest-bg-surface, rgba(14, 22, 33, 0.94)), var(--guest-bg-surface, rgba(14, 22, 33, 0.94))) padding-box,
                    var(--guest-card-border-grad, linear-gradient(135deg, #00f5d4 0%, #06b6d4 35%, #10b981 70%, #34d399 100%)) border-box;
        border: 1.5px solid transparent;
        border-radius: 22px;
        box-shadow: var(--guest-card-shadow, 0 16px 40px rgba(0, 0, 0, 0.45));
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        padding: 2.2rem 2.4rem;
        margin-bottom: 2.5rem;
        position: relative;
        overflow: hidden;
    }

    @media (max-width: 640px) {
        .search-hero-card {
            padding: 1.5rem 1.25rem;
        }
    }

    .search-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--guest-accent, #00f5d4);
        background: rgba(0, 245, 212, 0.1);
        border: 1px solid var(--guest-accent-glow, rgba(0, 245, 212, 0.3));
        padding: 4px 12px;
        border-radius: 9999px;
        margin-bottom: 0.8rem;
    }

    .search-hero-title {
        font-size: 1.85rem;
        font-weight: 800;
        letter-spacing: -0.025em;
        line-height: 1.2;
        color: var(--guest-text-headings, #ffffff);
        margin-bottom: 0.45rem;
    }

    .search-hero-title .accent-text {
        color: var(--guest-accent, #00f5d4);
    }

    .search-hero-desc {
        font-size: 0.88rem;
        color: var(--guest-text-muted, #94a3b8);
        max-width: 680px;
        line-height: 1.5;
        margin-bottom: 1.6rem;
    }

    /* ===== MAIN SEARCH BAR WRAPPER ===== */
    .search-input-form {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }

    .search-bar-outer {
        display: flex;
        align-items: center;
        background: var(--guest-bg-input, rgba(8, 14, 22, 0.85));
        border: 1.5px solid var(--guest-border, rgba(255, 255, 255, 0.14));
        border-radius: 16px;
        padding: 6px 8px 6px 18px;
        gap: 12px;
        transition: all 0.25s ease;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .search-bar-outer:focus-within {
        border-color: var(--guest-accent, #00f5d4);
        box-shadow: 0 0 0 4px var(--guest-accent-glow, rgba(0, 245, 212, 0.2)), inset 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .search-icon-lens {
        color: var(--guest-accent, #00f5d4);
        flex-shrink: 0;
    }

    .search-text-input {
        flex: 1;
        background: transparent;
        border: none;
        outline: none;
        font-family: inherit;
        font-size: 1rem;
        font-weight: 500;
        color: var(--guest-text-headings, #ffffff);
        min-width: 0;
    }

    .search-text-input::placeholder {
        color: var(--guest-text-muted, #94a3b8);
        opacity: 0.75;
    }

    .search-btn-submit {
        background: var(--guest-accent, #00f5d4);
        color: #042f2e;
        border: none;
        border-radius: 12px;
        padding: 0.75rem 1.6rem;
        font-size: 0.88rem;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.22s ease;
        box-shadow: 0 4px 16px var(--guest-accent-glow, rgba(0, 245, 212, 0.35));
        flex-shrink: 0;
        white-space: nowrap;
    }

    .search-btn-submit:hover {
        transform: translateY(-1px);
        filter: brightness(1.1);
        box-shadow: 0 6px 20px var(--guest-accent-glow, rgba(0, 245, 212, 0.5));
    }

    /* ===== RESULTS SUMMARY HEADER BAR ===== */
    .results-summary-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding-bottom: 1.1rem;
        margin-bottom: 1.4rem;
        border-bottom: 1px solid var(--guest-border, rgba(255, 255, 255, 0.1));
        flex-wrap: wrap;
    }

    .results-summary-info {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .results-count-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--guest-text-headings, #ffffff);
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .results-query-highlight {
        color: var(--guest-accent, #00f5d4);
    }

    .results-subtitle {
        font-size: 0.78rem;
        color: var(--guest-text-muted, #94a3b8);
        font-weight: 500;
    }

    .search-mode-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 0.72rem;
        font-weight: 700;
        font-family: 'JetBrains Mono', monospace;
    }

    .mode-semantic {
        background: rgba(16, 185, 129, 0.12);
        border: 1px solid rgba(16, 185, 129, 0.35);
        color: #34d399;
    }

    .mode-keyword {
        background: rgba(0, 245, 212, 0.12);
        border: 1px solid rgba(0, 245, 212, 0.35);
        color: var(--guest-accent, #00f5d4);
    }

    /* ===== SEARCH RESULT CARDS LIST ===== */
    .search-results-list {
        display: flex;
        flex-direction: column;
        gap: 1.1rem;
    }

    .search-result-card {
        background: linear-gradient(var(--guest-bg-surface, rgba(14, 22, 33, 0.94)), var(--guest-bg-surface, rgba(14, 22, 33, 0.94))) padding-box,
                    var(--guest-card-border-grad, linear-gradient(135deg, #00f5d4 0%, #06b6d4 35%, #10b981 70%, #34d399 100%)) border-box;
        border: 1.5px solid transparent;
        border-radius: 18px;
        box-shadow: var(--guest-card-shadow, 0 8px 24px rgba(0, 0, 0, 0.35));
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        padding: 1.4rem 1.6rem;
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
    }

    .search-result-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 36px rgba(0, 0, 0, 0.5), 0 0 20px var(--guest-accent-glow, rgba(0, 245, 212, 0.2));
    }

    .card-meta-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        flex-wrap: wrap;
    }

    .card-badges-group {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .card-badge {
        font-size: 0.68rem;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 9999px;
        white-space: nowrap;
    }

    .badge-match-score {
        background: rgba(16, 185, 129, 0.15);
        border: 1px solid rgba(16, 185, 129, 0.4);
        color: #34d399;
        font-family: 'JetBrains Mono', monospace;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .badge-category {
        background: rgba(56, 189, 248, 0.1);
        border: 1px solid rgba(56, 189, 248, 0.3);
        color: #38bdf8;
    }

    .badge-year {
        font-family: 'JetBrains Mono', monospace;
        color: var(--guest-text-muted, #94a3b8);
        font-size: 0.74rem;
        font-weight: 600;
    }

    .card-paper-title {
        font-size: 1.15rem;
        font-weight: 800;
        line-height: 1.35;
        margin: 0;
    }

    .card-paper-title a {
        color: var(--guest-text-headings, #ffffff);
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .card-paper-title a:hover {
        color: var(--guest-accent, #00f5d4);
    }

    .card-authors-row {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        color: var(--guest-accent, #00f5d4);
        font-weight: 600;
    }

    .card-authors-row svg {
        flex-shrink: 0;
    }

    .card-abstract-snippet {
        font-size: 0.82rem;
        line-height: 1.55;
        color: var(--guest-text-muted, #94a3b8);
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin: 0;
    }

    .card-footer-action-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding-top: 0.4rem;
        border-top: 1px solid var(--guest-border, rgba(255, 255, 255, 0.08));
        flex-wrap: wrap;
    }

    .card-keywords-list {
        display: flex;
        align-items: center;
        gap: 5px;
        flex-wrap: wrap;
        flex: 1;
        min-width: 200px;
    }

    .keyword-pill {
        font-size: 0.66rem;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--guest-border, rgba(255, 255, 255, 0.12));
        color: var(--guest-text-muted, #94a3b8);
        padding: 2px 7px;
        border-radius: 4px;
        white-space: nowrap;
    }

    .btn-view-paper {
        background: var(--guest-accent, #10b981);
        color: #042f2e;
        border-radius: 9999px;
        font-weight: 700;
        font-size: 0.78rem;
        padding: 0.45rem 1.15rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.22s ease;
        border: none;
        cursor: pointer;
        white-space: nowrap;
        box-shadow: 0 4px 14px var(--guest-accent-glow, rgba(16, 185, 129, 0.35));
    }

    .btn-view-paper:hover {
        transform: translateY(-1px);
        filter: brightness(1.1);
        color: #042f2e;
        box-shadow: 0 6px 18px var(--guest-accent-glow, rgba(16, 185, 129, 0.5));
    }

    /* ===== EMPTY STATE ===== */
    .search-empty-card {
        background: linear-gradient(var(--guest-bg-surface, rgba(14, 22, 33, 0.94)), var(--guest-bg-surface, rgba(14, 22, 33, 0.94))) padding-box,
                    var(--guest-card-border-grad, linear-gradient(135deg, #00f5d4 0%, #06b6d4 35%, #10b981 70%, #34d399 100%)) border-box;
        border: 1.5px solid transparent;
        border-radius: 22px;
        padding: 3rem 2rem;
        text-align: center;
        box-shadow: var(--guest-card-shadow, 0 16px 40px rgba(0, 0, 0, 0.45));
        margin: 2rem 0;
    }

    .search-empty-icon {
        width: 58px;
        height: 58px;
        border-radius: 50%;
        background: rgba(0, 245, 212, 0.1);
        border: 1.5px solid var(--guest-accent, #00f5d4);
        color: var(--guest-accent, #00f5d4);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        margin: 0 auto 1.2rem;
        box-shadow: 0 0 20px var(--guest-accent-glow, rgba(0, 245, 212, 0.25));
    }

    .search-empty-title {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--guest-text-headings, #ffffff);
        margin-bottom: 0.5rem;
    }

    .search-empty-desc {
        font-size: 0.85rem;
        color: var(--guest-text-muted, #94a3b8);
        max-width: 520px;
        margin: 0 auto 1.6rem;
        line-height: 1.6;
    }

    .search-empty-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-empty-action-primary {
        background: var(--guest-accent, #00f5d4);
        color: #042f2e;
        padding: 0.55rem 1.4rem;
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-empty-action-primary:hover {
        transform: translateY(-1px);
        filter: brightness(1.1);
    }

    .btn-empty-action-secondary {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid var(--guest-border, rgba(255, 255, 255, 0.14));
        color: var(--guest-text-headings, #ffffff);
        padding: 0.55rem 1.4rem;
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-empty-action-secondary:hover {
        border-color: var(--guest-accent, #00f5d4);
        color: var(--guest-accent, #00f5d4);
    }
</style>

<div class="search-page-container">

    {{-- Breadcrumb Navigation --}}
    <nav class="search-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ url('/') }}">Home</a>
        <span class="search-breadcrumb-sep">/</span>
        <a href="{{ route('repository.index') }}">Repository</a>
        <span class="search-breadcrumb-sep">/</span>
        <span class="search-breadcrumb-current">Search Results</span>
    </nav>

    {{-- Main Search Hero Console --}}
    <div class="search-hero-card">
        <div class="search-hero-badge">
            <span style="font-size: 8px;">●</span>
            CAPSTONEHUB SEARCH ENGINE
        </div>
        <h1 class="search-hero-title">
            Search <span class="accent-text">Capstone Repository</span>
        </h1>
        <p class="search-hero-desc">
            Search across published manuscripts by research title, author surname, problem objectives, or intelligent semantic intent.
        </p>

        <form action="{{ route('search.index') }}" method="GET" class="search-input-form" id="searchConsoleForm">
            <div class="search-bar-outer">
                <svg class="search-icon-lens" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                </svg>
                <input 
                    type="text" 
                    name="q" 
                    class="search-text-input" 
                    value="{{ $query }}" 
                    placeholder="Enter author surname (e.g., Estano), title, or keywords..." 
                    autocomplete="off" 
                    autofocus
                    id="searchQueryInput"
                >
                @if($query)
                    <button type="button" onclick="clearSearchQuery()" style="background: none; border: none; color: var(--guest-text-muted); cursor: pointer; padding: 4px;" title="Clear Search">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                @endif
                <button type="submit" class="search-btn-submit">
                    <span>Search</span>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>

    {{-- RESULTS PRESENTATION SECTION --}}
    @if($query !== '')
        <div class="results-summary-bar">
            <div class="results-summary-info">
                <h2 class="results-count-title">
                    <span>Results for</span>
                    <span class="results-query-highlight">"{{ $query }}"</span>
                </h2>
                <span class="results-subtitle">
                    Found <strong>{{ $results->count() }}</strong> {{ Str::plural('research paper', $results->count()) }}
                    @if($categoryId)
                        in category <em>{{ $categories->firstWhere('id', $categoryId)?->name }}</em>
                    @endif
                </span>
            </div>

            <div class="results-meta-pill-wrap">
                @if($searchMode === 'semantic')
                    <span class="search-mode-pill mode-semantic" title="Results ranked using vector similarity embeddings">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                        </svg>
                        AI Semantic Match
                    </span>
                @else
                    <span class="search-mode-pill mode-keyword" title="Results matched directly by keyword and author indexing">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Keyword & Author Matched
                    </span>
                @endif
            </div>
        </div>

        {{-- Results List Cards --}}
        @if($results->isNotEmpty())
            <div class="search-results-list">
                @foreach($results as $item)
                    @php 
                        $research = is_array($item) ? $item['research'] : $item;
                        $similarity = is_array($item) && isset($item['similarity']) ? $item['similarity'] : null;
                    @endphp

                    <article class="search-result-card">
                        <!-- Top Meta Row -->
                        <div class="card-meta-top">
                            <div class="card-badges-group">
                                @if($similarity && $similarity < 100)
                                    <span class="card-badge badge-match-score">
                                        ★ {{ $similarity }}% Semantic Similarity
                                    </span>
                                @elseif($similarity == 100)
                                    <span class="card-badge badge-match-score" style="background: rgba(0, 245, 212, 0.15); border-color: rgba(0, 245, 212, 0.4); color: var(--guest-accent, #00f5d4);">
                                        ✓ Direct Match
                                    </span>
                                @else
                                    <span class="card-badge badge-match-score" style="background: rgba(0, 245, 212, 0.12); border-color: rgba(0, 245, 212, 0.3); color: var(--guest-accent, #00f5d4);">
                                        ✓ Relevant Match
                                    </span>
                                @endif

                                @if($research->category)
                                    <span class="card-badge badge-category">
                                        📂 {{ $research->category->name }}
                                    </span>
                                @endif
                            </div>

                            <span class="badge-year">
                                📅 {{ $research->year ?: ($research->approved_at ? $research->approved_at->format('Y') : ($research->created_at ? $research->created_at->format('Y') : '2026')) }}
                            </span>
                        </div>

                        <!-- Paper Title -->
                        <h3 class="card-paper-title">
                            <a href="{{ route('repository.show', $research->id) }}">
                                {{ $research->title }}
                            </a>
                        </h3>

                        <!-- Authors Row -->
                        <div class="card-authors-row">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                            </svg>
                            <span>{{ $research->authors ?? 'Student Researcher' }}</span>
                        </div>

                        <!-- Abstract Preview -->
                        <p class="card-abstract-snippet">
                            {{ $research->abstract ?? $research->objectives ?? 'This archived capstone explores technological architectures, system designs, and automated computing solutions.' }}
                        </p>

                        <!-- Footer: Keywords & View Button -->
                        <div class="card-footer-action-row">
                            <div class="card-keywords-list">
                                @if($research->keywords)
                                    @foreach(array_slice(array_filter(array_map('trim', explode(',', $research->keywords))), 0, 4) as $kw)
                                        <span class="keyword-pill">#{{ $kw }}</span>
                                    @endforeach
                                @else
                                    <span class="keyword-pill">#Capstone</span>
                                    <span class="keyword-pill">#JHCSC</span>
                                    <span class="keyword-pill">#SCS</span>
                                @endif
                            </div>

                            <a href="{{ route('repository.show', $research->id) }}" class="btn-view-paper">
                                <span>View Details</span>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                                </svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <!-- No Results State -->
            <div class="search-empty-card">
                <div class="search-empty-icon">🔍</div>
                <h3 class="search-empty-title">No Capstone Papers Found</h3>
                <p class="search-empty-desc">
                    We could not find any research matching <strong style="color: var(--guest-accent, #00f5d4);">"{{ $query }}"</strong>. Try checking the author surname spelling (e.g. <em>Estano</em>, <em>Paña</em>), using simpler keywords, or browsing by discipline.
                </p>
                <div class="search-empty-actions">
                    <a href="{{ route('search.index') }}" class="btn-empty-action-secondary">
                        ✕ Clear Search
                    </a>
                    <a href="{{ route('repository.index') }}" class="btn-empty-action-primary">
                        Browse All Capstone Projects
                    </a>
                    <a href="{{ route('similarity-checker') }}" class="btn-empty-action-secondary">
                        Try Similarity Checker
                    </a>
                </div>
            </div>
        @endif

    @else
        <!-- Initial Discovery Landing when query is empty -->
        <div class="search-empty-card">
            <div class="search-empty-icon" style="font-size: 1.8rem;">📚</div>
            <h3 class="search-empty-title">Explore Verified Capstone Studies</h3>
            <p class="search-empty-desc">
                Type an author surname, research title, or project topic in the search console above to query all verified capstone records in the School of Computing Studies.
            </p>
            <div class="search-empty-actions">
                <a href="{{ route('repository.index') }}" class="btn-empty-action-primary">
                    Browse All Capstone Projects
                </a>
                <a href="{{ route('similarity-checker') }}" class="btn-empty-action-secondary">
                    Launch Similarity Checker
                </a>
            </div>
        </div>
    @endif

</div>

<script>
    function clearSearchQuery() {
        const input = document.getElementById('searchQueryInput');
        if (input) {
            input.value = '';
            input.focus();
        }
    }
</script>
@endsection
