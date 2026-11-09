@extends(auth()->check() ? (auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.app') : 'layouts.public')
@section('title', 'Similarity Checker — JHCSC Capstone Portal')

@section('content')
<style>
    /* ONLY MOBILE VIEW MINIMAL OVERRIDES (<= 640px) */
    @media (max-width: 640px) {
        .sim-mobile-container {
            padding-left: 0.85rem !important;
            padding-right: 0.85rem !important;
            padding-bottom: 2.5rem !important;
            margin-top: -0.25rem !important;
        }

        .sim-mobile-header {
            padding-bottom: 0.65rem !important;
        }

        .sim-mobile-title {
            font-size: 1.35rem !important;
            line-height: 1.25 !important;
            letter-spacing: -0.02em !important;
        }

        .sim-mobile-subtitle {
            font-size: 0.74rem !important;
            line-height: 1.4 !important;
            color: #64748b !important;
            margin-top: 0.25rem !important;
        }

        .sim-mobile-card {
            padding: 1rem !important;
            border-radius: 16px !important;
        }

        .sim-mobile-textarea {
            padding: 0.75rem !important;
            font-size: 0.82rem !important;
            min-height: 96px !important;
            line-height: 1.45 !important;
        }

        .sim-mobile-footnote {
            display: none !important;
        }

        .sim-mobile-btn {
            width: 100% !important;
            padding: 0.7rem 1rem !important;
        }

        .sim-mobile-result-card {
            padding: 0.9rem 1rem !important;
            border-radius: 16px !important;
        }
    }
</style>

<div class="sim-mobile-container space-y-6 max-w-5xl mx-auto pb-12">
    {{-- HEADER SECTION --}}
    <div class="sim-mobile-header flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-slate-200">
        <div>
            @if(auth()->check() && auth()->user()->role === 'admin')
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Administration</span>
                    <span class="text-slate-300">/</span>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Similarity Checker</span>
                </div>
            @endif
            <h1 class="sim-mobile-title text-2xl font-extrabold text-slate-900 tracking-tight mt-1">Research Similarity Checker</h1>
            <p class="sim-mobile-subtitle text-slate-500 text-xs font-medium mt-0.5">
                Screen proposed capstone titles, project context, general objectives, or specific objectives against published manuscripts to guarantee originality.
            </p>
        </div>
    </div>

    {{-- ERROR MESSAGES --}}
    @if (isset($errors) && $errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl text-xs font-semibold space-y-1">
            @foreach ($errors->all() as $error)
                <p>✕ {{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- INPUT CARD --}}
    <div class="sim-mobile-card bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-xs space-y-4">
        <form method="POST" action="{{ route('similarity-checker.check') }}" class="space-y-4">
            @csrf
            
            <div class="space-y-2">
                <div class="flex items-center justify-between text-xs font-semibold">
                    <label for="query" class="text-slate-700">
                        Enter Research Title, Context, General Objective, or Specific Objectives
                    </label>
                    <div class="flex items-center gap-2 text-slate-400 font-mono text-[11px]">
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
                        rows="4"
                        required
                        class="sim-mobile-textarea w-full bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-xs font-medium text-slate-900 outline-none focus:border-[#0c4233] focus:bg-white focus:ring-1 focus:ring-[#0c4233] transition leading-relaxed resize-y"
                        placeholder="Paste or enter your proposed research title, project context, general objective, or specific objectives here..."
                    >{{ old('query', $inputQuery ?? $inputTitle ?? $inputObjectives ?? '') }}</textarea>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-slate-100">
                <span class="sim-mobile-footnote text-xs text-slate-400 font-medium">
                    Evaluates textual overlap and semantic intent against research titles, context, general objectives, and specific objectives.
                </span>

                <button type="submit" 
                        class="sim-mobile-btn px-5 py-2.5 bg-[#0c4233] hover:bg-[#082d23] text-white font-bold text-xs rounded-xl transition shadow-xs inline-flex items-center justify-center gap-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <span>Run Similarity Check</span>
                </button>
            </div>
        </form>
    </div>

    {{-- RESULTS SECTION --}}
    @if(isset($results) && $results->count() > 0)
        <div class="space-y-4 pt-2">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">
                        Top {{ $results->count() }} Most Relevant Research {{ Str::plural('Project', $results->count()) }}
                    </h2>
                    <p class="text-[11px] text-slate-500">
                        Ranked using combined textual keywords and Sentence Transformer semantic embeddings.
                    </p>
                </div>
                <span class="px-2.5 py-1 bg-amber-50 text-amber-800 border border-amber-200 text-xs font-bold rounded-lg">
                    {{ $results->count() }} {{ Str::plural('Match', $results->count()) }} Evaluated
                </span>
            </div>

            <div class="space-y-4">
                @foreach($results as $item)
                    <div class="sim-mobile-result-card bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-xs hover:border-slate-300 transition space-y-3.5">
                        {{-- Header Row: Rank, Similarity Badge, Best-Matching Field, and Action Button --}}
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                            <div class="space-y-1.5 flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-extrabold bg-[#0c4233] text-white">
                                        #{{ $loop->iteration }} Ranked Match
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 font-mono">
                                        {{ $item['overall_similarity'] }}% Similarity
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                                        <span class="text-emerald-700 font-bold">🎯 Best Match:</span> {{ $item['best_matching_field'] }}
                                    </span>
                                </div>

                                <a href="{{ route('repository.show', $item['research']->id) }}" target="_blank" 
                                   class="text-sm sm:text-base font-extrabold text-slate-900 hover:text-emerald-700 transition leading-snug block pt-0.5">
                                    {{ $item['research']->title }}
                                </a>
                            </div>

                            <a href="{{ route('repository.show', $item['research']->id) }}" target="_blank" 
                               class="w-full sm:w-auto text-center justify-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-xl transition inline-flex items-center gap-1.5 shrink-0">
                                <span>View Manuscript</span>
                                <span>→</span>
                            </a>
                        </div>

                        {{-- Explanations: Similarities & Differences --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-0.5">
                            {{-- Explanation of Similarities --}}
                            <div class="bg-emerald-50/60 border border-emerald-100/90 rounded-xl p-3 sm:p-3.5 space-y-1">
                                <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-900">
                                    <svg class="w-3.5 h-3.5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>Explanation of Similarities</span>
                                </div>
                                <p class="text-xs text-slate-700 leading-relaxed font-normal">
                                    {!! Str::markdown($item['similarity_explanation']) !!}
                                </p>
                            </div>

                            {{-- Important Differences --}}
                            <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3 sm:p-3.5 space-y-1">
                                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-800">
                                    <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                    <span>Important Differences</span>
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed font-normal">
                                    {!! Str::markdown($item['differences_explanation']) !!}
                                </p>
                            </div>
                        </div>

                        {{-- Cross-Field Scores Breakdown & Metadata --}}
                        <div class="pt-2 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 text-xs text-slate-500 flex-wrap">
                            {{-- Metadata --}}
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <span>Authors: <strong class="text-slate-700 font-semibold">{{ $item['research']->authors }}</strong></span>
                                <span>·</span>
                                <span>Year: <strong class="text-slate-700 font-semibold font-mono">{{ $item['research']->year }}</strong></span>
                                @if($item['research']->category)
                                    <span>·</span>
                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-200/60 rounded text-[11px] font-semibold">
                                        {{ $item['research']->category->name }}
                                    </span>
                                @endif
                            </div>

                            {{-- Field comparison scores summary --}}
                            <div class="flex items-center gap-1.5 text-[11px] text-slate-500 font-mono flex-wrap">
                                @foreach($item['field_evaluations'] as $fKey => $fData)
                                    <span title="{{ $fData['name'] }}: Combined {{ $fData['combined'] }}% (Semantic {{ $fData['semantic'] }}%, Textual {{ $fData['textual'] }}%)"
                                          class="px-2 py-0.5 rounded {{ $fKey === $item['best_field_key'] ? 'bg-emerald-100 text-emerald-900 font-bold border border-emerald-300' : 'bg-slate-100 text-slate-600' }}">
                                        {{ str_replace(['Research ', 'Project '], '', $fData['name']) }}: {{ $fData['combined'] }}%
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif(isset($results))
        <div class="bg-white rounded-2xl border border-slate-200/90 p-8 text-center space-y-2 shadow-xs">
            <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center mx-auto text-base font-bold">
                ✓
            </div>
            <h3 class="font-bold text-sm text-slate-900">No Similar Manuscripts Found</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed">
                High originality detected. Both textual keyword overlap and Sentence Transformer semantic analysis did not find overlapping research studies in the repository.
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
