@extends('layouts.admin')
@section('title', 'Admin Dashboard — JHCSC Capstone Portal')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    .admin-dashboard-body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #2d2b56;
    }

    .stat-card-admin {
        background: #ffffff;
        border-radius: 20px;
        border: 1.5px solid #e2e8f0;
        padding: 22px;
        transition: all 0.25s ease;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    }

    .stat-card-admin:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
        border-color: #cbd5e1;
    }

    .section-card-admin {
        background: #ffffff;
        border-radius: 24px;
        border: 1.5px solid #e2e8f0;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
    }

    .dashed-divider {
        border-bottom: 1.5px dashed #f1f5f9;
    }
</style>

<div class="admin-dashboard-body space-y-8 max-w-7xl mx-auto">
    {{-- TOP WELCOME HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-200/80">
        <div>
            <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-amber-100 text-amber-900 mb-2">
                👑 Administrator Portal
            </span>
            <h1 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 2rem;" class="font-extrabold text-[#2d2b56] tracking-tight">
                Admin <span class="text-[#0c4233]">Dashboard</span>
            </h1>
            <p class="text-slate-500 text-xs font-medium mt-1">
                Manage research submissions, category metadata, user accounts, and platform operations.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('admin.pending-registrations.index') }}" class="px-4 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 rounded-full font-extrabold text-xs transition inline-flex items-center gap-2">
                ⏳ Pending Registrations
            </a>
            <a href="{{ route('admin.researches.index') }}" class="px-5 py-2.5 bg-[#0c4233] hover:bg-[#082d23] text-white rounded-full font-extrabold text-xs transition inline-flex items-center gap-2 shadow-xs">
                📋 Manage Researches
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold flex items-center gap-3">
            <span class="text-base">✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold flex items-center gap-3">
            <span class="text-base">⚠️</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- STATS GRID (4 COLUMNS) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Card 1: Total Researches --}}
        <div class="stat-card-admin">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Total Researches</span>
                <span class="w-10 h-10 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center text-lg font-bold">📚</span>
            </div>
            <div class="mt-4 flex items-baseline justify-between">
                <p class="text-3xl font-extrabold text-[#2d2b56]">{{ number_format($totalResearches) }}</p>
                <span class="text-[11px] font-extrabold text-purple-700 bg-purple-100 px-2.5 py-0.5 rounded-full">All time</span>
            </div>
        </div>

        {{-- Card 2: Approved Researches --}}
        <div class="stat-card-admin">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Approved Papers</span>
                <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg font-bold">✅</span>
            </div>
            <div class="mt-4 flex items-baseline justify-between">
                <p class="text-3xl font-extrabold text-emerald-700">{{ number_format($approvedResearches) }}</p>
                <span class="text-[11px] font-extrabold text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-full">In Repository</span>
            </div>
        </div>

        {{-- Card 3: Pending Researches --}}
        <div class="stat-card-admin">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Pending Review</span>
                <span class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center text-lg font-bold">⏳</span>
            </div>
            <div class="mt-4 flex items-baseline justify-between">
                <p class="text-3xl font-extrabold text-amber-700">{{ number_format($pendingResearches) }}</p>
                <span class="text-[11px] font-extrabold text-amber-900 bg-amber-100 px-2.5 py-0.5 rounded-full">Action Needed</span>
            </div>
        </div>

        {{-- Card 4: Total Users --}}
        <div class="stat-card-admin">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Total Registered</span>
                <span class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-lg font-bold">👥</span>
            </div>
            <div class="mt-4 flex items-baseline justify-between">
                <p class="text-3xl font-extrabold text-indigo-900">{{ number_format($totalUsers) }}</p>
                <span class="text-[11px] font-extrabold text-indigo-700 bg-indigo-100 px-2.5 py-0.5 rounded-full">Accounts</span>
            </div>
        </div>
    </div>

    {{-- TRENDS & ANALYTICS SECTION (GRAPHS) --}}
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-200/80">
            <div>
                <h2 class="text-xl font-extrabold text-[#2d2b56] flex items-center gap-2">
                    <span>📈</span>
                    <span>Repository Trends & Analytics</span>
                </h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">
                    Visual insights on research submissions, topic popularity, category distribution, and readership metrics.
                </p>
            </div>
            <span class="text-[11px] font-extrabold text-[#0c4233] bg-emerald-50 border border-emerald-200/80 px-3.5 py-1.5 rounded-full shrink-0 inline-flex items-center gap-1.5 shadow-2xs">
                <span>⚡</span> Institutional Analytics
            </span>
        </div>

        {{-- ROW 1: Submission Trend & Category Distribution --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Graph 1: Monthly Submission & Approval Trend --}}
            <div class="section-card-admin space-y-4">
                <div class="flex items-center justify-between pb-3 dashed-divider">
                    <div>
                        <h3 class="text-sm font-extrabold text-[#2d2b56] uppercase tracking-wider flex items-center gap-2">
                            <span>📊</span>
                            <span>Submission & Approval Trend</span>
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Monthly rate of manuscript uploads and approved papers</p>
                    </div>
                    <span class="text-[10px] font-extrabold px-3 py-1 rounded-full bg-emerald-50 text-[#0c4233] border border-emerald-200/80 shrink-0">
                        6-Month Activity
                    </span>
                </div>

                <div class="relative w-full h-64 sm:h-72">
                    <canvas id="adminMonthlyTrendChart"></canvas>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-3 dashed-divider">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-center">
                        <span class="text-[10px] uppercase font-extrabold text-slate-400 block tracking-wider">Submissions</span>
                        <span class="text-lg font-extrabold text-[#0c4233] font-mono mt-0.5 block">
                            {{ number_format(array_sum($monthlySubmissions)) }}
                        </span>
                        <span class="text-[10px] text-slate-400">Past 6 months</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-center">
                        <span class="text-[10px] uppercase font-extrabold text-slate-400 block tracking-wider">Approved</span>
                        <span class="text-lg font-extrabold text-emerald-600 font-mono mt-0.5 block">
                            {{ number_format(array_sum($monthlyApprovals)) }}
                        </span>
                        <span class="text-[10px] text-slate-400">Past 6 months</span>
                    </div>
                </div>
            </div>

            {{-- Graph 2: Research Category Distribution --}}
            <div class="section-card-admin space-y-4">
                <div class="flex items-center justify-between pb-3 dashed-divider">
                    <div>
                        <h3 class="text-sm font-extrabold text-[#2d2b56] uppercase tracking-wider flex items-center gap-2">
                            <span>📁</span>
                            <span>Category Distribution</span>
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Approved capstones grouped by institutional discipline</p>
                    </div>
                    <span class="text-[10px] font-extrabold px-3 py-1 rounded-full bg-purple-50 text-purple-700 border border-purple-200/80 shrink-0">
                        Disciplines
                    </span>
                </div>

                @if(!empty($catLabels) && $totalApprovedCategorized > 0)
                    <div class="relative w-full h-64 sm:h-72 flex items-center justify-center">
                        <canvas id="adminCategoryChart"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-center">
                            <span class="text-2xl sm:text-3xl font-mono font-black text-[#2d2b56] leading-none">
                                {{ number_format($totalApprovedCategorized) }}
                            </span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Papers</span>
                        </div>
                    </div>

                    {{-- Category Legend Badges with Counts and Percentages --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-3 dashed-divider">
                        @foreach($categoryDistribution as $idx => $cat)
                            @php
                                $color = $catPalette[$idx % count($catPalette)];
                                $val = $cat->approved_researches_count ?? 0;
                                $pct = $totalApprovedCategorized > 0 ? round(($val / $totalApprovedCategorized) * 100) : 0;
                            @endphp
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-3 h-3 rounded-full shrink-0 shadow-2xs" style="background-color: {{ $color }};"></span>
                                    <span class="font-bold text-xs text-[#2d2b56] truncate" title="{{ $cat->name }}">{{ $cat->name }}</span>
                                </div>
                                <div class="text-right shrink-0 ml-2">
                                    <span class="font-mono text-xs font-extrabold text-[#2d2b56] block">{{ number_format($val) }}</span>
                                    <span class="font-mono text-[10px] text-slate-400 font-bold block">{{ $pct }}%</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-64 flex flex-col items-center justify-center text-center p-6 bg-slate-50 rounded-2xl border border-slate-100">
                        <span class="text-3xl mb-2">📂</span>
                        <p class="text-xs font-bold text-[#2d2b56]">No Categorized Researches Yet</p>
                        <p class="text-[11px] text-slate-400 mt-1">Categorized manuscripts will automatically populate this graph.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ROW 2: Top Read Capstones & Popular Search Topics --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Graph 3: Top Read Capstones (Readership Impact) --}}
            <div class="section-card-admin space-y-4">
                <div class="flex items-center justify-between pb-3 dashed-divider">
                    <div>
                        <h3 class="text-sm font-extrabold text-[#2d2b56] uppercase tracking-wider flex items-center gap-2">
                            <span>📖</span>
                            <span>Top Read Capstones</span>
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Approved manuscripts with highest student readership</p>
                    </div>
                    <span class="text-[10px] font-extrabold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/80 shrink-0">
                        Readership
                    </span>
                </div>

                @if($topStudies->count() > 0)
                    <div class="relative w-full h-64 sm:h-72">
                        <canvas id="adminTopReadChart"></canvas>
                    </div>

                    {{-- Ranked Clickable List --}}
                    <div class="space-y-2 pt-3 dashed-divider">
                        @foreach($topStudies as $idx => $study)
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between gap-3 text-xs transition hover:bg-slate-100/70">
                                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                    <span class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-mono font-bold shrink-0 {{ $idx === 0 ? 'bg-[#0c4233] text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-500' }}">
                                        #{{ $idx + 1 }}
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('admin.researches.show', $study->id) }}" class="font-bold text-xs text-[#2d2b56] hover:text-[#0c4233] hover:underline truncate block" title="{{ $study->title }}">
                                            {{ $study->title }}
                                        </a>
                                        <span class="text-[10px] text-slate-400 truncate block">{{ $study->category->name ?? 'Uncategorized' }}</span>
                                    </div>
                                </div>
                                <span class="font-mono font-extrabold text-xs text-[#0c4233] shrink-0 px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200/80">
                                    {{ number_format($study->views_count) }} views
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-64 flex flex-col items-center justify-center text-center p-6 bg-slate-50 rounded-2xl border border-slate-100">
                        <span class="text-3xl mb-2">📚</span>
                        <p class="text-xs font-bold text-[#2d2b56]">No Readership Data Yet</p>
                        <p class="text-[11px] text-slate-400 mt-1">Manuscripts with view counts will appear here.</p>
                    </div>
                @endif
            </div>

            {{-- Graph 4: Popular Search Queries --}}
            <div class="section-card-admin space-y-4">
                <div class="flex items-center justify-between pb-3 dashed-divider">
                    <div>
                        <h3 class="text-sm font-extrabold text-[#2d2b56] uppercase tracking-wider flex items-center gap-2">
                            <span>🔍</span>
                            <span>Popular Search Topics</span>
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Most frequent search terms logged by repository users</p>
                    </div>
                    <span class="text-[10px] font-extrabold px-3 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200/80 shrink-0">
                        Query Logs
                    </span>
                </div>

                @if(!empty($searchLabels))
                    <div class="relative w-full h-64 sm:h-72">
                        <canvas id="adminSearchChart"></canvas>
                    </div>

                    {{-- Search Pill Badges --}}
                    <div class="flex flex-wrap gap-2 pt-3 dashed-divider">
                        @foreach($mostSearched as $idx => $s)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-100 text-xs text-[#2d2b56] font-semibold">
                                <span class="text-slate-400 font-mono text-[10px]">#{{ $idx + 1 }}</span>
                                <span>{{ $s->query }}</span>
                                <span class="ml-1 px-1.5 py-0.5 rounded-md bg-amber-100 text-amber-900 font-mono text-[10px] font-bold">
                                    {{ number_format($s->total) }}
                                </span>
                            </span>
                        @endforeach
                    </div>
                @else
                    <div class="h-64 flex flex-col items-center justify-center text-center p-6 bg-slate-50 rounded-2xl border border-slate-100">
                        <span class="text-3xl mb-2">🔎</span>
                        <p class="text-xs font-bold text-[#2d2b56]">No Searches Logged Yet</p>
                        <p class="text-[11px] text-slate-400 mt-1">Repository search terms will be automatically charted here.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ADMINISTRATIVE WORKFLOW & QUICK ACCESS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <a href="{{ route('admin.researches.index') }}" class="section-card-admin p-5 hover:border-[#0c4233] transition group block text-decoration-none">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-[#0c4233] flex items-center justify-center text-lg">📄</div>
                <span class="text-xs text-slate-400 group-hover:text-[#0c4233] transition">➔</span>
            </div>
            <h4 class="font-extrabold text-sm text-[#2d2b56] group-hover:text-[#0c4233] transition">Research Papers</h4>
            <p class="text-xs text-slate-400 mt-1">Review, approve, or reject student manuscripts.</p>
        </a>

        <a href="{{ route('admin.pending-registrations.index') }}" class="section-card-admin p-5 hover:border-amber-400 transition group block text-decoration-none">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center text-lg">⏳</div>
                <span class="text-xs text-slate-400 group-hover:text-amber-700 transition">➔</span>
            </div>
            <h4 class="font-extrabold text-sm text-[#2d2b56] group-hover:text-amber-700 transition">User Registrations</h4>
            <p class="text-xs text-slate-400 mt-1">Verify student identities and approve access.</p>
        </a>

        <a href="{{ route('admin.categories.index') }}" class="section-card-admin p-5 hover:border-purple-400 transition group block text-decoration-none">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center text-lg">📁</div>
                <span class="text-xs text-slate-400 group-hover:text-purple-700 transition">➔</span>
            </div>
            <h4 class="font-extrabold text-sm text-[#2d2b56] group-hover:text-purple-700 transition">Categories</h4>
            <p class="text-xs text-slate-400 mt-1">Organize subject disciplines and project types.</p>
        </a>

        <a href="{{ route('admin.download-requests.index') }}" class="section-card-admin p-5 hover:border-indigo-400 transition group block text-decoration-none">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-lg">📥</div>
                <span class="text-xs text-slate-400 group-hover:text-indigo-700 transition">➔</span>
            </div>
            <h4 class="font-extrabold text-sm text-[#2d2b56] group-hover:text-indigo-700 transition">Download Requests</h4>
            <p class="text-xs text-slate-400 mt-1">Grant or deny manuscript download permissions.</p>
        </a>
    </div>

    {{-- SYSTEM AI & PLATFORM CAPABILITIES --}}
    <div class="section-card-admin space-y-4">
        <h3 class="font-extrabold text-base text-[#2d2b56] flex items-center gap-2 border-b border-slate-100 pb-3">
            <span>✨</span> Automated System Capabilities
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-center space-y-1">
                <span class="text-2xl block">🎯</span>
                <h4 class="font-extrabold text-xs text-[#2d2b56]">Title Duplication</h4>
                <p class="text-[10px] text-slate-400">Embeddings + similarity vector calculation</p>
            </div>
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-center space-y-1">
                <span class="text-2xl block">📊</span>
                <h4 class="font-extrabold text-xs text-[#2d2b56]">Title & Objectives</h4>
                <p class="text-[10px] text-slate-400">Multi-field semantic match index</p>
            </div>
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-center space-y-1">
                <span class="text-2xl block">🔍</span>
                <h4 class="font-extrabold text-xs text-[#2d2b56]">Semantic Search</h4>
                <p class="text-[10px] text-slate-400">Meaning-based paper retrieval</p>
            </div>
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-center space-y-1">
                <span class="text-2xl block">💡</span>
                <h4 class="font-extrabold text-xs text-[#2d2b56]">Recommendations</h4>
                <p class="text-[10px] text-slate-400">Automatic related capstone recommendations</p>
            </div>
        </div>
    </div>
</div>

{{-- CHART.JS INITIALIZATION FOR ADMIN ANALYTICS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') {
        console.warn('Chart.js not yet loaded');
        return;
    }

    const isDark = document.documentElement.classList.contains('dark') || 
                   document.body.classList.contains('dark-theme') || 
                   localStorage.getItem('admin_theme_mode') === 'dark' ||
                   localStorage.getItem('student_theme_mode') === 'dark' ||
                   document.documentElement.getAttribute('data-admin-theme') === 'dark' ||
                   document.documentElement.getAttribute('data-guest-theme') === 'dark';

    const textMuted = isDark ? '#94a3b8' : '#64748b';
    const textHeadings = isDark ? '#f8fafc' : '#2d2b56';
    const gridColor = isDark ? 'rgba(255, 255, 255, 0.06)' : 'rgba(0, 0, 0, 0.05)';

    // 1. MONTHLY SUBMISSION & APPROVAL TREND CHART
    const ctxTrend = document.getElementById('adminMonthlyTrendChart');
    if (ctxTrend) {
        const trendLabels = @json($months);
        const subData = @json($monthlySubmissions);
        const appData = @json($monthlyApprovals);

        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [
                    {
                        label: 'Submissions',
                        data: subData,
                        borderColor: '#0c4233',
                        backgroundColor: function(context) {
                            const {ctx, chartArea} = context.chart;
                            if (!chartArea) return 'rgba(12, 66, 51, 0.15)';
                            const g = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                            g.addColorStop(0, 'rgba(12, 66, 51, 0.35)');
                            g.addColorStop(1, 'rgba(12, 66, 51, 0.00)');
                            return g;
                        },
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#0c4233',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    },
                    {
                        label: 'Approved',
                        data: appData,
                        borderColor: '#10b981',
                        backgroundColor: function(context) {
                            const {ctx, chartArea} = context.chart;
                            if (!chartArea) return 'rgba(16, 185, 129, 0.15)';
                            const g = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                            g.addColorStop(0, 'rgba(16, 185, 129, 0.30)');
                            g.addColorStop(1, 'rgba(16, 185, 129, 0.00)');
                            return g;
                        },
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            boxWidth: 12,
                            boxHeight: 12,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            color: textHeadings,
                            font: { size: 11, weight: '700' }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#ffffff',
                        bodyColor: '#cbd5e1',
                        padding: 10,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    x: {
                        grid: { color: gridColor, drawBorder: false },
                        ticks: { color: textMuted, font: { size: 11, weight: '600' } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor, drawBorder: false },
                        ticks: {
                            precision: 0,
                            color: textMuted,
                            font: { size: 11, weight: '600' }
                        }
                    }
                }
            }
        });
    }

    // 2. CATEGORY DISTRIBUTION DOUGHNUT CHART
    const ctxCat = document.getElementById('adminCategoryChart');
    if (ctxCat) {
        const cLabels = @json($catLabels);
        const cValues = @json($catValues);
        const cPalette = @json($catPalette);
        const cColors = cPalette.slice(0, cLabels.length);

        if (cLabels.length > 0) {
            new Chart(ctxCat, {
                type: 'doughnut',
                data: {
                    labels: cLabels,
                    datasets: [{
                        data: cValues,
                        backgroundColor: cColors,
                        borderWidth: 2,
                        borderColor: isDark ? '#1e293b' : '#ffffff',
                        hoverOffset: 6
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
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(c) {
                                    const total = c.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = total > 0 ? Math.round((c.raw / total) * 100) : 0;
                                    return ` ${c.label}: ${c.raw} papers (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // 3. TOP READ CAPSTONES CHART
    const ctxTopRead = document.getElementById('adminTopReadChart');
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
                        borderRadius: 8,
                        barThickness: 20,
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
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                title: function(items) {
                                    const i = items[0].dataIndex;
                                    return tFullTitles[i] || items[0].label;
                                },
                                label: function(c) {
                                    return ' ' + c.raw + ' views';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: gridColor, drawBorder: false },
                            ticks: {
                                precision: 0,
                                color: textMuted,
                                font: { size: 11, weight: '600' }
                            }
                        },
                        y: {
                            grid: { display: false, drawBorder: false },
                            ticks: {
                                color: textHeadings,
                                font: { size: 11, weight: '700' }
                            }
                        }
                    }
                }
            });
        }
    }

    // 4. POPULAR SEARCH TOPICS CHART
    const ctxSearch = document.getElementById('adminSearchChart');
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
                        borderRadius: 8,
                        barThickness: 20,
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
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(c) {
                                    return ' ' + c.raw + ' searches';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: gridColor, drawBorder: false },
                            ticks: {
                                precision: 0,
                                color: textMuted,
                                font: { size: 11, weight: '600' }
                            }
                        },
                        y: {
                            grid: { display: false, drawBorder: false },
                            ticks: {
                                color: textHeadings,
                                font: { size: 11, weight: '700' }
                            }
                        }
                    }
                }
            });
        }
    }
});
</script>
@endsection
