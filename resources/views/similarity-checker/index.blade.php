@extends(auth()->check() ? 'layouts.app' : 'layouts.public')
@section('title', 'Similarity Checker — JHCSC Capstone Portal')

@section('content')
@php $isAuth = auth()->check(); @endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    .sim-wrapper {
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    }

    /* AUTHENTICATED STUDENT THEME (LIGHT CANVAS) */
    .sim-auth-card {
        background: #ffffff;
        border-radius: 24px;
        border: 1.5px solid #e2e8f0;
        padding: 32px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
    }
    .sim-auth-input {
        width: 100%;
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 14px;
        padding: 12px 18px;
        font-size: 14px;
        font-weight: 500;
        color: #2d2b56;
        outline: none;
        transition: all 0.25s ease;
    }
    .sim-auth-input:focus {
        background: #ffffff;
        border-color: #0c4233;
        box-shadow: 0 0 0 4px rgba(12, 66, 51, 0.12);
    }
    .sim-auth-btn {
        background: #0c4233;
        color: #ffffff;
        padding: 12px 30px;
        height: 46px;
        border-radius: 50px;
        font-weight: 800;
        font-size: 14px;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        white-space: nowrap;
        transition: all 0.25s ease;
        box-shadow: 0 4px 16px rgba(12, 66, 51, 0.25);
    }
    .sim-auth-btn:hover {
        background: #082d23;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(12, 66, 51, 0.35);
    }
    .sim-auth-btn svg {
        width: 17px;
        height: 17px;
        flex-shrink: 0;
    }

    /* PUBLIC GUEST THEME */
    .sim-public-card {
        background: var(--guest-bg-surface, rgba(14, 23, 34, 0.94));
        border: 1px solid var(--guest-border, rgba(255, 255, 255, 0.12));
        border-radius: 24px;
        padding: 2.2rem;
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        box-shadow: var(--guest-card-shadow, 0 18px 46px rgba(0, 0, 0, 0.35));
    }

    @media (max-width: 640px) {
        .sim-public-card {
            padding: 1.4rem;
        }
    }

    /* THEME-ADAPTIVE VARIABLES FOR SIMILARITY INPUTS */
    :root {
        --sim-field-bg: rgba(7, 13, 22, 0.85);
        --sim-field-border: rgba(255, 255, 255, 0.14);
        --sim-field-border-hover: var(--guest-accent, #00f5d4);
        --sim-field-bg-hover: rgba(9, 17, 28, 0.92);
        --sim-field-bg-focus: rgba(12, 22, 36, 0.96);
        --sim-field-text: #ffffff;
        --sim-field-placeholder: rgba(148, 163, 184, 0.72);
        --sim-field-subbar: rgba(255, 255, 255, 0.04);
        --sim-field-divider: rgba(255, 255, 255, 0.08);
        --sim-chip-bg: rgba(255, 255, 255, 0.06);
        --sim-chip-border: rgba(255, 255, 255, 0.12);
        --sim-chip-text: #94a3b8;
    }

    html[data-guest-theme="light"] {
        --sim-field-bg: #f8fafc;
        --sim-field-border: #cbd5e1;
        --sim-field-border-hover: var(--guest-accent, #059669);
        --sim-field-bg-hover: #ffffff;
        --sim-field-bg-focus: #ffffff;
        --sim-field-text: #0f172a;
        --sim-field-placeholder: #64748b;
        --sim-field-subbar: #f1f5f9;
        --sim-field-divider: #e2e8f0;
        --sim-chip-bg: #f1f5f9;
        --sim-chip-border: #e2e8f0;
        --sim-chip-text: #475569;
    }

    html[data-guest-theme="sepia"] {
        --sim-field-bg: #fbf7ee;
        --sim-field-border: #ded2c0;
        --sim-field-border-hover: var(--guest-accent, #b45309);
        --sim-field-bg-hover: #fdfbf7;
        --sim-field-bg-focus: #ffffff;
        --sim-field-text: #262019;
        --sim-field-placeholder: #7c7265;
        --sim-field-subbar: #f2ebd9;
        --sim-field-divider: #ded2c0;
        --sim-chip-bg: #ede4d1;
        --sim-chip-border: #ded2c0;
        --sim-chip-text: #594f42;
    }

    html[data-guest-theme="oled"] {
        --sim-field-bg: #0a0a0a;
        --sim-field-border: #27272a;
        --sim-field-border-hover: var(--guest-accent, #00f5d4);
        --sim-field-bg-hover: #121212;
        --sim-field-bg-focus: #161616;
        --sim-field-text: #ffffff;
        --sim-field-placeholder: #71717a;
        --sim-field-subbar: #141414;
        --sim-field-divider: #27272a;
        --sim-chip-bg: #18181b;
        --sim-chip-border: #27272a;
        --sim-chip-text: #a1a1aa;
    }

    /* High-Clarity Field Groups */
    .sim-field-group {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
    }

    .sim-field-header {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .sim-field-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.88rem;
        font-weight: 700;
        color: var(--guest-text-headings, #ffffff);
        flex-wrap: wrap;
    }

    .sim-field-badge {
        width: 22px;
        height: 22px;
        border-radius: 6px;
        background: var(--guest-accent, #059669);
        color: var(--guest-accent-text, #ffffff) !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.72rem;
        font-weight: 800;
        flex-shrink: 0;
        box-shadow: 0 2px 6px var(--guest-accent-glow, rgba(5, 150, 105, 0.25));
    }

    .sim-field-tag-unified {
        display: inline-flex;
        align-items: center;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: var(--guest-accent, #059669);
        background: var(--sim-field-subbar);
        border: 1px solid var(--guest-border, rgba(255, 255, 255, 0.12));
        padding: 2px 8px;
        border-radius: 6px;
    }

    .sim-field-hint {
        font-size: 0.78rem;
        color: var(--guest-text-muted, #94a3b8);
        font-weight: 500;
        line-height: 1.5;
    }
    .sim-field-hint strong {
        color: var(--guest-text-headings, #ffffff);
        font-weight: 600;
    }

    .sim-counter-pill {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--guest-text-muted, #94a3b8);
        background: var(--sim-field-subbar);
        padding: 2px 8px;
        border-radius: 9999px;
        border: 1px solid var(--sim-field-divider);
    }

    .sim-clear-icon-btn {
        background: var(--sim-field-subbar);
        border: 1px solid var(--sim-field-divider);
        color: var(--guest-text-muted, #94a3b8);
        width: 26px;
        height: 26px;
        border-radius: 6px;
        display: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .sim-clear-icon-btn:hover {
        color: var(--guest-accent, #00f5d4);
        border-color: var(--guest-accent, #00f5d4);
        transform: scale(1.05);
    }

    /* QUICK SUGGESTIONS */
    .sim-quick-suggestions {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        padding-top: 4px;
    }

    .sim-sug-label {
        font-size: 0.74rem;
        font-weight: 600;
        color: var(--guest-text-muted, #94a3b8);
    }

    .sim-sug-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--sim-chip-bg);
        border: 1px solid var(--sim-chip-border);
        color: var(--sim-chip-text);
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.74rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.18s ease;
    }

    .sim-sug-chip:hover {
        background: var(--guest-accent, #059669);
        color: var(--guest-accent-text, #ffffff) !important;
        border-color: var(--guest-accent, #059669);
        transform: translateY(-1px);
        box-shadow: 0 2px 8px var(--guest-accent-glow, rgba(5, 150, 105, 0.25));
    }

    /* HIGH-DEFINITION TEXTAREA SHELL */
    .sim-textarea-shell {
        display: flex;
        flex-direction: column;
        background: var(--sim-field-bg);
        border: 1.5px solid var(--sim-field-border);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }

    .sim-textarea-shell:hover {
        border-color: var(--sim-field-border-hover);
        background: var(--sim-field-bg-hover);
    }

    .sim-textarea-shell:focus-within {
        border-color: var(--guest-accent, #059669);
        background: var(--sim-field-bg-focus);
        box-shadow: 0 0 0 3px var(--guest-accent-glow, rgba(5, 150, 105, 0.2)), 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .sim-textarea-header-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 9px 14px;
        background: var(--sim-field-subbar);
        border-bottom: 1px solid var(--sim-field-divider);
        font-size: 0.75rem;
    }

    .sim-textarea-header-title {
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--guest-text-muted, #94a3b8);
        font-weight: 600;
        letter-spacing: 0.01em;
    }

    .sim-public-textarea-field {
        width: 100%;
        min-height: 135px;
        background: transparent;
        border: none;
        outline: none;
        padding: 14px 16px;
        font-family: inherit;
        font-size: 0.92rem;
        font-weight: 500;
        line-height: 1.6;
        color: var(--sim-field-text);
        resize: vertical;
    }

    .sim-public-textarea-field:focus {
        outline: none;
        box-shadow: none;
        border: none;
    }

    .sim-public-textarea-field::placeholder {
        color: var(--sim-field-placeholder);
        font-weight: 400;
        line-height: 1.55;
    }

    /* Smart Tip Box */
    .sim-tip-box {
        background: var(--sim-field-subbar);
        border: 1px solid var(--guest-border, rgba(255, 255, 255, 0.12));
        color: var(--guest-text-main, #334155);
        border-radius: 16px;
        padding: 14px 18px;
        font-size: 0.82rem;
        line-height: 1.6;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    }
    .sim-tip-box strong {
        color: var(--guest-text-headings, #0f172a);
        font-weight: 700;
    }

    /* ACTION BUTTON (GUEST THEME COMPLIANT) */
    .sim-public-btn {
        background: var(--guest-accent, #10b981) !important;
        color: var(--guest-accent-text, #ffffff) !important;
        padding: 11px 26px !important;
        height: 44px !important;
        border-radius: 9999px !important;
        font-weight: 700 !important;
        font-size: 0.88rem !important;
        border: none !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        white-space: nowrap !important;
        text-decoration: none !important;
        box-shadow: 0 4px 16px var(--guest-accent-glow, rgba(16, 185, 129, 0.35)) !important;
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }
    .sim-public-btn:hover {
        filter: brightness(1.1) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 22px var(--guest-accent-glow, rgba(16, 185, 129, 0.5)) !important;
        color: var(--guest-accent-text, #ffffff) !important;
    }
    .sim-public-btn:active {
        transform: translateY(0) !important;
    }
    .sim-public-btn svg {
        width: 17px !important;
        height: 17px !important;
        min-width: 17px !important;
        min-height: 17px !important;
        max-width: 17px !important;
        max-height: 17px !important;
        flex-shrink: 0 !important;
        display: inline-block !important;
    }
    .sim-public-btn span {
        white-space: nowrap !important;
        display: inline-block !important;
        line-height: 1 !important;
    }

    /* Result Card Item (Guest) */
    .sim-result-card-public {
        background: var(--guest-bg-surface, rgba(14, 23, 34, 0.85));
        border: 1px solid var(--guest-border, rgba(255, 255, 255, 0.1));
        border-radius: 18px;
        padding: 1.25rem 1.5rem;
        transition: all 0.22s ease;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
    }
    .sim-result-card-public:hover {
        border-color: var(--guest-accent, #10b981);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }

    .progress-bar-wrap {
        width: 100%;
        height: 6px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 9999px;
        overflow: hidden;
        margin-top: 10px;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: 9999px;
        transition: width 0.6s ease;
    }
</style>

<div class="sim-wrapper space-y-8 max-w-4xl mx-auto {{ $isAuth ? 'py-4' : 'py-10 px-4 sm:px-6' }}">

    {{-- HEADER SECTION --}}
    <div class="text-center md:text-left pb-4 border-b" style="border-color: {{ $isAuth ? 'rgba(226, 232, 240, 0.8)' : 'var(--guest-border, rgba(255, 255, 255, 0.12))' }};">
        <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider mb-2" style="{{ $isAuth ? 'background: #d1fae5; color: #0c4233;' : 'background: var(--sim-field-subbar); color: var(--guest-accent); border: 1px solid var(--guest-border);' }}">
            ⚖️ Academic Integrity Verification
        </span>
        <h1 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 2.15rem; color: {{ $isAuth ? '#2d2b56' : 'var(--guest-text-headings, #ffffff)' }};" class="font-extrabold tracking-tight leading-tight">
            Similarity <span style="color: {{ $isAuth ? '#0c4233' : 'var(--guest-accent, #10b981)' }};">Checker</span>
        </h1>
        <p class="text-xs font-medium mt-1" style="color: {{ $isAuth ? '#64748b' : 'var(--guest-text-muted, #94a3b8)' }};">
            Analyze your proposed research title and objectives against existing published capstone manuscripts to prevent topic duplication.
        </p>
    </div>

    {{-- ANALYSIS CONSOLE CARD --}}
    <div class="{{ $isAuth ? 'sim-auth-card' : 'sim-public-card' }} space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b pb-4" style="border-color: {{ $isAuth ? '#f1f5f9' : 'var(--guest-border, rgba(255, 255, 255, 0.12))' }};">
            <h3 class="font-extrabold text-base flex items-center gap-2" style="color: {{ $isAuth ? '#2d2b56' : 'var(--guest-text-headings, #ffffff)' }};">
                <span>🔍</span> Research Verification Console
            </h3>
            <span class="inline-flex items-center gap-2 text-xs font-bold {{ $isAuth ? 'text-slate-400 font-sans' : 'font-mono' }}" style="{{ !$isAuth ? 'color: var(--guest-accent); background: rgba(var(--guest-accent-rgb, 16, 185, 129), 0.08); padding: 4px 12px; border-radius: 9999px; border: 1px solid rgba(var(--guest-accent-rgb, 16, 185, 129), 0.2);' : '' }}">
                <span class="w-2 h-2 rounded-full animate-pulse" style="background: {{ $isAuth ? '#34d399' : 'var(--guest-accent, #10b981)' }};"></span>
                Automated Semantic Match
            </span>
        </div>

        <div class="p-4 {{ $isAuth ? 'bg-amber-50/80 border-amber-200 text-amber-900 border rounded-2xl text-xs font-semibold flex items-start gap-3' : 'sim-tip-box' }}">
            <span class="text-lg shrink-0">💡</span>
            <p class="leading-relaxed">
                <strong>Unified Smart Checker:</strong> Paste your proposed research <strong>Title</strong>, <strong>Project Context</strong>, <strong>Objectives</strong>, or full topic abstract in the single input box below. The semantic engine automatically detects and cross-references all dimensions simultaneously with existing capstone manuscripts.
            </p>
        </div>

        @if (isset($errors) && $errors->any())
            <div class="p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-2xl text-xs font-bold space-y-1">
                @foreach ($errors->all() as $error)
                    <p>⚠️ {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('similarity-checker.check') }}" class="space-y-6">
            @csrf

            <!-- Unified Field: Proposed Research Title, Context, or Objectives -->
            <div class="{{ $isAuth ? '' : 'sim-field-group' }}">
                <div class="{{ $isAuth ? '' : 'sim-field-header mb-1' }}">
                    <label for="query" class="{{ $isAuth ? 'block text-xs font-extrabold text-[#2d2b56] uppercase tracking-wider mb-2' : 'sim-field-label' }}">
                        @if(!$isAuth)
                            <span class="sim-field-badge">1</span>
                            <span>Proposed Research Topic (Title, Context, or Objectives)</span>
                            <span class="sim-field-tag-unified">Unified Input</span>
                        @else
                            Research Title, Project Context, or Objectives <span class="text-slate-400 font-normal lowercase">(enter title, context, objectives, or topic description)</span>
                        @endif
                    </label>
                    @if(!$isAuth)
                        <span class="sim-field-hint">Enter your proposed <strong>Title</strong>, <strong>Project Context</strong>, specific <strong>Objectives</strong>, or full topic abstract. Our AI cross-references all dimensions simultaneously.</span>
                    @endif
                </div>

                @if(!$isAuth)
                    <div class="sim-textarea-shell">
                        <div class="sim-textarea-header-bar">
                            <span class="sim-textarea-header-title">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                </svg>
                                Manuscript Title, Project Context, Specific Objectives, or Topic Scope
                            </span>
                            <div class="flex items-center gap-2">
                                <span id="queryCharCount" class="sim-counter-pill">0 chars</span>
                                <span id="queryWordCount" class="sim-counter-pill">0 words</span>
                                <button type="button" class="sim-clear-icon-btn" id="queryClearBtn" onclick="clearField('query')" title="Clear Input">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <textarea
                            name="query"
                            id="query"
                            rows="4"
                            placeholder="e.g. Enter a proposed title (e.g. Smart IoT Flood Warning System) OR paste project context (e.g. In Barangay San Jose...) OR paste your specific objectives (e.g. 1. To develop a web portal... 2. To test accuracy...) OR enter an abstract..."
                            class="sim-public-textarea-field">{{ old('query', $inputQuery ?? $inputTitle ?? $inputObjectives ?? '') }}</textarea>
                    </div>

                    <!-- Quick Suggestions -->
                    <div class="sim-quick-suggestions">
                        <span class="sim-sug-label">💡 Try example:</span>
                        <button type="button" class="sim-sug-chip" onclick="fillExample('Web-Based Capstone Repository and AI Classification System')">
                            AI Repository (Title)
                        </button>
                        <button type="button" class="sim-sug-chip" onclick="fillExample('The study focuses on automated inventory management and record keeping in the municipal health center of Aurora, Zamboanga del Sur.')">
                            Sample Context
                        </button>
                        <button type="button" class="sim-sug-chip" onclick="fillExample('1. To design and implement a web-based repository for capstone archiving. 2. To analyze research originality using semantic similarity vectors.')">
                            Sample Objectives
                        </button>
                        <button type="button" class="sim-sug-chip" onclick="fillExample('IoT-Based Flood Early Warning and Monitoring System with SMS Alert')">
                            IoT Flood Alert (Title)
                        </button>
                    </div>
                @else
                    <div class="space-y-2">
                        <textarea
                            name="query"
                            id="query"
                            rows="4"
                            placeholder="Enter your proposed research title, project context, specific objectives, or full topic abstract..."
                            class="sim-auth-input">{{ old('query', $inputQuery ?? $inputTitle ?? $inputObjectives ?? '') }}</textarea>
                        
                        <!-- Quick Suggestions for Auth User -->
                        <div class="flex items-center gap-2 pt-1 flex-wrap text-xs">
                            <span class="text-slate-400 font-semibold">💡 Try example:</span>
                            <button type="button" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-[#2d2b56] font-medium transition cursor-pointer text-[11px]" onclick="fillExample('Web-Based Capstone Repository and AI Classification System')">
                                AI Repository (Title)
                            </button>
                            <button type="button" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-[#2d2b56] font-medium transition cursor-pointer text-[11px]" onclick="fillExample('The study focuses on automated inventory management and record keeping in the municipal health center of Aurora, Zamboanga del Sur.')">
                                Sample Context
                            </button>
                            <button type="button" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-[#2d2b56] font-medium transition cursor-pointer text-[11px]" onclick="fillExample('1. To design and implement a web-based repository for capstone archiving. 2. To analyze research originality using semantic similarity vectors.')">
                                Sample Objectives
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <div class="pt-3 flex items-center justify-end">
                <button type="submit" class="{{ $isAuth ? 'sim-auth-btn' : 'sim-public-btn' }}">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
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
        <div class="space-y-4 pt-4">
            <div class="flex items-center justify-between">
                <h3 class="font-extrabold text-base flex items-center gap-2" style="color: {{ $isAuth ? '#2d2b56' : 'var(--guest-text-headings, #ffffff)' }};">
                    <span>📊</span> Analysis Results
                </h3>
                <span class="px-3.5 py-1 font-extrabold text-xs rounded-full {{ $isAuth ? 'bg-amber-100 text-amber-900' : 'font-mono' }}" style="{{ !$isAuth ? 'background: rgba(var(--guest-accent-rgb, 16, 185, 129), 0.1); color: var(--guest-accent); border: 1px solid rgba(var(--guest-accent-rgb, 16, 185, 129), 0.25);' : '' }}">
                    Found {{ $results->count() }} Similar {{ Str::plural('Study', $results->count()) }}
                </span>
            </div>

            <div class="space-y-3">
                @foreach($results as $item)
                    @php
                        $score = isset($item['overall_similarity']) ? round($item['overall_similarity'], 1) : 0;
                        $isHigh = $score >= 75;
                        $isMed = $score >= 60 && $score < 75;
                    @endphp

                    <div class="{{ $isAuth ? 'p-5 rounded-2xl border transition-all duration-250 bg-white border-slate-200 hover:border-slate-300 hover:shadow-md' : 'sim-result-card-public' }}">
                        <div class="flex items-start justify-between gap-4">
                            <div class="space-y-1 flex-1">
                                <h4 class="font-extrabold text-sm leading-snug">
                                    <a href="{{ route('repository.show', $item['research']->id) }}" target="_blank" class="transition hover:underline" style="color: {{ $isAuth ? '#2d2b56' : 'var(--guest-text-headings, #ffffff)' }};">
                                        {{ $item['research']->title }}
                                    </a>
                                </h4>

                                {{-- Similarity Match Level Badge --}}
                                <div class="flex items-center gap-2.5 pt-1.5 flex-wrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold {{ $isHigh ? 'bg-rose-500/15 text-rose-500 border border-rose-500/30' : ($isMed ? 'bg-amber-500/15 text-amber-500 border border-amber-500/30' : 'bg-emerald-500/15 text-emerald-500 border border-emerald-500/30') }}">
                                        {{ $isHigh ? '⚠️ High Potential Match' : ($isMed ? '⚡ Moderate Match' : '✓ Related Study Found') }}
                                    </span>

                                    @if(!empty($item['primary_match']))
                                        <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full" style="{{ $isAuth ? 'background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;' : 'background: var(--sim-field-subbar); color: var(--guest-accent); border: 1px solid var(--guest-border);' }}">
                                            🎯 {{ $item['primary_match'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <a href="{{ route('repository.show', $item['research']->id) }}" target="_blank" class="px-3.5 py-1.5 font-extrabold text-xs rounded-full shrink-0 transition flex items-center gap-1.5" style="{{ $isAuth ? 'background: #f1f5f9; color: #2d2b56;' : 'background: var(--sim-field-subbar); color: var(--guest-accent); border: 1px solid var(--guest-border);' }}">
                                View Paper ➔
                            </a>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 text-xs font-medium mt-3 pt-2.5 border-t" style="color: {{ $isAuth ? '#64748b' : 'var(--guest-text-muted, #94a3b8)' }}; border-color: {{ $isAuth ? '#f1f5f9' : 'var(--guest-border, rgba(255, 255, 255, 0.1))' }};">
                            <span>👤 Authors: <strong style="color: {{ $isAuth ? '#334155' : 'var(--guest-text-headings, #ffffff)' }};">{{ $item['research']->authors }}</strong></span>
                            <span>·</span>
                            <span>📅 Published: <strong class="font-mono" style="color: {{ $isAuth ? '#334155' : 'var(--guest-text-headings, #ffffff)' }};">{{ $item['research']->year }}</strong></span>
                            @if($item['research']->category)
                                <span>·</span>
                                <span class="px-2.5 py-0.5 font-extrabold text-[11px] rounded-full" style="{{ $isAuth ? 'background: #faf5ff; color: #6b21a8;' : 'background: var(--sim-field-subbar); color: var(--guest-accent); border: 1px solid var(--guest-border);' }}">
                                    📂 {{ $item['research']->category->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif(isset($results))
        <div class="p-8 border rounded-3xl text-center space-y-3" style="{{ $isAuth ? 'background: rgba(236, 253, 245, 0.8); border-color: #a7f3d0; color: #064e3b;' : 'background: var(--guest-bg-surface); border-color: var(--guest-border); color: var(--guest-text-headings);' }}">
            <div class="w-14 h-14 rounded-full flex items-center justify-center font-black text-2xl mx-auto" style="{{ $isAuth ? 'background: #d1fae5; color: #065f46;' : 'background: rgba(var(--guest-accent-rgb, 16, 185, 129), 0.12); color: var(--guest-accent); border: 1px solid rgba(var(--guest-accent-rgb, 16, 185, 129), 0.3);' }}">
                ✓
            </div>
            <div>
                <h4 class="font-extrabold text-base" style="color: {{ $isAuth ? '#064e3b' : 'var(--guest-text-headings)' }};">
                    No Similar Studies Found (High Originality)
                </h4>
                <p class="text-xs max-w-md mx-auto mt-1 font-medium leading-relaxed" style="color: {{ $isAuth ? '#065f46' : 'var(--guest-text-muted)' }};">
                    Great news! Based on automated semantic vector analysis, your entered research topic does not overlap with any existing manuscripts in the capstone repository.
                </p>
            </div>
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

    // Initialize counts on page load
    document.addEventListener('DOMContentLoaded', updateSimCounters);
    updateSimCounters();
</script>
@endsection