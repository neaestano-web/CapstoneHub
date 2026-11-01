@extends('layouts.app')
@section('title', 'capstoneHub — Student Research Repository')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    .student-browse-container {
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        color: var(--guest-text-main, #f8fafc);
        max-width: 1420px;
        margin: 0 auto;
        padding: 0.5rem 0.5rem 6.5rem;
        width: 100%;
        position: relative;
    }

    /* Ambient Background Glow Highlights */
    .browse-ambient-glow {
        position: absolute;
        top: -60px;
        left: 50%;
        transform: translateX(-50%);
        width: 750px;
        height: 380px;
        background: radial-gradient(circle, rgba(0, 245, 212, 0.12) 0%, rgba(139, 92, 246, 0.08) 50%, transparent 70%);
        pointer-events: none;
        z-index: 0;
        filter: blur(40px);
    }

    /* ===== MODERN HERO BANNER ===== */
    .hero-banner-card {
        position: relative;
        z-index: 1;
        margin-bottom: 2.25rem;
        padding: 2.75rem 2rem 2.25rem;
        text-align: center;
        background: linear-gradient(var(--guest-bg-surface, rgba(14, 22, 33, 0.85)), var(--guest-bg-surface, rgba(14, 22, 33, 0.85))) padding-box,
                    var(--guest-card-border-grad, linear-gradient(135deg, rgba(0, 245, 212, 0.6) 0%, rgba(99, 102, 241, 0.5) 50%, rgba(239, 68, 68, 0.5) 100%)) border-box;
        border: 1.5px solid transparent;
        border-radius: 28px;
        box-shadow: var(--guest-card-shadow, 0 20px 45px rgba(0, 0, 0, 0.35)), 0 0 25px rgba(0, 245, 212, 0.08);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        overflow: hidden;
    }

    /* Subtle Animated Grid Lines in Banner */
    .hero-banner-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: 
            linear-gradient(rgba(128, 128, 128, 0.05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(128, 128, 128, 0.05) 1px, transparent 1px);
        background-size: 36px 36px;
        pointer-events: none;
        opacity: 0.6;
    }

    /* Floating Badge */
    .hero-tech-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 5px 16px;
        border-radius: 9999px;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        background: rgba(0, 245, 212, 0.08);
        border: 1px solid rgba(0, 245, 212, 0.35);
        color: var(--guest-accent, #00f5d4);
        box-shadow: 0 0 15px rgba(0, 245, 212, 0.15);
        margin-bottom: 1.1rem;
        position: relative;
        z-index: 2;
    }

    .hero-pulsing-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--guest-accent, #00f5d4);
        box-shadow: 0 0 8px var(--guest-accent, #00f5d4);
        animation: pulseDot 2s infinite ease-in-out;
    }

    @keyframes pulseDot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(0.85); }
    }

    .hero-title {
        font-size: 2.35rem;
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1.18;
        color: var(--guest-text-headings, #ffffff);
        max-width: 820px;
        margin: 0 auto 0.75rem;
        position: relative;
        z-index: 2;
    }

    .hero-title-highlight {
        background: linear-gradient(135deg, var(--guest-accent, #00f5d4) 0%, #38bdf8 45%, #a855f7 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
    }

    .hero-subtitle {
        font-size: 0.92rem;
        color: var(--guest-text-muted, #94a3b8);
        max-width: 620px;
        margin: 0 auto 1.6rem;
        line-height: 1.5;
        font-weight: 500;
        position: relative;
        z-index: 2;
    }

    /* Key Statistics Pill Group */
    .hero-stats-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1.5rem;
        flex-wrap: wrap;
        margin-bottom: 1.75rem;
        position: relative;
        z-index: 2;
    }

    .hero-stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.76rem;
        font-weight: 600;
        color: var(--guest-text-main, #cbd5e1);
        background: var(--guest-bg-input, rgba(255, 255, 255, 0.04));
        border: 1px solid var(--guest-border, rgba(255, 255, 255, 0.09));
        padding: 4px 12px;
        border-radius: 9999px;
        backdrop-filter: blur(8px);
    }

    .hero-stat-pill strong {
        color: var(--guest-accent, #00f5d4);
        font-family: 'JetBrains Mono', monospace;
    }

    /* Futuristic Capsule Search Wrap */
    .browse-search-wrap {
        background: linear-gradient(var(--guest-bg-input, rgba(10, 18, 28, 0.94)), var(--guest-bg-input, rgba(10, 18, 28, 0.94))) padding-box,
                    var(--guest-card-border-grad, linear-gradient(135deg, #00f5d4 0%, #06b6d4 35%, #8b5cf6 65%, #ef4444 100%)) border-box;
        border: 1.5px solid transparent;
        border-radius: 9999px;
        padding: 6px 8px 6px 18px;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.25), 0 0 20px rgba(0, 245, 212, 0.16);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        align-items: center;
        max-width: 780px;
        margin: 0 auto;
        position: relative;
        z-index: 2;
    }

    .browse-search-wrap:focus-within {
        box-shadow: 0 16px 45px rgba(0, 0, 0, 0.65), 0 0 30px rgba(0, 245, 212, 0.32);
        transform: translateY(-2px);
    }

    .browse-search-icon-wrap {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: rgba(0, 245, 212, 0.1);
        border: 1px solid rgba(0, 245, 212, 0.25);
        color: var(--guest-accent, #00f5d4);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-right: 8px;
    }

    .browse-search-input {
        background: transparent;
        border: none;
        color: var(--guest-text-headings, #ffffff);
        font-family: inherit;
        font-weight: 600;
        font-size: 0.94rem;
        width: 100%;
        outline: none;
        padding: 0.4rem 0.5rem;
    }

    .browse-search-input::placeholder {
        color: var(--guest-text-muted, #94a3b8);
        font-weight: 500;
    }

    .browse-search-btn {
        background: var(--guest-accent, #00f5d4);
        color: #042f2e;
        padding: 0.65rem 1.45rem;
        border-radius: 9999px;
        font-weight: 800;
        font-size: 0.84rem;
        letter-spacing: 0.02em;
        border: none;
        cursor: pointer;
        transition: all 0.24s cubic-bezier(0.16, 1, 0.3, 1);
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 16px rgba(0, 245, 212, 0.4);
        flex-shrink: 0;
    }

    .browse-search-btn:hover {
        transform: translateY(-1px) scale(1.02);
        filter: brightness(1.1);
        box-shadow: 0 8px 24px rgba(0, 245, 212, 0.55);
    }

    /* ===== HEADER ROW (Title & Controls) ===== */
    .projects-header-row {
        position: relative;
        background: transparent !important;
        padding: 0.9rem 0 0.85rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
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
        color: var(--guest-text-headings, #ffffff);
        margin: 0;
    }

    .projects-subtitle {
        font-size: 0.84rem;
        color: var(--guest-text-muted, #94a3b8);
        font-weight: 500;
        margin: 0;
    }

    .projects-count-highlight {
        color: var(--guest-text-headings, #ffffff);
        font-weight: 700;
    }

    .projects-controls-wrap {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    /* ===== FILTER PROJECTS DROPDOWN POLISH ===== */
    .filter-dropdown-wrapper {
        position: relative;
    }

    .filter-trigger-btn {
        background: linear-gradient(var(--guest-bg-surface, rgba(14, 23, 34, 0.9)), var(--guest-bg-surface, rgba(14, 23, 34, 0.9))) padding-box,
                    var(--guest-card-border-grad, linear-gradient(135deg, #00f5d4 0%, #06b6d4 35%, #8b5cf6 65%, #ef4444 100%)) border-box;
        border: 1.5px solid transparent;
        color: var(--guest-text-headings, #ffffff);
        font-family: inherit;
        font-size: 0.82rem;
        font-weight: 700;
        padding: 0.48rem 1.2rem;
        border-radius: 9999px;
        outline: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
        user-select: none;
    }

    .filter-trigger-btn:hover {
        border-color: var(--guest-accent, #00f5d4);
        box-shadow: 0 0 18px rgba(0, 245, 212, 0.28);
        transform: translateY(-1px);
    }

    .filter-trigger-btn.has-active {
        border-color: var(--guest-accent, #00f5d4);
        box-shadow: 0 0 18px rgba(0, 245, 212, 0.38);
    }

    .filter-badge-count {
        background: var(--guest-accent, #00f5d4);
        color: #042f2e;
        font-size: 0.7rem;
        font-weight: 800;
        padding: 2px 7px;
        border-radius: 9999px;
        line-height: 1.2;
    }

    .filter-trigger-chevron {
        color: var(--guest-accent, #00f5d4);
        transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .filter-trigger-btn.active .filter-trigger-chevron {
        transform: rotate(180deg);
    }

    /* DROPDOWN PANEL POPUP */
    .filter-dropdown-panel {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        width: 360px;
        max-width: calc(100vw - 32px);
        background: linear-gradient(var(--guest-bg-sidebar, rgba(13, 21, 32, 0.98)), var(--guest-bg-sidebar, rgba(13, 21, 32, 0.98))) padding-box,
                    var(--guest-card-border-grad, linear-gradient(135deg, #00f5d4 0%, #06b6d4 35%, #8b5cf6 65%, #ef4444 100%)) border-box;
        border: 1.5px solid transparent;
        border-radius: 22px;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.75), 0 0 30px rgba(0, 245, 212, 0.18);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        z-index: 100;
        display: none;
        flex-direction: column;
        max-height: 80vh;
        overflow: hidden;
        animation: filterPopDown 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .filter-dropdown-panel.open {
        display: flex;
    }

    @keyframes filterPopDown {
        from {
            opacity: 0;
            transform: translateY(-10px) scale(0.97);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .filter-panel-header {
        flex-shrink: 0;
        padding: 1.1rem 1.2rem 0.85rem;
        background: var(--guest-bg-sidebar, rgba(13, 21, 32, 0.99));
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .filter-panel-title {
        font-size: 0.86rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--guest-text-headings, #ffffff);
    }

    .filter-close-btn {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: var(--guest-text-muted, #94a3b8);
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 0.8rem;
        transition: all 0.2s ease;
    }

    .filter-close-btn:hover {
        color: #ef4444;
        border-color: #ef4444;
        background: rgba(239, 68, 68, 0.14);
        transform: rotate(90deg);
    }

    .filter-search-input {
        width: 100%;
        background: var(--guest-bg-input, rgba(8, 14, 22, 0.85));
        border: 1px solid var(--guest-border, rgba(0, 245, 212, 0.35));
        border-radius: 12px;
        padding: 0.52rem 0.85rem;
        color: var(--guest-text-headings, #ffffff);
        font-size: 0.82rem;
        outline: none;
        transition: all 0.2s ease;
        margin-top: 0.75rem;
    }

    .filter-search-input:focus {
        border-color: var(--guest-accent, #00f5d4);
        box-shadow: 0 0 12px var(--guest-accent-glow, rgba(0, 245, 212, 0.3));
    }

    .filter-dropdown-scrollable {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        padding: 1.1rem 1.2rem;
        scrollbar-width: thin;
        scrollbar-color: rgba(0, 245, 212, 0.25) transparent;
    }

    .filter-dropdown-scrollable::-webkit-scrollbar {
        width: 5px;
    }
    .filter-dropdown-scrollable::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.18);
        border-radius: 999px;
    }

    /* Accordion Sections inside Dropdown */
    .filter-section {
        margin-bottom: 1.35rem;
    }
    .filter-section:last-child {
        margin-bottom: 0;
    }

    .filter-section-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.74rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--guest-text-muted, #94a3b8);
        cursor: pointer;
        margin-bottom: 0.7rem;
        user-select: none;
        transition: color 0.2s ease;
    }

    .filter-section-title:hover {
        color: var(--guest-accent, #00f5d4);
    }

    .filter-chevron {
        color: var(--guest-accent, #00f5d4);
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
        gap: 0.55rem;
    }

    .filter-item-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.82rem;
        color: var(--guest-text-main, #cbd5e1);
        cursor: pointer;
        user-select: none;
        transition: color 0.15s ease;
    }

    .filter-item-label:hover {
        color: var(--guest-text-headings, #ffffff);
    }

    .filter-item-label span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .filter-item-count {
        font-size: 0.7rem;
        color: var(--guest-text-muted, #64748b);
        margin-left: auto;
        font-family: 'JetBrains Mono', monospace;
    }

    /* Checkbox */
    .custom-checkbox {
        appearance: none;
        -webkit-appearance: none;
        width: 17px;
        height: 17px;
        border-radius: 5px;
        background: var(--guest-bg-input, rgba(15, 23, 42, 0.75));
        border: 1.5px solid var(--guest-border, rgba(255, 255, 255, 0.25));
        cursor: pointer;
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .custom-checkbox:hover {
        border-color: var(--guest-accent, #00f5d4);
    }

    .custom-checkbox:checked {
        background: #10b981;
        border-color: #10b981;
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.45);
    }

    .custom-checkbox:checked::after {
        content: '';
        width: 5px;
        height: 8px;
        border: solid #042f2e;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg) translate(-0.5px, -1px);
        position: absolute;
    }

    .filter-grid-2col {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 0.55rem 0.55rem;
    }

    .filter-show-more {
        display: inline-block;
        margin-top: 0.45rem;
        color: var(--guest-accent, #00f5d4);
        font-size: 0.76rem;
        font-weight: 700;
        cursor: pointer;
        transition: color 0.2s ease;
    }

    .filter-show-more:hover {
        text-decoration: underline;
    }

    .filter-panel-footer {
        flex-shrink: 0;
        padding: 0.85rem 1.2rem;
        background: var(--guest-bg-sidebar, rgba(13, 21, 32, 0.99));
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }

    .filter-reset-btn {
        color: #ef4444;
        font-size: 0.78rem;
        font-weight: 700;
        text-decoration: none;
        transition: opacity 0.2s;
    }

    .filter-reset-btn:hover {
        opacity: 0.8;
        text-decoration: underline;
    }

    .btn-apply-filters {
        background: #10b981;
        color: #042f2e;
        border-radius: 9999px;
        font-weight: 800;
        font-size: 0.8rem;
        padding: 0.48rem 1.35rem;
        border: none;
        cursor: pointer;
        transition: all 0.24s cubic-bezier(0.16, 1, 0.3, 1);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-apply-filters:hover {
        background: #34d399;
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(16, 185, 129, 0.5);
    }

    /* SORT DROPDOWN */
    .sort-control-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: var(--guest-text-muted, #94a3b8);
    }

    .sort-dropdown {
        background: transparent !important;
        border: 1.5px solid var(--guest-border, rgba(255, 255, 255, 0.2)) !important;
        color: var(--guest-text-headings, #ffffff) !important;
        font-family: inherit;
        font-size: 0.82rem;
        font-weight: 600;
        padding: 0.48rem 1.15rem;
        border-radius: 9999px;
        outline: none;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: none !important;
    }

    .sort-dropdown:hover {
        border-color: var(--guest-accent, #00f5d4);
        box-shadow: 0 0 14px rgba(0, 245, 212, 0.25);
    }

    .sort-dropdown option {
        background: #0f1926;
        color: #ffffff;
    }

    /* ACTIVE FILTER CHIPS BAR */
    .active-chips-bar {
        display: flex;
        align-items: center;
        gap: 7px;
        flex-wrap: wrap;
        margin: 0.6rem 0 1.25rem;
        padding: 0.5rem 0.85rem;
        background: rgba(255, 255, 255, 0.02);
        border: 1px dashed rgba(255, 255, 255, 0.08);
        border-radius: 14px;
    }

    .active-chips-label {
        font-size: 0.72rem;
        font-weight: 800;
        color: var(--guest-text-muted, #94a3b8);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-right: 4px;
    }

    .active-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.74rem;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 9999px;
        background: rgba(0, 245, 212, 0.08);
        border: 1px solid rgba(0, 245, 212, 0.35);
        color: var(--guest-accent, #00f5d4);
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .active-chip:hover {
        background: rgba(239, 68, 68, 0.15);
        border-color: #ef4444;
        color: #fca5a5;
    }

    .active-chip-x {
        font-size: 0.7rem;
        opacity: 0.75;
    }

    .clear-all-chip {
        font-size: 0.74rem;
        font-weight: 700;
        color: #ef4444;
        margin-left: auto;
        text-decoration: underline;
        cursor: pointer;
    }

    /* ===== CYBER CAPSTONE CARDS GRID ===== */
    .capstone-cards-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1.2rem;
        margin-top: 0.5rem;
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

    /* Capstone Card with Two-Tone Glowing Border & Smooth Hover */
    .capstone-card {
        background: linear-gradient(var(--guest-bg-surface, rgba(14, 22, 33, 0.94)), var(--guest-bg-surface, rgba(14, 22, 33, 0.94))) padding-box,
                    var(--guest-card-border-grad, linear-gradient(135deg, #00f5d4 0%, #06b6d4 35%, #8b5cf6 65%, #ef4444 100%)) border-box;
        border: 1.5px solid transparent;
        border-radius: 18px;
        box-shadow: var(--guest-card-shadow, 0 8px 24px rgba(0, 0, 0, 0.35)), 0 0 14px rgba(0, 245, 212, 0.08);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        padding: 1.2rem 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 0.85rem;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
    }

    .capstone-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--guest-card-shadow, 0 16px 38px rgba(0, 0, 0, 0.55)), 0 0 24px rgba(0, 245, 212, 0.22), 0 0 16px rgba(239, 68, 68, 0.12);
    }

    .card-top-content {
        display: flex;
        flex-direction: column;
        gap: 0.55rem;
    }

    .card-title-link {
        font-size: 0.96rem;
        font-weight: 700;
        line-height: 1.34;
        color: var(--guest-text-headings, #ffffff);
        text-decoration: none;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        transition: color 0.2s ease;
    }

    .capstone-card:hover .card-title-link {
        color: var(--guest-accent, #00f5d4);
    }

    .card-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        font-size: 0.74rem;
        color: var(--guest-text-muted, #94a3b8);
    }

    .card-author-name {
        color: var(--guest-text-main, #cbd5e1);
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 175px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .card-year-badge {
        font-family: 'JetBrains Mono', monospace;
        color: var(--guest-text-muted, #94a3b8);
        font-weight: 600;
        font-size: 0.74rem;
        flex-shrink: 0;
        background: var(--guest-bg-input, rgba(255, 255, 255, 0.04));
        padding: 2px 7px;
        border-radius: 6px;
        border: 1px solid var(--guest-border, rgba(255, 255, 255, 0.08));
    }

    .card-tags-row {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .card-tag {
        font-size: 0.68rem;
        font-weight: 600;
        padding: 2.5px 8px;
        border-radius: 9999px;
        white-space: nowrap;
    }

    .card-tag-cyan {
        border: 1px solid rgba(0, 245, 212, 0.45);
        color: var(--guest-accent, #00f5d4);
        background: rgba(0, 245, 212, 0.07);
    }

    .card-tag-ruby {
        border: 1px solid rgba(239, 68, 68, 0.45);
        color: #fca5a5;
        background: rgba(239, 68, 68, 0.1);
        font-weight: 700;
    }

    .card-abstract-text {
        font-size: 0.76rem;
        color: var(--guest-text-muted, #94a3b8);
        line-height: 1.45;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .card-footer-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 0.25rem;
        padding-top: 0.5rem;
        border-top: 1px solid var(--guest-border, rgba(255, 255, 255, 0.06));
    }

    .card-stats-info {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.72rem;
        color: var(--guest-text-muted, #94a3b8);
        font-family: 'JetBrains Mono', monospace;
    }

    .card-stat-item {
        display: flex;
        align-items: center;
        gap: 3px;
    }

    .btn-view-project {
        background: #10b981;
        color: #042f2e;
        border-radius: 9999px;
        font-weight: 800;
        font-size: 0.75rem;
        padding: 0.44rem 1.15rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        justify-content: center;
        transition: all 0.22s ease;
        border: none;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-view-project:hover {
        background: #34d399;
        transform: translateY(-1px);
        box-shadow: 0 4px 18px rgba(16, 185, 129, 0.45);
        color: #042f2e;
    }

    /* EMPTY STATE CARD */
    .empty-state-card {
        grid-column: 1 / -1;
        background: var(--guest-bg-surface, rgba(14, 22, 33, 0.7));
        border: 1.5px dashed var(--guest-border, rgba(255, 255, 255, 0.15));
        border-radius: 22px;
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-code {
        font-family: 'JetBrains Mono', monospace;
        color: var(--guest-accent, #00f5d4);
        font-size: 0.88rem;
        margin-bottom: 0.5rem;
    }

    .empty-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--guest-text-headings, #ffffff);
        margin-bottom: 0.4rem;
    }

    .empty-desc {
        color: var(--guest-text-muted, #94a3b8);
        font-size: 0.9rem;
        max-width: 440px;
        margin: 0 auto 1.5rem;
    }

    /* FLOATING BOTTOM PAGINATION BAR */
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
        background: var(--guest-bg-sidebar, rgba(10, 18, 28, 0.94));
        border: 1.5px solid var(--guest-accent, rgba(0, 245, 212, 0.35));
        border-radius: 9999px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.75), 0 0 18px var(--guest-accent-glow, rgba(0, 245, 212, 0.2));
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
        color: var(--guest-text-main, #cbd5e1);
        background: var(--guest-bg-input, rgba(255, 255, 255, 0.05));
        border: 1px solid var(--guest-border, rgba(255, 255, 255, 0.12));
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .pag-btn:hover {
        background: var(--guest-accent-glow, rgba(0, 245, 212, 0.18));
        color: var(--guest-accent, #00f5d4);
        border-color: var(--guest-accent, rgba(0, 245, 212, 0.45));
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

<div class="student-browse-container">
    <div class="browse-ambient-glow"></div>

    {{-- ================= MODERN HERO BANNER ================= --}}
    <div class="hero-banner-card">
        <div class="hero-tech-badge">
            <span class="hero-pulsing-dot"></span>
            <span>Academic Discovery Engine</span>
        </div>

        <h1 class="hero-title">
            Explore Institutional <span class="hero-title-highlight">Capstone Intelligence</span>
        </h1>

        <p class="hero-subtitle">
            Search verified research studies, student theses, and technological systems with AI-driven Semantic Discovery and multi-category filtering.
        </p>

        <div class="hero-stats-row">
            <span class="hero-stat-pill">
                <strong>{{ \App\Models\Research::where('status', 'approved')->count() }}+</strong> Published Studies
            </span>
            <span class="hero-stat-pill">
                <strong>{{ $categories->count() }}</strong> Research Categories
            </span>
            <span class="hero-stat-pill">
                <strong>AI Vector</strong> Semantic Matching
            </span>
        </div>

        {{-- Futuristic Capsule Search Wrap --}}
        <form action="{{ route('repository.index') }}" method="GET" class="browse-search-wrap">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            @if(!empty($selectedCategories))
                @foreach($selectedCategories as $sc)
                    <input type="hidden" name="categories[]" value="{{ $sc }}">
                @endforeach
            @endif

            <div class="browse-search-icon-wrap">
                <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <input 
                type="text" 
                name="query" 
                id="mainSearchQueryInput"
                value="{{ $query }}" 
                placeholder="Search research manuscripts by topic, concept, author name, or keywords…"
                class="browse-search-input" 
                autocomplete="off"
            >

            @if($query)
                <a href="{{ route('repository.index', array_filter(['categories' => $selectedCategories])) }}" class="text-slate-400 hover:text-white px-2 text-sm font-bold" title="Clear query">
                    ✕
                </a>
            @endif

            <button type="submit" class="browse-search-btn">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span>Search Catalog</span>
            </button>
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

    {{-- ================= HEADER ROW: TITLE, COUNT, FILTER DROPDOWN, & SORT ================= --}}
    <div class="projects-header-row">
        <div class="projects-heading-group">
            <h2 class="projects-title">Browse Capstone Projects</h2>
            <p class="projects-subtitle">
                @if($query)
                    Found <span class="projects-count-highlight">{{ $totalCount }}</span> result(s) for "{{ $query }}"
                    @if(isset($searchMode) && $searchMode === 'semantic')
                        <span class="inline-block ml-2 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-purple-500/20 text-purple-300 border border-purple-500/30">Semantic Search</span>
                    @endif
                @else
                    Browsing <span class="projects-count-highlight">{{ number_format($totalCount) }}</span> projects found
                @endif
            </p>
        </div>

        <!-- Controls Wrap: Filter Projects Dropdown & Sort Select -->
        <div class="projects-controls-wrap">
            <!-- FILTER PROJECTS DROPDOWN -->
            <div class="filter-dropdown-wrapper" id="filterDropdownWrapper">
                <button type="button" class="filter-trigger-btn {{ $activeFiltersCount > 0 ? 'has-active' : '' }}" id="filterDropdownToggle" onclick="toggleFilterDropdown()">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
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
                    <span>Filter Projects</span>
                    @if($activeFiltersCount > 0)
                        <span class="filter-badge-count">{{ $activeFiltersCount }}</span>
                    @endif
                    <svg class="filter-trigger-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>

                <!-- DROPDOWN PANEL -->
                <div class="filter-dropdown-panel" id="filterDropdownPanel">
                    <form action="{{ route('repository.index') }}" method="GET" id="browseFilterForm">
                        @if($query)
                            <input type="hidden" name="query" value="{{ $query }}">
                        @endif
                        <input type="hidden" name="sort" id="filterSortHidden" value="{{ $selectedSort }}">

                        <!-- HEADER -->
                        <div class="filter-panel-header">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="filter-panel-title">FILTER PROJECTS</span>
                                    @if($activeFiltersCount > 0)
                                        <span class="filter-badge-count">{{ $activeFiltersCount }} active</span>
                                    @endif
                                </div>
                                <button type="button" class="filter-close-btn" onclick="closeFilterDropdown()" title="Close">✕</button>
                            </div>

                            <!-- Keyword input inside dropdown -->
                            <input 
                                type="text" 
                                name="keyword" 
                                id="filterKeywordInput" 
                                class="filter-search-input" 
                                placeholder="Filter by author or keyword..." 
                                autocomplete="off"
                                value="{{ $keywordFilter }}"
                            >
                        </div>

                        <!-- SCROLLABLE SECTIONS -->
                        <div class="filter-dropdown-scrollable">
                            <!-- SECTION 1: CATEGORY -->
                            <div class="filter-section" id="sectionCategory">
                                <div class="filter-section-title" onclick="toggleFilterSection('sectionCategory')">
                                    <span>CATEGORY ({{ $categories->count() }})</span>
                                    <svg class="filter-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="18 15 12 9 6 15"></polyline>
                                    </svg>
                                </div>
                                <div class="filter-items">
                                    @foreach($categories as $cat)
                                        <label class="filter-item-label" title="{{ $cat->name }}">
                                            <input 
                                                type="checkbox" 
                                                name="categories[]" 
                                                value="{{ $cat->id }}" 
                                                class="custom-checkbox filter-category-cb"
                                                {{ in_array($cat->id, $selectedCategories) ? 'checked' : '' }}
                                            >
                                            <span class="truncate">{{ $cat->name }}</span>
                                            <span class="filter-item-count">({{ $cat->classified_researches_count }})</span>
                                        </label>
                                    @endforeach
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
                                    @php
                                        $authorsToShow = $authorsList->take(6);
                                        $hiddenAuthors = $authorsList->skip(6);
                                        $hasHiddenSelected = !empty($selectedAuthors) && $hiddenAuthors->intersect($selectedAuthors)->isNotEmpty();
                                    @endphp

                                    <div class="filter-grid-2col">
                                        @foreach($authorsToShow as $author)
                                            <label class="filter-item-label" title="{{ $author }}">
                                                <input 
                                                    type="checkbox" 
                                                    name="authors[]" 
                                                    value="{{ $author }}" 
                                                    class="custom-checkbox filter-author-cb"
                                                    {{ in_array($author, $selectedAuthors) ? 'checked' : '' }}
                                                >
                                                <span class="truncate">{{ $author }}</span>
                                            </label>
                                        @endforeach
                                    </div>

                                    @if($authorsList->count() > 6)
                                        <div class="filter-grid-2col" id="moreAuthorsList" style="display: {{ $hasHiddenSelected ? 'grid' : 'none' }}; margin-top: 0.4rem;">
                                            @foreach($hiddenAuthors as $author)
                                                <label class="filter-item-label" title="{{ $author }}">
                                                    <input 
                                                        type="checkbox" 
                                                        name="authors[]" 
                                                        value="{{ $author }}" 
                                                        class="custom-checkbox filter-author-cb"
                                                        {{ in_array($author, $selectedAuthors) ? 'checked' : '' }}
                                                    >
                                                    <span class="truncate">{{ $author }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <span class="filter-show-more" id="showMoreAuthorsBtn" onclick="toggleMoreAuthors()">
                                            {{ $hasHiddenSelected ? '- Show Less' : '+ Show More (' . ($authorsList->count() - 6) . ' more)' }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- SECTION 3: YEAR -->
                            <div class="filter-section" id="sectionYear">
                                <div class="filter-section-title" onclick="toggleFilterSection('sectionYear')">
                                    <span>YEAR</span>
                                    <svg class="filter-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="18 15 12 9 6 15"></polyline>
                                    </svg>
                                </div>
                                <div class="filter-items filter-grid-2col">
                                    @foreach(['2026', '2025', '2024', '2023', '2022', 'Older'] as $yr)
                                        <label class="filter-item-label">
                                            <input 
                                                type="checkbox" 
                                                name="years[]" 
                                                value="{{ strtolower($yr) }}" 
                                                class="custom-checkbox filter-year-cb"
                                                {{ in_array(strtolower($yr), array_map('strtolower', $selectedYears)) ? 'checked' : '' }}
                                            >
                                            <span>{{ $yr }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- ACTIONS FOOTER -->
                        <div class="filter-panel-footer">
                            @if($activeFiltersCount > 0)
                                <a href="{{ route('repository.index', array_filter(['query' => $query])) }}" class="filter-reset-btn">
                                    ✕ Reset All
                                </a>
                            @else
                                <span></span>
                            @endif
                            <button type="submit" class="btn-apply-filters">
                                <span>Apply Filters</span>
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- SORT BY DROPDOWN PILL -->
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
    </div>

    {{-- ================= ACTIVE FILTER CHIPS BAR ================= --}}
    @if($activeFiltersCount > 0 || $query)
        <div class="active-chips-bar">
            <span class="active-chips-label">Active Filters:</span>

            @if($query)
                <a href="{{ route('repository.index', request()->except('query')) }}" class="active-chip" title="Remove query">
                    <span>Query: "{{ $query }}"</span>
                    <span class="active-chip-x">✕</span>
                </a>
            @endif

            @foreach($categories->whereIn('id', $selectedCategories) as $c)
                <a href="{{ route('repository.index', array_merge(request()->except('categories'), ['categories' => array_diff($selectedCategories, [$c->id])])) }}" class="active-chip" title="Remove {{ $c->name }}">
                    <span>{{ $c->name }}</span>
                    <span class="active-chip-x">✕</span>
                </a>
            @endforeach

            @foreach($selectedAuthors as $a)
                <a href="{{ route('repository.index', array_merge(request()->except('authors'), ['authors' => array_diff($selectedAuthors, [$a])])) }}" class="active-chip" title="Remove author {{ $a }}">
                    <span>{{ $a }}</span>
                    <span class="active-chip-x">✕</span>
                </a>
            @endforeach

            @foreach($selectedYears as $y)
                <a href="{{ route('repository.index', array_merge(request()->except('years'), ['years' => array_diff($selectedYears, [$y])])) }}" class="active-chip" title="Remove year {{ $y }}">
                    <span>Year: {{ ucfirst($y) }}</span>
                    <span class="active-chip-x">✕</span>
                </a>
            @endforeach

            @if($keywordFilter !== '')
                <a href="{{ route('repository.index', request()->except(['keyword', 'search', 'q'])) }}" class="active-chip" title="Remove keyword filter">
                    <span>Keyword: "{{ $keywordFilter }}"</span>
                    <span class="active-chip-x">✕</span>
                </a>
            @endif

            <a href="{{ route('repository.index') }}" class="clear-all-chip">
                Clear All
            </a>
        </div>
    @endif

    {{-- ================= CAPSTONE CARDS GRID ================= --}}
    <div class="capstone-cards-grid" id="capstoneCardsGrid">
        @if(count($items) === 0)
            <div class="empty-state-card">
                <div class="empty-code">// STATUS: 0_PROJECTS_FOUND</div>
                <h3 class="empty-title">
                    {{ $query ? 'No Capstone Projects Match Your Query' : 'No Capstone Projects Match Your Criteria' }}
                </h3>
                <p class="empty-desc">
                    {{ $query ? 'Try searching with different concepts or clear your filters to browse more projects.' : 'Try adjusting your category, author, or year filters to browse more archived research works.' }}
                </p>
                <a href="{{ route('repository.index') }}" class="btn-view-project" style="display: inline-flex;">
                    Browse All Projects
                </a>
            </div>
        @else
            <!-- Client-Side Search Empty State -->
            <div class="empty-state-card" id="clientSearchNoResults" style="display: none; grid-column: 1 / -1; width: 100%;">
                <div class="empty-code">// STATUS: 0_SEARCH_RESULTS</div>
                <h3 class="empty-title">No Capstone Projects Match Your Search</h3>
                <p class="empty-desc" id="clientSearchNoResultsDesc">
                    No projects match the keyword.
                </p>
                <button type="button" class="btn-view-project" onclick="clearFilterSearch()" style="display: inline-flex; margin-top: 0.5rem; cursor: pointer;">
                    Clear Search Filter
                </button>
            </div>

            @foreach($items as $index => $item)
                @php
                    $research = is_array($item) ? $item['research'] : $item;
                    $similarity = is_array($item) && isset($item['similarity']) ? $item['similarity'] : null;
                @endphp
                <div class="capstone-card project-card-item" 
                     data-title="{{ strtolower($research->title) }}" 
                     data-authors="{{ strtolower($research->authors ?? '') }}"
                     data-abstract="{{ strtolower($research->abstract ?? '') }}"
                     data-category="{{ strtolower($research->category->name ?? '') }}">
                    
                    <div class="card-top-content">
                        <!-- Top Meta Bar: Category Tag, Semantic Match & Year -->
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <div class="card-tags-row">
                                <span class="card-tag card-tag-cyan">
                                    {{ $research->category->name ?? 'Uncategorized' }}
                                </span>
                                @if($similarity !== null)
                                    <span class="card-tag card-tag-ruby" title="Semantic Match Score">
                                        🎯 {{ $similarity }}% match
                                    </span>
                                @endif
                            </div>
                            <span class="card-year-badge">
                                {{ $research->year ?: ($research->approved_at ? $research->approved_at->format('Y') : ($research->created_at ? $research->created_at->format('Y') : '2026')) }}
                            </span>
                        </div>

                        <!-- Title Link -->
                        <a href="{{ route('repository.show', $research->id) }}" class="card-title-link">
                            {{ $research->title }}
                        </a>

                        <!-- Author Line -->
                        <div class="card-meta-row">
                            <span class="card-author-name" title="{{ $research->authors ?? 'Student Researcher' }}">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="opacity-70">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>{{ $research->authors ?? 'SCS Researchers' }}</span>
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
                            <span>View Project</span>
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- ================= CUSTOM FLOATING PAGINATION BAR ================= --}}
    @if(!$query && $researches instanceof \Illuminate\Pagination\LengthAwarePaginator && $researches->hasPages())
        <div class="custom-pagination-bar">
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
                moreList.style.display = 'grid';
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
