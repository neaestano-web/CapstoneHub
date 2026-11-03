@extends('layouts.app')
@section('title', 'Student Dashboard — JHCSC Capstone Portal')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    /* ── THEME DESIGN SYSTEM (LIGHT & DARK ADAPTIVE) ── */
    :root, html[data-student-theme="light"] {
        --sd-canvas-bg: #ffffff;
        --sd-bg-surface: #ffffff;
        --sd-bg-subtle: #f8fafc;
        --sd-bg-card-hover: #ffffff;
        --sd-border: #e2e8f0;
        --sd-border-subtle: #f1f5f9;
        --sd-border-grad: linear-gradient(135deg, var(--guest-accent, rgba(0, 245, 212, 0.45)) 0%, rgba(6, 182, 212, 0.35) 40%, rgba(139, 92, 246, 0.25) 100%);
        --sd-text-headings: #0f172a;
        --sd-text-main: #334155;
        --sd-text-muted: #64748b;
        --sd-accent: var(--guest-accent, #059669);
        --sd-accent-cyan: var(--guest-accent, #0891b2);
        --sd-accent-glow: var(--guest-accent-glow, rgba(5, 150, 105, 0.18));
        --sd-accent-badge-bg: var(--guest-accent-glow, rgba(5, 150, 105, 0.1));
        --sd-card-shadow: 0 4px 20px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(15, 23, 42, 0.02);
        --sd-card-shadow-hover: 0 12px 30px rgba(15, 23, 42, 0.08);
        --sd-chart-grid: #f1f5f9;
        --sd-chart-line: var(--guest-accent, #0c4233);
        --sd-chart-fill-start: rgba(12, 66, 51, 0.22);
        --sd-chart-fill-end: rgba(12, 66, 51, 0.01);
        --sd-stat-purple-bg: #f5f3ff;
        --sd-stat-purple-text: #7c3aed;
        --sd-stat-emerald-bg: #ecfdf5;
        --sd-stat-emerald-text: #059669;
        --sd-stat-indigo-bg: #eef2ff;
        --sd-stat-indigo-text: #4f46e5;
    }

    html[data-student-theme="dark"] {
        --sd-canvas-bg: #0b1119;
        --sd-bg-surface: rgba(14, 23, 34, 0.94);
        --sd-bg-subtle: rgba(8, 14, 22, 0.85);
        --sd-bg-card-hover: rgba(18, 30, 44, 0.96);
        --sd-border: rgba(255, 255, 255, 0.12);
        --sd-border-subtle: rgba(255, 255, 255, 0.06);
        --sd-border-grad: linear-gradient(135deg, var(--guest-accent, #00f5d4) 0%, #06b6d4 35%, #8b5cf6 65%, #ef4444 100%);
        --sd-text-headings: #ffffff;
        --sd-text-main: #f8fafc;
        --sd-text-muted: #94a3b8;
        --sd-accent: var(--guest-accent, #00f5d4);
        --sd-accent-cyan: var(--guest-accent, #00f5d4);
        --sd-accent-glow: var(--guest-accent-glow, rgba(0, 245, 212, 0.35));
        --sd-accent-badge-bg: var(--guest-accent-glow, rgba(0, 245, 212, 0.12));
        --sd-card-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        --sd-card-shadow-hover: 0 16px 40px rgba(0, 0, 0, 0.7), 0 0 20px var(--guest-accent-glow, rgba(0, 245, 212, 0.15));
        --sd-chart-grid: rgba(255, 255, 255, 0.06);
        --sd-chart-line: var(--guest-accent, #00f5d4);
        --sd-chart-fill-start: rgba(0, 245, 212, 0.28);
        --sd-chart-fill-end: rgba(0, 245, 212, 0.01);
        --sd-stat-purple-bg: rgba(139, 92, 246, 0.14);
        --sd-stat-purple-text: #c084fc;
        --sd-stat-emerald-bg: rgba(16, 185, 129, 0.14);
        --sd-stat-emerald-text: #34d399;
        --sd-stat-indigo-bg: rgba(99, 102, 241, 0.14);
        --sd-stat-indigo-text: #818cf8;
    }

    /* Seamless Canvas Adaptation with Luminous Cyber Mesh Gradient */
    .student-main-canvas {
        border-radius: 0 !important;
    }

    html[data-student-theme="dark"] .student-main-canvas,
    html[data-guest-theme="dark"] .student-main-canvas {
        background-color: #0b1119 !important;
        background-image: 
            radial-gradient(circle at 50% 24%, rgba(16, 185, 129, 0.20) 0%, rgba(56, 189, 248, 0.10) 35%, transparent 65%),
            radial-gradient(circle at 16% 48%, rgba(16, 185, 129, 0.18) 0%, transparent 45%),
            radial-gradient(circle at 84% 48%, rgba(0, 245, 212, 0.12) 0%, transparent 45%),
            radial-gradient(circle at 50% 92%, rgba(56, 189, 248, 0.10) 0%, transparent 52%),
            radial-gradient(ellipse at 50% 45%, #152538 0%, #0b1119 100%) !important;
        background-attachment: fixed !important;
        border-radius: 0 !important;
        border: none !important;
        box-shadow: none !important;
    }

    .sd-root {
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        color: var(--sd-text-main);
        transition: color 0.25s ease;
    }

    /* ── HEADER BADGE & GREETING ── */
    .sd-badge-portal {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--sd-accent);
        background: var(--sd-accent-badge-bg);
        border: 1px solid var(--sd-accent-glow);
        padding: 4px 12px;
        border-radius: 9999px;
        box-shadow: 0 0 12px var(--sd-accent-glow);
    }

    .sd-accent-text {
        color: var(--sd-accent);
        transition: color 0.25s ease;
    }

    /* ── THEME SWITCHER TOGGLE PILL ── */
    .sd-theme-toggle {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--sd-bg-subtle);
        border: 1.5px solid var(--sd-border);
        color: var(--sd-text-headings);
        padding: 8px 16px;
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.22s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .sd-theme-toggle:hover {
        border-color: var(--sd-accent);
        transform: translateY(-1px);
        box-shadow: 0 4px 16px var(--sd-accent-glow);
    }

    .sd-btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--sd-accent);
        color: #ffffff;
        padding: 8px 20px;
        border-radius: 9999px;
        font-size: 0.82rem;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.22s ease;
        box-shadow: 0 4px 16px var(--sd-accent-glow);
    }

    html[data-student-theme="dark"] .sd-btn-primary {
        color: #042f2e;
    }

    .sd-btn-primary:hover {
        filter: brightness(1.1);
        transform: translateY(-1px);
        box-shadow: 0 6px 22px var(--sd-accent-glow);
    }

    .sd-btn-subtle {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--sd-bg-subtle);
        border: 1px solid var(--sd-border);
        color: var(--sd-text-main);
        padding: 8px 16px;
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .sd-btn-subtle:hover {
        border-color: var(--sd-accent);
        color: var(--sd-accent);
        transform: translateY(-1px);
    }

    /* ── STAT CARDS (CYBER-CLEAN GLASSMORPHISM) ── */
    .sd-stat-card {
        background: var(--sd-bg-surface);
        border: 1.5px solid var(--sd-border);
        border-radius: 22px;
        padding: 22px 24px;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: var(--sd-card-shadow);
        position: relative;
        overflow: hidden;
    }

    .sd-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--sd-border-grad);
        opacity: 0;
        transition: opacity 0.25s ease;
    }

    .sd-stat-card:hover {
        transform: translateY(-3px);
        border-color: rgba(0, 245, 212, 0.45);
        box-shadow: var(--sd-card-shadow-hover);
    }

    .sd-stat-card:hover::before {
        opacity: 1;
    }

    .sd-icon-box {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
        transition: transform 0.25s ease;
    }

    .sd-stat-card:hover .sd-icon-box {
        transform: scale(1.08) rotate(3deg);
    }

    /* ── SECTION CARDS ── */
    .sd-section-card {
        background: var(--sd-bg-surface);
        border: 1.5px solid var(--sd-border);
        border-radius: 24px;
        padding: 24px 26px;
        transition: all 0.25s ease;
        box-shadow: var(--sd-card-shadow);
    }

    .sd-section-card:hover {
        border-color: rgba(255, 255, 255, 0.18);
        box-shadow: var(--sd-card-shadow-hover);
    }

    html[data-guest-theme="dark"] .sd-stat-card,
    html[data-guest-theme="dark"] .sd-section-card,
    html[data-student-theme="dark"] .sd-stat-card,
    html[data-student-theme="dark"] .sd-section-card {
        background: linear-gradient(rgba(14, 23, 34, 0.88), rgba(14, 23, 34, 0.88)) padding-box,
                    linear-gradient(135deg, rgba(0, 245, 212, 0.45) 0%, rgba(6, 182, 212, 0.35) 40%, rgba(139, 92, 246, 0.25) 100%) border-box !important;
        border: 1.5px solid transparent !important;
        backdrop-filter: blur(16px) !important;
        -webkit-backdrop-filter: blur(16px) !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.45), 0 0 18px rgba(0, 245, 212, 0.12) !important;
    }

    .sd-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--sd-border-subtle);
        margin-bottom: 16px;
    }

    .sd-card-title {
        font-size: 1rem;
        font-weight: 800;
        color: var(--sd-text-headings);
        display: flex;
        align-items: center;
        gap: 8px;
        letter-spacing: -0.01em;
    }

    /* ── STATUS BADGES (MATCHING GUEST STANDARDS) ── */
    .badge-approved {
        background: rgba(16, 185, 129, 0.14);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.35);
        font-family: 'JetBrains Mono', monospace;
        font-weight: 800;
        font-size: 11px;
        padding: 4px 12px;
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .badge-pending {
        background: rgba(245, 158, 11, 0.14);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.35);
        font-family: 'JetBrains Mono', monospace;
        font-weight: 800;
        font-size: 11px;
        padding: 4px 12px;
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .badge-rejected {
        background: rgba(239, 68, 68, 0.14);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.35);
        font-family: 'JetBrains Mono', monospace;
        font-weight: 800;
        font-size: 11px;
        padding: 4px 12px;
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .badge-empty {
        background: var(--sd-bg-subtle);
        color: var(--sd-text-muted);
        border: 1px solid var(--sd-border);
        font-family: 'JetBrains Mono', monospace;
        font-weight: 700;
        font-size: 11px;
        padding: 4px 12px;
        border-radius: 50px;
    }

    /* ── RANKING PILLS & LIST ITEMS ── */
    .sd-rank-pill {
        width: 22px;
        height: 22px;
        border-radius: 6px;
        background: var(--sd-bg-subtle);
        border: 1px solid var(--sd-border);
        color: var(--sd-text-muted);
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.72rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .sd-rank-top {
        background: var(--sd-accent-badge-bg);
        border-color: var(--sd-accent-glow);
        color: var(--sd-accent);
    }

    .sd-list-divider {
        border-bottom: 1px dashed var(--sd-border-subtle);
        padding-bottom: 12px;
        margin-bottom: 12px;
    }

    .sd-list-divider:last-child {
        border-bottom: none;
        padding-bottom: 0;
        margin-bottom: 0;
    }

    /* Submissions List Item */
    .sd-project-item {
        background: var(--sd-bg-subtle);
        border: 1px solid var(--sd-border-subtle);
        border-radius: 18px;
        padding: 16px 20px;
        transition: all 0.22s ease;
    }

    .sd-project-item:hover {
        background: var(--sd-bg-card-hover);
        border-color: var(--sd-accent);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }
</style>

<div class="sd-root space-y-8 max-w-7xl mx-auto">
    {{-- TOP WELCOME HEADER --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b" style="border-color: var(--sd-border);">
        <div>
            <div class="flex items-center gap-3 mb-2 flex-wrap">
                <span class="sd-badge-portal">
                    🎓 Student Research Portal
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold" style="color: var(--sd-text-muted);">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Live Repository Session
                </span>
            </div>
            <h1 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 2.15rem;" class="font-extrabold tracking-tight" style="color: var(--sd-text-headings);">
                Welcome back, <span class="sd-accent-text">{{ Auth::user()->name }}</span> 👋
            </h1>
            <p class="text-xs font-medium mt-1" style="color: var(--sd-text-muted);">
                Overview of your manuscript engagement, submission status, and repository trends.
            </p>
        </div>

        {{-- HEADER CONTROLS (ACTIONS) --}}
        <div class="flex items-center gap-3 flex-wrap">
            {{-- Quick Shortcut: Similarity Check --}}
            <a href="{{ route('similarity-checker') }}" class="sd-btn-subtle">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Similarity Check</span>
            </a>

            {{-- Quick Action: Upload Manuscript --}}
            <a href="{{ route('student.researches.create') }}" class="sd-btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Upload Manuscript</span>
            </a>
        </div>
    </div>

    {{-- KEY METRICS SUMMARY ROW --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        {{-- Card 1: My Submission Status --}}
        <div class="sd-stat-card">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider" style="color: var(--sd-text-muted);">
                    My Submission Status
                </span>
                <div class="sd-icon-box" style="background: var(--sd-stat-purple-bg); color: var(--sd-stat-purple-text);">
                    📑
                </div>
            </div>
            <div class="mt-3 flex items-center justify-between gap-3">
                @php $mySubmission = $recentResearches->first(); @endphp
                @if($mySubmission)
                    <div class="min-w-0 flex-1">
                        <p class="font-extrabold text-sm truncate" style="color: var(--sd-text-headings);" title="{{ $mySubmission->title }}">
                            {{ $mySubmission->title }}
                        </p>
                        <p class="text-[11px] font-semibold mt-0.5" style="color: var(--sd-text-muted);">
                            Submitted {{ $mySubmission->created_at->format('M d, Y') }}
                        </p>
                    </div>
                    @if($mySubmission->status === 'approved')
                        <span class="badge-approved shrink-0">Approved</span>
                    @elseif($mySubmission->status === 'rejected')
                        <span class="badge-rejected shrink-0">⚠️ Rejected</span>
                    @else
                        <span class="badge-pending shrink-0">⏳ In Review</span>
                    @endif
                @else
                    <div>
                        <p class="font-extrabold text-sm" style="color: var(--sd-text-muted);">No Submission</p>
                        <p class="text-[11px] font-medium" style="color: var(--sd-text-muted);">Ready to upload paper</p>
                    </div>
                    <span class="badge-empty shrink-0">No Upload</span>
                @endif
            </div>
        </div>

        {{-- Card 2: Reader Engagement --}}
        <div class="sd-stat-card">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider" style="color: var(--sd-text-muted);">
                    My Manuscript Views
                </span>
                <div class="sd-icon-box" style="background: var(--sd-stat-emerald-bg); color: var(--sd-stat-emerald-text);">
                    👁️
                </div>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-2xl font-extrabold" style="color: var(--sd-accent);">
                    {{ number_format($myTotalViews ?? 0) }}
                </p>
                <span class="text-[11px] font-bold" style="color: var(--sd-text-muted);">
                    Total View
                </span>
            </div>
        </div>

        {{-- Card 3: Repository Catalog --}}
        <div class="sd-stat-card">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider" style="color: var(--sd-text-muted);">
                    Capstone Repository
                </span>
                <div class="sd-icon-box" style="background: var(--sd-stat-indigo-bg); color: var(--sd-stat-indigo-text);">
                    📚
                </div>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-2xl font-extrabold" style="color: var(--sd-text-headings);">
                    {{ number_format($analytics['approved_researches']) }}
                </p>
                <span class="text-[11px] font-bold" style="color: var(--sd-text-muted);">
                    Number of Studies
                </span>
            </div>
        </div>
    </div>

    {{-- MAIN 2-COLUMN GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- LEFT COLUMN: SUBMISSION DETAIL --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- MY RECENT SUBMISSION DETAIL CARD --}}
            <div class="sd-section-card space-y-4">
                <div class="sd-card-header">
                    <h3 class="sd-card-title">
                        <span>📑</span> My Capstone Project
                    </h3>
                    <a href="{{ route('student.researches.index') }}" class="text-xs font-extrabold hover:underline" style="color: var(--sd-accent);">
                        Upload Status →
                    </a>
                </div>

                @if($recentResearches->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentResearches as $research)
                            <div class="sd-project-item flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                                <div class="space-y-1 min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-full" style="background: var(--sd-stat-purple-bg); color: var(--sd-stat-purple-text);">
                                            {{ $research->category->name ?? 'Uncategorized' }}
                                        </span>
                                        @if($research->status === 'approved')
                                            <span class="badge-approved text-[10px] py-0 px-2">Approved</span>
                                        @elseif($research->status === 'rejected')
                                            <span class="badge-rejected text-[10px] py-0 px-2">⚠️ Needs Revision</span>
                                        @else
                                            <span class="badge-pending text-[10px] py-0 px-2">⏳ Pending Review</span>
                                        @endif
                                    </div>

                                    <h4 class="font-extrabold text-sm line-clamp-1" style="color: var(--sd-text-headings);">
                                        <a href="{{ route('student.researches.show', $research) }}" class="hover:underline transition">
                                            {{ $research->title }}
                                        </a>
                                    </h4>
                                    <p class="text-[11px]" style="color: var(--sd-text-muted);">
                                        Submitted {{ $research->created_at->format('M d, Y') }} · Authors: <strong>{{ $research->authors }}</strong>
                                    </p>
                                </div>

                                <a href="{{ route('student.researches.show', $research) }}" class="sd-btn-subtle shrink-0 text-xs">
                                    View Manuscript ➔
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-10 text-center space-y-3">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center font-bold text-2xl mx-auto" style="background: var(--sd-stat-purple-bg); color: var(--sd-stat-purple-text); border: 1px solid var(--sd-border);">
                            📂
                        </div>
                        <div>
                            <p class="text-sm font-bold" style="color: var(--sd-text-headings);">No capstone manuscript submitted yet</p>
                            <p class="text-xs mt-0.5" style="color: var(--sd-text-muted);">Share your research with the JHCSC academic community.</p>
                        </div>
                        <a href="{{ route('student.researches.create') }}" class="sd-btn-primary mt-2">
                            <span>Upload Your Manuscript</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT SIDEBAR: REPOSITORY TRENDS & SEARCHES --}}
        <div class="space-y-6">
            {{-- 🔥 TOP SEARCHED TOPICS --}}
            <div class="sd-section-card space-y-4">
                <div class="sd-card-header">
                    <h3 class="sd-card-title text-sm">
                        <span>🔥</span> Popular Search Topics
                    </h3>
                </div>
                <div class="space-y-2.5">
                    @forelse($mostSearched as $index => $search)
                        <div class="sd-list-divider flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="sd-rank-pill {{ $index === 0 ? 'sd-rank-top' : '' }}">
                                    #{{ $index + 1 }}
                                </span>
                                <span class="font-bold text-xs truncate" style="color: var(--sd-text-headings);">
                                    "{{ $search->query }}"
                                </span>
                            </div>
                            <span class="px-2.5 py-0.5 font-mono text-[10px] font-extrabold rounded-full shrink-0" style="background: var(--sd-stat-indigo-bg); color: var(--sd-stat-indigo-text);">
                                {{ number_format($search->total) }} searches
                            </span>
                        </div>
                    @empty
                        <div class="py-3 text-center text-xs" style="color: var(--sd-text-muted);">
                            No search logs recorded yet.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- 📖 TOP READ CAPSTONES --}}
            <div class="sd-section-card space-y-4">
                <div class="sd-card-header">
                    <h3 class="sd-card-title text-sm">
                        <span>📖</span> Top Read Capstones
                    </h3>
                </div>
                <div class="space-y-3">
                    @forelse($topStudies as $index => $study)
                        <div class="sd-list-divider space-y-1.5">
                            <div class="flex items-start gap-2">
                                <span class="sd-rank-pill {{ $index === 0 ? 'sd-rank-top' : '' }} mt-0.5">
                                    #{{ $index + 1 }}
                                </span>
                                <a href="{{ route('repository.show', $study->id) }}" class="font-extrabold text-xs line-clamp-2 hover:underline transition flex-1" style="color: var(--sd-text-headings);">
                                    {{ $study->title }}
                                </a>
                            </div>
                            <div class="flex items-center justify-between text-[10px] pl-7" style="color: var(--sd-text-muted);">
                                <span>{{ $study->category->name ?? 'Uncategorized' }}</span>
                                <span class="font-extrabold px-2 py-0.5 rounded-full" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
                                    👁️ {{ number_format($study->views_count) }} views
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="py-3 text-center text-xs" style="color: var(--sd-text-muted);">
                            No published studies available.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- 📁 MOST EXPLORED CATEGORIES --}}
            <div class="sd-section-card space-y-4">
                <div class="sd-card-header">
                    <h3 class="sd-card-title text-sm">
                        <span>📁</span> Popular Categories
                    </h3>
                </div>
                <div class="space-y-2">
                    @forelse($mostExploredCategories as $cat)
                        <div class="flex items-center justify-between p-2.5 rounded-xl border transition hover:translate-x-1" style="background: var(--sd-bg-subtle); border-color: var(--sd-border-subtle);">
                            <span class="font-bold text-xs truncate" style="color: var(--sd-text-headings);">
                                {{ $cat->name }}
                            </span>
                            <span class="font-mono text-[10px] font-extrabold px-2.5 py-0.5 rounded-full shrink-0" style="background: var(--sd-stat-purple-bg); color: var(--sd-stat-purple-text);">
                                {{ number_format($cat->search_logs_count) }} queries
                            </span>
                        </div>
                    @empty
                        <div class="text-xs text-center py-3" style="color: var(--sd-text-muted);">
                            No category logs available.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- THEME SWITCHER & CHART SCRIPT --}}
