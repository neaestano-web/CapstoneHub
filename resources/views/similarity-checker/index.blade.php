@extends(auth()->check() ? 'layouts.app' : 'layouts.public')
@section('title', 'Similarity Checker — JHCSC Capstone Portal')

@section('content')
@php $isAuth = auth()->check(); @endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    /* Scoped Container Styles */
    .sim-page-shell {
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        max-width: 960px;
        margin: 0 auto;
        padding: {{ $isAuth ? '1.5rem 1rem' : '2.5rem 1.25rem' }};
    }

    /* THEME DEFAULTS & GUEST PRESET ADAPTATIONS */
    :root {
        --sim-card-bg: {{ $isAuth ? '#ffffff' : 'var(--guest-bg-surface, #ffffff)' }};
        --sim-card-border: {{ $isAuth ? '#e2e8f0' : 'var(--guest-border, #e2e8f0)' }};
        --sim-card-shadow: {{ $isAuth ? '0 10px 30px rgba(0, 0, 0, 0.04)' : 'var(--guest-card-shadow, 0 10px 30px rgba(0,0,0,0.05))' }};
        --sim-text-heading: {{ $isAuth ? '#0f172a' : 'var(--guest-text-headings, #0f172a)' }};
        --sim-text-body: {{ $isAuth ? '#334155' : 'var(--guest-text-main, #334155)' }};
        --sim-text-muted: {{ $isAuth ? '#64748b' : 'var(--guest-text-muted, #64748b)' }};
        --sim-accent: {{ $isAuth ? '#0c4233' : 'var(--guest-accent, #059669)' }};
        --sim-accent-text: {{ $isAuth ? '#ffffff' : 'var(--guest-accent-text, #ffffff)' }};
        --sim-accent-glow: {{ $isAuth ? 'rgba(12, 66, 51, 0.25)' : 'var(--guest-accent-glow, rgba(5, 150, 105, 0.25))' }};
        --sim-box-bg: {{ $isAuth ? '#f8fafc' : 'var(--guest-bg-input, #f8fafc)' }};
        --sim-box-border: {{ $isAuth ? '#cbd5e1' : 'var(--guest-border, #cbd5e1)' }};
        --sim-box-border-focus: {{ $isAuth ? '#0c4233' : 'var(--guest-accent, #059669)' }};
        --sim-chip-bg: {{ $isAuth ? '#f1f5f9' : 'rgba(0, 0, 0, 0.04)' }};
        --sim-chip-border: {{ $isAuth ? '#e2e8f0' : 'var(--guest-border, #e2e8f0)' }};
        --sim-chip-text: {{ $isAuth ? '#475569' : 'var(--guest-text-main, #475569)' }};
        --sim-banner-bg: {{ $isAuth ? 'rgba(12, 66, 51, 0.05)' : 'rgba(var(--guest-accent-rgb, 5, 150, 105), 0.07)' }};
        --sim-banner-border: {{ $isAuth ? 'rgba(12, 66, 51, 0.15)' : 'rgba(var(--guest-accent-rgb, 5, 150, 105), 0.2)' }};
    }

    html[data-guest-theme="oled"] {
        --sim-chip-bg: rgba(255, 255, 255, 0.06);
        --sim-banner-bg: rgba(0, 245, 212, 0.08);
        --sim-banner-border: rgba(0, 245, 212, 0.25);
    }

    /* ── 1. HERO SECTION ── */
    .sim-hero {
        margin-bottom: 2rem;
        text-align: left;
    }

    .sim-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 9999px;
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        background: var(--sim-banner-bg);
        border: 1px solid var(--sim-banner-border);
        color: var(--sim-accent);
        margin-bottom: 0.75rem;
    }

    .sim-hero-badge-dot {
        width: 7px;
        height: 7px;
        border-radius: 9999px;
        background: var(--sim-accent);
        box-shadow: 0 0 8px var(--sim-accent-glow);
    }

    .sim-hero-title {
        font-size: 2.1rem;
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -0.025em;
        color: var(--sim-text-heading);
        margin: 0 0 0.5rem 0;
    }

    .sim-hero-title span {
        color: var(--sim-accent);
    }

    .sim-hero-desc {
        font-size: 0.92rem;
        line-height: 1.6;
        color: var(--sim-text-muted);
        max-width: 680px;
        margin: 0;
    }

    /* ── 2. MAIN CARD SHELL ── */
    .sim-main-card {
        background: var(--sim-card-bg);
        border: 1px solid var(--sim-card-border);
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--sim-card-shadow);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
    }

    @media (max-width: 640px) {
        .sim-main-card {
            padding: 1.35rem;
            border-radius: 16px;
        }
    }

    /* Card Top Header */
    .sim-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding-bottom: 1.25rem;
        margin-bottom: 1.25rem;
        border-bottom: 1px solid var(--sim-card-border);
        flex-wrap: wrap;
    }

    .sim-card-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .sim-card-icon-box {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: var(--sim-banner-bg);
        border: 1px solid var(--sim-banner-border);
        color: var(--sim-accent);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .sim-card-icon-box svg {
        width: 19px;
        height: 19px;
    }

    .sim-card-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--sim-text-heading);
        margin: 0;
        line-height: 1.3;
    }

    .sim-card-sub {
        font-size: 0.78rem;
        color: var(--sim-text-muted);
        margin: 0;
        line-height: 1.3;
    }

    .sim-status-chip {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 5px 13px;
        border-radius: 9999px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.72rem;
        font-weight: 700;
        background: var(--sim-banner-bg);
        border: 1px solid var(--sim-banner-border);
        color: var(--sim-accent);
        white-space: nowrap;
    }

    .sim-pulse-dot {
        width: 7px;
        height: 7px;
        border-radius: 9999px;
        background: var(--sim-accent);
        animation: simPulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes simPulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.45; transform: scale(0.85); }
    }

    /* ── 3. SMART INFORMATION BANNER ── */
    .sim-smart-banner {
        background: var(--sim-banner-bg);
        border: 1px solid var(--sim-banner-border);
        border-radius: 14px;
        padding: 12px 16px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 1.5rem;
    }

    .sim-banner-icon {
        font-size: 1.15rem;
        line-height: 1;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .sim-banner-content {
        font-size: 0.82rem;
        line-height: 1.55;
        color: var(--sim-text-body);
    }

    .sim-banner-content strong {
        color: var(--sim-text-heading);
        font-weight: 700;
    }

    /* ── 4. MODERN INPUT CONSOLE ── */
    .sim-input-box {
        background: var(--sim-box-bg);
        border: 1.5px solid var(--sim-box-border);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .sim-input-box:focus-within {
        border-color: var(--sim-box-border-focus);
        box-shadow: 0 0 0 3px var(--sim-accent-glow), 0 4px 14px rgba(0, 0, 0, 0.04);
        background: {{ $isAuth ? '#ffffff' : 'var(--sim-card-bg)' }};
    }

    .sim-input-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 9px 14px;
        background: rgba(0, 0, 0, 0.02);
        border-bottom: 1px solid var(--sim-box-border);
        font-size: 0.74rem;
        flex-wrap: wrap;
        gap: 8px;
    }

    html[data-guest-theme="oled"] .sim-input-header,
    :root:not([data-guest-theme]) .sim-input-header {
        background: rgba(255, 255, 255, 0.03);
    }

    .sim-input-label-text {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 700;
        color: var(--sim-text-heading);
    }

    .sim-input-label-text svg {
        width: 15px;
        height: 15px;
        color: var(--sim-accent);
    }

    .sim-input-meta {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .sim-count-badge {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--sim-text-muted);
        background: var(--sim-chip-bg);
        border: 1px solid var(--sim-chip-border);
        padding: 2px 8px;
        border-radius: 9999px;
    }

    .sim-clear-button {
        background: var(--sim-chip-bg);
        border: 1px solid var(--sim-chip-border);
        color: var(--sim-text-muted);
        width: 24px;
        height: 24px;
        border-radius: 6px;
        display: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 0.7rem;
        line-height: 1;
        transition: all 0.15s ease;
    }

    .sim-clear-button:hover {
        color: #ef4444;
        border-color: #ef4444;
        background: rgba(239, 68, 68, 0.08);
    }

    .sim-main-textarea {
        width: 100%;
        min-height: 140px;
        padding: 14px 16px;
        background: transparent;
        border: none;
        outline: none;
        font-family: inherit;
        font-size: 0.94rem;
        font-weight: 500;
        line-height: 1.6;
        color: var(--sim-text-heading);
        resize: vertical;
        display: block;
    }

    .sim-main-textarea:focus {
        outline: none;
        box-shadow: none;
        border: none;
    }

    .sim-main-textarea::placeholder {
        color: var(--sim-text-muted);
        opacity: 0.7;
        font-weight: 400;
        line-height: 1.6;
    }

    /* Examples Sub-bar */
    .sim-examples-bar {
        padding: 10px 14px 12px 14px;
        border-top: 1px dashed var(--sim-box-border);
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        background: rgba(0, 0, 0, 0.015);
    }

    .sim-examples-caption {
        font-size: 0.74rem;
        font-weight: 700;
        color: var(--sim-text-muted);
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .sim-example-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 11px;
        border-radius: 9999px;
        font-size: 0.74rem;
        font-weight: 600;
        background: var(--sim-chip-bg);
        border: 1px solid var(--sim-chip-border);
        color: var(--sim-chip-text);
        cursor: pointer;
        transition: all 0.18s ease;
    }

    .sim-example-chip:hover {
        background: var(--sim-accent);
        color: var(--sim-accent-text) !important;
        border-color: var(--sim-accent);
        transform: translateY(-1px);
        box-shadow: 0 3px 10px var(--sim-accent-glow);
    }

    /* ── 5. ACTION BAR & SUBMIT BUTTON ── */
    .sim-action-row {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 1rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .sim-action-guide {
        font-size: 0.78rem;
        color: var(--sim-text-muted);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .sim-action-guide svg {
        width: 15px;
        height: 15px;
        color: var(--sim-accent);
        flex-shrink: 0;
    }

    .sim-submit-btn {
        background: var(--sim-accent) !important;
        color: var(--sim-accent-text) !important;
        padding: 12px 28px !important;
        height: 46px !important;
        border-radius: 9999px !important;
        font-weight: 700 !important;
        font-size: 0.9rem !important;
        border: none !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 9px !important;
        white-space: nowrap !important;
        text-decoration: none !important;
        box-shadow: 0 4px 18px var(--sim-accent-glow) !important;
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1) !important;
        margin-left: auto;
    }

    .sim-submit-btn:hover {
        filter: brightness(1.1) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 24px var(--sim-accent-glow) !important;
        color: var(--sim-accent-text) !important;
    }

    .sim-submit-btn:active {
        transform: translateY(0) !important;
    }

    .sim-submit-btn svg {
        width: 18px !important;
        height: 18px !important;
        min-width: 18px !important;
        min-height: 18px !important;
        max-width: 18px !important;
        max-height: 18px !important;
        flex-shrink: 0 !important;
        display: inline-block !important;
    }

    .sim-submit-btn span {
        white-space: nowrap !important;
        display: inline-block !important;
        line-height: 1 !important;
    }

    @media (max-width: 640px) {
        .sim-action-row {
            flex-direction: column;
            align-items: stretch !important;
        }
        .sim-submit-btn {
            width: 100% !important;
            margin-left: 0;
        }
    }

    /* ── 6. RESULTS SECTION ── */
    .sim-results-card {
        background: var(--sim-card-bg);
        border: 1px solid var(--sim-card-border);
        border-radius: 18px;
        padding: 1.35rem 1.6rem;
        transition: all 0.22s ease;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    }

    .sim-results-card:hover {
        border-color: var(--sim-accent);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
        transform: translateY(-2px);
    }
</style>

<div class="sim-page-shell">

    {{-- HERO HEADER --}}
    <div class="sim-hero">
        <div class="sim-hero-badge">
            <span class="sim-hero-badge-dot"></span>
            Academic Integrity Screening
        </div>
        <h1 class="sim-hero-title">
            Research Similarity <span>Checker</span>
        </h1>
        <p class="sim-hero-desc">
            Instantly cross-reference your proposed capstone title, context, or specific objectives against published manuscripts to guarantee originality and avoid duplication.
        </p>
    </div>

    {{-- MAIN VERIFICATION CARD --}}
    <div class="sim-main-card">
        {{-- Card Header --}}
        <div class="sim-card-header">
            <div class="sim-card-header-left">
                <div class="sim-card-icon-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
                <div>
                    <h2 class="sim-card-title">Automated Research Screening</h2>
                    <p class="sim-card-sub">Real-time cosine semantic vector matching</p>
                </div>
            </div>
            <div class="sim-status-chip">
                <span class="sim-pulse-dot"></span>
                <span>FastEmbed ONNX Ready</span>
            </div>
        </div>

        {{-- Smart Unified Input Notice --}}
        <div class="sim-smart-banner">
            <div class="sim-banner-icon">💡</div>
            <div class="sim-banner-content">
                <strong>Unified AI Input:</strong> You can enter your proposed <strong>Title</strong>, <strong>Project Context</strong>, <strong>Objectives</strong>, or full abstract together in the box below. The semantic engine automatically evaluates all dimensions simultaneously.
            </div>
        </div>

        @if (isset($errors) && $errors->any())
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; border-radius: 14px; padding: 12px 16px; margin-bottom: 1.25rem; font-size: 0.8rem; font-weight: 700;">
                @foreach ($errors->all() as $error)
                    <p style="margin: 2px 0;">⚠️ {{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- FORM --}}
        <form method="POST" action="{{ route('similarity-checker.check') }}">
            @csrf

            <div class="sim-input-box">
                <div class="sim-input-header">
                    <label for="query" class="sim-input-label-text">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                        </svg>
                        <span>Proposed Research Title, Context, or Objectives</span>
                    </label>
                    <div class="sim-input-meta">
                        <span id="queryCharCount" class="sim-count-badge">0 chars</span>
                        <span id="queryWordCount" class="sim-count-badge">0 words</span>
                        <button type="button" id="queryClearBtn" onclick="clearField('query')" class="sim-clear-button" title="Clear text">
                            ✕
                        </button>
                    </div>
                </div>

                <textarea
                    name="query"
                    id="query"
                    rows="4"
                    class="sim-main-textarea"
                    placeholder="Type or paste your proposed research title, background context, or specific objectives here...&#10;&#10;e.g. 'Web-Based Barangay Health Information System with SMS Notifications and Geospatial Mapping...'"
                >{{ old('query', $inputQuery ?? $inputTitle ?? $inputObjectives ?? '') }}</textarea>

                {{-- Examples Bar --}}
                <div class="sim-examples-bar">
                    <span class="sim-examples-caption">💡 Try sample:</span>
                    <button type="button" class="sim-example-chip" onclick="fillExample('Web-Based Capstone Repository and AI Classification System')">
                        💻 AI Repository
                    </button>
                    <button type="button" class="sim-example-chip" onclick="fillExample('IoT-Based Flood Early Warning and Monitoring System with SMS Notification')">
                        🌊 IoT Flood Warning
                    </button>
                    <button type="button" class="sim-example-chip" onclick="fillExample('The study focuses on automated inventory management and record keeping in the municipal health center.')">
                        🏥 Health Record Context
                    </button>
                    <button type="button" class="sim-example-chip" onclick="fillExample('1. To design and deploy a centralized web repository. 2. To analyze topic originality using semantic similarity vectors.')">
                        🎯 Specific Objectives
                    </button>
                </div>
            </div>

            {{-- Action Row --}}
            <div class="sim-action-row">
                <div class="sim-action-guide">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <span>Recommended: at least 5 words for accurate semantic vector matching</span>
                </div>
                <button type="submit" class="sim-submit-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <span>Run Similarity Check</span>
                </button>
            </div>
        </form>
    </div>

    {{-- RESULTS SECTION --}}
    @if(isset($results) && $results->count() > 0)
        <div style="margin-top: 2rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: 8px;">
                <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--sim-text-heading); display: flex; align-items: center; gap: 8px; margin: 0;">
                    <span>📊</span> Analysis Results
                </h3>
                <span style="background: var(--sim-banner-bg); color: var(--sim-accent); border: 1px solid var(--sim-banner-border); font-family: 'JetBrains Mono', monospace; font-size: 0.76rem; font-weight: 700; padding: 4px 12px; border-radius: 9999px;">
                    Found {{ $results->count() }} Similar {{ Str::plural('Study', $results->count()) }}
                </span>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                @foreach($results as $item)
                    @php
                        $score = isset($item['overall_similarity']) ? round($item['overall_similarity'], 1) : 0;
                        $isHigh = $score >= 75;
                        $isMed = $score >= 60 && $score < 75;
                    @endphp

                    <div class="sim-results-card">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                            <div style="flex: 1; min-width: 260px;">
                                <h4 style="font-size: 0.98rem; font-weight: 800; line-height: 1.4; margin: 0 0 6px 0;">
                                    <a href="{{ route('repository.show', $item['research']->id) }}" target="_blank" style="color: var(--sim-text-heading); text-decoration: none; transition: color 0.15s ease;" onmouseover="this.style.color='var(--sim-accent)'" onmouseout="this.style.color='var(--sim-text-heading)'">
                                        {{ $item['research']->title }}
                                    </a>
                                </h4>

                                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 8px;">
                                    <span style="display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 9999px; font-size: 0.72rem; font-weight: 800; {{ $isHigh ? 'background: rgba(239, 68, 68, 0.12); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3);' : ($isMed ? 'background: rgba(245, 158, 11, 0.12); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3);' : 'background: rgba(16, 185, 129, 0.12); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);') }}">
                                        {{ $isHigh ? '⚠️ High Potential Match' : ($isMed ? '⚡ Moderate Match' : '✓ Related Study Found') }}
                                        ({{ $score }}%)
                                    </span>

                                    @if(!empty($item['primary_match']))
                                        <span style="font-size: 0.72rem; font-weight: 600; padding: 3px 10px; border-radius: 9999px; background: var(--sim-chip-bg); color: var(--sim-accent); border: 1px solid var(--sim-chip-border);">
                                            🎯 {{ $item['primary_match'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <a href="{{ route('repository.show', $item['research']->id) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 15px; border-radius: 9999px; font-size: 0.76rem; font-weight: 700; background: var(--sim-banner-bg); color: var(--sim-accent); border: 1px solid var(--sim-banner-border); text-decoration: none; transition: all 0.2s ease; white-space: nowrap;" onmouseover="this.style.background='var(--sim-accent)'; this.style.color='var(--sim-accent-text)';" onmouseout="this.style.background='var(--sim-banner-bg)'; this.style.color='var(--sim-accent)';">
                                View Manuscript ➔
                            </a>
                        </div>

                        <div style="display: flex; align-items: center; gap: 12px; font-size: 0.76rem; color: var(--sim-text-muted); margin-top: 12px; padding-top: 10px; border-top: 1px solid var(--sim-card-border); flex-wrap: wrap;">
                            <span>👤 Authors: <strong style="color: var(--sim-text-heading); font-weight: 600;">{{ $item['research']->authors }}</strong></span>
                            <span>·</span>
                            <span>📅 Year: <strong style="color: var(--sim-text-heading); font-family: 'JetBrains Mono', monospace;">{{ $item['research']->year }}</strong></span>
                            @if($item['research']->category)
                                <span>·</span>
                                <span style="padding: 2px 8px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; background: var(--sim-chip-bg); color: var(--sim-accent); border: 1px solid var(--sim-chip-border);">
                                    📂 {{ $item['research']->category->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif(isset($results))
        <div style="margin-top: 2rem; background: var(--sim-card-bg); border: 1px solid var(--sim-card-border); border-radius: 20px; padding: 2.5rem; text-align: center; box-shadow: var(--sim-card-shadow);">
            <div style="width: 52px; height: 52px; border-radius: 9999px; background: var(--sim-banner-bg); border: 1px solid var(--sim-banner-border); color: var(--sim-accent); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 900; margin: 0 auto 1rem auto;">
                ✓
            </div>
            <h4 style="font-size: 1.1rem; font-weight: 800; color: var(--sim-text-heading); margin: 0 0 6px 0;">
                No Similar Studies Found (High Originality)
            </h4>
            <p style="font-size: 0.85rem; color: var(--sim-text-muted); max-width: 500px; margin: 0 auto; line-height: 1.6;">
                Great news! Based on automated semantic vector cross-referencing, your proposed research topic does not overlap with any published capstone manuscripts in the repository.
            </p>
        </div>
    @endif
</div>

<script>
    function clearField(fieldId) {
        const el = document.getElementById(fieldId);
        if (el) {
            el.value = '';
            el.focus();
            updateSimCounters();
        }
    }

    function fillExample(text) {
        const queryEl = document.getElementById('query');
        if (queryEl) {
            queryEl.value = text;
            queryEl.focus();
            updateSimCounters();
        }
    }

    function updateSimCounters() {
        const queryInput = document.getElementById('query');
        const queryClearBtn = document.getElementById('queryClearBtn');
        const queryCharCount = document.getElementById('queryCharCount');
        const queryWordCount = document.getElementById('queryWordCount');
        if (queryInput) {
            const val = queryInput.value;
            const len = val.length;
            const words = val.trim().length > 0 ? val.trim().split(/\s+/).filter(Boolean).length : 0;
            if (queryCharCount) queryCharCount.textContent = len + (len === 1 ? ' char' : ' chars');
            if (queryWordCount) queryWordCount.textContent = words + (words === 1 ? ' word' : ' words');
            if (queryClearBtn) queryClearBtn.style.display = len > 0 ? 'inline-flex' : 'none';
        }
    }

    const simQueryInput = document.getElementById('query');
    if (simQueryInput) {
        simQueryInput.addEventListener('input', updateSimCounters);
    }

    document.addEventListener('DOMContentLoaded', updateSimCounters);
    updateSimCounters();
</script>
@endsection