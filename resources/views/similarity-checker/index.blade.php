@extends(auth()->check() ? (auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.app') : 'layouts.public')
@section('title', 'Similarity Checker — JHCSC Capstone Portal')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto pb-12">
    {{-- HEADER SECTION --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-slate-200">
        <div>
            @if(auth()->check() && auth()->user()->role === 'admin')
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Administration</span>
                    <span class="text-slate-300">/</span>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Similarity Checker</span>
                </div>
            @endif
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-1">Research Similarity Checker</h1>
            <p class="text-slate-500 text-xs font-medium mt-0.5">
                Screen proposed capstone titles, abstracts, or objectives against published manuscripts to guarantee originality.
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
    <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-xs space-y-4">
        <form method="POST" action="{{ route('similarity-checker.check') }}" class="space-y-4">
            @csrf
            
            <div class="space-y-2">
                <div class="flex items-center justify-between text-xs font-semibold">
                    <label for="query" class="text-slate-700">
                        Enter Title, Abstract, or Objectives
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
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-xs font-medium text-slate-900 outline-none focus:border-[#0c4233] focus:bg-white focus:ring-1 focus:ring-[#0c4233] transition leading-relaxed resize-y"
                        placeholder="Paste or type your proposed capstone title, background, or specific objectives here..."
                    >{{ old('query', $inputQuery ?? $inputTitle ?? $inputObjectives ?? '') }}</textarea>
                </div>

                {{-- Example Chips --}}
                <div class="flex items-center gap-2 flex-wrap pt-1">
                    <span class="text-[11px] font-bold text-slate-400">Sample topics:</span>
                    <button type="button" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-medium transition" onclick="fillExample('Research Repository System for Capstone Projects')">
                        Research Repository
                    </button>
                    <button type="button" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-medium transition" onclick="fillExample('Arduino-Based Flood Warning with SMS Notification')">
                        Flood Warning Alert
                    </button>
                    <button type="button" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-medium transition" onclick="fillExample('Decision Support System for Freshmen College Sectioning')">
                        College Sectioning DSS
                    </button>
                    <button type="button" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-medium transition" onclick="fillExample('Mobile Vehicle Booking and Reservation System')">
                        Vehicle Booking App
                    </button>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-slate-100">
                <span class="text-xs text-slate-400 font-medium">
                    Evaluates title, abstract, and objectives simultaneously using cosine vector similarity.
                </span>

                <button type="submit" 
                        class="px-5 py-2.5 bg-[#0c4233] hover:bg-[#082d23] text-white font-bold text-xs rounded-xl transition shadow-xs inline-flex items-center justify-center gap-2 shrink-0">
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
                <h2 class="text-sm font-bold text-slate-900">
                    Similarity Analysis Results
                </h2>
                <span class="px-2.5 py-1 bg-amber-50 text-amber-800 border border-amber-200 text-xs font-bold rounded-lg">
                    Found {{ $results->count() }} Similar {{ Str::plural('Manuscript', $results->count()) }}
                </span>
            </div>

            <div class="space-y-3">
                @foreach($results as $item)
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-xs hover:border-slate-300 transition space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                            <div class="space-y-1 flex-1">
                                <a href="{{ route('repository.show', $item['research']->id) }}" target="_blank" 
                                   class="text-sm font-bold text-slate-900 hover:text-emerald-700 transition leading-snug block">
                                    {{ $item['research']->title }}
                                </a>

                                @if(!empty($item['primary_match']))
                                    <div class="pt-0.5">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            Match: {{ $item['primary_match'] }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <a href="{{ route('repository.show', $item['research']->id) }}" target="_blank" 
                               class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition inline-flex items-center gap-1 shrink-0 w-fit">
                                View Manuscript →
                            </a>
                        </div>

                        <div class="flex items-center gap-3 text-xs text-slate-500 pt-2 border-t border-slate-100 flex-wrap">
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
