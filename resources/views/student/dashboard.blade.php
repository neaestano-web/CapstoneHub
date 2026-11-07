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
            @php
                $searchLabels = $mostSearched->pluck('query')->map(fn($q) => strlen($q) > 16 ? substr($q, 0, 16) . '…' : $q)->values();
                $searchValues = $mostSearched->pluck('total')->values();

                $topStudyLabels = $topStudies->pluck('title')->map(fn($t) => strlen($t) > 20 ? substr($t, 0, 20) . '…' : $t)->values();
                $topStudyFullTitles = $topStudies->pluck('title')->values();
                $topStudyViews = $topStudies->pluck('views_count')->values();

                $catLabels = $mostExploredCategories->pluck('name')->values();
                $catValues = $mostExploredCategories->pluck('search_logs_count')->values();
                $totalCatQueries = $mostExploredCategories->sum('search_logs_count') ?: 1;
                $catPalette = ['#10b981', '#06b6d4', '#8b5cf6', '#f59e0b', '#ec4899'];
            @endphp

            {{-- 1. Popular Search Topics (Interactive Chart) --}}
            <div class="sd-card p-5 space-y-3.5">
                <div class="flex items-center justify-between pb-2 sd-divider">
                    <h3 class="text-xs font-bold sd-title uppercase tracking-wider flex items-center gap-1.5">
                        <span>🔥</span>
                        <span>Popular Search Topics</span>
                    </h3>
                    <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/20">
                        Interactive Bar Chart
                    </span>
                </div>

                @if($mostSearched->count() > 0)
                    {{-- Chart.js Canvas --}}
                    <div class="relative w-full h-44">
                        <canvas id="popularSearchChart"></canvas>
                    </div>

                    {{-- Data Summary Badges --}}
                    <div class="flex flex-wrap gap-1.5 pt-1">
                        @foreach($mostSearched as $idx => $s)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold sd-card-subtle sd-title border sd-border" title="{{ $s->query }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                <span class="truncate max-w-[120px]">{{ $s->query }}</span>
                                <span class="font-mono font-bold sd-muted text-[10px]">({{ number_format($s->total) }})</span>
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs sd-muted text-center py-6">No searches logged yet.</p>
                @endif
            </div>

            {{-- 2. Top Read Capstones (Interactive Chart) --}}
            <div class="sd-card p-5 space-y-3.5">
                <div class="flex items-center justify-between pb-2 sd-divider">
                    <h3 class="text-xs font-bold sd-title uppercase tracking-wider flex items-center gap-1.5">
                        <span>📖</span>
                        <span>Top Read Capstones</span>
                    </h3>
                    <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20">
                        Readership Chart
                    </span>
                </div>

                @if($topStudies->count() > 0)
                    {{-- Chart.js Canvas --}}
                    <div class="relative w-full h-44">
                        <canvas id="topReadChart"></canvas>
                    </div>

                    {{-- Ranked Clickable List Underneath --}}
                    <div class="space-y-2 pt-1 border-t sd-divider">
                        @foreach($topStudies as $idx => $study)
                            <div class="flex items-center justify-between gap-2 text-xs py-1">
                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                    <span class="w-4 h-4 rounded-md flex items-center justify-center text-[10px] font-mono font-bold shrink-0 {{ $idx === 0 ? 'bg-[#0c4233] text-white' : 'sd-card-subtle sd-muted' }}">
                                        {{ $idx + 1 }}
                                    </span>
                                    <a href="{{ route('repository.show', $study->id) }}" class="font-semibold text-xs sd-title hover:underline truncate" title="{{ $study->title }}">
                                        {{ $study->title }}
                                    </a>
                                </div>
                                <span class="font-mono font-extrabold text-[11px] sd-accent shrink-0">
                                    {{ number_format($study->views_count) }} views
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs sd-muted text-center py-6">No studies published yet.</p>
                @endif
            </div>

            {{-- 3. Popular Categories (Doughnut Chart) --}}
            <div class="sd-card p-5 space-y-3.5">
                <div class="flex items-center justify-between pb-2 sd-divider">
                    <h3 class="text-xs font-bold sd-title uppercase tracking-wider flex items-center gap-1.5">
                        <span>📁</span>
                        <span>Popular Categories</span>
                    </h3>
                    <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-purple-500/10 text-purple-700 dark:text-purple-300 border border-purple-500/20">
                        Doughnut Chart
                    </span>
                </div>

                @if($mostExploredCategories->count() > 0)
                    {{-- Interactive Doughnut Chart --}}
                    <div class="relative w-full h-48 flex items-center justify-center">
                        <canvas id="popularCategoryChart"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-center">
                            <span class="text-lg font-mono font-black sd-title leading-none">{{ number_format($totalCatQueries) }}</span>
                            <span class="text-[10px] font-bold sd-muted uppercase tracking-wider mt-0.5">Queries</span>
                        </div>
                    </div>

                    {{-- Category Legend Badges with Percentages --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 pt-1">
                        @foreach($mostExploredCategories as $idx => $cat)
                            @php
                                $color = $catPalette[$idx % count($catPalette)];
                                $pct = round(($cat->search_logs_count / $totalCatQueries) * 100);
                            @endphp
                            <div class="flex items-center justify-between p-2 rounded-xl sd-card-subtle text-xs border sd-border">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0 shadow-xs" style="background-color: {{ $color }};"></span>
                                    <span class="font-medium text-xs sd-title truncate" title="{{ $cat->name }}">{{ $cat->name }}</span>
                                </div>
                                <span class="font-mono text-[10px] font-bold sd-muted shrink-0 ml-1">{{ $pct }}%</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs sd-muted text-center py-6">No category logs available.</p>
                @endif
            </div>
        </div>

        {{-- CHART.JS INITIALIZATION SCRIPTS --}}
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Chart === 'undefined') {
                console.warn('Chart.js not yet loaded');
                return;
            }

            const isDark = document.documentElement.classList.contains('dark') || 
                           document.body.classList.contains('dark-theme') || 
                           localStorage.getItem('capstone_theme_mode') === 'dark' ||
                           localStorage.getItem('capstone_theme_mode') === 'oled';

            const textMuted = isDark ? '#94a3b8' : '#64748b';
            const textHeadings = isDark ? '#f8fafc' : '#0f172a';
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.06)' : 'rgba(0, 0, 0, 0.04)';

            // 1. POPULAR SEARCH TOPICS CHART
            const ctxSearch = document.getElementById('popularSearchChart');
            if (ctxSearch) {
                const sLabels = @json($searchLabels);
                const sValues = @json($searchValues);
                if (sLabels.length > 0) {
                    new Chart(ctxSearch, {
                        type: 'bar',
                        data: {
                            labels: sLabels,
                            datasets: [{
                                data: sValues,
                                backgroundColor: function(context) {
                                    const {ctx, chartArea} = context.chart;
                                    if (!chartArea) return '#f59e0b';
                                    const g = ctx.createLinearGradient(chartArea.left, 0, chartArea.right, 0);
                                    g.addColorStop(0, '#f59e0b');
                                    g.addColorStop(1, '#10b981');
                                    return g;
                                },
                                borderRadius: 6,
                                barThickness: 15,
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#0f172a',
                                    titleColor: '#ffffff',
                                    bodyColor: '#cbd5e1',
                                    padding: 8,
                                    cornerRadius: 8,
                                    callbacks: {
                                        label: function(c) {
                                            return c.raw + ' searches';
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { color: gridColor, drawBorder: false },
                                    ticks: { color: textMuted, font: { size: 10, weight: '600' } }
                                },
                                y: {
                                    grid: { display: false, drawBorder: false },
                                    ticks: { color: textHeadings, font: { size: 11, weight: '600' } }
                                }
                            }
                        }
                    });
                }
            }

            // 2. TOP READ CAPSTONES CHART
            const ctxTopRead = document.getElementById('topReadChart');
            if (ctxTopRead) {
                const tLabels = @json($topStudyLabels);
                const tFullTitles = @json($topStudyFullTitles);
                const tViews = @json($topStudyViews);
                if (tLabels.length > 0) {
                    new Chart(ctxTopRead, {
                        type: 'bar',
                        data: {
                            labels: tLabels,
                            datasets: [{
                                data: tViews,
                                backgroundColor: function(context) {
                                    const {ctx, chartArea} = context.chart;
                                    if (!chartArea) return '#0c4233';
                                    const g = ctx.createLinearGradient(chartArea.left, 0, chartArea.right, 0);
                                    g.addColorStop(0, '#0c4233');
                                    g.addColorStop(1, '#00f5d4');
                                    return g;
                                },
                                borderRadius: 6,
                                barThickness: 15,
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#0f172a',
                                    titleColor: '#ffffff',
                                    bodyColor: '#cbd5e1',
                                    padding: 8,
                                    cornerRadius: 8,
                                    callbacks: {
                                        title: function(items) {
                                            const i = items[0].dataIndex;
                                            return tFullTitles[i] || items[0].label;
                                        },
                                        label: function(c) {
                                            return c.raw + ' views';
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { color: gridColor, drawBorder: false },
                                    ticks: { color: textMuted, font: { size: 10, weight: '600' } }
                                },
                                y: {
                                    grid: { display: false, drawBorder: false },
                                    ticks: { color: textHeadings, font: { size: 11, weight: '600' } }
                                }
                            }
                        }
                    });
                }
            }

            // 3. POPULAR CATEGORIES DOUGHNUT CHART
            const ctxCat = document.getElementById('popularCategoryChart');
            if (ctxCat) {
                const cLabels = @json($catLabels);
                const cValues = @json($catValues);
                if (cLabels.length > 0) {
                    new Chart(ctxCat, {
                        type: 'doughnut',
                        data: {
                            labels: cLabels,
                            datasets: [{
                                data: cValues,
                                backgroundColor: [
                                    '#10b981',
                                    '#06b6d4',
                                    '#8b5cf6',
                                    '#f59e0b',
                                    '#ec4899'
                                ],
                                borderWidth: 2,
                                borderColor: isDark ? '#1e293b' : '#ffffff',
                                hoverOffset: 6,
                                borderRadius: 4,
                                spacing: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '72%',
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#0f172a',
                                    titleColor: '#ffffff',
                                    bodyColor: '#cbd5e1',
                                    padding: 8,
                                    cornerRadius: 8,
                                    callbacks: {
                                        label: function(c) {
                                            const total = c.dataset.data.reduce((a, b) => a + b, 0);
                                            const p = Math.round((c.raw / (total || 1)) * 100);
                                            return ' ' + c.label + ': ' + c.raw + ' queries (' + p + '%)';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }
        });
        </script>
    </div>
</div>
@endsection
