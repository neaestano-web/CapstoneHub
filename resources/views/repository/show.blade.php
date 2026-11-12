@extends(auth()->check() ? (auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.app') : 'layouts.public')
@section('title', $research->title . ' — JHCSC Capstone Repository')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    :root {
        --view-bg-surface: var(--guest-bg-surface, #0f172a);
        --view-bg-subtle: var(--guest-bg-input, #1e293b);
        --view-border: var(--guest-border, rgba(148, 163, 184, 0.16));
        --view-text-headings: var(--guest-text-headings, #f8fafc);
        --view-text-main: var(--guest-text-main, #cbd5e1);
        --view-text-muted: var(--guest-text-muted, #94a3b8);
        --view-accent: #10b981;
        --view-accent-soft: #34d399;
        --view-accent-subtle: rgba(16, 185, 129, 0.1);
        --view-accent-border: rgba(16, 185, 129, 0.28);
        --view-accent-glow: rgba(16, 185, 129, 0.2);
        --view-card-border-grad: linear-gradient(135deg, rgba(16, 185, 129, 0.28) 0%, rgba(56, 189, 248, 0.12) 50%, rgba(16, 185, 129, 0.22) 100%);
        --view-card-shadow: 0 12px 32px rgba(0, 0, 0, 0.35);
    }

    .public-detail-body {
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        color: var(--view-text-main);
        max-width: 1200px;
        margin: 0 auto;
        padding: 1.25rem 1.5rem 5rem;
    }

    /* ===== TOP ACTION BAR ===== */
    .detail-top-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.4rem;
        flex-wrap: wrap;
    }

    .back-btn-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 0.52rem 1.25rem;
        background: var(--view-bg-surface);
        color: var(--view-text-main);
        border: 1px solid var(--view-border);
        border-radius: 9999px;
        font-size: 0.82rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.22s ease;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
    }

    .back-btn-pill:hover {
        background: rgba(16, 185, 129, 0.1);
        color: var(--view-accent-soft);
        border-color: rgba(16, 185, 129, 0.4);
        transform: translateX(-3px);
    }

    .detail-actions-group {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .view-count-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.46rem 1.1rem;
        background: rgba(16, 185, 129, 0.08);
        border: 1px solid rgba(16, 185, 129, 0.25);
        color: var(--view-accent-soft);
        font-size: 0.78rem;
        font-weight: 700;
        border-radius: 9999px;
        font-family: 'JetBrains Mono', monospace;
    }

    .action-pill-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.46rem 1.1rem;
        background: var(--view-bg-surface);
        border: 1px solid var(--view-border);
        color: var(--view-text-headings);
        font-size: 0.78rem;
        font-weight: 700;
        border-radius: 9999px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .action-pill-btn:hover {
        border-color: rgba(16, 185, 129, 0.4);
        color: var(--view-accent-soft);
        transform: translateY(-1px);
    }

    /* ===== MAIN CYBER GLASSMORPHISM CARD ===== */
    .detail-card-cyber {
        background: linear-gradient(var(--view-bg-surface), var(--view-bg-surface)) padding-box,
                    var(--view-card-border-grad) border-box;
        border: 1.5px solid transparent;
        border-radius: 24px;
        box-shadow: var(--view-card-shadow);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        padding: 2.4rem;
        display: flex;
        flex-direction: column;
        gap: 1.8rem;
    }

    @media (max-width: 640px) {
        .detail-card-cyber {
            padding: 1.5rem;
            gap: 1.4rem;
            border-radius: 20px;
        }
    }

    /* Badge Pills Row */
    .badge-pills-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.34rem 0.95rem;
        border-radius: 9999px;
        font-size: 0.74rem;
        font-weight: 700;
        white-space: nowrap;
        text-decoration: none;
    }

    .badge-category {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: var(--view-accent-soft);
        transition: all 0.2s ease;
    }

    .badge-category:hover {
        filter: brightness(1.15);
        transform: translateY(-1px);
    }

    .badge-year {
        background: rgba(148, 163, 184, 0.08);
        border: 1px solid var(--view-border);
        color: var(--view-text-muted);
        font-family: 'JetBrains Mono', monospace;
    }

    /* Title */
    .detail-title {
        font-size: clamp(1.5rem, 3.2vw, 2.3rem);
        font-weight: 800;
        color: var(--view-text-headings);
        letter-spacing: -0.025em;
        line-height: 1.28;
        margin-top: 0.35rem;
    }

    /* Meta Cards 2-Col Grid: Authors & Adviser */
    .meta-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1rem;
    }

    .meta-card {
        background: var(--view-bg-subtle);
        border: 1px solid var(--view-border);
        border-radius: 18px;
        padding: 1.2rem 1.4rem;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        transition: border-color 0.2s ease;
    }

    .meta-card:hover {
        border-color: rgba(16, 185, 129, 0.35);
    }

    .meta-card-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.25);
        color: var(--view-accent-soft);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .meta-card-content {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
    }

    .meta-card-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--view-accent-soft);
    }

    .meta-card-value {
        font-size: 0.98rem;
        font-weight: 700;
        color: var(--view-text-headings);
        word-break: break-word;
        line-height: 1.4;
    }

    .meta-card-sub {
        font-size: 0.78rem;
        color: var(--view-text-muted);
    }

    [x-cloak] { display: none !important; }

    /* Single Row Interactive Mini-Cards: Abstract, Context, & Objectives */
    .research-mini-card {
        background: var(--view-bg-subtle);
        border: 1px solid var(--view-border);
        border-radius: 18px;
        padding: 1.25rem 1.35rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 0.85rem;
        cursor: pointer;
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        user-select: none;
    }

    .research-mini-card:hover {
        transform: translateY(-3px);
        border-color: rgba(16, 185, 129, 0.45);
        box-shadow: 0 10px 24px rgba(16, 185, 129, 0.12);
        background: linear-gradient(145deg, var(--view-bg-subtle) 0%, rgba(16, 185, 129, 0.05) 100%);
    }

    .mini-card-badge-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .mini-card-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 0.25rem 0.65rem;
        font-size: 0.68rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        border-radius: 8px;
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.25);
        color: var(--view-accent-soft);
    }

    .mini-card-arrow {
        font-size: 0.85rem;
        color: var(--view-accent-soft);
        opacity: 0.5;
        transition: all 0.2s ease;
    }

    .research-mini-card:hover .mini-card-arrow {
        opacity: 1;
        transform: translate(2px, -2px);
    }

    .mini-card-title {
        font-size: 0.82rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--view-text-headings);
        line-height: 1.35;
    }

    .mini-card-preview {
        font-size: 0.81rem;
        line-height: 1.6;
        color: var(--view-text-muted);
        font-weight: 400;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .mini-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 0.65rem;
        border-top: 1px solid var(--view-border);
        font-size: 0.74rem;
    }

    .mini-card-footer-prompt {
        font-size: 0.72rem;
        font-weight: 500;
        color: var(--view-text-muted);
    }

    .mini-card-footer-action {
        font-size: 0.74rem;
        font-weight: 800;
        color: var(--view-accent-soft);
        display: flex;
        align-items: center;
        gap: 3px;
        transition: transform 0.2s ease;
    }

    .research-mini-card:hover .mini-card-footer-action {
        transform: translateX(2px);
    }

    /* Centered Expand Modal */
    .expand-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(5, 10, 20, 0.82);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.25rem;
    }

    .expand-modal-container {
        background: var(--view-bg-surface);
        border: 1.5px solid var(--view-border);
        border-radius: 22px;
        width: 100%;
        max-width: 680px;
        max-height: 85vh;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.65);
        color: var(--view-text-main);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        margin: auto;
    }

    .expand-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.2rem 1.6rem;
        border-bottom: 1px solid var(--view-border);
        background: var(--view-bg-surface);
        flex-shrink: 0;
    }

    .expand-modal-body {
        padding: 1.5rem 1.6rem;
        overflow-y: auto;
        flex: 1 1 auto;
        min-height: 0;
        max-height: calc(85vh - 135px);
        overscroll-behavior: contain;
    }

    .expand-modal-footer {
        padding: 0.85rem 1.6rem;
        border-top: 1px solid var(--view-border);
        background: var(--view-bg-subtle);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-shrink: 0;
    }

    /* Section Panels: Keywords & Information */
    .detail-section-panel {
        background: var(--view-bg-subtle);
        border: 1px solid var(--view-border);
        border-radius: 18px;
        padding: 1.5rem 1.8rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .section-header-title {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--view-accent-soft);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-body-text {
        color: var(--view-text-main);
        font-size: 0.92rem;
        line-height: 1.78;
        font-weight: 400;
    }

    /* Keyword Tags */
    .keywords-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .keyword-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 0.34rem 0.95rem;
        border-radius: 9999px;
        font-size: 0.76rem;
        font-weight: 600;
        background: rgba(16, 185, 129, 0.08);
        border: 1px solid rgba(16, 185, 129, 0.25);
        color: var(--view-accent-soft);
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .keyword-pill:hover {
        background: #10b981;
        border-color: #10b981;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.25);
    }

    /* View Full Project Action Box (For Guest Users) */
    .view-full-project-box {
        background: linear-gradient(135deg, rgba(12, 66, 51, 0.04) 0%, rgba(16, 185, 129, 0.03) 100%), var(--view-bg-subtle);
        border: 1px solid var(--view-border);
        border-left: 4px solid var(--guest-accent, #0c4233);
        border-radius: 18px;
        padding: 1.35rem 1.7rem;
        display: flex;
        flex-direction: column;
        gap: 1.2rem;
        margin-top: 0.5rem;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        transition: all 0.2s ease;
    }

    @media (min-width: 768px) {
        .view-full-project-box {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
    }

    .view-full-project-info {
        display: flex;
        align-items: center;
        gap: 14px;
        flex: 1;
        min-width: 0;
    }

    .lock-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(12, 66, 51, 0.08);
        border: 1px solid rgba(12, 66, 51, 0.18);
        color: var(--guest-accent, #0c4233);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .view-full-project-heading {
        font-size: 1rem;
        font-weight: 800;
        color: var(--view-text-headings);
        letter-spacing: -0.01em;
        line-height: 1.35;
    }

    .view-full-project-desc {
        font-size: 0.82rem;
        color: var(--view-text-muted);
        line-height: 1.5;
        margin-top: 2px;
        max-width: 600px;
    }

    .guest-action-buttons {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        flex-shrink: 0;
    }

    .btn-view-full-project {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0.65rem 1.45rem;
        background: var(--guest-accent, #0c4233);
        color: #ffffff !important;
        border-radius: 10px;
        font-size: 0.84rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        white-space: nowrap;
        border: 1px solid transparent;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(12, 66, 51, 0.2);
    }

    .btn-view-full-project:hover {
        filter: brightness(1.15);
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(12, 66, 51, 0.28);
        color: #ffffff !important;
    }

    .btn-register-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 0.65rem 1.35rem;
        background: var(--view-bg-surface);
        border: 1.5px solid var(--view-border);
        color: var(--view-text-headings) !important;
        border-radius: 10px;
        font-size: 0.84rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        white-space: nowrap;
        cursor: pointer;
    }

    .btn-register-secondary:hover {
        background: var(--view-bg-surface);
        border-color: var(--guest-accent, #0c4233);
        color: var(--guest-accent, #0c4233) !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
    }

    /* Dark Mode Specific Polish for Guest Action Box */
    html[data-guest-theme="dark"] .view-full-project-box {
        border-color: var(--view-border);
        border-left-color: #10b981;
    }

    html[data-guest-theme="dark"] .lock-icon-wrap {
        background: rgba(16, 185, 129, 0.14);
        border-color: rgba(16, 185, 129, 0.3);
        color: #34d399;
    }

    html[data-guest-theme="dark"] .btn-view-full-project {
        background: #059669;
        box-shadow: 0 2px 10px rgba(16, 185, 129, 0.3);
    }

    html[data-guest-theme="dark"] .btn-register-secondary:hover {
        border-color: #34d399;
        color: #34d399 !important;
    }

    /* Mobile Responsive Polish for Guest Action Box */
    @media (max-width: 640px) {
        .view-full-project-box {
            padding: 1.15rem 1.15rem;
            border-radius: 16px;
            gap: 1rem;
        }

        .view-full-project-info {
            align-items: flex-start;
            gap: 12px;
        }

        .lock-icon-wrap {
            width: 38px;
            height: 38px;
            border-radius: 10px;
        }

        .lock-icon-wrap svg {
            width: 18px;
            height: 18px;
        }

        .view-full-project-heading {
            font-size: 0.95rem;
        }

        .view-full-project-desc {
            font-size: 0.78rem;
            line-height: 1.45;
        }

        .guest-action-buttons {
            width: 100%;
            flex-direction: column;
            gap: 8px;
        }

        .btn-view-full-project,
        .btn-register-secondary {
            width: 100%;
            justify-content: center;
            padding: 0.65rem 1rem;
            font-size: 0.82rem;
        }
    }

    /* ===== CITATION MODAL ===== */
    .modal-backdrop-custom {
        position: fixed;
        inset: 0;
        background: rgba(8, 14, 22, 0.82);
        backdrop-filter: blur(8px);
        z-index: 1000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1.25rem;
    }

    .modal-backdrop-custom.active {
        display: flex;
    }

    .citation-modal-card {
        background: var(--view-bg-surface);
        border: 1px solid var(--view-border);
        border-radius: 24px;
        width: 100%;
        max-width: 580px;
        padding: 2rem;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.6);
        color: var(--view-text-headings);
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .citation-modal-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
    }

    .citation-header-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .citation-icon {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: rgba(16, 185, 129, 0.12);
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: var(--view-accent-soft);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    .citation-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--view-text-headings);
    }

    .citation-subtitle {
        font-size: 0.78rem;
        color: var(--view-text-muted);
    }

    .citation-close-btn {
        background: none;
        border: none;
        color: var(--view-text-muted);
        font-size: 1.1rem;
        cursor: pointer;
        padding: 4px;
        transition: color 0.2s ease;
    }

    .citation-close-btn:hover {
        color: var(--view-text-headings);
    }

    .citation-tabs-row {
        display: flex;
        align-items: center;
        gap: 6px;
        border-bottom: 1px solid var(--view-border);
        padding-bottom: 8px;
    }

    .cite-tab-btn {
        background: transparent;
        border: none;
        padding: 0.4rem 1rem;
        border-radius: 9999px;
        color: var(--view-text-muted);
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .cite-tab-btn.active {
        background: rgba(16, 185, 129, 0.12);
        color: var(--view-accent-soft);
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .citation-text-box {
        background: var(--view-bg-subtle);
        border: 1px solid var(--view-border);
        border-radius: 14px;
        padding: 1.1rem 1.25rem;
        font-size: 0.85rem;
        line-height: 1.65;
        color: var(--view-text-main);
        user-select: all;
    }

    .btn-copy-cite {
        background: #10b981;
        color: #ffffff;
        border: none;
        border-radius: 9999px;
        padding: 0.65rem 1.4rem;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        align-self: flex-end;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.25);
    }

    .btn-copy-cite:hover {
        background: #059669;
        transform: translateY(-1px);
    }

    /* ===== FLOATING TOAST NOTIFICATION ===== */
    .detail-toast {
        position: fixed;
        bottom: 28px;
        right: 28px;
        background: var(--view-bg-surface);
        border: 1px solid rgba(16, 185, 129, 0.35);
        color: var(--view-text-headings);
        padding: 0.75rem 1.4rem;
        border-radius: 9999px;
        font-size: 0.82rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.5), 0 0 16px var(--view-accent-glow);
        z-index: 2000;
        transform: translateY(100px);
        opacity: 0;
        pointer-events: none;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .detail-toast.show {
        transform: translateY(0);
        opacity: 1;
        pointer-events: auto;
    }

    .toast-icon {
        color: var(--view-accent-soft);
        font-weight: 900;
    }
</style>

<div class="public-detail-body space-y-6">

    {{-- TOP ACTION BAR --}}
    <div class="detail-top-bar">
        <a href="{{ route('repository.index') }}" class="back-btn-pill">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Back</span>
        </a>

        <div class="detail-actions-group">
            <span class="view-count-pill" title="Total Views">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                <span>{{ number_format($research->views_count) }} Views</span>
            </span>

            <button type="button" class="action-pill-btn" onclick="openCitationModal()" title="Cite this research paper">
                <span>❝ Cite</span>
            </button>

            @guest
                <a href="{{ route('repository.full', $research->id) }}" class="btn-view-full-project" style="padding: 0.46rem 1.25rem; font-size: 0.78rem;">
                    View Full Project
                </a>
            @endguest
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-950/80 border border-emerald-500/40 text-emerald-300 rounded-2xl text-xs font-bold flex items-center justify-between shadow-lg">
            <span>✅ {{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-400 font-black cursor-pointer">✕</button>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-rose-950/80 border border-rose-500/40 text-rose-300 rounded-2xl text-xs font-bold flex items-center justify-between shadow-lg">
            <span>⚠️ {{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-rose-400 font-black cursor-pointer">✕</button>
        </div>
    @endif
    @if(session('status'))
        <div class="p-4 bg-sky-950/80 border border-sky-500/40 text-sky-300 rounded-2xl text-xs font-bold flex items-center justify-between shadow-lg">
            <span>ℹ️ {{ session('status') }}</span>
            <button onclick="this.parentElement.remove()" class="text-sky-400 font-black cursor-pointer">✕</button>
        </div>
    @endif
    @if(session('warning'))
        <div class="p-4 bg-amber-950/80 border border-amber-500/40 text-amber-300 rounded-2xl text-xs font-bold flex items-center justify-between shadow-lg">
            <span>⚠️ {{ session('warning') }}</span>
            <button onclick="this.parentElement.remove()" class="text-amber-400 font-black cursor-pointer">✕</button>
        </div>
    @endif

    {{-- MAIN MANUSCRIPT DETAIL CARD --}}
    <article class="detail-card-cyber">

        <!-- Top Header: Badges & Title -->
        <div class="space-y-3">
            <div class="badge-pills-row">
                @if($research->category)
                    <a href="{{ route('repository.index', ['categories' => [$research->category->id]]) }}" class="badge-pill badge-category">
                        📂 {{ $research->category->name }}
                    </a>
                @endif
                <span class="badge-pill badge-year">
                    🗓 Year {{ $research->year ?: ($research->approved_at ? $research->approved_at->format('Y') : ($research->created_at ? $research->created_at->format('Y') : '2026')) }}
                </span>
            </div>

            <h1 class="detail-title">
                {{ $research->title }}
            </h1>
        </div>

        <!-- Meta Cards Grid: Authors -->
        <div class="meta-cards-grid">
            <div class="meta-card">
                <div class="meta-card-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div class="meta-card-content">
                    <span class="meta-card-label">Researchers / Authors</span>
                    <span class="meta-card-value">{{ $research->authors ?? 'SCS Researchers' }}</span>
                    <span class="meta-card-sub">Student Researchers, School of Computing Studies</span>
                </div>
            </div>
        </div>

        {{-- SINGLE ROW INTERACTIVE CARDS: ABSTRACT, PROJECT CONTEXT, & STUDY OBJECTIVES --}}
        <div id="projectDetailsSection">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-stretch">
                
                {{-- 1. ABSTRACT CARD --}}
                <div onclick="openDetailModal('abstract')"
                     class="research-mini-card"
                     title="Click to view full Abstract">
                    <div class="space-y-2">
                        <div class="mini-card-badge-row">
                            <h3 class="mini-card-title m-0">
                                <span>📝</span> Abstract
                            </h3>
                            <span class="mini-card-arrow">↗</span>
                        </div>
                        <p class="mini-card-preview">
                            {{ $research->abstract ? Str::limit(trim(strip_tags($research->abstract)), 140) : 'No abstract provided for this research project.' }}
                        </p>
                    </div>
                    <div class="mini-card-footer">
                        <span class="mini-card-footer-prompt">Click to expand</span>
                        <span class="mini-card-footer-action">
                            Expand ↗
                        </span>
                    </div>
                </div>

                {{-- 2. PROJECT CONTEXT CARD --}}
                <div onclick="openDetailModal('context')"
                     class="research-mini-card"
                     title="Click to view full Project Context">
                    <div class="space-y-2">
                        <div class="mini-card-badge-row">
                            <h3 class="mini-card-title m-0">
                                <span>🏢</span> Project Context
                            </h3>
                            <span class="mini-card-arrow">↗</span>
                        </div>
                        <p class="mini-card-preview">
                            {{ $research->project_context ? Str::limit(trim(strip_tags($research->project_context)), 140) : 'No project context or setting provided for this research project.' }}
                        </p>
                    </div>
                    <div class="mini-card-footer">
                        <span class="mini-card-footer-prompt">Click to expand</span>
                        <span class="mini-card-footer-action">
                            Expand ↗
                        </span>
                    </div>
                </div>

                {{-- 3. STUDY OBJECTIVES CARD --}}
                <div onclick="openDetailModal('objectives')"
                     class="research-mini-card"
                     title="Click to view full Objective">
                    <div class="space-y-2">
                        <div class="mini-card-badge-row">
                            <h3 class="mini-card-title m-0">
                                <span>🎯</span> Objective
                            </h3>
                            <span class="mini-card-arrow">↗</span>
                        </div>
                        <p class="mini-card-preview">
                            {{ $research->objectives ? Str::limit(trim(strip_tags($research->objectives)), 140) : 'No specific study objectives provided for this research project.' }}
                        </p>
                    </div>
                    <div class="mini-card-footer">
                        <span class="mini-card-footer-prompt">Click to expand</span>
                        <span class="mini-card-footer-action">
                            Expand ↗
                        </span>
                    </div>
                </div>

            </div>

        </div>

        {{-- KEYWORDS --}}
        @if($research->keywords)
            <div class="detail-section-panel">
                <h3 class="section-header-title">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                        <line x1="7" y1="7" x2="7.01" y2="7"></line>
                    </svg>
                    Keywords
                </h3>
                <div class="keywords-row">
                    @foreach(array_map('trim', explode(',', $research->keywords)) as $kw)
                        @if(!empty($kw))
                            <a href="{{ route('search.index', ['q' => $kw]) }}" class="keyword-pill" title="Search capstones tagged #{{ $kw }}">
                                #{{ $kw }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- VIEW FULL PROJECT ACTION BOX (FOR GUEST USERS) --}}
        @guest
            <div class="view-full-project-box">
                <div class="view-full-project-info">
                    <div class="lock-icon-wrap">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="view-full-project-heading">Complete Research Manuscript Available</div>
                        <p class="view-full-project-desc">
                            This capstone project is available in the SCS Repository. Sign in with your student account to view full chapters, methodology, and attached documents.
                        </p>
                    </div>
                </div>

                <div class="guest-action-buttons">
                    <a href="{{ route('repository.full', $research->id) }}" class="btn-view-full-project">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                            <polyline points="10 17 15 12 10 7"></polyline>
                            <line x1="15" y1="12" x2="3" y2="12"></line>
                        </svg>
                        <span>Sign In to Read Full PDF</span>
                    </a>
                    <a href="{{ route('register') }}" class="btn-register-secondary">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="8.5" cy="7" r="4"></circle>
                            <line x1="20" y1="8" x2="20" y2="14"></line>
                            <line x1="23" y1="11" x2="17" y2="11"></line>
                        </svg>
                        <span>Create Account</span>
                    </a>
                </div>
            </div>
        @endguest

    </article>

    {{-- AUTHENTICATED USERS ONLY: MANUSCRIPT PDF READER CONTAINER --}}
    @auth
        @if($research->pdf_path || ($research->files && $research->files->count() > 0))
            @php
                $allFiles = $research->files ? $research->files->sortBy('sort_order')->values() : collect();
                $isMultiFile = $allFiles->count() > 1;
                $isAdmin = auth()->check() && auth()->user()->role === 'admin';
                $downloadRequest = (auth()->check() && !$isAdmin)
                    ? \App\Models\DownloadRequest::where('user_id', auth()->id())->where('research_id', $research->id)->latest()->first()
                    : null;
                $userDownloadApproved = $isAdmin || ($downloadRequest && $downloadRequest->status === 'approved');
                $userDownloadPending = !$isAdmin && $downloadRequest && $downloadRequest->status === 'pending';
            @endphp
            <div class="detail-card-cyber p-6 space-y-4">
                {{-- Multi-file Tab Bar (Admin only: student users view only the first file) --}}
                @if($isMultiFile && $isAdmin)
                    <div class="flex items-center justify-between flex-wrap gap-2 pb-2 border-b border-slate-700/60">
                        <div class="flex items-center gap-2 text-xs text-slate-300">
                            <span class="text-base">📚</span>
                            <span class="font-extrabold text-white">Attached Files ({{ $allFiles->count() }}):</span>
                            <span class="text-[11px] text-emerald-400 font-semibold bg-emerald-950/70 border border-emerald-500/30 px-2 py-0.5 rounded-full">
                                🥇 First file viewed first
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            @foreach($allFiles as $idx => $f)
                                <button type="button" 
                                        id="tab-file-{{ $f->id }}"
                                        onclick="switchViewerFile('{{ route('document.pdf', $research->id) }}?file_id={{ $f->id }}', 'tab-file-{{ $f->id }}', 'File #{{ $idx + 1 }}: {{ addslashes($f->original_name) }}')" 
                                        class="viewer-tab-btn px-3 py-1 text-xs font-bold rounded-lg transition border {{ $idx === 0 ? 'active border-emerald-400/40 bg-emerald-500/20 text-emerald-300' : 'border-slate-700 bg-slate-800/80 text-slate-300 hover:text-white hover:bg-slate-700' }}">
                                    {{ $idx === 0 ? '🥇 #1 ' : '#' . ($idx + 1) . ' ' }}{{ Str::limit($f->original_name, 22) }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="relative w-full rounded-2xl overflow-hidden border border-slate-700/80 shadow-inner bg-slate-900 select-none"
                     style="height: 780px;"
                     oncontextmenu="return false;">
                    
                    {{-- Protective Shield Bar --}}
                    <div class="absolute top-0 left-0 right-0 h-11 bg-slate-900/95 backdrop-blur-md z-20 flex items-center justify-between px-5 border-b border-slate-700/80 text-white text-xs font-semibold">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="truncate max-w-md font-bold" id="viewerTitleLabel">
                                {{ ($isAdmin && $isMultiFile) ? 'File #1: ' . $allFiles->first()->original_name : ($allFiles->isNotEmpty() ? $allFiles->first()->original_name : 'Manuscript Reader') }}
                            </span>
                            @if(auth()->check() && auth()->user()->role === 'student')
                                <span class="hidden sm:inline-block text-[10px] text-emerald-400 font-bold bg-emerald-950/70 border border-emerald-500/30 px-2 py-0.5 rounded-full">
                                    Page 1 Preview
                                </span>
                            @endif
                        </div>
                        <div class="text-[11px] text-slate-300 flex items-center gap-3">
                            @if($isAdmin)
                                <span class="hidden sm:inline-block">👑 Admin Authorized</span>
                                <a href="{{ route('repository.download', $research->id) }}" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-extrabold rounded-full transition shadow-xs inline-flex items-center gap-1.5">
                                    <span>📥</span> Download Full PDF {{ $isMultiFile ? '(' . $allFiles->count() . ' Files Combined)' : '' }}
                                </a>
                            @elseif($userDownloadApproved)
                                <span class="hidden sm:inline-block text-emerald-400 font-bold">✅ Download Approved</span>
                                <a href="{{ route('repository.download', $research->id) }}" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-extrabold rounded-full transition shadow-xs inline-flex items-center gap-1.5">
                                    <span>📥</span> Download Full PDF {{ $isMultiFile ? '(' . $allFiles->count() . ' Files Combined)' : '' }}
                                </a>
                            @elseif($userDownloadPending)
                                <span class="px-3.5 py-1.5 bg-amber-500/20 border border-amber-500/40 text-amber-300 rounded-full font-bold text-[11px] inline-flex items-center gap-1.5 shadow-xs">
                                    <span>⏳</span> Download Request Pending Approval
                                </span>
                            @else
                                <span class="hidden sm:inline-block text-slate-400">🔒 Direct Download Restricted</span>
                                <button type="button" onclick="openDownloadModal()" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-extrabold rounded-full transition shadow-xs cursor-pointer">
                                    Request Download
                                </button>
                            @endif
                        </div>
                    </div>

                    <iframe id="manuscriptIframe"
                            src="{{ route('document.pdf', $research->id) }}{{ ($isAdmin && $isMultiFile) ? '?file_id=' . $allFiles->first()->id . '&' : '?' }}embedded=1#page=1&toolbar=0&navpanes=0&scrollbar=1" 
                            class="w-full h-full border-none rounded-2xl bg-white pt-10" 
                            title="Research Document Viewer"
                            oncontextmenu="return false;">
                    </iframe>
                </div>
            </div>

            <script>
            function switchViewerFile(url, activeTabId, titleText) {
                const iframe = document.getElementById('manuscriptIframe');
                if (iframe) {
                    iframe.src = url + (url.includes('?') ? '&' : '?') + 'embedded=1#toolbar=0&navpanes=0&scrollbar=1';
                }
                const label = document.getElementById('viewerTitleLabel');
                if (label && titleText) {
                    label.textContent = titleText;
                }
                document.querySelectorAll('.viewer-tab-btn').forEach(btn => {
                    btn.classList.remove('active', 'border-emerald-400/40', 'bg-emerald-500/20', 'text-emerald-300');
                    btn.classList.add('border-slate-700', 'bg-slate-800/80', 'text-slate-300');
                });
                const activeTab = document.getElementById(activeTabId);
                if (activeTab) {
                    activeTab.classList.remove('border-slate-700', 'bg-slate-800/80', 'text-slate-300');
                    activeTab.classList.add('active', 'border-emerald-400/40', 'bg-emerald-500/20', 'text-emerald-300');
                }
            }
            </script>
        @endif
    @endauth

</div>

{{-- CENTERED EXPAND POPUP MODAL (Placed outside .detail-card-cyber so backdrop-filter does not trap position:fixed) --}}
<div class="expand-modal-backdrop"
     id="researchDetailModal"
     style="display: none;"
     onclick="if(event.target === this) closeDetailModal()">

    <div class="expand-modal-container"
         onclick="event.stopPropagation()">

        <!-- Modal Header -->
        <div class="expand-modal-header">
            <div class="flex items-center gap-3 min-w-0 pr-2">
                <span class="text-2xl shrink-0" id="modal-dyn-icon">📝</span>
                <div class="min-w-0">
                    <span class="mini-card-badge inline-block mb-1" id="modal-dyn-tag">
                        Research Detail
                    </span>
                    <h3 class="font-extrabold text-base sm:text-lg truncate" style="color: var(--view-text-headings);" id="modal-dyn-title">
                        Project Details
                    </h3>
                </div>
            </div>
            <button type="button"
                    onclick="closeDetailModal()"
                    class="p-2 rounded-full transition shrink-0 hover:bg-slate-700/30 cursor-pointer"
                    style="color: var(--view-text-muted);"
                    title="Close (Esc)">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Scrollable Body -->
        <div class="expand-modal-body">
            {{-- 1. Abstract Content --}}
            <div id="modal-pane-abstract" class="detail-modal-panel" style="display: none;">
                <div class="text-sm leading-relaxed whitespace-pre-line select-text" style="color: var(--view-text-main);">
                    {{ $research->abstract ?: 'No abstract available for this research.' }}
                </div>
            </div>

            {{-- 2. Project Context Content --}}
            <div id="modal-pane-context" class="detail-modal-panel" style="display: none;">
                <div class="text-sm leading-relaxed whitespace-pre-line select-text" style="color: var(--view-text-main);">
                    {{ $research->project_context ?: 'No project context provided for this research.' }}
                </div>
            </div>

            {{-- 3. Objectives Content --}}
            <div id="modal-pane-objectives" class="detail-modal-panel" style="display: none;">
                <div class="text-sm leading-relaxed whitespace-pre-line select-text" style="color: var(--view-text-main);">
                    {{ $research->objectives ?: 'No objectives provided for this research.' }}
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="expand-modal-footer">
            <button type="button"
                    onclick="closeDetailModal()"
                    class="px-5 py-2 rounded-full text-xs font-extrabold transition shadow-sm hover:brightness-110 cursor-pointer"
                    style="background: #10b981; color: #ffffff;">
                Close
            </button>
        </div>

    </div>
</div>

{{-- CITATION MODAL --}}
<div class="modal-backdrop-custom" id="citationModal" onclick="closeCitationModal(event)">
    <div class="citation-modal-card" onclick="event.stopPropagation()">
        <div class="citation-modal-header">
            <div class="citation-header-title">
                <span class="citation-icon">❝</span>
                <div>
                    <h3 class="citation-title">Cite This Research Paper</h3>
                    <p class="citation-subtitle">Standard scholarly bibliographic formats</p>
                </div>
            </div>
            <button type="button" class="citation-close-btn" onclick="closeCitationModal()" aria-label="Close Citation Modal">✕</button>
        </div>

        <!-- Citation Format Tab Buttons -->
        <div class="citation-tabs-row">
            <button type="button" class="cite-tab-btn active" onclick="switchCiteTab('apa', this)">APA 7th</button>
            <button type="button" class="cite-tab-btn" onclick="switchCiteTab('ieee', this)">IEEE</button>
            <button type="button" class="cite-tab-btn" onclick="switchCiteTab('mla', this)">MLA 9th</button>
        </div>

        @php
            $pubYear = $research->year ?: ($research->approved_at ? $research->approved_at->format('Y') : ($research->created_at ? $research->created_at->format('Y') : '2026'));
            $authorFormatted = $research->authors ?: 'School of Computing Studies Researchers';
        @endphp

        <!-- APA 7th Tab -->
        <div class="citation-tab-panel" id="cite-tab-apa">
            <div class="citation-text-box" id="citeTextApa">{{ $authorFormatted }} ({{ $pubYear }}). <em>{{ $research->title }}</em>. School of Computing Studies, J.H. Cerilles State College Capstone Repository.</div>
            <div class="flex justify-end pt-2">
                <button type="button" class="btn-copy-cite" onclick="copyCitation('citeTextApa', this)">
                    <span>📋 Copy APA Citation</span>
                </button>
            </div>
        </div>

        <!-- IEEE Tab -->
        <div class="citation-tab-panel" id="cite-tab-ieee" style="display: none;">
            <div class="citation-text-box" id="citeTextIeee">{{ $authorFormatted }}, "{{ $research->title }}," J.H. Cerilles State College Capstone Repository, {{ $pubYear }}.</div>
            <div class="flex justify-end pt-2">
                <button type="button" class="btn-copy-cite" onclick="copyCitation('citeTextIeee', this)">
                    <span>📋 Copy IEEE Citation</span>
                </button>
            </div>
        </div>

        <!-- MLA 9th Tab -->
        <div class="citation-tab-panel" id="cite-tab-mla" style="display: none;">
            <div class="citation-text-box" id="citeTextMla">{{ $authorFormatted }}. "{{ $research->title }}." <em>JHCSC Capstone Repository</em>, {{ $pubYear }}.</div>
            <div class="flex justify-end pt-2">
                <button type="button" class="btn-copy-cite" onclick="copyCitation('citeTextMla', this)">
                    <span>📋 Copy MLA Citation</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- REQUEST DOWNLOAD MODAL (ONLY FOR LOGGED IN STUDENTS) --}}
@auth
    @if(auth()->user()->role !== 'admin' && ($research->pdf_path || ($research->files && $research->files->count() > 0)))
    <div class="modal-backdrop-custom" id="downloadModal" onclick="closeDownloadModal(event)">
        <div class="bg-slate-900 rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-slate-700/80 text-center space-y-4 text-slate-100" onclick="event.stopPropagation()">
            <div class="w-14 h-14 bg-emerald-950/80 border border-emerald-500/40 text-emerald-400 rounded-full flex items-center justify-center font-bold text-2xl mx-auto shadow-sm">
                📥
            </div>
            <div>
                <h3 class="text-base font-extrabold text-white">Request PDF Download</h3>
                <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                    Submit a download request for <strong class="text-white">"{{ $research->title }}"</strong> for faculty approval.
                </p>
            </div>

            <form action="{{ route('repository.request-download', $research->id) }}" method="POST" class="space-y-4">
                @csrf
                <div class="text-left">
                    <label for="reason" class="block text-xs font-bold text-emerald-400 uppercase tracking-wider mb-1">
                        Purpose / Reason for Download *
                    </label>
                    <textarea name="reason" id="reason" rows="3" required
                              placeholder="e.g. Academic reference for capstone literature review..."
                              class="w-full bg-slate-950/80 border border-slate-700 rounded-xl p-3 text-xs font-medium text-slate-100 outline-none focus:border-emerald-500 transition"></textarea>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="button" onclick="closeDownloadModal()" class="w-1/2 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-full transition cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="w-1/2 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-full transition shadow-sm cursor-pointer">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openDownloadModal() {
        const modal = document.getElementById('downloadModal');
        if (modal) modal.classList.add('active');
    }
    function closeDownloadModal(e) {
        const modal = document.getElementById('downloadModal');
        if (modal && (!e || e.target === modal)) {
            modal.classList.remove('active');
        }
    }
    </script>
    @endif
@endauth

{{-- FLOATING TOAST NOTIFICATION --}}
<div id="projectDetailToast" class="detail-toast">
    <span class="toast-icon">✓</span>
    <span id="projectDetailToastMsg">Link copied to clipboard!</span>
</div>

<script>
    function openDetailModal(type) {
        const section = document.getElementById('projectDetailsSection');
        if (window.Alpine && section && section._x_dataStack && section._x_dataStack.length > 0) {
            section._x_dataStack[0].activeDetailModal = type;
        }
        const modal = document.getElementById('researchDetailModal');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            ['abstract', 'context', 'objectives'].forEach(function(t) {
                const el = document.getElementById('modal-pane-' + t);
                if (el) el.style.display = (t === type ? 'block' : 'none');
            });
            const titles = {
                abstract: 'Abstract',
                context: 'Project Context',
                objectives: 'Objective'
            };
            const icons = { abstract: '📝', context: '🏢', objectives: '🎯' };
            const tags = { abstract: 'Abstract', context: 'Project Context', objectives: 'Objective' };
            const titleEl = document.getElementById('modal-dyn-title');
            const iconEl = document.getElementById('modal-dyn-icon');
            const tagEl = document.getElementById('modal-dyn-tag');
            if (titleEl) titleEl.textContent = titles[type] || 'Project Details';
            if (iconEl) iconEl.textContent = icons[type] || '📝';
            if (tagEl) tagEl.textContent = tags[type] || 'Research Detail';
        }
    }

    function closeDetailModal() {
        const section = document.getElementById('projectDetailsSection');
        if (window.Alpine && section && section._x_dataStack && section._x_dataStack.length > 0) {
            section._x_dataStack[0].activeDetailModal = null;
        }
        const modal = document.getElementById('researchDetailModal');
        if (modal) {
            modal.style.display = 'none';
        }
        document.body.style.overflow = '';
    }

    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDetailModal();
        }
    });

    function openCitationModal() {
        const modal = document.getElementById('citationModal');
        if (modal) modal.classList.add('active');
    }

    function closeCitationModal(e) {
        const modal = document.getElementById('citationModal');
        if (modal && (!e || e.target === modal)) {
            modal.classList.remove('active');
        }
    }

    function switchCiteTab(format, btn) {
        document.querySelectorAll('.citation-tab-panel').forEach(p => p.style.display = 'none');
        document.querySelectorAll('.cite-tab-btn').forEach(b => b.classList.remove('active'));
        const panel = document.getElementById('cite-tab-' + format);
        if (panel) panel.style.display = 'block';
        if (btn) btn.classList.add('active');
    }

    function copyCitation(textId, btn) {
        const el = document.getElementById(textId);
        if (el && navigator.clipboard) {
            navigator.clipboard.writeText(el.innerText).then(() => {
                const orig = btn.innerHTML;
                btn.innerHTML = '<span>✅ Copied to Clipboard!</span>';
                setTimeout(() => {
                    btn.innerHTML = orig;
                }, 2000);
            });
        }
    }

    function showDetailToast(msg) {
        const toast = document.getElementById('projectDetailToast');
        const msgEl = document.getElementById('projectDetailToastMsg');
        if (toast) {
            if (msgEl) msgEl.textContent = msg;
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 2500);
        }
    }
</script>
@endsection
