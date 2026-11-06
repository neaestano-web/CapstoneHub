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

    {{-- SYSTEM MAINTENANCE / FRESH START (DATA RESET) --}}
    <div class="section-card-admin p-6 border border-rose-200 bg-rose-50/30 rounded-3xl space-y-4">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="text-xl">⚠️</span>
                    <h3 class="font-extrabold text-base text-rose-950">Repository Fresh Start (System Wipe)</h3>
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-rose-100 text-rose-700 border border-rose-200 uppercase tracking-wide">Danger Zone</span>
                </div>
                <p class="text-xs text-rose-700/80 leading-relaxed max-w-2xl">
                    Permanently deletes all research projects, uploaded manuscripts, vector embeddings, download requests, and student accounts so you can start from scratch.
                    <span class="font-bold underline text-rose-950">Administrator accounts, categories, and project types will remain intact.</span>
                </p>
            </div>
            <form action="{{ route('admin.system.wipe-data') }}" method="POST" onsubmit="return confirm('⚠️ DANGER: ARE YOU ABSOLUTELY SURE?\n\nThis action will permanently delete:\n- All research projects & manuscripts\n- All student / non-admin user accounts\n- All download requests and embeddings\n\nAdministrator accounts and categories will remain safe.\n\nDo you want to proceed?');">
                @csrf
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-200 hover:shadow-lg transition flex items-center gap-2 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    <span>Wipe Data (Keep Admins Only)</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
