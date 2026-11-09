@extends(auth()->check() ? (auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.app') : 'layouts.public')
@section('title', 'Similarity Checker — JHCSC Capstone Portal')

@section('content')
<style>
    .sim-container {
        max-width: 56rem;
        margin: 0 auto;
        padding: 0 1rem 3rem;
    }

    .sim-card {
        background: var(--guest-bg-surface, #ffffff);
        border: 1px solid var(--guest-border, #e2e8f0);
        border-radius: 18px;
        box-shadow: var(--guest-card-shadow, 0 2px 10px rgba(15, 23, 42, 0.04));
        padding: 1.15rem;
        transition: border-color 0.2s ease;
    }

    .sim-textarea {
        width: 100%;
        background: var(--guest-bg-input, #f8fafc);
        border: 1.5px solid var(--guest-border, #cbd5e1);
        border-radius: 12px;
        padding: 0.75rem 0.85rem;
        font-size: 0.82rem;
        font-weight: 500;
        color: var(--guest-text-headings, #0f172a);
        outline: none;
        transition: all 0.2s ease;
        line-height: 1.5;
        resize: vertical;
    }

    .sim-textarea:focus {
        border-color: var(--guest-accent, #0c4233);
        background: var(--guest-bg-surface, #ffffff);
        box-shadow: 0 0 0 3px rgba(12, 66, 51, 0.12);
    }

    /* Single-Row Clean Minimal Chip Carousel on Mobile */
    .sim-sample-wrap {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        overflow: hidden;
        padding-top: 0.25rem;
    }

    .sim-sample-label {
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--guest-text-muted, #94a3b8);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        flex-shrink: 0;
    }

    .sim-sample-scroll {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        overflow-x: auto;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 2px;
    }

    .sim-sample-scroll::-webkit-scrollbar {
        display: none;
    }

    .sim-chip-btn {
        white-space: nowrap;
        flex-shrink: 0;
        padding: 0.3rem 0.65rem;
        background: var(--guest-bg-input, #f1f5f9);
        border: 1px solid var(--guest-border, #e2e8f0);
        border-radius: 9999px;
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--guest-text-main, #475569);
        transition: all 0.15s ease;
        cursor: pointer;
    }

    .sim-chip-btn:hover {
        background: var(--guest-border, #e2e8f0);
        color: var(--guest-text-headings, #0f172a);
        border-color: var(--guest-accent, #0c4233);
    }

    /* Mobile Minimal Adaptations */
    @media (max-width: 640px) {
        .sim-container {
            padding: 0 0.75rem 2.5rem;
        }

        .sim-card {
            padding: 1rem;
            border-radius: 16px;
        }

        .sim-header-title {
            font-size: 1.35rem !important;
            line-height: 1.25 !important;
        }

        .sim-header-desc {
            font-size: 0.75rem !important;
        }

        .sim-btn-submit {
            width: 100% !important;
            padding: 0.7rem 1rem !important;
        }
    }
</style>

<div class="sim-container space-y-4 sm:space-y-6">
    {{-- HEADER SECTION --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-4 pb-3 sm:pb-4 border-b border-slate-200 dark:border-slate-800">
        <div>
            @if(auth()->check() && auth()->user()->role === 'admin')
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Administration</span>
                    <span class="text-slate-300 dark:text-slate-700">/</span>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Similarity Checker</span>
                </div>
            @endif
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20">
                    ⚡ Originality Screener
                </span>
            </div>
            <h1 class="sim-header-title text-xl sm:text-2xl font-black text-slate-900 dark:text-slate-100 tracking-tight mt-1">Research Similarity Checker</h1>
            <p class="sim-header-desc text-slate-500 dark:text-slate-400 text-xs font-medium mt-0.5 max-w-xl">
                Screen proposed capstone titles, abstracts, or objectives against published manuscripts to guarantee originality.
            </p>
        </div>
    </div>

    {{-- ERROR MESSAGES --}}
    @if (isset($errors) && $errors->any())
        <div class="p-3.5 sm:p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl text-xs font-semibold space-y-1">
            @foreach ($errors->all() as $error)
                <p>✕ {{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- INPUT CARD (Minimal, Clean & Mobile-Optimized) --}}
    <div class="sim-card space-y-3.5">
        <form method="POST" action="{{ route('similarity-checker.check') }}" class="space-y-3.5">
            @csrf
            
            <div class="space-y-2">
                <div class="flex items-center justify-between text-xs font-bold">
                    <label for="query" class="text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                        <span>✏️</span>
                        <span>Title, Abstract, or Objectives</span>
                    </label>
                    <div class="flex items-center gap-1.5 text-slate-400 font-mono text-[10px] sm:text-[11px]">
                        <span id="queryCharCount">0 chars</span>
                        <span>·</span>
                        <span id="queryWordCount">0 words</span>
                        <button type="button" id="queryClearBtn" onclick="clearField('query')" class="text-slate-400 hover:text-rose-600 font-bold ml-1 hidden" title="Clear">
                            ✕
                        </button>
                    </div>
                </div>

                <div class="relative">
                    <textarea
                        name="query"
                        id="query"
                        rows="3"
                        required
                        class="sim-textarea"
                        placeholder="Paste or type your proposed capstone title, background, or specific objectives here..."
                    >{{ old('query', $inputQuery ?? $inputTitle ?? $inputObjectives ?? '') }}</textarea>
                </div>

                {{-- Clean Single-Row Horizontal Scrollable Sample Chips --}}
                <div class="sim-sample-wrap">
                    <span class="sim-sample-label">Try:</span>
                    <div class="sim-sample-scroll">
                        <button type="button" class="sim-chip-btn" onclick="fillExample('Research Repository System for Capstone Projects')">
                            Research Repository
                        </button>
                        <button type="button" class="sim-chip-btn" onclick="fillExample('Arduino-Based Flood Warning with SMS Notification')">
                            Flood Warning Alert
                        </button>
                        <button type="button" class="sim-chip-btn" onclick="fillExample('Decision Support System for Freshmen College Sectioning')">
                            College Sectioning DSS
                        </button>
                        <button type="button" class="sim-chip-btn" onclick="fillExample('Mobile Vehicle Booking and Reservation System')">
                            Vehicle Booking App
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-slate-100 dark:border-slate-800/80">
                <span class="text-[11px] text-slate-400 font-medium hidden sm:block">
                    Evaluates title, abstract, and objectives simultaneously using cosine vector similarity.
                </span>
                <span class="text-[10px] text-slate-400 font-medium text-center sm:hidden block">
                    ⚡ Evaluates semantic vector similarity across repository
                </span>

                <button type="submit" 
                        class="sim-btn-submit px-5 py-2.5 bg-[#0c4233] hover:bg-[#082d23] text-white font-bold text-xs rounded-xl transition shadow-xs inline-flex items-center justify-center gap-2 shrink-0 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <span>Run Similarity Check</span>
                </button>
            </div>
        </form>
    </div>

    {{-- RESULTS SECTION (Minimal, Sleek & Mobile-First) --}}
    @if(isset($results) && $results->count() > 0)
        <div class="space-y-3 pt-2">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <h2 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">
                    Similarity Analysis Results
                </h2>
                <span class="px-2.5 py-0.5 bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20 text-xs font-bold rounded-lg">
                    Found {{ $results->count() }} Similar {{ Str::plural('Study', $results->count()) }}
                </span>
            </div>

            <div class="space-y-2.5">
                @foreach($results as $item)
                    <div class="sim-card p-3.5 sm:p-4 space-y-2.5 hover:border-slate-300 dark:hover:border-slate-700 transition">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2.5">
                            <div class="space-y-1 flex-1 min-w-0">
                                <a href="{{ route('repository.show', $item['research']->id) }}" target="_blank" 
                                   class="text-xs sm:text-sm font-bold text-slate-900 dark:text-slate-100 hover:text-emerald-700 dark:hover:text-emerald-400 transition leading-snug block">
                                    {{ $item['research']->title }}
                                </a>

                                @if(!empty($item['primary_match']))
                                    <div class="pt-0.5">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] sm:text-[11px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                            Match: {{ $item['primary_match'] }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <a href="{{ route('repository.show', $item['research']->id) }}" target="_blank" 
                               class="w-full sm:w-auto text-center justify-center px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-lg transition inline-flex items-center gap-1 shrink-0">
                                View Manuscript →
                            </a>
                        </div>

                        <div class="flex items-center gap-2 text-[11px] text-slate-500 dark:text-slate-400 pt-2 border-t border-slate-100 dark:border-slate-800/80 flex-wrap">
                            <span>Authors: <strong class="text-slate-700 dark:text-slate-300 font-medium">{{ $item['research']->authors }}</strong></span>
                            <span>·</span>
                            <span>Year: <strong class="text-slate-700 dark:text-slate-300 font-mono">{{ $item['research']->year }}</strong></span>
                            @if($item['research']->category)
                                <span>·</span>
                                <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-800 dark:text-emerald-300 border border-emerald-500/20 rounded text-[10px] font-semibold">
                                    {{ $item['research']->category->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif(isset($results))
        <div class="sim-card p-6 sm:p-8 text-center space-y-2">
            <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center mx-auto text-base font-bold">
                ✓
            </div>
            <h3 class="font-bold text-sm text-slate-900 dark:text-slate-100">No Similar Manuscripts Found</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto leading-relaxed">
                High originality detected. Automated semantic analysis did not find overlapping research studies in the repository.
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

    // Proactive AI Wake-up & Heartbeat: Keeps Render AI service awake ONLY while user is on this similarity page
    (function initAIKeepAlive() {
        const aiUrl = @json(config('services.ai.url', 'https://capstone-ai-fqz3.onrender.com'));
        const warmupRoute = '{{ route("ai.warmup") }}';

        function pingAI() {
            if (aiUrl) {
                try {
                    fetch(aiUrl.replace(/\/$/, '') + '/', { mode: 'no-cors', cache: 'no-cache' }).catch(function() {});
                } catch (e) {}
            }
            try {
                fetch(warmupRoute, { cache: 'no-cache' }).catch(function() {});
            } catch (e) {}
        }

        // 1. Immediate wake-up ping upon opening the similarity page
        pingAI();

        // 2. Heartbeat every 3.5 minutes (210,000 ms) to keep AI awake while user remains on this page
        const heartbeatInterval = setInterval(pingAI, 210000);

        // 3. Re-ping when tab becomes visible again
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                pingAI();
            }
        });

        // 4. Clean up interval when navigating away so Render can sleep naturally after 15 mins
        window.addEventListener('beforeunload', function() {
            clearInterval(heartbeatInterval);
        });
    })();
</script>
@endsection
