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
        padding: 12px 32px;
        border-radius: 50px;
        font-weight: 800;
        font-size: 14px;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.25s ease;
        box-shadow: 0 4px 16px rgba(12, 66, 51, 0.25);
    }
    .sim-auth-btn:hover {
        background: #082d23;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(12, 66, 51, 0.35);
    }

    /* PUBLIC GUEST THEME (CYBER DARK GLASSMORPHISM) */
    .sim-public-card {
        background: linear-gradient(var(--guest-bg-surface, rgba(14, 23, 34, 0.94)), var(--guest-bg-surface, rgba(14, 23, 34, 0.94))) padding-box,
                    var(--guest-card-border-grad, linear-gradient(135deg, rgba(0, 245, 212, 0.6) 0%, rgba(6, 182, 212, 0.4) 35%, rgba(16, 185, 129, 0.4) 65%, rgba(0, 245, 212, 0.5) 100%)) border-box;
        border: 1.5px solid transparent;
        border-radius: 24px;
        padding: 2.2rem;
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        box-shadow: var(--guest-card-shadow, 0 18px 46px rgba(0, 0, 0, 0.6));
    }

    @media (max-width: 640px) {
        .sim-public-card {
            padding: 1.5rem;
        }
    }

    /* THEME-ADAPTIVE VARIABLES FOR SIMILARITY INPUTS */
    :root {
        --sim-field-bg: rgba(7, 13, 22, 0.92);
        --sim-field-border: rgba(255, 255, 255, 0.22);
        --sim-field-border-hover: rgba(0, 245, 212, 0.55);
        --sim-field-bg-hover: rgba(9, 17, 28, 0.96);
        --sim-field-bg-focus: rgba(12, 22, 36, 0.98);
        --sim-field-text: #ffffff;
        --sim-field-placeholder: rgba(148, 163, 184, 0.72);
        --sim-field-subbar: rgba(255, 255, 255, 0.04);
        --sim-field-divider: rgba(255, 255, 255, 0.1);
        --sim-chip-bg: rgba(255, 255, 255, 0.06);
        --sim-chip-border: rgba(255, 255, 255, 0.14);
        --sim-chip-text: #94a3b8;
    }

    html[data-guest-theme="light"] {
        --sim-field-bg: #f8fafc;
        --sim-field-border: #94a3b8;
        --sim-field-border-hover: #059669;
        --sim-field-bg-hover: #ffffff;
        --sim-field-bg-focus: #ffffff;
        --sim-field-text: #0f172a;
        --sim-field-placeholder: #64748b;
        --sim-field-subbar: #f1f5f9;
        --sim-field-divider: #e2e8f0;
        --sim-chip-bg: #e2e8f0;
        --sim-chip-border: #cbd5e1;
        --sim-chip-text: #334155;
    }

    html[data-guest-theme="sepia"] {
        --sim-field-bg: #fbf7ee;
        --sim-field-border: #c4b59f;
        --sim-field-border-hover: #b45309;
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
        --sim-field-border: #3f3f46;
        --sim-field-border-hover: #00f5d4;
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
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        flex-wrap: wrap;
    }

    .sim-field-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.88rem;
        font-weight: 800;
        color: var(--guest-text-headings, #ffffff);
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .sim-field-badge {
        width: 24px;
        height: 24px;
        border-radius: 8px;
        background: var(--guest-accent, #00f5d4);
        color: #042f2e;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 900;
        flex-shrink: 0;
        box-shadow: 0 2px 6px var(--guest-accent-glow, rgba(0, 245, 212, 0.3));
    }

    .sim-field-tag-required {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #fbbf24;
        background: rgba(245, 158, 11, 0.12);
        border: 1px solid rgba(245, 158, 11, 0.35);
        padding: 2px 8px;
        border-radius: 6px;
    }

    .sim-field-tag-optional {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--guest-accent, #00f5d4);
        background: rgba(0, 245, 212, 0.1);
        border: 1px solid var(--guest-accent-glow, rgba(0, 245, 212, 0.25));
        padding: 2px 8px;
        border-radius: 6px;
    }

    .sim-field-hint {
        font-size: 0.78rem;
        color: var(--guest-text-muted, #94a3b8);
        font-weight: 500;
    }

    /* HIGH-DEFINITION INPUT SHELL FOR TITLE */
    .sim-input-shell {
        position: relative;
        display: flex;
        align-items: center;
        background: var(--sim-field-bg);
        border: 1.5px solid var(--sim-field-border);
        border-radius: 16px;
        padding: 6px 14px 6px 16px;
        gap: 12px;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.25), 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .sim-input-shell:hover {
        border-color: var(--sim-field-border-hover);
        background: var(--sim-field-bg-hover);
    }

    .sim-input-shell:focus-within {
        border-color: var(--guest-accent, #00f5d4);
        background: var(--sim-field-bg-focus);
        box-shadow: 0 0 0 4px var(--guest-accent-glow, rgba(0, 245, 212, 0.22)), 
                    0 8px 24px rgba(0, 0, 0, 0.35),
                    inset 0 1px 3px rgba(0, 0, 0, 0.15);
        transform: translateY(-1px);
    }

    .sim-input-icon-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(0, 245, 212, 0.12);
        color: var(--guest-accent, #00f5d4);
        flex-shrink: 0;
        transition: transform 0.2s ease;
    }

    .sim-input-shell:focus-within .sim-input-icon-badge {
        transform: scale(1.08);
        background: rgba(0, 245, 212, 0.2);
    }

    .sim-public-input-field {
        flex: 1;
        min-width: 0;
        background: transparent;
        border: none;
        outline: none;
        font-family: inherit;
        font-size: 1rem;
        font-weight: 600;
        color: var(--sim-field-text);
        padding: 10px 0;
    }

    .sim-public-input-field::placeholder {
        color: var(--sim-field-placeholder);
        font-weight: 400;
        font-size: 0.94rem;
    }

    .sim-shell-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }

    .sim-counter-pill {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--guest-text-muted, #94a3b8);
        background: var(--sim-field-subbar);
        padding: 3px 8px;
        border-radius: 9999px;
        border: 1px solid var(--sim-field-divider);
    }

    .sim-clear-icon-btn {
        background: var(--sim-field-subbar);
        border: 1px solid var(--sim-field-divider);
        color: var(--guest-text-muted, #94a3b8);
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .sim-clear-icon-btn:hover {
        color: #38bdf8;
        background: rgba(56, 189, 248, 0.12);
        border-color: rgba(56, 189, 248, 0.35);
        transform: scale(1.08);
    }

    /* QUICK SUGGESTIONS */
    .sim-quick-suggestions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        padding-top: 4px;
    }

    .sim-sug-label {
        font-size: 0.74rem;
        font-weight: 700;
        color: var(--guest-text-muted, #94a3b8);
    }

    .sim-sug-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: var(--sim-chip-bg);
        border: 1px solid var(--sim-chip-border);
        color: var(--sim-chip-text);
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.74rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .sim-sug-chip:hover {
        background: var(--guest-accent, #00f5d4);
        color: #042f2e;
        border-color: var(--guest-accent, #00f5d4);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px var(--guest-accent-glow, rgba(0, 245, 212, 0.3));
    }

    /* HIGH-DEFINITION TEXTAREA SHELL */
    .sim-textarea-shell {
        display: flex;
        flex-direction: column;
        background: var(--sim-field-bg);
        border: 1.5px solid var(--sim-field-border);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.25), 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .sim-textarea-shell:hover {
        border-color: var(--sim-field-border-hover);
        background: var(--sim-field-bg-hover);
    }

    .sim-textarea-shell:focus-within {
        border-color: var(--guest-accent, #00f5d4);
        background: var(--sim-field-bg-focus);
        box-shadow: 0 0 0 4px var(--guest-accent-glow, rgba(0, 245, 212, 0.22)), 
                    0 8px 24px rgba(0, 0, 0, 0.35),
                    inset 0 1px 3px rgba(0, 0, 0, 0.15);
        transform: translateY(-1px);
    }

    .sim-textarea-header-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 9px 16px;
        background: var(--sim-field-subbar);
        border-bottom: 1px solid var(--sim-field-divider);
        font-size: 0.76rem;
    }

    .sim-textarea-header-title {
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--guest-text-muted, #94a3b8);
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .sim-public-textarea-field {
        width: 100%;
        min-height: 135px;
        background: transparent;
        border: none;
        outline: none;
        padding: 14px 18px;
        font-family: inherit;
        font-size: 0.95rem;
        font-weight: 500;
        line-height: 1.65;
        color: var(--sim-field-text);
        resize: vertical;
    }

    .sim-public-textarea-field::placeholder {
        color: var(--sim-field-placeholder);
        font-weight: 400;
        line-height: 1.6;
    }

    /* Smart Tip Box */
    .sim-tip-box {
        background: var(--sim-field-bg);
        border: 1px solid var(--sim-field-border);
        color: var(--guest-text-main, #cbd5e1);
    }

    .sim-public-btn {
        background: var(--guest-accent, #00f5d4);
        color: #042f2e;
        padding: 13px 36px;
        border-radius: 9999px;
        font-weight: 800;
        font-size: 14px;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 4px 18px var(--guest-accent-glow, rgba(0, 245, 212, 0.45));
    }
    .sim-public-btn:hover {
        filter: brightness(1.1);
        color: #042f2e;
        transform: translateY(-2px);
        box-shadow: 0 8px 26px var(--guest-accent-glow, rgba(0, 245, 212, 0.65));
    }

    /* Result Card Item (Guest Cyber) */
    .sim-result-card-public {
        background: rgba(14, 23, 34, 0.85);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        padding: 1.3rem 1.6rem;
        transition: all 0.25s ease;
        backdrop-filter: blur(12px);
    }
    .sim-result-card-public:hover {
        border-color: rgba(0, 245, 212, 0.45);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4), 0 0 16px rgba(0, 245, 212, 0.15);
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
    <div class="text-center md:text-left pb-4 border-b {{ $isAuth ? 'border-slate-200/80' : 'border-slate-800' }}">
        <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider {{ $isAuth ? 'bg-emerald-100 text-[#0c4233]' : 'bg-[#00f5d4]/10 text-[#00f5d4] border border-[#00f5d4]/35 shadow-[0_0_12px_rgba(0,245,212,0.18)]' }} mb-2">
            ⚖️ Academic Integrity Verification
        </span>
        <h1 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 2.15rem;" class="font-extrabold {{ $isAuth ? 'text-[#2d2b56]' : 'text-white' }} tracking-tight leading-tight">
            Similarity <span class="{{ $isAuth ? 'text-[#0c4233]' : 'text-[#00f5d4]' }}">Checker</span>
        </h1>
        <p class="text-xs font-medium mt-1 {{ $isAuth ? 'text-slate-500' : 'text-slate-400' }}">
            Analyze your proposed research title and objectives against existing published capstone manuscripts to prevent topic duplication.
        </p>
    </div>

    {{-- ANALYSIS CONSOLE CARD --}}
    <div class="{{ $isAuth ? 'sim-auth-card' : 'sim-public-card' }} space-y-6">
        <div class="flex items-center justify-between border-b {{ $isAuth ? 'border-slate-100' : 'border-slate-800' }} pb-4">
            <h3 class="font-extrabold text-base {{ $isAuth ? 'text-[#2d2b56]' : 'text-white' }} flex items-center gap-2">
                <span>🔍</span> Research Verification Console
            </h3>
            <span class="inline-flex items-center gap-2 text-xs font-bold {{ $isAuth ? 'text-slate-400 font-sans' : 'text-emerald-400 font-mono' }}">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Automated Semantic Match
            </span>
        </div>

        <div class="p-4 {{ $isAuth ? 'bg-amber-50/80 border-amber-200 text-amber-900' : 'sim-tip-box' }} border rounded-2xl text-xs font-semibold flex items-start gap-3">
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
                <div class="{{ $isAuth ? '' : 'sim-field-header' }}">
                    <label for="query" class="{{ $isAuth ? 'block text-xs font-extrabold text-[#2d2b56] uppercase tracking-wider mb-2' : 'sim-field-label' }}">
                        @if(!$isAuth)
                            <span class="sim-field-badge">1</span>
                            <span>Proposed Research Topic (Title, Context, or Objectives)</span>
                            <span class="sim-field-tag-required" style="color: #38bdf8; background: rgba(56, 189, 248, 0.12); border-color: rgba(56, 189, 248, 0.35);">Unified Input</span>
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

            <div class="pt-2 text-right">
                <button type="submit" class="{{ $isAuth ? 'sim-auth-btn' : 'sim-public-btn' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Run Similarity Check</span>
                </button>
            </div>
        </form>
    </div>

    {{-- RESULTS SECTION --}}
    @if(isset($results) && $results->count() > 0)
        <div class="space-y-4 pt-4">
            <div class="flex items-center justify-between">
                <h3 class="font-extrabold text-base {{ $isAuth ? 'text-[#2d2b56]' : 'text-white' }} flex items-center gap-2">
                    <span>📊</span> Analysis Results
                </h3>
                <span class="px-3.5 py-1 {{ $isAuth ? 'bg-amber-100 text-amber-900' : 'bg-emerald-950/80 text-emerald-400 border border-emerald-500/40 shadow-[0_0_12px_rgba(16,185,129,0.25)] font-mono' }} font-extrabold text-xs rounded-full">
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
                                    <a href="{{ route('repository.show', $item['research']->id) }}" target="_blank" class="{{ $isAuth ? 'text-[#2d2b56] hover:text-[#5840bb]' : 'text-white hover:text-[#00f5d4]' }} transition">
                                        {{ $item['research']->title }}
                                    </a>
                                </h4>

                                {{-- Similarity Match Level Badge --}}
                                <div class="flex items-center gap-2.5 pt-1.5 flex-wrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold {{ $isHigh ? 'bg-rose-950/80 text-rose-400 border border-rose-500/40' : ($isMed ? 'bg-amber-950/80 text-amber-400 border border-amber-500/40' : 'bg-emerald-950/80 text-emerald-400 border border-emerald-500/40') }}">
                                        {{ $isHigh ? '⚠️ High Potential Match' : ($isMed ? '⚡ Moderate Match' : '✓ Related Study Found') }}
                                    </span>

                                    @if(!empty($item['primary_match']))
                                        <span class="text-[11px] font-semibold {{ $isAuth ? 'text-slate-600 bg-slate-100 border border-slate-200' : 'text-cyan-300 bg-cyan-950/60 border border-cyan-500/30' }} px-2.5 py-0.5 rounded-full">
                                            🎯 {{ $item['primary_match'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <a href="{{ route('repository.show', $item['research']->id) }}" target="_blank" class="px-3.5 py-1.5 {{ $isAuth ? 'bg-slate-100 text-[#2d2b56] hover:bg-slate-200' : 'bg-emerald-500/15 hover:bg-emerald-500/25 text-[#34d399] border border-emerald-500/35 hover:border-emerald-400 shadow-[0_0_10px_rgba(16,185,129,0.2)]' }} font-extrabold text-xs rounded-full shrink-0 transition flex items-center gap-1.5">
                                View Paper ➔
                            </a>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 text-xs {{ $isAuth ? 'text-slate-500' : 'text-slate-400' }} font-medium mt-3 pt-2.5 border-t {{ $isAuth ? 'border-slate-100' : 'border-slate-800' }}">
                            <span>👤 Authors: <strong class="{{ $isAuth ? 'text-slate-700' : 'text-slate-200' }}">{{ $item['research']->authors }}</strong></span>
                            <span>·</span>
                            <span>📅 Published: <strong class="{{ $isAuth ? 'text-slate-700' : 'text-slate-200 font-mono' }}">{{ $item['research']->year }}</strong></span>
                            @if($item['research']->category)
                                <span>·</span>
                                <span class="px-2.5 py-0.5 {{ $isAuth ? 'bg-purple-50 text-purple-800' : 'bg-cyan-950/60 text-[#38bdf8] border border-cyan-500/30' }} font-extrabold text-[11px] rounded-full">
                                    📂 {{ $item['research']->category->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif(isset($results))
        <div class="p-8 {{ $isAuth ? 'bg-emerald-50/80 border-emerald-200 text-emerald-950' : 'bg-slate-900/90 border-slate-700/80 text-white shadow-2xl' }} border rounded-3xl text-center space-y-3">
            <div class="w-14 h-14 {{ $isAuth ? 'bg-emerald-100 text-emerald-800' : 'bg-emerald-950/80 border border-emerald-500/40 text-[#34d399] shadow-[0_0_20px_rgba(16,185,129,0.3)]' }} rounded-full flex items-center justify-center font-black text-2xl mx-auto">
                ✓
            </div>
            <div>
                <h4 class="font-extrabold text-base">
                    No Similar Studies Found (High Originality)
                </h4>
                <p class="text-xs {{ $isAuth ? 'text-emerald-800' : 'text-slate-300' }} max-w-md mx-auto mt-1 font-medium leading-relaxed">
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
