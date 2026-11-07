@extends('layouts.app')
@section('title', 'Student Dashboard — JHCSC Capstone Portal')

@section('content')
<style>
    .sd-card {
        background-color: var(--guest-bg-surface, #ffffff);
        border: 1px solid var(--guest-border, #e2e8f0);
        border-radius: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }
    .sd-card-subtle {
        background-color: var(--guest-bg-input, #f8fafc);
        border: 1px solid var(--guest-border, #e2e8f0);
        border-radius: 0.75rem;
    }
    .sd-title {
        color: var(--guest-text-headings, #0f172a) !important;
    }
    .sd-text {
        color: var(--guest-text-main, #334155) !important;
    }
    .sd-muted {
        color: var(--guest-text-muted, #64748b) !important;
    }
    .sd-border {
        border-color: var(--guest-border, #e2e8f0) !important;
    }
    .sd-accent {
        color: var(--guest-accent, #059669) !important;
    }
    .sd-divider {
        border-bottom: 1px solid var(--guest-border, #f1f5f9);
    }
</style>

<div class="space-y-6 max-w-7xl mx-auto pb-12">
    {{-- TOP WELCOME HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-5 border-b sd-border">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold uppercase tracking-wider sd-muted">Student Portal</span>
                <span class="sd-muted opacity-50">/</span>
                <span class="text-xs font-bold uppercase tracking-wider sd-accent">Dashboard</span>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight sd-title">
                Welcome back, {{ Auth::user()->name }} 👋
            </h1>
            <p class="text-xs font-medium mt-0.5 sd-muted">
                Overview of your submission status, manuscript engagement, and repository trends.
            </p>
        </div>

        {{-- HEADER ACTIONS --}}
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('similarity-checker') }}" 
               class="px-3.5 py-2 sd-card-subtle sd-text hover:opacity-85 font-bold text-xs rounded-xl transition inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 sd-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <span>Similarity Checker</span>
            </a>

            <a href="{{ route('student.researches.create') }}" 
               class="px-4 py-2 bg-[#0c4233] hover:bg-[#082d23] text-white font-bold text-xs rounded-xl transition shadow-xs inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Upload Manuscript</span>
            </a>
        </div>
    </div>

    {{-- KEY METRICS SUMMARY ROW --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {{-- Card 1: My Submission Status --}}
        <div class="sd-card p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs font-bold uppercase tracking-wider sd-muted">
                <span>My Submission</span>
                <span class="text-base">📑</span>
            </div>
            <div class="mt-3">
                @php $mySubmission = $recentResearches->first(); @endphp
                @if($mySubmission)
                    <div class="space-y-1">
                        <p class="font-bold text-xs sd-title line-clamp-1" title="{{ $mySubmission->title }}">
                            {{ $mySubmission->title }}
                        </p>
                        <div class="flex items-center justify-between pt-1">
                            <span class="text-[11px] sd-muted font-medium">
                                Submitted {{ $mySubmission->created_at->format('M d, Y') }}
                            </span>
                            @if($mySubmission->status === 'approved')
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-200/80 rounded-full text-[10px] font-bold">Approved</span>
                            @elseif($mySubmission->status === 'rejected')
                                <span class="px-2 py-0.5 bg-rose-50 text-rose-800 border border-rose-200/80 rounded-full text-[10px] font-bold">Needs Revision</span>
                            @else
                                <span class="px-2 py-0.5 bg-amber-50 text-amber-800 border border-amber-200/80 rounded-full text-[10px] font-bold">In Review</span>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-bold text-xs sd-text">No Submission</p>
                            <p class="text-[11px] sd-muted">Ready to upload paper</p>
                        </div>
                        <span class="px-2 py-0.5 sd-card-subtle sd-muted rounded-full text-[10px] font-bold">No Upload</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Card 2: Reader Engagement (My Manuscript Views) --}}
        <div class="sd-card p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs font-bold uppercase tracking-wider sd-muted">
                <span>Manuscript Views</span>
                <span class="text-base">👁️</span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-2xl font-extrabold sd-accent tracking-tight font-mono">
                    {{ number_format($myTotalViews ?? 0) }}
                </p>
                <span class="text-[11px] sd-muted font-medium">Total views</span>
            </div>
        </div>

        {{-- Card 3: Repository Catalog --}}
        <div class="sd-card p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs font-bold uppercase tracking-wider sd-muted">
                <span>Repository Catalog</span>
                <span class="text-base">📚</span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-2xl font-extrabold sd-title tracking-tight font-mono">
                    {{ number_format($analytics['approved_researches']) }}
                </p>
                <span class="text-[11px] sd-muted font-medium">Approved studies</span>
            </div>
        </div>
    </div>

    {{-- MAIN 2-COLUMN GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- LEFT COLUMN: MY RECENT SUBMISSIONS --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="sd-card p-5 sm:p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 sd-divider">
                    <h2 class="text-sm font-bold sd-title flex items-center gap-2">
                        <span>📑</span>
                        <span>My Capstone Project</span>
                    </h2>
                    <a href="{{ route('student.researches.index') }}" class="text-xs font-bold sd-accent hover:underline">
                        View All Submissions →
                    </a>
                </div>

                @if($recentResearches->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentResearches as $research)
                            <div class="p-4 sd-card-subtle flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                                <div class="space-y-1.5 min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        @if($research->category)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                                                {{ $research->category->name }}
                                            </span>
                                        @endif
                                        @if($research->status === 'approved')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/80">Approved</span>
                                        @elseif($research->status === 'rejected')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-800 border border-rose-200/80">Needs Revision</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200/80">In Review</span>
                                        @endif
                                    </div>

                                    <h3 class="font-bold text-xs sd-title line-clamp-1">
                                        <a href="{{ route('student.researches.show', $research) }}" class="hover:underline transition">
                                            {{ $research->title }}
                                        </a>
                                    </h3>

                                    <div class="flex items-center gap-3 text-[11px] sd-muted flex-wrap">
                                        <span>Submitted: <strong class="sd-text font-medium">{{ $research->created_at->format('M d, Y') }}</strong></span>
                                        <span>·</span>
                                        <span>Authors: <strong class="sd-text font-medium">{{ $research->authors }}</strong></span>
                                    </div>
                                </div>

                                <a href="{{ route('student.researches.show', $research) }}" 
                                   class="px-3 py-1.5 sd-card sd-text hover:opacity-85 font-bold text-xs rounded-lg transition inline-flex items-center gap-1 shrink-0 w-fit">
                                    View Manuscript →
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-12 text-center space-y-3">
                        <div class="w-10 h-10 rounded-full sd-card-subtle sd-muted flex items-center justify-center mx-auto text-base">
                            📂
                        </div>
                        <div>
                            <h3 class="font-bold text-xs sd-title">No Capstone Manuscript Submitted Yet</h3>
                            <p class="text-[11px] sd-muted mt-0.5 max-w-sm mx-auto">
                                Share your capstone research paper with the institutional repository catalog.
                            </p>
                        </div>
                        <a href="{{ route('student.researches.create') }}" 
                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#0c4233] hover:bg-[#082d23] text-white font-bold text-xs rounded-xl transition shadow-xs">
                            <span>Upload Your Manuscript</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT COLUMN: REPOSITORY TRENDS --}}
        <div class="space-y-4">
            {{-- Popular Search Topics (Bar Graph) --}}
            <div class="sd-card p-5 space-y-3.5">
                <div class="flex items-center justify-between pb-2 sd-divider">
                    <h3 class="text-xs font-bold sd-title uppercase tracking-wider flex items-center gap-1.5">
                        <span>🔥</span>
                        <span>Popular Search Topics</span>
                    </h3>
                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/20">
                        Search Graph
                    </span>
                </div>

                @php
                    $maxSearchTotal = $mostSearched->max('total') ?: 1;
                @endphp

                <div class="space-y-3">
                    @forelse($mostSearched as $index => $search)
                        @php
                            $searchPct = round(($search->total / $maxSearchTotal) * 100);
                        @endphp
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between gap-2 text-xs">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] font-mono font-bold shrink-0 {{ $index === 0 ? 'bg-amber-500 text-white shadow-xs' : 'sd-card-subtle sd-muted' }}">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="font-semibold text-xs sd-title truncate" title="{{ $search->query }}">
                                        "{{ $search->query }}"
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span class="font-mono text-[11px] font-extrabold sd-title">
                                        {{ number_format($search->total) }}
                                    </span>
                                    <span class="text-[10px] sd-muted">searches</span>
                                </div>
                            </div>

                            {{-- Graph Bar --}}
                            <div class="h-2 w-full rounded-full bg-slate-100 dark:bg-white/10 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500 {{ $index === 0 ? 'bg-gradient-to-r from-amber-500 to-amber-400' : 'bg-gradient-to-r from-emerald-600 to-teal-400' }}"
                                     style="width: {{ max(6, $searchPct) }}%;"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs sd-muted text-center py-4">No searches logged yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Top Read Capstones (Readership Bar Graph) --}}
            <div class="sd-card p-5 space-y-3.5">
                <div class="flex items-center justify-between pb-2 sd-divider">
                    <h3 class="text-xs font-bold sd-title uppercase tracking-wider flex items-center gap-1.5">
                        <span>📖</span>
                        <span>Top Read Capstones</span>
                    </h3>
                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20">
                        Readership Graph
                    </span>
                </div>

                @php
                    $maxStudyViews = $topStudies->max('views_count') ?: 1;
                @endphp

                <div class="space-y-3">
                    @forelse($topStudies as $index => $study)
                        @php
                            $viewsPct = round(($study->views_count / $maxStudyViews) * 100);
                        @endphp
                        <div class="space-y-1.5">
                            <div class="flex items-start justify-between gap-2 text-xs">
                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                    <span class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] font-mono font-bold shrink-0 {{ $index === 0 ? 'bg-[#0c4233] text-white shadow-xs' : 'sd-card-subtle sd-muted' }}">
                                        {{ $index + 1 }}
                                    </span>
                                    <a href="{{ route('repository.show', $study->id) }}" class="font-bold text-xs sd-title hover:underline line-clamp-1 truncate" title="{{ $study->title }}">
                                        {{ $study->title }}
                                    </a>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <span class="font-mono text-[11px] font-extrabold sd-accent">
                                        {{ number_format($study->views_count) }}
                                    </span>
                                    <span class="text-[10px] sd-muted">views</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-[10px] sd-muted pl-7">
                                <span class="truncate">{{ $study->category->name ?? 'Uncategorized' }}</span>
                                <span class="font-mono text-[9px] sd-muted font-bold">{{ $viewsPct }}% of peak</span>
                            </div>

                            {{-- Graph Bar --}}
                            <div class="h-2 w-full rounded-full bg-slate-100 dark:bg-white/10 overflow-hidden pl-7">
                                <div class="h-full rounded-full transition-all duration-500 {{ $index === 0 ? 'bg-gradient-to-r from-emerald-600 to-teal-400' : 'bg-gradient-to-r from-slate-400 to-slate-300 dark:from-slate-500 dark:to-slate-600' }}"
                                     style="width: {{ max(6, $viewsPct) }}%;"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs sd-muted text-center py-4">No studies published yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Popular Categories (Distribution Graph) --}}
            <div class="sd-card p-5 space-y-3.5">
                <div class="flex items-center justify-between pb-2 sd-divider">
                    <h3 class="text-xs font-bold sd-title uppercase tracking-wider flex items-center gap-1.5">
                        <span>📁</span>
                        <span>Popular Categories</span>
                    </h3>
                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-purple-500/10 text-purple-700 dark:text-purple-300 border border-purple-500/20">
                        Category Graph
                    </span>
                </div>

                @php
                    $totalCatQueries = $mostExploredCategories->sum('search_logs_count') ?: 1;
                    $catColors = ['#059669', '#3b82f6', '#8b5cf6', '#f59e0b', '#ec4899'];
                @endphp

                @if($mostExploredCategories->count() > 0)
                    {{-- Segmented Distribution Bar Graph --}}
                    <div class="space-y-1.5">
                        <div class="h-3 w-full rounded-full bg-slate-100 dark:bg-white/10 overflow-hidden flex gap-0.5 p-0.5">
                            @foreach($mostExploredCategories as $index => $cat)
                                @php
                                    $slicePct = max(3, round(($cat->search_logs_count / $totalCatQueries) * 100));
                                    $color = $catColors[$index % count($catColors)];
                                @endphp
                                <div class="h-full rounded-sm transition-all duration-500 first:rounded-l-full last:rounded-r-full" 
                                     style="width: {{ $slicePct }}%; background-color: {{ $color }};" 
                                     title="{{ $cat->name }}: {{ number_format($cat->search_logs_count) }} queries ({{ $slicePct }}%)"></div>
                            @endforeach
                        </div>
                        <div class="flex justify-between items-center text-[10px] sd-muted px-0.5">
                            <span>Category Share</span>
                            <span class="font-mono font-bold">{{ number_format($totalCatQueries) }} total queries</span>
                        </div>
                    </div>

                    {{-- Individual Category Progress Bars --}}
                    <div class="space-y-2.5 pt-1">
                        @foreach($mostExploredCategories as $index => $cat)
                            @php
                                $catPct = round(($cat->search_logs_count / $totalCatQueries) * 100);
                                $color = $catColors[$index % count($catColors)];
                            @endphp
                            <div class="space-y-1">
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-1.5 min-w-0">
                                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $color }};"></span>
                                        <span class="font-medium text-xs sd-title truncate" title="{{ $cat->name }}">
                                            {{ $cat->name }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <span class="font-mono text-[11px] font-bold sd-title">
                                            {{ number_format($cat->search_logs_count) }}
                                        </span>
                                        <span class="font-mono text-[10px] sd-muted font-bold">({{ $catPct }}%)</span>
                                    </div>
                                </div>
                                <div class="h-1.5 w-full rounded-full bg-slate-100 dark:bg-white/10 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500" 
                                         style="width: {{ max(4, $catPct) }}%; background-color: {{ $color }};"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs sd-muted text-center py-4">No category logs available.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
