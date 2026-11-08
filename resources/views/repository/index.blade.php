@extends(auth()->check() ? (auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.app') : 'layouts.public')
@section('title', 'capstoneHub — Browse Capstone Projects')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    .browse-page-container {
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        color: var(--guest-text-main, #334155);
        max-width: 1420px;
        margin: 0 auto;
        padding: 0 2rem 5.5rem;
        overflow-x: clip;
        width: 100%;
    }

    /* ===== 2-COLUMN MAIN LAYOUT ===== */
    .browse-layout {
        display: grid;
        grid-template-columns: 275px minmax(0, 1fr);
        gap: 1.6rem;
        align-items: flex-start;
        max-width: 100%;
        overflow-x: clip;
    }

    @media (max-width: 1024px) {
        .browse-layout {
            grid-template-columns: 1fr;
        }

        .filter-sidebar-wrapper {
            width: 100% !important;
        }

        .filter-sidebar {
            position: static !important;
            width: 100% !important;
            left: auto !important;
            top: auto !important;
            max-height: none !important;
            overflow-y: visible !important;
        }
    }

    /* ===== LEFT SIDEBAR: FILTER PROJECTS (Fixed Position + Independent Scroll) ===== */
    .filter-sidebar-wrapper {
        width: 275px;
        flex-shrink: 0;
    }

    .filter-sidebar {
        background: var(--guest-bg-sidebar, #ffffff);
        border: 1px solid var(--guest-border, #e2e8f0);
        border-radius: 18px;
        box-shadow: var(--guest-card-shadow, 0 4px 20px rgba(15, 23, 42, 0.06));
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        padding: 0;
        position: fixed;
        top: 86px;
        left: max(2rem, calc((100vw - 1420px) / 2 + 2rem));
        width: 275px;
        max-height: calc(100vh - 100px);
        height: auto;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        z-index: 60;
    }

    #browseFilterForm {
        display: flex;
        flex-direction: column;
        height: 100%;
        max-height: calc(100vh - 100px);
        min-height: 0;
        width: 100%;
        overflow: hidden;
    }

    /* Pinned Fixed Top Header for Filter Projects + Search Icon */
    .filter-header-pinned {
        flex-shrink: 0;
        padding: 1.15rem 1.15rem 0.75rem 1.15rem;
        background: var(--guest-bg-sidebar, #ffffff);
        border-bottom: 1px solid var(--guest-border, #e2e8f0);
        z-index: 10;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }

    .filter-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .filter-header-title {
        font-size: 0.88rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--guest-text-headings, #0f172a);
    }

    .filter-search-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--guest-bg-input, #f1f5f9);
        border: 1px solid var(--guest-border, #cbd5e1);
        color: var(--guest-accent, #0c4233);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .filter-search-btn:hover {
        background: rgba(0, 245, 212, 0.15);
        border-color: var(--guest-accent, #00f5d4);
        transform: scale(1.05);
    }

    .filter-search-box {
        margin-top: 0.75rem;
        margin-bottom: 0.2rem;
        display: none;
    }

    .filter-search-box.show {
        display: block;
    }

    /* Scrollable Filter Body Container */
    .filter-scrollable-body {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden !important;
        overscroll-behavior: contain !important;
        overscroll-behavior-x: none !important;
        touch-action: pan-y !important;
        padding: 0.95rem 1.15rem 1.25rem 1.15rem;
        scrollbar-width: thin;
        scrollbar-color: rgba(0, 245, 212, 0.25) transparent;
    }

    /* Subtle custom scrollbar */
    .filter-scrollable-body::-webkit-scrollbar {
        width: 5px;
    }

    .filter-scrollable-body::-webkit-scrollbar-track {
        background: transparent;
        margin: 6px 0;
    }

    .filter-scrollable-body::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 999px;
    }

    .filter-scrollable-body:hover::-webkit-scrollbar-thumb {
        background: var(--guest-accent, rgba(0, 245, 212, 0.4));
    }

    .filter-search-input {
        width: 100%;
        background: var(--guest-bg-input, #f8fafc);
        border: 1px solid var(--guest-border, #cbd5e1);
        border-radius: 10px;
        padding: 0.5rem 0.8rem;
        color: var(--guest-text-headings, #0f172a);
        font-size: 0.82rem;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .filter-search-input:focus {
        border-color: var(--guest-accent, #0c4233);
        box-shadow: 0 0 10px var(--guest-accent-glow, rgba(12, 66, 51, 0.18));
    }

    /* Filter Accordion Sections */
    .filter-section {
        margin-bottom: 1.4rem;
    }

    .filter-section:last-child {
        margin-bottom: 0;
    }

    .filter-section-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--guest-text-muted, #64748b);
        cursor: pointer;
        margin-bottom: 0.85rem;
        user-select: none;
    }

    .filter-chevron {
        color: var(--guest-accent, #0c4233);
        font-size: 0.75rem;
        transition: transform 0.25s ease;
    }

    .filter-section.collapsed .filter-chevron {
        transform: rotate(180deg);
    }

    .filter-section.collapsed .filter-items {
        display: none;
    }

    .filter-items {
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
        max-width: 100%;
        overflow-x: hidden;
    }

    .filter-item-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.84rem;
        color: var(--guest-text-main, #334155);
        cursor: pointer;
        user-select: none;
        transition: color 0.15s ease;
        min-width: 0;
        max-width: 100%;
        overflow: hidden;
    }

    .filter-item-label:hover {
        color: var(--guest-text-headings, #0f172a);
    }

    .filter-item-label span {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: block;
        flex: 1;
    }

    /* Custom Checkbox */
    .custom-checkbox {
        appearance: none;
        -webkit-appearance: none;
        width: 17px;
        height: 17px;
        border-radius: 4px;
        background: var(--guest-bg-input, #ffffff);
        border: 1.5px solid var(--guest-border, #cbd5e1);
        cursor: pointer;
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .custom-checkbox:hover {
        border-color: var(--guest-accent, #0c4233);
    }

    .custom-checkbox:checked {
        background: var(--guest-accent, #0c4233);
        border-color: var(--guest-accent, #0c4233);
        box-shadow: 0 0 8px var(--guest-accent-glow, rgba(12, 66, 51, 0.3));
    }

    .custom-checkbox:checked::after {
        content: '';
        width: 5px;
        height: 9px;
        border: solid #ffffff;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg) translate(-0.5px, -1px);
        position: absolute;
    }

    /* Two-column checkboxes for authors/year */
    .filter-grid-2col {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 0.6rem 0.5rem;
        max-width: 100%;
        overflow-x: hidden;
        width: 100%;
    }

    .filter-show-more {
        display: inline-block;
        margin-top: 0.5rem;
        color: var(--guest-accent, #0c4233);
        font-size: 0.78rem;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: color 0.2s ease;
    }

    .filter-show-more:hover {
        color: #047857;
        text-decoration: underline;
    }

    /* Reset Filters Link */
    .filter-reset-link {
        display: block;
        text-align: center;
        margin-top: 1.2rem;
        padding-top: 0.8rem;
        border-top: 1px solid var(--guest-border, #e2e8f0);
        color: var(--guest-text-muted, #64748b);
        font-size: 0.78rem;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .filter-reset-link:hover {
        color: var(--guest-accent, #0c4233);
    }

    /* ===== RIGHT COLUMN: MAIN CONTENT ===== */
    .projects-main {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    /* Header Bar (Fixed / Sticky below navbar, transparent background) */
    .projects-header-row {
        position: sticky;
        top: 72px;
        z-index: 45;
        background: transparent;
        padding: 0.9rem 0 0.85rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .projects-heading-group {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    .projects-title {
        font-size: 1.65rem;
        font-weight: 800;
        letter-spacing: -0.025em;
        line-height: 1.2;
        color: var(--guest-text-headings, #0f172a);
    }

    .projects-subtitle {
        font-size: 0.85rem;
        color: var(--guest-text-muted, #64748b);
        font-weight: 500;
    }

    .projects-count-highlight {
        color: var(--guest-text-headings, #0f172a);
        font-weight: 700;
    }

    /* Sort By Control */
    .sort-control-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: var(--guest-text-muted, #64748b);
    }

    .sort-dropdown {
        background: var(--guest-bg-surface, #ffffff) !important;
        border: 1.5px solid var(--guest-border, #cbd5e1) !important;
        color: var(--guest-text-headings, #0f172a) !important;
        font-family: inherit;
        font-size: 0.82rem;
        font-weight: 600;
        padding: 0.45rem 1.1rem;
        border-radius: 9999px;
        outline: none;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: none !important;
    }

    .sort-dropdown:hover {
        border-color: var(--guest-accent, #0c4233);
        box-shadow: 0 0 10px var(--guest-accent-glow, rgba(12, 66, 51, 0.15));
    }

    .sort-dropdown option {
        background: #ffffff;
        color: #0f172a;
    }

    /* ===== 3-COLUMN CAPSTONE CARDS GRID ===== */
    .capstone-cards-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1.05rem;
    }

    @media (max-width: 1200px) {
        .capstone-cards-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .capstone-cards-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Capstone Card with Clean Sleek Academic Frame */
    .capstone-card {
        background: var(--guest-bg-surface, #ffffff);
        border: 1px solid var(--guest-border, #e2e8f0);
        border-radius: 16px;
        box-shadow: var(--guest-card-shadow, 0 2px 10px rgba(15, 23, 42, 0.05));
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        padding: 1.1rem 1.15rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 0.65rem;
        transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
    }

    .capstone-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.09), 0 0 0 1px rgba(12, 66, 51, 0.15);
        border-color: #cbd5e1;
    }

    .card-top-content {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
    }

    /* Card Title */
    .card-title-link {
        font-size: 0.94rem;
        font-weight: 700;
        line-height: 1.32;
        color: var(--guest-text-headings, #0f172a);
        text-decoration: none;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        transition: color 0.2s ease;
    }

    .capstone-card:hover .card-title-link {
        color: var(--guest-accent, #0c4233);
    }

    /* Author & Year Row */
    .card-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        font-size: 0.74rem;
        color: var(--guest-text-muted, #64748b);
    }

    .card-author-name {
        color: var(--guest-text-main, #334155);
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
    }

    .card-year-badge {
        font-family: 'JetBrains Mono', monospace;
        color: var(--guest-text-muted, #64748b);
        font-weight: 600;
        font-size: 0.74rem;
        flex-shrink: 0;
    }

    /* Tags Row */
    .card-tags-row {
        display: flex;
        align-items: center;
        gap: 5px;
        flex-wrap: wrap;
    }

    .card-tag {
        font-size: 0.66rem;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 9999px;
        white-space: nowrap;
    }

    .card-tag-cyan {
        border: 1px solid #a7f3d0;
        color: #047857;
        background: #ecfdf5;
    }

    .card-tag-emerald {
        border: 1px solid #a7f3d0;
        color: #065f46;
        background: #f0fdf4;
    }

    .card-tag-blue {
        border: 1px solid #bae6fd;
        color: #0369a1;
        background: #f0f9ff;
    }

    /* Abstract snippet (2 lines for sleeker card height) */
    .card-abstract-text {
        font-size: 0.75rem;
        color: var(--guest-text-main, #475569);
        line-height: 1.48;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Card Bottom Action Bar */
    .card-footer-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 0.15rem;
    }

    .card-stats-info {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.72rem;
        color: var(--guest-text-muted, #64748b);
        font-family: 'JetBrains Mono', monospace;
    }

    .card-stat-item {
        display: flex;
        align-items: center;
        gap: 3px;
    }

    /* The Solid Green View Project Button */
    .btn-view-project {
        background: var(--guest-accent, #0c4233);
        color: #ffffff;
        border-radius: 9999px;
        font-weight: 700;
        font-size: 0.75rem;
        padding: 0.42rem 1rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.22s ease;
        border: none;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-view-project.full-width {
        width: 100%;
    }

    .btn-view-project:hover {
        background: #082d23;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(12, 66, 51, 0.35);
        color: #ffffff;
    }

    /* ===== EMPTY STATE ===== */
    .empty-state-card {
        grid-column: 1 / -1;
        background: var(--guest-bg-surface, #ffffff);
        border: 1.5px dashed var(--guest-border, #cbd5e1);
        border-radius: 20px;
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-code {
        font-family: 'JetBrains Mono', monospace;
        color: var(--guest-accent, #0c4233);
        font-size: 0.88rem;
        margin-bottom: 0.5rem;
    }

    .empty-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--guest-text-headings, #0f172a);
        margin-bottom: 0.4rem;
    }

    .empty-desc {
        color: var(--guest-text-muted, #64748b);
        font-size: 0.9rem;
        max-width: 440px;
        margin: 0 auto 1.5rem;
    }

    /* ===== INFINITE SCROLL LOADER & SENTINEL ===== */
    .infinite-scroll-sentinel {
        width: 100%;
        padding: 2.5rem 0 3.5rem 0;
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    .infinite-scroll-loader {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.65rem 1.35rem;
        background: var(--guest-bg-surface, #ffffff);
        border: 1.5px solid var(--guest-accent, #0c4233);
        border-radius: 9999px;
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        color: var(--guest-accent, #0c4233);
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.08);
    }

    .infinite-scroll-spinner {
        width: 16px;
        height: 16px;
        border: 2.5px solid rgba(12, 66, 51, 0.2);
        border-top-color: var(--guest-accent, #0c4233);
        border-radius: 50%;
        animation: infiniteScrollSpin 0.75s linear infinite;
    }

    @keyframes infiniteScrollSpin {
        to { transform: rotate(360deg); }
    }

    .infinite-scroll-end {
        color: #64748b;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.5rem 1rem;
        letter-spacing: 0.01em;
    }
</style>

<div class="browse-page-container">

    <div class="browse-layout">

        <!-- ================= LEFT COLUMN: FILTER PROJECTS (Fixed) ================= -->
        <div class="filter-sidebar-wrapper">
            <aside class="filter-sidebar">
            <form action="{{ route('repository.index') }}" method="GET" id="browseFilterForm">
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif

                <!-- Pinned Fixed Top Header: Filter Projects + Search Icon -->
                <div class="filter-header-pinned">
                    <!-- Filter Header -->
                    <div class="filter-header">
                        <span class="filter-header-title">FILTER PROJECTS</span>
                        <button type="button" class="filter-search-btn" id="filterSearchToggle" title="Filter by keyword">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="M21 21l-4.35-4.35"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Hidden Keyword Search Box (Toggled) -->
                    <div class="filter-search-box {{ request('keyword') || request('search') || request('q') ? 'show' : '' }}" id="filterSearchBox">
                        <input 
                            type="text" 
                            name="keyword" 
                            id="filterKeywordInput" 
                            class="filter-search-input" 
                            placeholder="Filter by author or keyword..." 
                            autocomplete="off"
                            value="{{ request('keyword', request('search', request('q', ''))) }}"
                        >
                    </div>
                </div>

                <!-- Scrollable Filter Body Container -->
                <div class="filter-scrollable-body">

                <!-- SECTION 1: CATEGORY -->
                <div class="filter-section" id="sectionCategory">
                    <div class="filter-section-title" onclick="toggleFilterSection('sectionCategory')">
                        <span>CATEGORY ({{ $categories->count() }})</span>
                        <svg class="filter-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="18 15 12 9 6 15"></polyline>
                        </svg>
                    </div>

                    <div class="filter-items">
                        @if($categories->isEmpty())
                            <p style="font-size: 0.78rem; color: #94a3b8; padding: 0.25rem 0; font-style: italic;">
                                No categories yet
                            </p>
                        @else
                            @php
                                $topCategories = $categories->take(8);
                            @endphp

                            @foreach($topCategories as $cat)
                                <label class="filter-item-label" title="{{ $cat->name }}">
                                    <input 
                                        type="checkbox" 
                                        name="categories[]" 
                                        value="{{ $cat->id }}" 
                                        class="custom-checkbox filter-category-cb"
                                        {{ in_array($cat->id, $selectedCategories ?? []) ? 'checked' : '' }}
                                        onchange="document.getElementById('browseFilterForm').submit()"
                                    >
                                    <span class="truncate">{{ $cat->name }}</span>
                                </label>
                            @endforeach

                            @if($categories->count() > 8)
                                <div id="moreCategoriesList" style="display: none; margin-top: 0.35rem;">
                                    @foreach($categories->skip(8) as $cat)
                                        <label class="filter-item-label" title="{{ $cat->name }}">
                                            <input 
                                                type="checkbox" 
                                                name="categories[]" 
                                                value="{{ $cat->id }}" 
                                                class="custom-checkbox filter-category-cb"
                                                {{ in_array($cat->id, $selectedCategories ?? []) ? 'checked' : '' }}
                                                onchange="document.getElementById('browseFilterForm').submit()"
                                            >
                                            <span class="truncate">{{ $cat->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <span class="filter-show-more" id="showMoreCategoriesBtn" onclick="toggleMoreCategories()">
                                    + Show More ({{ $categories->count() - 8 }} more)
                                </span>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- SECTION 2: AUTHOR -->
                <div class="filter-section" id="sectionAuthor">
                    <div class="filter-section-title" onclick="toggleFilterSection('sectionAuthor')">
                        <span>AUTHOR ({{ $authorsList->count() }})</span>
                        <svg class="filter-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="18 15 12 9 6 15"></polyline>
                        </svg>
                    </div>

                    <div class="filter-items">
                        @if($authorsList->isEmpty())
                            <p style="font-size: 0.78rem; color: #94a3b8; padding: 0.25rem 0; font-style: italic;">
                                No authors yet
                            </p>
                        @else
                            @php
                                $hasHiddenSelectedAuthor = false;
                                if (!empty($selectedAuthors) && $authorsList->count() > 6) {
                                    $hiddenAuthors = $authorsList->skip(6);
                                    foreach ($selectedAuthors as $sa) {
                                        if ($hiddenAuthors->contains($sa)) {
                                            $hasHiddenSelectedAuthor = true;
                                            break;
                                        }
                                    }
                                }
                                $authorsToShow = $authorsList->take(6);
                            @endphp

                            <div class="filter-grid-2col">
                                @foreach($authorsToShow as $author)
                                    <label class="filter-item-label" title="{{ $author }}">
                                        <input 
                                            type="checkbox" 
                                            name="authors[]" 
                                            value="{{ $author }}" 
                                            class="custom-checkbox filter-author-cb"
                                            {{ in_array($author, $selectedAuthors ?? []) ? 'checked' : '' }}
                                            onchange="document.getElementById('browseFilterForm').submit()"
                                        >
                                        <span class="truncate">{{ $author }}</span>
                                    </label>
                                @endforeach
                            </div>

                            @if($authorsList->count() > 6)
                                <div class="filter-grid-2col" id="moreAuthorsList" style="display: {{ $hasHiddenSelectedAuthor ? 'grid' : 'none' }}; margin-top: 0.4rem;">
                                    @foreach($authorsList->skip(6) as $author)
                                        <label class="filter-item-label" title="{{ $author }}">
                                            <input 
                                                type="checkbox" 
                                                name="authors[]" 
                                                value="{{ $author }}" 
                                                class="custom-checkbox filter-author-cb"
                                                {{ in_array($author, $selectedAuthors ?? []) ? 'checked' : '' }}
                                                onchange="document.getElementById('browseFilterForm').submit()"
                                            >
                                            <span class="truncate">{{ $author }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <span class="filter-show-more" id="showMoreAuthorsBtn" onclick="toggleMoreAuthors()">
                                    {{ $hasHiddenSelectedAuthor ? '- Show Less' : '+ Show More (' . ($authorsList->count() - 6) . ' more)' }}
                                </span>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- SECTION 3: YEAR -->
                <div class="filter-section" id="sectionYear">
                    <div class="filter-section-title" onclick="toggleFilterSection('sectionYear')">
                        <span>YEAR ({{ ($yearsList ?? collect())->count() }})</span>
                        <svg class="filter-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="18 15 12 9 6 15"></polyline>
                        </svg>
                    </div>

                    <div class="filter-items">
                        @if(($yearsList ?? collect())->isEmpty())
                            <p style="font-size: 0.78rem; color: #94a3b8; padding: 0.25rem 0; font-style: italic;">
                                No years yet
                            </p>
                        @else
                            <div class="filter-grid-2col">
                                @foreach($yearsList as $yr)
                                    <label class="filter-item-label">
                                        <input 
                                            type="checkbox" 
                                            name="years[]" 
                                            value="{{ $yr }}" 
                                            class="custom-checkbox filter-year-cb"
                                            {{ in_array((string)$yr, array_map('strval', $selectedYears ?? [])) ? 'checked' : '' }}
                                            onchange="document.getElementById('browseFilterForm').submit()"
                                        >
                                        <span>{{ $yr }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Reset Link if filters applied -->
                @if(!empty($selectedCategories) || !empty($selectedYears) || !empty($selectedAuthors))
                    <a href="{{ route('repository.index') }}" class="filter-reset-link">
                        ✕ Clear All Active Filters
                    </a>
                @endif
                </div><!-- End .filter-scrollable-body -->
            </form>
        </aside>
        </div>

        <!-- ================= RIGHT COLUMN: CAPSTONE PROJECTS SHOWCASE ================= -->
        <main class="projects-main">

            <!-- Header Row: Title, Project Count, and Sort Dropdown -->
            <div class="projects-header-row">
                <div class="projects-heading-group">
                    <h1 class="projects-title">Browse Capstone Projects</h1>
                    <p class="projects-subtitle">
                        Browsing <span class="projects-count-highlight">{{ number_format($researches->total()) }}</span> projects found
                    </p>
                </div>

                <!-- Sort By Dropdown Pill -->
                <div class="sort-control-wrap">
                    <span>Sort by:</span>
                    <select class="sort-dropdown" onchange="applySort(this.value)">
                        <option value="newest" {{ ($selectedSort ?? 'newest') === 'newest' ? 'selected' : '' }}>Newest First ⌵</option>
                        <option value="oldest" {{ ($selectedSort ?? '') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="views" {{ ($selectedSort ?? '') === 'views' ? 'selected' : '' }}>Most Viewed</option>
                        <option value="title" {{ ($selectedSort ?? '') === 'title' ? 'selected' : '' }}>Title (A-Z)</option>
                    </select>
                </div>
            </div>

            <!-- 3-Column Capstone Cards Grid -->
            <div class="capstone-cards-grid" id="capstoneCardsGrid">
                @if($researches->isEmpty())
                    <div class="empty-state-card">
                        @if(!empty($selectedCategories) || !empty($selectedYears) || !empty($selectedAuthors) || request('keyword') || request('search') || request('q'))
                            <h2 class="empty-title">No Capstone Projects Match Your Criteria</h2>
                            <p class="empty-desc">
                                Try adjusting your category, author, or year filters to browse more archived research works.
                            </p>
                            <a href="{{ route('repository.index') }}" class="btn-view-project" style="display: inline-flex;">
                                Clear Filters
                            </a>
                        @else
                            <h2 class="empty-title">No Capstone Projects Available Yet</h2>
                            <p class="empty-desc">
                                There are currently no approved capstone projects in the repository. New submissions will appear here once approved.
                            </p>
                        @endif
                    </div>
                @else
                    <!-- Dynamic Client-Side Search Empty State -->
                    <div class="empty-state-card" id="clientSearchNoResults" style="display: none; grid-column: 1 / -1; width: 100%;">
                        <div class="empty-code">// STATUS: 0_SEARCH_RESULTS</div>
                        <h2 class="empty-title">No Capstone Projects Match Your Search</h2>
                        <p class="empty-desc" id="clientSearchNoResultsDesc">
                            No projects match the keyword.
                        </p>
                        <button type="button" class="btn-view-project" onclick="clearFilterSearch()" style="display: inline-flex; margin-top: 0.5rem; cursor: pointer;">
                            Clear Search Filter
                        </button>
                    </div>

                    @include('repository.partials.card-items', ['researches' => $researches])
                @endif
            </div>

            <!-- Infinite Scroll Sentinel & Loader -->
            <div id="infiniteScrollSentinel" class="infinite-scroll-sentinel">
                <div id="infiniteScrollLoader" class="infinite-scroll-loader" style="display: none;">
                    <div class="infinite-scroll-spinner"></div>
                    <span>Loading more projects...</span>
                </div>
                <div id="infiniteScrollEnd" class="infinite-scroll-end" style="{{ isset($researches) && method_exists($researches, 'hasMorePages') && $researches->hasMorePages() ? 'display: none;' : '' }}">
                    @if(isset($researches) && method_exists($researches, 'total') && $researches->total() > 9)
                        <span>✓ All {{ number_format($researches->total()) }} projects loaded</span>
                    @endif
                </div>
            </div>

        </main>

    </div>

</div>

<!-- Filter Interactions Script -->
<script>
    function toggleFilterSection(sectionId) {
        const el = document.getElementById(sectionId);
        if (el) el.classList.toggle('collapsed');
    }

    function toggleMoreAuthors() {
        const moreList = document.getElementById('moreAuthorsList');
        const btn = document.getElementById('showMoreAuthorsBtn');
        if (moreList) {
            if (moreList.style.display === 'none') {
                moreList.style.display = 'grid';
                if (btn) btn.innerText = '- Show Less';
            } else {
                moreList.style.display = 'none';
                if (btn) btn.innerText = '+ Show More';
            }
        }
    }

    function toggleMoreCategories() {
        const moreList = document.getElementById('moreCategoriesList');
        const btn = document.getElementById('showMoreCategoriesBtn');
        if (moreList) {
            if (moreList.style.display === 'none') {
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

    // ==========================================
    // AUTO INFINITE SCROLL ON SCROLL
    // ==========================================
    (function initInfiniteScroll() {
        let nextPageUrl = @json(isset($researches) && method_exists($researches, 'hasMorePages') && $researches->hasMorePages() ? $researches->nextPageUrl() : null);
        let isLoading = false;
        let hasMore = Boolean(nextPageUrl);

        const grid = document.getElementById('capstoneCardsGrid');
        const sentinel = document.getElementById('infiniteScrollSentinel');
        const loader = document.getElementById('infiniteScrollLoader');
        const endMessage = document.getElementById('infiniteScrollEnd');

        if (!grid || !sentinel) return;

        async function loadNextPage() {
            if (!hasMore || isLoading || !nextPageUrl) return;

            isLoading = true;
            if (loader) loader.style.display = 'inline-flex';
            if (endMessage) endMessage.style.display = 'none';

            try {
                const response = await fetch(nextPageUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-Infinite-Scroll': '1',
                        'Accept': 'application/json, text/html'
                    }
                });

                if (!response.ok) {
                    throw new Error('Network error loading more projects');
                }

                const contentType = response.headers.get('content-type') || '';
                let newCardsHtml = '';

                if (contentType.includes('application/json')) {
                    const data = await response.json();
                    newCardsHtml = data.html || '';
                    hasMore = Boolean(data.hasMore);
                    nextPageUrl = data.nextPageUrl || null;
                } else {
                    const htmlText = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(htmlText, 'text/html');
                    const cardNodes = doc.querySelectorAll('#capstoneCardsGrid .project-card-item');
                    cardNodes.forEach(node => {
                        newCardsHtml += node.outerHTML;
                    });
                    const currentUrlObj = new URL(nextPageUrl, window.location.origin);
                    const curPage = parseInt(currentUrlObj.searchParams.get('page') || '1', 10);
                    currentUrlObj.searchParams.set('page', curPage + 1);
                    nextPageUrl = cardNodes.length > 0 ? currentUrlObj.toString() : null;
                    hasMore = Boolean(cardNodes.length > 0);
                }

                if (newCardsHtml.trim() !== '') {
                    const temp = document.createElement('div');
                    temp.innerHTML = newCardsHtml;
                    while (temp.firstChild) {
                        grid.appendChild(temp.firstChild);
                    }

                    // Re-run search/filter if user currently has filter input
                    if (typeof runCardFilter === 'function') {
                        runCardFilter();
                    }
                } else {
                    hasMore = false;
                }

            } catch (err) {
                console.error('Error auto-loading projects:', err);
                hasMore = false;
            } finally {
                isLoading = false;
                if (loader) loader.style.display = 'none';
                if (!hasMore && endMessage) {
                    endMessage.innerHTML = '<span>✓ All projects loaded</span>';
                    endMessage.style.display = 'block';
                }
            }
        }

        // IntersectionObserver for auto loading projects as user scrolls down
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        loadNextPage();
                    }
                });
            }, {
                root: null,
                rootMargin: '450px 0px',
                threshold: 0.01
            });

            observer.observe(sentinel);
        } else {
            // Scroll event fallback
            window.addEventListener('scroll', function() {
                if (isLoading || !hasMore) return;
                const rect = sentinel.getBoundingClientRect();
                if (rect.top <= window.innerHeight + 450) {
                    loadNextPage();
                }
            }, { passive: true });
        }
    })();

    // Quick filter search box toggle & realtime filter
    const filterSearchToggle = document.getElementById('filterSearchToggle');
    const filterSearchBox = document.getElementById('filterSearchBox');
    const filterKeywordInput = document.getElementById('filterKeywordInput');

    if (filterSearchToggle && filterSearchBox) {
        filterSearchToggle.addEventListener('click', function() {
            filterSearchBox.classList.toggle('show');
            if (filterSearchBox.classList.contains('show') && filterKeywordInput) {
                filterKeywordInput.focus();
            }
        });
    }

    // Helper to normalize and strip diacritics/accents (e.g. ñ -> n, é -> e) and punctuation
    function normalizeSearchText(str) {
        return (str || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '') // converts 'estaño' -> 'estano', 'paña' -> 'pana'
            .replace(/[^a-z0-9\s]/gi, ' ')   // replaces punctuation/dots/commas with space
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

    function runCardFilter() {
        if (!filterKeywordInput) return;
        const rawVal = filterKeywordInput.value.trim();
        const normalizedQuery = normalizeSearchText(rawVal);
        const tokens = normalizedQuery.split(' ').filter(Boolean);

        let visibleCount = 0;
        const projectCards = document.querySelectorAll('.project-card-item');

        projectCards.forEach(card => {
            if (tokens.length === 0) {
                card.style.display = 'flex';
                visibleCount++;
                return;
            }

            const title = normalizeSearchText(card.getAttribute('data-title'));
            const authors = normalizeSearchText(card.getAttribute('data-authors'));
            const abstract = normalizeSearchText(card.getAttribute('data-abstract'));
            const category = normalizeSearchText(card.getAttribute('data-category'));

            const haystack = `${title} ${authors} ${abstract} ${category}`;

            // Every token in query must match
            const isMatch = tokens.every(token => haystack.includes(token));

            if (isMatch) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const noResultsEl = document.getElementById('clientSearchNoResults');
        const noResultsDesc = document.getElementById('clientSearchNoResultsDesc');
        if (noResultsEl) {
            if (visibleCount === 0 && tokens.length > 0) {
                noResultsEl.style.display = 'block';
                if (noResultsDesc) {
                    noResultsDesc.textContent = `No projects matching "${rawVal}". Try searching by author surname, project keyword, or title.`;
                }
            } else {
                noResultsEl.style.display = 'none';
            }
        }
    }

    if (filterKeywordInput) {
        filterKeywordInput.addEventListener('input', runCardFilter);

        // Run initially if keyword input has existing value
        if (filterKeywordInput.value.trim() !== '') {
            runCardFilter();
        }
    }

    // Synchronize fixed sidebar position with wrapper column
    function alignFilterSidebar() {
        const wrapper = document.querySelector('.filter-sidebar-wrapper');
        const sidebar = document.querySelector('.filter-sidebar');
        if (wrapper && sidebar && window.innerWidth > 1024) {
            const rect = wrapper.getBoundingClientRect();
            sidebar.style.left = rect.left + 'px';
            sidebar.style.width = rect.width + 'px';
        } else if (sidebar) {
            sidebar.style.left = '';
            sidebar.style.width = '';
        }
    }
    window.addEventListener('resize', alignFilterSidebar);
    window.addEventListener('scroll', alignFilterSidebar, { passive: true });
    alignFilterSidebar();

    // Strictly suppress horizontal trackpad/touchpad swipe on filter sidebar to prevent horizontal scroll chaining
    const filterSidebarEl = document.querySelector('.filter-sidebar');
    const filterBodyEl = document.querySelector('.filter-scrollable-body');
    [filterSidebarEl, filterBodyEl].forEach(function(el) {
        if (el) {
            el.addEventListener('wheel', function(e) {
                if (Math.abs(e.deltaX) > Math.abs(e.deltaY)) {
                    e.preventDefault();
                }
            }, { passive: false });
        }
    });
</script>
@endsection
