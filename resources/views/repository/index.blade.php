@extends(auth()->check() ? (auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.app') : 'layouts.public')
@section('title', 'capstoneHub — Browse Capstone Projects')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    .browse-page-container {
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        color: #f8fafc;
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
        background: linear-gradient(var(--guest-bg-sidebar, rgba(13, 21, 32, 0.94)), var(--guest-bg-sidebar, rgba(13, 21, 32, 0.94))) padding-box,
                    var(--guest-card-border-grad, linear-gradient(135deg, #00f5d4 0%, #06b6d4 35%, #10b981 70%, #34d399 100%)) border-box;
        border: 1.5px solid transparent;
        border-radius: 18px;
        box-shadow: 0 12px 34px rgba(0, 0, 0, 0.45), 0 0 20px rgba(0, 245, 212, 0.12), 0 0 20px rgba(16, 185, 129, 0.08);
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
        background: var(--guest-bg-sidebar, rgba(13, 21, 32, 0.96));
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        z-index: 10;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);
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
        color: var(--guest-text-headings, #ffffff);
    }

    .filter-search-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.14);
        color: var(--guest-accent, #00f5d4);
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
        background: rgba(8, 14, 22, 0.8);
        border: 1px solid rgba(0, 245, 212, 0.35);
        border-radius: 10px;
        padding: 0.5rem 0.8rem;
        color: #ffffff;
        font-size: 0.82rem;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .filter-search-input:focus {
        border-color: #00f5d4;
        box-shadow: 0 0 10px rgba(0, 245, 212, 0.25);
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
        color: #94a3b8;
        cursor: pointer;
        margin-bottom: 0.85rem;
        user-select: none;
    }

    .filter-chevron {
        color: #00f5d4;
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
        color: #cbd5e1;
        cursor: pointer;
        user-select: none;
        transition: color 0.15s ease;
        min-width: 0;
        max-width: 100%;
        overflow: hidden;
    }

    .filter-item-label:hover {
        color: #ffffff;
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
        background: rgba(15, 23, 42, 0.7);
        border: 1.5px solid rgba(255, 255, 255, 0.22);
        cursor: pointer;
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .custom-checkbox:hover {
        border-color: #00f5d4;
    }

    .custom-checkbox:checked {
        background: #10b981;
        border-color: #10b981;
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.4);
    }

    .custom-checkbox:checked::after {
        content: '';
        width: 5px;
        height: 9px;
        border: solid #042f2e;
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
        color: #00f5d4;
        font-size: 0.78rem;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: color 0.2s ease;
    }

    .filter-show-more:hover {
        color: #38bdf8;
        text-decoration: underline;
    }

    /* Reset Filters Link */
    .filter-reset-link {
        display: block;
        text-align: center;
        margin-top: 1.2rem;
        padding-top: 0.8rem;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        color: #94a3b8;
        font-size: 0.78rem;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .filter-reset-link:hover {
        color: #38bdf8;
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
        color: #ffffff;
    }

    .projects-subtitle {
        font-size: 0.85rem;
        color: #94a3b8;
        font-weight: 500;
    }

    .projects-count-highlight {
        color: #cbd5e1;
        font-weight: 600;
    }

    /* Sort By Control */
    .sort-control-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: #94a3b8;
    }

    .sort-dropdown {
        background: transparent !important;
        border: 1.5px solid var(--guest-border, rgba(255, 255, 255, 0.2)) !important;
        color: var(--guest-text-headings, #ffffff) !important;
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
        border-color: #00f5d4;
        box-shadow: 0 0 14px rgba(0, 245, 212, 0.25);
    }

    .sort-dropdown option {
        background: #0f1926;
        color: #ffffff;
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

    /* Capstone Card with Two-Tone Glowing Border (Compact Sleek Size) */
    .capstone-card {
        background: linear-gradient(rgba(14, 22, 33, 0.94), rgba(14, 22, 33, 0.94)) padding-box,
                    linear-gradient(135deg, #00f5d4 0%, #06b6d4 35%, #10b981 70%, #34d399 100%)) border-box;
        border: 1.5px solid transparent;
        border-radius: 15px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35), 0 0 14px rgba(0, 245, 212, 0.08);
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
        box-shadow: 0 14px 34px rgba(0, 0, 0, 0.5), 0 0 20px rgba(0, 245, 212, 0.18), 0 0 16px rgba(16, 185, 129, 0.15);
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
        color: #ffffff;
        text-decoration: none;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        transition: color 0.2s ease;
    }

    .capstone-card:hover .card-title-link {
        color: #00f5d4;
    }

    /* Author & Year Row */
    .card-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        font-size: 0.74rem;
        color: #94a3b8;
    }

    .card-author-name {
        color: #cbd5e1;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
    }

    .card-year-badge {
        font-family: 'JetBrains Mono', monospace;
        color: #94a3b8;
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
        border: 1px solid rgba(0, 245, 212, 0.45);
        color: #00f5d4;
        background: rgba(0, 245, 212, 0.06);
    }

    .card-tag-emerald {
        border: 1px solid rgba(16, 185, 129, 0.45);
        color: #6ee7b7;
        background: rgba(16, 185, 129, 0.08);
    }

    .card-tag-blue {
        border: 1px solid rgba(56, 189, 248, 0.45);
        color: #38bdf8;
        background: rgba(56, 189, 248, 0.06);
    }

    /* Abstract snippet (2 lines for sleeker card height) */
    .card-abstract-text {
        font-size: 0.75rem;
        color: #94a3b8;
        line-height: 1.42;
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
        color: #94a3b8;
        font-family: 'JetBrains Mono', monospace;
    }

    .card-stat-item {
        display: flex;
        align-items: center;
        gap: 3px;
    }

    /* The Solid Green View Project Button */
    .btn-view-project {
        background: #10b981;
        color: #042f2e;
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
        background: #34d399;
        transform: translateY(-1px);
        box-shadow: 0 4px 18px rgba(16, 185, 129, 0.45);
        color: #042f2e;
    }

    /* ===== EMPTY STATE ===== */
    .empty-state-card {
        grid-column: 1 / -1;
        background: rgba(14, 22, 33, 0.7);
        border: 1.5px dashed rgba(255, 255, 255, 0.15);
        border-radius: 20px;
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-code {
        font-family: 'JetBrains Mono', monospace;
        color: #00f5d4;
        font-size: 0.88rem;
        margin-bottom: 0.5rem;
    }

    .empty-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 0.4rem;
    }

    .empty-desc {
        color: #94a3b8;
        font-size: 0.9rem;
        max-width: 440px;
        margin: 0 auto 1.5rem;
    }

    /* ===== PAGINATION ===== */
    /* ===== FIXED BOTTOM FLOATING PAGINATION BAR ===== */
    .custom-pagination-bar {
        position: fixed;
        bottom: 18px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 110;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 5px 10px;
        background: rgba(10, 18, 28, 0.94);
        border: 1.5px solid rgba(0, 245, 212, 0.35);
        border-radius: 9999px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.75), 0 0 18px rgba(0, 245, 212, 0.2);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        margin: 0;
        max-width: calc(100vw - 24px);
    }

    .pag-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 26px;
        height: 26px;
        padding: 0 0.5rem;
        border-radius: 9999px;
        font-size: 0.74rem;
        font-weight: 600;
        text-decoration: none;
        color: #cbd5e1;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.12);
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .pag-btn:hover {
        background: rgba(0, 245, 212, 0.18);
        color: #00f5d4;
        border-color: rgba(0, 245, 212, 0.45);
        transform: translateY(-1px);
    }

    .pag-btn.active {
        background: #00f5d4;
        color: #042f2e;
        border-color: #00f5d4;
        box-shadow: 0 0 12px rgba(0, 245, 212, 0.55);
        font-weight: 800;
    }

    .pag-dots {
        color: #64748b;
        padding: 0 3px;
        font-size: 0.75rem;
        font-weight: 700;
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

                    @foreach($researches as $index => $research)
                        <div class="capstone-card project-card-item" 
                             data-title="{{ strtolower($research->title) }}" 
                             data-authors="{{ strtolower($research->authors ?? '') }}"
                             data-abstract="{{ strtolower($research->abstract ?? '') }}"
                             data-category="{{ strtolower($research->category->name ?? '') }}">
                            
                            <div class="card-top-content">
                                <!-- Title -->
                                <a href="{{ route('repository.show', $research->id) }}" class="card-title-link">
                                    {{ $research->title }}
                                </a>

                                <!-- Author & Year Row -->
                                <div class="card-meta-row">
                                    <span class="card-author-name" title="{{ $research->authors ?? 'Student Researcher' }}">
                                        {{ $research->authors ?? 'SCS Researchers' }}
                                    </span>
                                    <span class="card-year-badge">
                                        {{ $research->year ?: ($research->approved_at ? $research->approved_at->format('Y') : ($research->created_at ? $research->created_at->format('Y') : '2026')) }}
                                    </span>
                                </div>

                                <!-- Abstract Snippet -->
                                <p class="card-abstract-text">
                                    {{ $research->abstract ?? $research->objectives ?? 'This capstone focuses on developing modern software solutions and technological architectures for institutional automation.' }}
                                </p>
                            </div>

                            <!-- Footer Row: Stats + Solid Green View Project Button -->
                            <div class="card-footer-row">
                                <div class="card-stats-info">
                                    <span class="card-stat-item" title="Views">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        {{ number_format($research->views_count ?? 0) }}
                                    </span>
                                    <span class="card-stat-item" title="Downloads">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                            <polyline points="7 10 12 15 17 10"/>
                                            <line x1="12" y1="15" x2="12" y2="3"/>
                                        </svg>
                                        {{ number_format($research->approved_downloads_count ?? 0) }}
                                    </span>
                                </div>
                                <a href="{{ route('repository.show', $research->id) }}" class="btn-view-project">
                                    View Project
                                </a>
                            </div>

                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Custom Pagination -->
            <div class="custom-pagination-bar">
                @if($researches->hasPages())
                    @if($researches->onFirstPage())
                        <span class="pag-btn" style="opacity: 0.4; cursor: not-allowed;">‹ Previous</span>
                    @else
                        <a href="{{ $researches->previousPageUrl() }}" class="pag-btn">‹ Previous</a>
                    @endif

                    @foreach(range(1, min(5, $researches->lastPage())) as $page)
                        @if($page == $researches->currentPage())
                            <span class="pag-btn active">{{ $page }}</span>
                        @else
                            <a href="{{ $researches->url($page) }}" class="pag-btn">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($researches->lastPage() > 5)
                        <span class="pag-dots">...</span>
                        <a href="{{ $researches->url($researches->lastPage()) }}" class="pag-btn">{{ $researches->lastPage() }}</a>
                    @endif

                    @if($researches->hasMorePages())
                        <a href="{{ $researches->nextPageUrl() }}" class="pag-btn">Next ›</a>
                    @else
                        <span class="pag-btn" style="opacity: 0.4; cursor: not-allowed;">Next ›</span>
                    @endif
                @else
                    <span class="pag-btn" style="opacity: 0.4; cursor: not-allowed;">‹ Previous</span>
                    <span class="pag-btn active">1</span>
                    <span class="pag-btn" style="opacity: 0.65;">2</span>
                    <span class="pag-btn" style="opacity: 0.65;">3</span>
                    <span class="pag-btn" style="opacity: 0.65;">4</span>
                    <span class="pag-btn" style="opacity: 0.65;">5</span>
                    <span class="pag-dots">...</span>
                    <span class="pag-btn" style="opacity: 0.65;">120</span>
                    <span class="pag-btn" style="opacity: 0.4; cursor: not-allowed;">Next ›</span>
                @endif
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

    // Quick filter search box toggle & realtime filter
    const filterSearchToggle = document.getElementById('filterSearchToggle');
    const filterSearchBox = document.getElementById('filterSearchBox');
    const filterKeywordInput = document.getElementById('filterKeywordInput');
    const projectCards = document.querySelectorAll('.project-card-item');

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

    if (filterKeywordInput) {
        function runCardFilter() {
            const rawVal = filterKeywordInput.value.trim();
            const normalizedQuery = normalizeSearchText(rawVal);
            const tokens = normalizedQuery.split(' ').filter(Boolean);

            let visibleCount = 0;

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
