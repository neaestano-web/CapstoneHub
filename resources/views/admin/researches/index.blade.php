@extends('layouts.admin')
@section('title', 'Manage Researches — JHCSC Capstone Portal')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    .admin-researches-body {
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        color: #1e293b;
    }

    /* ===== FIXED / STATIONARY TOP HEADER DECK ===== */
    .fixed-admin-researches-header {
        position: sticky;
        top: 0;
        z-index: 40;
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        margin-top: -36px;
        margin-left: -40px;
        margin-right: -40px;
        padding: 18px 40px 14px;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04);
        margin-bottom: 24px;
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }

    @media (max-width: 768px) {
        .fixed-admin-researches-header {
            margin-top: -24px;
            margin-left: -20px;
            margin-right: -20px;
            padding: 14px 20px 12px;
        }
    }

    /* Fixed Deck Action Buttons */
    .min-btn-upload {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 16px;
        font-size: 0.8125rem;
        font-weight: 700;
        color: #ffffff;
        background: #0c4233;
        border-radius: 10px;
        text-decoration: none;
        transition: all 0.15s ease;
        box-shadow: 0 1px 2px rgba(12, 66, 51, 0.15);
        white-space: nowrap;
    }

    .min-btn-upload:hover {
        background: #082d23;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(12, 66, 51, 0.25);
        color: #ffffff;
    }

    /* Minimal Filter Pills */
    .min-filter-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.15s ease;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #64748b;
        white-space: nowrap;
    }

    .min-filter-pill:hover {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #cbd5e1;
    }

    .min-filter-pill.active {
        background: #0c4233;
        color: #ffffff;
        border-color: #0c4233;
        box-shadow: 0 2px 8px rgba(12, 66, 51, 0.2);
    }

    .min-filter-count {
        font-size: 0.6875rem;
        font-weight: 700;
        padding: 1px 6px;
        border-radius: 9999px;
        background: rgba(0, 0, 0, 0.08);
    }

    .min-filter-pill.active .min-filter-count {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    /* Fixed Search Bar */
    .min-search-form {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        max-width: 320px;
    }

    .min-search-box {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 6px 12px;
        flex: 1;
        transition: all 0.15s ease;
    }

    .min-search-box:focus-within {
        background: #ffffff;
        border-color: #0c4233;
        box-shadow: 0 0 0 3px rgba(12, 66, 51, 0.1);
    }

    .min-search-input {
        background: transparent;
        border: none;
        outline: none;
        font-size: 0.8125rem;
        font-weight: 500;
        color: #0f172a;
        width: 100%;
    }

    .min-search-input::placeholder {
        color: #94a3b8;
    }

    .min-search-btn {
        padding: 6px 14px;
        font-size: 0.75rem;
        font-weight: 700;
        color: #334155;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.15s ease;
        white-space: nowrap;
    }

    .min-search-btn:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    /* Minimal Table Card */
    .min-table-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    }

    .min-table th {
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 18px;
    }

    .min-table td {
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.8125rem;
        color: #334155;
        vertical-align: middle;
    }

    .min-table tr:last-child td {
        border-bottom: none;
    }

    .min-table tr:hover td {
        background: #f8fafc;
    }

    /* Minimal Action Buttons */
    .btn-action-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        font-size: 0.6875rem;
        font-weight: 700;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.15s ease;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .btn-action-view {
        background: #f1f5f9;
        color: #334155;
        border-color: #e2e8f0;
    }
    .btn-action-view:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .btn-action-approve {
        background: #ecfdf5;
        color: #065f46;
        border-color: #a7f3d0;
    }
    .btn-action-approve:hover {
        background: #d1fae5;
        color: #047857;
    }

    .btn-action-reject {
        background: #fffbeb;
        color: #92400e;
        border-color: #fde68a;
    }
    .btn-action-reject:hover {
        background: #fef3c7;
        color: #78350f;
    }

    .btn-action-delete {
        background: #fef2f2;
        color: #991b1b;
        border-color: #fecaca;
    }
    .btn-action-delete:hover {
        background: #fee2e2;
        color: #7f1d1d;
    }

    /* Modal Overlay */
    .modal-backdrop-custom {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        z-index: 1000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .modal-backdrop-custom.active {
        display: flex;
    }

    /* ===== DARK THEME OVERRIDES ===== */
    html[data-guest-theme="dark"] .fixed-admin-researches-header,
    html[data-guest-theme="oled"] .fixed-admin-researches-header,
    html[data-admin-theme="dark"] .fixed-admin-researches-header,
    html[data-admin-theme="oled"] .fixed-admin-researches-header {
        background: #111827 !important;
        border-bottom-color: #374151 !important;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.5) !important;
    }

    html[data-guest-theme="dark"] .fixed-admin-researches-header h1,
    html[data-guest-theme="oled"] .fixed-admin-researches-header h1,
    html[data-admin-theme="dark"] .fixed-admin-researches-header h1,
    html[data-admin-theme="oled"] .fixed-admin-researches-header h1 {
        color: #f8fafc !important;
    }

    html[data-guest-theme="dark"] .min-filter-pill,
    html[data-guest-theme="oled"] .min-filter-pill,
    html[data-admin-theme="dark"] .min-filter-pill,
    html[data-admin-theme="oled"] .min-filter-pill {
        background: #1f2937 !important;
        border-color: #374151 !important;
        color: #cbd5e1 !important;
    }

    html[data-guest-theme="dark"] .min-filter-pill.active,
    html[data-guest-theme="oled"] .min-filter-pill.active,
    html[data-admin-theme="dark"] .min-filter-pill.active,
    html[data-admin-theme="oled"] .min-filter-pill.active {
        background: #10b981 !important;
        border-color: #10b981 !important;
        color: #ffffff !important;
    }

    html[data-guest-theme="dark"] .min-search-box,
    html[data-guest-theme="oled"] .min-search-box,
    html[data-admin-theme="dark"] .min-search-box,
    html[data-admin-theme="oled"] .min-search-box {
        background: #1f2937 !important;
        border-color: #374151 !important;
    }

    html[data-guest-theme="dark"] .min-search-input,
    html[data-guest-theme="oled"] .min-search-input,
    html[data-admin-theme="dark"] .min-search-input,
    html[data-admin-theme="oled"] .min-search-input {
        color: #f8fafc !important;
    }

    html[data-guest-theme="dark"] .min-search-btn,
    html[data-guest-theme="oled"] .min-search-btn,
    html[data-admin-theme="dark"] .min-search-btn,
    html[data-admin-theme="oled"] .min-search-btn {
        background: #1f2937 !important;
        border-color: #374151 !important;
        color: #cbd5e1 !important;
    }

    html[data-guest-theme="dark"] .min-table-card,
    html[data-guest-theme="oled"] .min-table-card,
    html[data-admin-theme="dark"] .min-table-card,
    html[data-admin-theme="oled"] .min-table-card {
        background: #111827 !important;
        border-color: #1f2937 !important;
    }

    html[data-guest-theme="dark"] .min-table th,
    html[data-guest-theme="oled"] .min-table th,
    html[data-admin-theme="dark"] .min-table th,
    html[data-admin-theme="oled"] .min-table th {
        background: #1f2937 !important;
        border-bottom-color: #374151 !important;
        color: #94a3b8 !important;
    }

    html[data-guest-theme="dark"] .min-table td,
    html[data-guest-theme="oled"] .min-table td,
    html[data-admin-theme="dark"] .min-table td,
    html[data-admin-theme="oled"] .min-table td {
        border-bottom-color: #1f2937 !important;
        color: #cbd5e1 !important;
    }

    html[data-guest-theme="dark"] .min-table tr:hover td,
    html[data-guest-theme="oled"] .min-table tr:hover td,
    html[data-admin-theme="dark"] .min-table tr:hover td,
    html[data-admin-theme="oled"] .min-table tr:hover td {
        background: #1a2234 !important;
    }
</style>

<div class="admin-researches-body">

    {{-- COMPLETELY FIXED / STATIONARY TOP HEADER & CONTROLS DECK --}}
    <div class="fixed-admin-researches-header">
        <div class="max-w-7xl mx-auto space-y-3">
            
            {{-- Top Row: Title, Subtitle, Counter & Upload Button --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">
                            Manage Researches
                        </h1>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-200/80 rounded-md">
                            Review Desk
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 font-medium">
                        Review, approve, or reject student capstone submissions for the institutional catalog.
                    </p>
                </div>

                <div class="flex items-center gap-2.5 shrink-0">
                    <span class="text-xs font-semibold text-slate-500 bg-slate-100/80 border border-slate-200/60 px-3 py-1.5 rounded-xl">
                        {{ $researches->total() }} total {{ $researches->total() === 1 ? 'record' : 'records' }}
                    </span>

                    <a href="{{ route('admin.researches.create') }}" class="min-btn-upload">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>Upload Capstone</span>
                    </a>
                </div>
            </div>

            {{-- Bottom Row: Status Filter Pills + Fixed Search Form --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pt-1 border-t border-slate-100">
                
                {{-- Status Filter Pills --}}
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0">
                    <a href="{{ route('admin.researches.index', array_filter(['search' => request('search')])) }}" 
                       class="min-filter-pill {{ !request('status') ? 'active' : '' }}">
                        <span>All</span>
                        <span class="min-filter-count">{{ $counts['all'] ?? $researches->total() }}</span>
                    </a>

                    <a href="{{ route('admin.researches.index', array_filter(['status' => 'pending', 'search' => request('search')])) }}" 
                       class="min-filter-pill {{ request('status') === 'pending' ? 'active' : '' }}">
                        <span>⏳ Pending</span>
                        <span class="min-filter-count">{{ $counts['pending'] ?? 0 }}</span>
                    </a>

                    <a href="{{ route('admin.researches.index', array_filter(['status' => 'approved', 'search' => request('search')])) }}" 
                       class="min-filter-pill {{ request('status') === 'approved' ? 'active' : '' }}">
                        <span>✅ Approved</span>
                        <span class="min-filter-count">{{ $counts['approved'] ?? 0 }}</span>
                    </a>

                    <a href="{{ route('admin.researches.index', array_filter(['status' => 'rejected', 'search' => request('search')])) }}" 
                       class="min-filter-pill {{ request('status') === 'rejected' ? 'active' : '' }}">
                        <span>❌ Rejected</span>
                        <span class="min-filter-count">{{ $counts['rejected'] ?? 0 }}</span>
                    </a>
                </div>

                {{-- Fixed Position Search Form --}}
                <form method="GET" action="{{ route('admin.researches.index') }}" class="min-search-form">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="min-search-box">
                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="text" 
                               name="search" 
                               placeholder="Search title, author, keyword..." 
                               value="{{ request('search') }}" 
                               class="min-search-input">
                        @if(request('search'))
                            <a href="{{ route('admin.researches.index', array_filter(['status' => request('status')])) }}" 
                               class="text-xs text-slate-400 hover:text-slate-600 px-1 font-bold" 
                               title="Clear search">✕</a>
                        @endif
                    </div>
                    <button type="submit" class="min-search-btn">
                        Search
                    </button>
                </form>
            </div>

            {{-- Active Filter Bar (shown only when searching or filtering) --}}
            @if(request('search') || request('status'))
                <div class="flex items-center justify-between text-xs text-slate-500 pt-1 border-t border-slate-100">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-[11px] font-semibold text-slate-400">Filtering:</span>
                        @if(request('status'))
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-slate-100 border border-slate-200 rounded text-slate-700 font-semibold text-[11px]">
                                Status: {{ ucfirst(request('status')) }}
                            </span>
                        @endif
                        @if(request('search'))
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 border border-emerald-200 rounded text-emerald-800 font-semibold text-[11px]">
                                Query: "{{ request('search') }}"
                            </span>
                        @endif
                    </div>
                    <a href="{{ route('admin.researches.index') }}" class="text-[11px] font-bold text-slate-500 hover:text-rose-600 transition">
                        ✕ Reset all
                    </a>
                </div>
            @endif

        </div>
    </div>

    {{-- MAIN CONTENT AREA --}}
    <div class="max-w-7xl mx-auto space-y-4">

        {{-- FLASH MESSAGES --}}
        @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <span class="text-base">✅</span>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 text-sm font-bold">✕</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-semibold flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <span class="text-base">⚠️</span>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 text-sm font-bold">✕</button>
            </div>
        @endif

        {{-- MINIMAL TABLE CARD --}}
        <div class="min-table-card">
            <div class="overflow-x-auto">
                <table class="w-full text-left min-table">
                    <thead>
                        <tr>
                            <th class="w-2/5">Manuscript Title</th>
                            <th>Authors</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Submitted Date</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($researches as $research)
                            <tr>
                                {{-- Manuscript Title --}}
                                <td class="max-w-md">
                                    <a href="{{ route('admin.researches.show', $research) }}" 
                                       class="font-bold text-slate-900 hover:text-emerald-700 transition leading-snug line-clamp-2 block">
                                        {{ $research->title }}
                                    </a>
                                </td>

                                {{-- Authors --}}
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 font-bold text-[10px] flex items-center justify-center shrink-0 border border-slate-200">
                                            {{ substr($research->authors ?? 'R', 0, 1) }}
                                        </div>
                                        <span class="font-medium text-slate-700 truncate max-w-[150px]" title="{{ $research->authors }}">
                                            {{ $research->authors ?? 'SCS Researchers' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Category --}}
                                <td>
                                    @if($research->category)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60 truncate max-w-[140px]" title="{{ $research->category->name }}">
                                            {{ $research->category->name }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-500">
                                            Uncategorized
                                        </span>
                                    @endif
                                </td>

                                {{-- Status Badge --}}
                                <td>
                                    @if($research->status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200/70">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Pending
                                        </span>
                                    @elseif($research->status === 'approved')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                                            Approved
                                        </span>
                                    @elseif($research->status === 'rejected')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-200/70">
                                            ✕ Rejected
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-600">
                                            {{ ucfirst($research->status) }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Submitted Date --}}
                                <td class="text-slate-400 font-medium whitespace-nowrap">
                                    {{ $research->created_at ? $research->created_at->format('M d, Y') : '—' }}
                                </td>

                                {{-- Actions Group --}}
                                <td class="text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.researches.show', $research) }}" 
                                           class="btn-action-pill btn-action-view" 
                                           title="View Details">
                                            View
                                        </a>

                                        @if($research->status === 'pending')
                                            <form action="{{ route('admin.researches.approve', $research) }}" method="POST" class="inline" onsubmit="return confirm('Approve this research for inclusion in the repository?')">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn-action-pill btn-action-approve" title="Approve Submission">
                                                    Approve
                                                </button>
                                            </form>

                                            <button type="button" 
                                                    onclick="openRejectModal({{ $research->id }}, '{{ addslashes($research->title) }}')" 
                                                    class="btn-action-pill btn-action-reject" 
                                                    title="Reject Submission">
                                                Reject
                                            </button>
                                        @endif

                                        <form action="{{ route('admin.researches.destroy', $research) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to permanently delete this research?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-action-pill btn-action-delete" title="Delete Research">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-14 text-center space-y-2">
                                    <span class="text-3xl block">🔍</span>
                                    <h4 class="font-bold text-sm text-slate-800">No researches found</h4>
                                    <p class="text-xs text-slate-400 max-w-sm mx-auto">
                                        No capstone submissions match your search query or filter selection.
                                    </p>
                                    @if(request('search') || request('status'))
                                        <div class="pt-2">
                                            <a href="{{ route('admin.researches.index') }}" 
                                               class="inline-flex items-center gap-1 px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
                                                Reset Filters
                                            </a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination footer --}}
            @if($researches->hasPages())
                <div class="p-3.5 border-t border-slate-100 bg-slate-50/50">
                    {{ $researches->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

{{-- REJECT MODAL --}}
<div class="modal-backdrop-custom" id="rejectModal">
    <div class="bg-white rounded-2xl p-6 max-w-lg w-full shadow-2xl border border-slate-200 space-y-4" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-sm text-slate-900">Reject Research Submission</h3>
            <button type="button" onclick="closeRejectModal()" class="text-slate-400 hover:text-slate-700 font-bold text-base">✕</button>
        </div>

        <form id="rejectForm" method="POST" class="space-y-4">
            @csrf @method('PATCH')
            <div>
                <p class="text-[11px] text-slate-500 font-semibold mb-1">Target Research Paper:</p>
                <p id="researchTitle" class="text-xs font-bold text-slate-800 bg-slate-50 p-2.5 rounded-lg border border-slate-200 leading-relaxed"></p>
            </div>

            <div>
                <label for="rejection_reason" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Rejection Feedback / Reason *
                </label>
                <textarea
                    name="rejection_reason"
                    id="rejection_reason"
                    rows="3"
                    required
                    placeholder="Provide specific feedback or required revisions for the student researcher..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-xs font-medium text-slate-900 outline-none focus:border-emerald-600 focus:bg-white transition"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-1">
                <button type="button" onclick="closeRejectModal()" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-lg shadow-xs transition">
                    Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal(id, title) {
    document.getElementById('researchTitle').textContent = title;
    document.getElementById('rejectForm').action = '{{ url("admin/researches") }}/' + id + '/reject';
    document.getElementById('rejectModal').classList.add('active');
}
function closeRejectModal() {
    document.getElementById('rejectModal').classList.remove('active');
    document.getElementById('rejection_reason').value = '';
}
</script>
@endsection