<script>
    let studentChartInstance = null;
    const chartMonths = {!! json_encode($months ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) !!};
    const chartViewsData = {!! json_encode($monthlyViewsData ?? [0, 0, 0, 0, 0, 0]) !!};

    function initOrUpdateChart(theme) {
        const currentTheme = theme || document.documentElement.getAttribute('data-guest-theme') || 'dark';
        const isDark = currentTheme === 'dark' || currentTheme === 'oled';
        const ctx = document.getElementById('capstoneAnalyticsChart');
        if (!ctx) return;

        const activeAccent = getComputedStyle(document.documentElement).getPropertyValue('--guest-accent').trim() || (isDark ? '#00f5d4' : '#059669');
        const activeGlow = getComputedStyle(document.documentElement).getPropertyValue('--guest-accent-glow').trim() || (isDark ? 'rgba(0, 245, 212, 0.15)' : 'rgba(5, 150, 105, 0.12)');
        const lineColor = activeAccent;
        const fillColor = isDark ? (activeGlow.indexOf('rgba') !== -1 ? activeGlow.replace(/0\.\d+\)/, '0.15)') : 'rgba(0, 245, 212, 0.12)') : 'rgba(5, 150, 105, 0.08)';
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.06)' : '#f1f5f9';
        const tickColor = isDark ? '#94a3b8' : '#64748b';

        if (studentChartInstance) {
            studentChartInstance.data.datasets[0].borderColor = lineColor;
            studentChartInstance.data.datasets[0].backgroundColor = fillColor;
            studentChartInstance.data.datasets[0].pointBackgroundColor = lineColor;
            studentChartInstance.options.scales.x.ticks.color = tickColor;
            studentChartInstance.options.scales.y.ticks.color = tickColor;
            studentChartInstance.options.scales.y.grid.color = gridColor;
            studentChartInstance.update();
            return;
        }

        studentChartInstance = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: chartMonths,
                datasets: [{
                    label: 'My Manuscript Views',
                    data: chartViewsData,
                    borderColor: lineColor,
                    backgroundColor: fillColor,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2.5,
                    pointBackgroundColor: lineColor,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDark ? 'rgba(15, 23, 42, 0.95)' : 'rgba(12, 66, 51, 0.95)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        padding: 10,
                        cornerRadius: 10,
                        displayColors: false
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: tickColor, font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: '600' } }
                    },
                    y: {
                        grid: { color: gridColor },
                        ticks: { color: tickColor, font: { family: "'JetBrains Mono', monospace", size: 11, weight: '600' }, precision: 0 },
                        beginAtZero: true
                    }
                }
            }
        });
    }

    function applyStudentTheme(theme) {
        document.documentElement.setAttribute('data-student-theme', theme);
        document.documentElement.setAttribute('data-guest-theme', theme);

        const icon = document.getElementById('studentThemeIcon');
        const text = document.getElementById('studentThemeText');
        if (icon && text) {
            if (theme === 'dark') {
                icon.textContent = '🌙';
                text.textContent = 'Cyber Dark';
            } else if (theme === 'sepia') {
                icon.textContent = '📜';
                text.textContent = 'Warm Sepia';
            } else if (theme === 'oled') {
                icon.textContent = '🖤';
                text.textContent = 'OLED Black';
            } else if (theme === 'custom') {
                icon.textContent = '🎨';
                text.textContent = 'Custom Canvas';
            } else {
                icon.textContent = '☀️';
                text.textContent = 'Light Mode';
            }
        }

        initOrUpdateChart(theme);
    }

    function toggleStudentTheme() {
        if (window.openGuestThemeModal) {
            window.openGuestThemeModal();
        } else if (window.selectThemeMode) {
            const current = document.documentElement.getAttribute('data-guest-theme') || 'dark';
            window.selectThemeMode(current === 'dark' ? 'light' : 'dark');
        }
    }

    window.addEventListener('studentThemeChanged', function(e) {
        if (e.detail && e.detail.theme) {
            applyStudentTheme(e.detail.theme);
        }
        initOrUpdateChart(e.detail ? e.detail.theme : null);
    });

    document.addEventListener('DOMContentLoaded', function() {
        try {
            const raw = localStorage.getItem('capstone_guest_display_theme');
            if (raw) {
                const s = JSON.parse(raw);
                if (s.mode) {
                    applyStudentTheme(s.mode);
                    return;
                }
            }
        } catch(e) {}
        const savedTheme = localStorage.getItem('student_theme_mode') || 'dark';
        applyStudentTheme(savedTheme);
    });
</script>
@endsection
