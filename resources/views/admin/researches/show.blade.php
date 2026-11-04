@extends('layouts.admin')
@section('title', $research->title . ' — Research Review')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    .admin-show-body {
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        color: #1e293b;
    }

    /* ===== FIXED / STICKY TOP ACTION BAR ===== */
    .fixed-admin-show-header {
        position: sticky;
        top: 0;
        z-index: 30;
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        margin-top: -36px;
        margin-left: -40px;
        margin-right: -40px;
        padding: 14px 40px;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04);
        margin-bottom: 24px;
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }

    @media (max-width: 768px) {
        .fixed-admin-show-header {
            margin-top: -24px;
            margin-left: -20px;
            margin-right: -20px;
            padding: 12px 20px;
        }
    }

    /* Minimal Action Buttons */
    .btn-top-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        font-size: 0.75rem;
        font-weight: 700;
        color: #475569;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 9999px;
        text-decoration: none;
        transition: all 0.15s ease;
        white-space: nowrap;
    }

    .btn-top-back:hover {
        background: #e2e8f0;
        color: #0f172a;
        transform: translateX(-2px);
    }

    .btn-top-approve {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 16px;
        font-size: 0.75rem;
        font-weight: 700;
        color: #ffffff;
        background: #0c4233;
        border-radius: 9999px;
        transition: all 0.15s ease;
        border: none;
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(12, 66, 51, 0.2);
    }

    .btn-top-approve:hover {
        background: #082d23;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(12, 66, 51, 0.3);
    }

    .btn-top-reject {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        font-size: 0.75rem;
        font-weight: 700;
        color: #92400e;
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 9999px;
        transition: all 0.15s ease;
        cursor: pointer;
    }

    .btn-top-reject:hover {
        background: #fef3c7;
        color: #78350f;
    }

    .btn-top-delete {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        font-size: 0.75rem;
        font-weight: 700;
        color: #991b1b;
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 9999px;
        transition: all 0.15s ease;
        cursor: pointer;
    }

    .btn-top-delete:hover {
        background: #fee2e2;
        color: #7f1d1d;
    }

    .btn-top-secondary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        font-size: 0.75rem;
        font-weight: 700;
        color: #334155;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 9999px;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .btn-top-secondary:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a;
    }

    /* Minimal Content Cards */
    .min-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .min-meta-block {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .min-meta-label {
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #94a3b8;
    }

    .min-meta-val {
        font-size: 0.875rem;
        font-weight: 700;
        color: #0f172a;
    }

    [x-cloak] {
        display: none !important;
    }

    /* Repository-style Small Detail Cards */
    .capstone-detail-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 180px;
        cursor: pointer;
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
    }

    .capstone-detail-card:hover {
        transform: translateY(-2px);
        border-color: #10b981;
        box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.12), 0 8px 10px -6px rgba(16, 185, 129, 0.08);
    }

    .custom-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: #94a3b8 rgba(0, 0, 0, 0.05);
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch;
        overscroll-behavior: contain;
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.04);
        border-radius: 9999px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #94a3b8;
        border-radius: 9999px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }

    /* Centered Detail Modal (with blurred backdrop) */
    .centered-detail-modal {
        background: #ffffff;
        border: 1px solid #e2e8f0;
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
    html[data-guest-theme="dark"] .fixed-admin-show-header,
    html[data-guest-theme="oled"] .fixed-admin-show-header,
    html[data-admin-theme="dark"] .fixed-admin-show-header,
    html[data-admin-theme="oled"] .fixed-admin-show-header {
        background: #111827 !important;
        border-bottom-color: #374151 !important;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.5) !important;
    }

    html[data-guest-theme="dark"] .btn-top-back,
    html[data-guest-theme="oled"] .btn-top-back,
    html[data-admin-theme="dark"] .btn-top-back,
    html[data-admin-theme="oled"] .btn-top-back {
        background: #1f2937 !important;
        border-color: #374151 !important;
        color: #cbd5e1 !important;
    }

    html[data-guest-theme="dark"] .min-card,
    html[data-guest-theme="oled"] .min-card,
    html[data-admin-theme="dark"] .min-card,
    html[data-admin-theme="oled"] .min-card {
        background: #111827 !important;
        border-color: #1f2937 !important;
    }

    html[data-guest-theme="dark"] .min-card h1,
    html[data-guest-theme="oled"] .min-card h1,
    html[data-admin-theme="dark"] .min-card h1,
    html[data-admin-theme="oled"] .min-card h1,
    html[data-guest-theme="dark"] .min-card h3,
    html[data-guest-theme="oled"] .min-card h3,
    html[data-admin-theme="dark"] .min-card h3,
    html[data-admin-theme="oled"] .min-card h3 {
        color: #f8fafc !important;
    }

    html[data-guest-theme="dark"] .min-card p,
    html[data-guest-theme="oled"] .min-card p,
    html[data-admin-theme="dark"] .min-card p,
    html[data-admin-theme="oled"] .min-card p {
        color: #cbd5e1 !important;
    }

    html[data-guest-theme="dark"] .min-card .border-slate-100,
    html[data-guest-theme="oled"] .min-card .border-slate-100,
    html[data-admin-theme="dark"] .min-card .border-slate-100,
    html[data-admin-theme="oled"] .min-card .border-slate-100 {
        border-color: #1f2937 !important;
    }

    html[data-guest-theme="dark"] .min-meta-val,
    html[data-guest-theme="oled"] .min-meta-val,
    html[data-admin-theme="dark"] .min-meta-val,
    html[data-admin-theme="oled"] .min-meta-val {
        color: #f8fafc !important;
    }

    html[data-guest-theme="dark"] .capstone-detail-card,
    html[data-guest-theme="oled"] .capstone-detail-card,
    html[data-admin-theme="dark"] .capstone-detail-card,
    html[data-admin-theme="oled"] .capstone-detail-card {
        background: #111827 !important;
        border-color: #1f2937 !important;
    }

    html[data-guest-theme="dark"] .capstone-detail-card:hover,
    html[data-guest-theme="oled"] .capstone-detail-card:hover,
    html[data-admin-theme="dark"] .capstone-detail-card:hover,
    html[data-admin-theme="oled"] .capstone-detail-card:hover {
        border-color: #059669 !important;
        box-shadow: 0 10px 25px -5px rgba(5, 150, 105, 0.2) !important;
    }

    html[data-guest-theme="dark"] .capstone-detail-card h3,
    html[data-guest-theme="oled"] .capstone-detail-card h3,
    html[data-admin-theme="dark"] .capstone-detail-card h3,
    html[data-admin-theme="oled"] .capstone-detail-card h3 {
        color: #f8fafc !important;
    }

    html[data-guest-theme="dark"] .capstone-detail-card p,
    html[data-guest-theme="oled"] .capstone-detail-card p,
    html[data-admin-theme="dark"] .capstone-detail-card p,
    html[data-admin-theme="oled"] .capstone-detail-card p {
        color: #cbd5e1 !important;
    }

    html[data-guest-theme="dark"] .capstone-detail-card .border-slate-100,
    html[data-guest-theme="oled"] .capstone-detail-card .border-slate-100,
    html[data-admin-theme="dark"] .capstone-detail-card .border-slate-100,
    html[data-admin-theme="oled"] .capstone-detail-card .border-slate-100 {
        border-color: #1f2937 !important;
    }

    /* Centered Modal in Dark Mode */
    html[data-guest-theme="dark"] .centered-detail-modal,
    html[data-guest-theme="oled"] .centered-detail-modal,
    html[data-admin-theme="dark"] .centered-detail-modal,
    html[data-admin-theme="oled"] .centered-detail-modal {
        background: #111827 !important;
        border-color: #1f2937 !important;
        color: #cbd5e1 !important;
    }

    html[data-guest-theme="dark"] .centered-detail-modal h3,
    html[data-guest-theme="oled"] .centered-detail-modal h3,
    html[data-admin-theme="dark"] .centered-detail-modal h3,
    html[data-admin-theme="oled"] .centered-detail-modal h3 {
        color: #f8fafc !important;
    }

    html[data-guest-theme="dark"] .centered-detail-modal p,
    html[data-guest-theme="oled"] .centered-detail-modal p,
    html[data-admin-theme="dark"] .centered-detail-modal p,
    html[data-admin-theme="oled"] .centered-detail-modal p,
    html[data-guest-theme="dark"] .centered-detail-modal .text-slate-700,
    html[data-guest-theme="oled"] .centered-detail-modal .text-slate-700,
    html[data-admin-theme="dark"] .centered-detail-modal .text-slate-700,
    html[data-admin-theme="oled"] .centered-detail-modal .text-slate-700,
    html[data-guest-theme="dark"] .centered-detail-modal .text-slate-900,
    html[data-guest-theme="oled"] .centered-detail-modal .text-slate-900,
    html[data-admin-theme="dark"] .centered-detail-modal .text-slate-900,
    html[data-admin-theme="oled"] .centered-detail-modal .text-slate-900 {
        color: #cbd5e1 !important;
    }

    html[data-guest-theme="dark"] .centered-detail-modal .border-slate-100,
    html[data-guest-theme="oled"] .centered-detail-modal .border-slate-100,
    html[data-admin-theme="dark"] .centered-detail-modal .border-slate-100,
    html[data-admin-theme="oled"] .centered-detail-modal .border-slate-100 {
        border-color: #1f2937 !important;
    }

    html[data-guest-theme="dark"] .centered-detail-modal .bg-white,
    html[data-guest-theme="oled"] .centered-detail-modal .bg-white,
    html[data-admin-theme="dark"] .centered-detail-modal .bg-white,
    html[data-admin-theme="oled"] .centered-detail-modal .bg-white,
    html[data-guest-theme="dark"] .centered-detail-modal .modal-header-bar,
    html[data-guest-theme="oled"] .centered-detail-modal .modal-header-bar,
    html[data-admin-theme="dark"] .centered-detail-modal .modal-header-bar,
    html[data-admin-theme="oled"] .centered-detail-modal .modal-header-bar,
    html[data-guest-theme="dark"] .centered-detail-modal .modal-footer-bar,
    html[data-guest-theme="oled"] .centered-detail-modal .modal-footer-bar,
    html[data-admin-theme="dark"] .centered-detail-modal .modal-footer-bar,
    html[data-admin-theme="oled"] .centered-detail-modal .modal-footer-bar {
        background: #111827 !important;
    }

    html[data-guest-theme="dark"] .centered-detail-modal .bg-slate-100,
    html[data-guest-theme="oled"] .centered-detail-modal .bg-slate-100,
    html[data-admin-theme="dark"] .centered-detail-modal .bg-slate-100,
    html[data-admin-theme="oled"] .centered-detail-modal .bg-slate-100 {
        background: #1f2937 !important;
        border-color: #374151 !important;
        color: #cbd5e1 !important;
    }

    html[data-guest-theme="dark"] .custom-scrollbar,
    html[data-guest-theme="oled"] .custom-scrollbar,
    html[data-admin-theme="dark"] .custom-scrollbar,
    html[data-admin-theme="oled"] .custom-scrollbar {
        scrollbar-color: #475569 rgba(255, 255, 255, 0.05) !important;
    }
    html[data-guest-theme="dark"] .custom-scrollbar::-webkit-scrollbar-track,
    html[data-guest-theme="oled"] .custom-scrollbar::-webkit-scrollbar-track,
    html[data-admin-theme="dark"] .custom-scrollbar::-webkit-scrollbar-track,
    html[data-admin-theme="oled"] .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05) !important;
    }
    html[data-guest-theme="dark"] .custom-scrollbar::-webkit-scrollbar-thumb,
    html[data-guest-theme="oled"] .custom-scrollbar::-webkit-scrollbar-thumb,
    html[data-admin-theme="dark"] .custom-scrollbar::-webkit-scrollbar-thumb,
    html[data-admin-theme="oled"] .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #475569 !important;
    }
</style>

<div class="admin-show-body">

    @php
        $attachedFiles = $research->files ? $research->files->sortBy('sort_order') : collect();
        $hasAttachedFiles = $attachedFiles->isNotEmpty();
        $isMultiFile = $attachedFiles->count() > 1;
        $hasAnyDocument = $hasAttachedFiles || !empty($research->pdf_path);
    @endphp

    {{-- COMPLETELY FIXED / STICKY TOP ACTION BAR --}}
    <div class="fixed-admin-show-header">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            
            {{-- Left: Back to List + Status Pill --}}
            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('admin.researches.index') }}" class="btn-top-back">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <span>Back</span>
                </a>

                @if($research->status === 'pending')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200/70">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        Pending Review
                    </span>
                @elseif($research->status === 'approved')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                        ✓ Approved
                    </span>
                @elseif($research->status === 'rejected')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200/70">
                        ✕ Rejected
                    </span>
                @endif
            </div>

            {{-- Right: Status Actions & Tools --}}
            <div class="flex items-center gap-2 flex-wrap">
                @if($research->status === 'pending')
                    <form action="{{ route('admin.researches.approve', $research) }}" method="POST" class="inline" onsubmit="return confirm('Approve this research for inclusion in the institutional repository?')">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-top-approve">
                            ✓ Approve
                        </button>
                    </form>

                    <button type="button" 
                            onclick="openRejectModal({{ $research->id }}, '{{ addslashes($research->title) }}')" 
                            class="btn-top-reject">
                        ✕ Reject
                    </button>
                @endif

                <button type="button" onclick="openUpdateFileModal()" class="btn-top-secondary" title="Re-attach or replace manuscript file">
                    <span>📎</span> Re-attach File
                </button>

                @if($hasAnyDocument)
                    <a href="{{ route('repository.download', $research->id) }}" class="btn-top-secondary" title="Download Manuscript PDF">
                        <span>📥</span> Download PDF
                    </a>
                @endif

                <form action="{{ route('admin.researches.destroy', $research) }}" method="POST" class="inline" onsubmit="return confirm('Permanently delete this research entry? This action cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-top-delete" title="Delete Research">
                        🗑 Delete
                    </button>
                </form>
            </div>

        </div>
    </div>

    {{-- MAIN CONTENT WRAPPER --}}
    <div class="max-w-7xl mx-auto space-y-5">

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

        {{-- REJECTION REASON BANNER (IF REJECTED) --}}
        @if($research->rejection_reason)
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-900 space-y-1 shadow-xs">
                <div class="flex items-center gap-2 font-bold text-rose-800 uppercase tracking-wider text-[11px]">
                    <span>⚠️</span>
                    <span>Rejection Feedback</span>
                </div>
                <p class="font-medium leading-relaxed">{{ $research->rejection_reason }}</p>
            </div>
        @endif

        {{-- MANUSCRIPT OVERVIEW CARD --}}
        <div class="min-card space-y-5">

            {{-- Category Pill & Year Header --}}
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div class="flex items-center gap-2 flex-wrap">
                    @if($research->category)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-emerald-800 bg-emerald-50 border border-emerald-200/70">
                            📂 {{ $research->category->name }}
                        </span>
                    @endif
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold text-slate-500 bg-slate-100 border border-slate-200/60">
                        🗓 Year {{ $research->year }}
                    </span>
                </div>

                {{-- Views Count pill --}}
                <span class="text-xs font-semibold text-slate-400">
                    👁 {{ number_format($research->views_count) }} views
                </span>
            </div>

            {{-- Main Title --}}
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-snug tracking-tight">
                {{ $research->title }}
            </h1>

            {{-- 4-Column Key Info Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-3 border-t border-slate-100">
                <div class="min-meta-block">
                    <span class="min-meta-label">Researchers / Authors</span>
                    <span class="min-meta-val truncate" title="{{ $research->authors }}">{{ $research->authors ?? 'SCS Researchers' }}</span>
                </div>

                <div class="min-meta-block">
                    <span class="min-meta-label">Faculty Adviser</span>
                    <span class="min-meta-val truncate" title="{{ $research->adviser }}">{{ $research->adviser ?? 'None specified' }}</span>
                </div>

                <div class="min-meta-block">
                    <span class="min-meta-label">Submitted By</span>
                    <span class="min-meta-val truncate">{{ $research->user->name ?? 'Faculty Upload' }}</span>
                </div>

                <div class="min-meta-block">
                    <span class="min-meta-label">Submission Date</span>
                    <span class="min-meta-val">{{ $research->created_at ? $research->created_at->format('M d, Y h:i A') : '—' }}</span>
                </div>
            </div>

            {{-- Inline Category Classifications Management --}}
            <div class="pt-3 border-t border-slate-100" x-data="{ editing: false }">
                <div class="flex items-center justify-between gap-2 mb-2">
                    <span class="min-meta-label">Category Classifications</span>
                    <button type="button" @click="editing = !editing" class="text-xs font-bold text-emerald-800 hover:text-emerald-950 transition">
                        <span x-show="!editing">✏️ Edit Categories</span>
                        <span x-show="editing" x-cloak>✕ Cancel</span>
                    </button>
                </div>

                <div x-show="!editing" class="flex flex-wrap gap-1.5">
                    @forelse($research->categories as $cat)
                        <span class="px-2.5 py-0.5 bg-slate-100 text-slate-700 border border-slate-200/80 font-semibold text-xs rounded-lg">
                            {{ $cat->name }}
                        </span>
                    @empty
                        <span class="text-xs text-slate-400 italic">No secondary categories assigned</span>
                    @endforelse
                </div>

                {{-- Edit Categories Form (Collapsible) --}}
                <div x-show="editing" x-cloak class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-3 mt-2">
                    <form action="{{ route('admin.researches.updateCategory', $research) }}" method="POST" class="space-y-3">
                        @csrf @method('PATCH')
                        <div>
                            <span class="block text-[11px] font-bold text-slate-700 mb-2">Select Applicable Categories:</span>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                @php $selectedCats = $research->categories->pluck('id')->toArray(); @endphp
                                @foreach($categories as $cat)
                                    <label class="inline-flex items-center gap-2 p-2 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-800 cursor-pointer hover:bg-emerald-50/50">
                                        <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}" {{ in_array($cat->id, $selectedCats) ? 'checked' : '' }} class="rounded text-emerald-700">
                                        <span class="truncate">{{ $cat->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            <button type="submit" class="px-3.5 py-1.5 bg-[#0c4233] text-white font-bold text-xs rounded-lg shadow-xs hover:bg-[#082d23] transition">
                                Save Categories
                            </button>
                            <button type="button" @click="editing = false" class="px-3 py-1.5 bg-slate-200 text-slate-700 font-bold text-xs rounded-lg hover:bg-slate-300 transition">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 4 REPOSITORY-SIZED CARDS: CONTEXT, OBJECTIVES, ABSTRACT, KEYWORDS --}}
        {{-- Small cards with 3-line truncated preview; clicking opens centered modal with blurred backdrop --}}
        @php
            $keywordList = array_values(array_filter(array_map('trim', explode(',', $research->keywords ?? ''))));

            $formatResearchText = function($text) {
                if (empty($text)) return [];

                // Normalize line endings
                $text = str_replace(["\r\n", "\r"], "\n", trim($text));

                // Fix orphaned bullet markers where bullet or numbering is on its own line before the text
                // e.g. "•\nText" or "-\nText" or "1.\nText" -> "• Text"
                $text = preg_replace('/([•\-\*]|\d+[\.\)])\s*\n\s*/u', '$1 ', $text);

                // Split text by lines
                $lines = explode("\n", $text);
                $blocks = [];
                $currentBlock = '';
                $isCurrentBullet = false;

                foreach ($lines as $rawLine) {
                    $line = trim($rawLine);
                    if ($line === '') {
                        if ($currentBlock !== '') {
                            $blocks[] = ['text' => $currentBlock, 'is_bullet' => $isCurrentBullet];
                            $currentBlock = '';
                            $isCurrentBullet = false;
                        }
                        continue;
                    }

                    // Check if line starts with a bullet marker or numbering (e.g. •, -, *, 1., 1))
                    $isBullet = (bool) preg_match('/^([•\-\*]|\d+[\.\)])\s+/u', $line);

                    if ($isBullet) {
                        if ($currentBlock !== '') {
                            $blocks[] = ['text' => $currentBlock, 'is_bullet' => $isCurrentBullet];
                        }
                        $currentBlock = $line;
                        $isCurrentBullet = true;
                    } else {
                        if ($currentBlock === '') {
                            $currentBlock = $line;
                            $isCurrentBullet = false;
                        } else {
                            if ($isCurrentBullet) {
                                $currentBlock .= ' ' . $line;
                            } elseif (str_ends_with(trim($currentBlock), ':')) {
                                // Introductory heading like "The specific objectives are:"
                                $blocks[] = ['text' => $currentBlock, 'is_bullet' => false];
                                $currentBlock = $line;
                                $isCurrentBullet = false;
                            } else {
                                // Regular paragraph continuation (heals arbitrary line wraps from Word/PDF)
                                $currentBlock .= ' ' . $line;
                            }
                        }
                    }
                }

                if ($currentBlock !== '') {
                    $blocks[] = ['text' => $currentBlock, 'is_bullet' => $isCurrentBullet];
                }

                return $blocks;
            };

            $contextBlocks = $formatResearchText($research->project_context);
            $objectiveBlocks = $formatResearchText($research->objectives);
            $abstractBlocks = $formatResearchText($research->abstract);
        @endphp

        <div x-data="{ 
            activeDetailModal: null, 
            modalIcons: { 
                context: '🏢', 
                objectives: '🎯', 
                abstract: '📝', 
                keywords: '🏷️' 
            }, 
            modalTags: { 
                context: 'Project Context', 
                objectives: 'Objective', 
                abstract: 'Abstract', 
                keywords: 'Keywords' 
            }, 
            modalTitles: { 
                context: 'Project Context', 
                objectives: 'Objective', 
                abstract: 'Abstract', 
                keywords: 'Keywords' 
            } 
        }">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-stretch">
                
                {{-- 1. PROJECT CONTEXT CARD --}}
                <div @click="activeDetailModal = 'context'" 
                     class="capstone-detail-card group"
                     title="Click to view full Project Context">
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-100 rounded-lg">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 shrink-0"></span>
                                <span>Context</span>
                            </span>
                            <span class="text-xs text-slate-400 group-hover:text-emerald-700 transition">🏢</span>
                        </div>

                        <h3 class="text-xs font-bold text-slate-900 group-hover:text-emerald-800 transition-colors uppercase tracking-wider line-clamp-1">
                            Project Context
                        </h3>

                        <p class="text-xs text-slate-500 leading-relaxed line-clamp-3 font-normal">
                            {{ count($contextBlocks) > 0 ? $contextBlocks[0]['text'] : ($research->project_context ?: 'No context provided for this research project.') }}
                        </p>
                    </div>

                    <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-[11px] font-medium text-slate-400">Click to expand</span>
                        <span class="text-emerald-700 font-bold text-[11px] group-hover:translate-x-0.5 transition-transform flex items-center gap-1">
                            Expand ↗
                        </span>
                    </div>
                </div>

                {{-- 2. OBJECTIVE CARD --}}
                <div @click="activeDetailModal = 'objectives'" 
                     class="capstone-detail-card group"
                     title="Click to view full Objective">
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-100 rounded-lg">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 shrink-0"></span>
                                <span>Objective</span>
                            </span>
                            <span class="text-xs text-slate-400 group-hover:text-emerald-700 transition">🎯</span>
                        </div>

                        <h3 class="text-xs font-bold text-slate-900 group-hover:text-emerald-800 transition-colors uppercase tracking-wider line-clamp-1">
                            Objective
                        </h3>

                        <p class="text-xs text-slate-500 leading-relaxed line-clamp-3 font-normal">
                            @if(count($objectiveBlocks) > 1)
                                {{ $objectiveBlocks[1]['text'] }}
                            @elseif(count($objectiveBlocks) > 0)
                                {{ $objectiveBlocks[0]['text'] }}
                            @else
                                {{ $research->objectives ?: 'No objectives provided for this research project.' }}
                            @endif
                        </p>
                    </div>

                    <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-[11px] font-medium text-slate-400">Click to expand</span>
                        <span class="text-emerald-700 font-bold text-[11px] group-hover:translate-x-0.5 transition-transform flex items-center gap-1">
                            Expand ↗
                        </span>
                    </div>
                </div>

                {{-- 3. ABSTRACT CARD --}}
                <div @click="activeDetailModal = 'abstract'" 
                     class="capstone-detail-card group"
                     title="Click to view full Abstract">
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-100 rounded-lg">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 shrink-0"></span>
                                <span>Abstract</span>
                            </span>
                            <span class="text-xs text-slate-400 group-hover:text-emerald-700 transition">📝</span>
                        </div>

                        <h3 class="text-xs font-bold text-slate-900 group-hover:text-emerald-800 transition-colors uppercase tracking-wider line-clamp-1">
                            Abstract
                        </h3>

                        <p class="text-xs text-slate-500 leading-relaxed line-clamp-3 font-normal">
                            {{ count($abstractBlocks) > 0 ? $abstractBlocks[0]['text'] : ($research->abstract ?: 'No abstract available for this project.') }}
                        </p>
                    </div>

                    <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-[11px] font-medium text-slate-400">Click to expand</span>
                        <span class="text-emerald-700 font-bold text-[11px] group-hover:translate-x-0.5 transition-transform flex items-center gap-1">
                            Expand ↗
                        </span>
                    </div>
                </div>

                {{-- 4. KEYWORDS CARD --}}
                <div @click="activeDetailModal = 'keywords'" 
                     class="capstone-detail-card group"
                     title="Click to view full Keywords">
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-100 rounded-lg">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 shrink-0"></span>
                                <span>Keywords</span>
                            </span>
                            <span class="text-xs text-slate-400 group-hover:text-emerald-700 transition">🏷️</span>
                        </div>

                        <h3 class="text-xs font-bold text-slate-900 group-hover:text-emerald-800 transition-colors uppercase tracking-wider line-clamp-1">
                            Keywords
                        </h3>

                        @if(count($keywordList) > 0)
                            <div class="flex flex-wrap gap-1 max-h-[60px] overflow-hidden">
                                @foreach(array_slice($keywordList, 0, 4) as $kw)
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-700 text-[10px] font-semibold rounded border border-slate-200 truncate max-w-[120px]">
                                        #{{ $kw }}
                                    </span>
                                @endforeach
                                @if(count($keywordList) > 4)
                                    <span class="text-[10px] text-slate-400 font-semibold self-center">
                                        +{{ count($keywordList) - 4 }} more
                                    </span>
                                @endif
                            </div>
                        @else
                            <p class="text-xs text-slate-400 italic">No keywords tagged.</p>
                        @endif
                    </div>

                    <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-[11px] font-medium text-slate-400">Click to expand</span>
                        <span class="text-emerald-700 font-bold text-[11px] group-hover:translate-x-0.5 transition-transform flex items-center gap-1">
                            Expand ↗
                        </span>
                    </div>
                </div>

            </div>

            {{-- CENTERED MODAL DIALOG WITH BLURRED BACKDROP --}}
            <div x-show="activeDetailModal !== null" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @keydown.escape.window="activeDetailModal = null"
                 class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-slate-900/60 backdrop-blur-md overflow-y-auto"
                 style="display: none;">

                <div x-show="activeDetailModal !== null"
                     x-transition:enter="transition ease-out duration-250"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-3"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-3"
                     @click.outside="activeDetailModal = null"
                     class="centered-detail-modal bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-slate-200 flex flex-col overflow-hidden my-auto"
                     style="max-height: 85vh; height: auto;">
                    
                    {{-- Modal Header (Fixed at top) --}}
                    <div class="modal-header-bar flex items-center justify-between px-6 py-4 sm:px-7 sm:py-4.5 border-b border-slate-100 shrink-0 bg-white">
                        <div class="flex items-center gap-3 min-w-0 pr-2">
                            <span class="text-2xl shrink-0" x-text="modalIcons[activeDetailModal]"></span>
                            <div class="min-w-0">
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100 inline-block mb-1"
                                      x-text="modalTags[activeDetailModal]"></span>
                                <h3 class="font-extrabold text-base sm:text-lg text-slate-900 truncate"
                                    x-text="modalTitles[activeDetailModal]"></h3>
                            </div>
                        </div>
                        <button type="button" 
                                @click="activeDetailModal = null" 
                                class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-full transition shrink-0"
                                title="Close (Esc)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Modal Scrollable Body with complete unabridged content (Guaranteed Scrollable) --}}
                    <div class="modal-content-scroll overflow-y-auto px-6 py-5 sm:px-7 sm:py-6 text-slate-700 text-sm leading-relaxed custom-scrollbar flex-1 min-h-0"
                         style="overflow-y: auto !important; max-height: calc(85vh - 145px); min-height: 120px; overscroll-behavior: contain; -webkit-overflow-scrolling: touch;">
                        {{-- 1. Context Content --}}
                        <div x-show="activeDetailModal === 'context'" class="space-y-3">
                            @if(count($contextBlocks) > 0)
                                @foreach($contextBlocks as $block)
                                    @if($block['is_bullet'])
                                        <div class="flex items-start gap-2.5 text-slate-700 leading-relaxed text-sm select-text pl-1">
                                            <span class="text-emerald-700 font-bold shrink-0 text-base leading-none mt-0.5">•</span>
                                            <span class="flex-1">{{ preg_replace('/^[•\-\*]\s*/u', '', $block['text']) }}</span>
                                        </div>
                                    @else
                                        <p class="text-slate-700 leading-relaxed text-sm select-text font-normal text-justify sm:text-left">{{ $block['text'] }}</p>
                                    @endif
                                @endforeach
                            @else
                                <p class="text-slate-400 italic text-sm">No project context provided for this research.</p>
                            @endif
                        </div>

                        {{-- 2. Objectives Content --}}
                        <div x-show="activeDetailModal === 'objectives'" class="space-y-3">
                            @if(count($objectiveBlocks) > 0)
                                @foreach($objectiveBlocks as $block)
                                    @if($block['is_bullet'])
                                        <div class="flex items-start gap-2.5 text-slate-700 leading-relaxed text-sm select-text pl-1">
                                            <span class="text-emerald-700 font-bold shrink-0 text-base leading-none mt-0.5">•</span>
                                            <span class="flex-1">{{ preg_replace('/^[•\-\*]\s*/u', '', $block['text']) }}</span>
                                        </div>
                                    @else
                                        <p class="text-slate-900 font-semibold leading-relaxed text-sm select-text">{{ $block['text'] }}</p>
                                    @endif
                                @endforeach
                            @else
                                <p class="text-slate-400 italic text-sm">No objectives provided for this research.</p>
                            @endif
                        </div>

                        {{-- 3. Abstract Content --}}
                        <div x-show="activeDetailModal === 'abstract'" class="space-y-3">
                            @if(count($abstractBlocks) > 0)
                                @foreach($abstractBlocks as $block)
                                    @if($block['is_bullet'])
                                        <div class="flex items-start gap-2.5 text-slate-700 leading-relaxed text-sm select-text pl-1">
                                            <span class="text-emerald-700 font-bold shrink-0 text-base leading-none mt-0.5">•</span>
                                            <span class="flex-1">{{ preg_replace('/^[•\-\*]\s*/u', '', $block['text']) }}</span>
                                        </div>
                                    @else
                                        <p class="text-slate-700 leading-relaxed text-sm select-text font-normal text-justify sm:text-left">{{ $block['text'] }}</p>
                                    @endif
                                @endforeach
                            @else
                                <p class="text-slate-400 italic text-sm">No abstract provided for this research.</p>
                            @endif
                        </div>

                        {{-- 4. Keywords Content --}}
                        <div x-show="activeDetailModal === 'keywords'">
                            @if(count($keywordList) > 0)
                                <div class="space-y-3">
                                    <p class="text-xs text-slate-500 font-medium">All indexed classification keywords for this capstone paper:</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($keywordList as $kw)
                                            <span class="px-3 py-1.5 bg-emerald-50 text-emerald-900 text-xs font-bold rounded-xl border border-emerald-200">
                                                #{{ $kw }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <p class="text-slate-400 italic">No keywords tagged for this research.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Modal Footer (Fixed at bottom) --}}
                    <div class="modal-footer-bar px-6 py-3.5 sm:px-7 border-t border-slate-100 flex items-center justify-between text-xs shrink-0 bg-slate-50/70 rounded-b-2xl">
                        <span class="text-[11px] text-slate-400">Press <kbd class="px-1.5 py-0.5 bg-white rounded border border-slate-200 font-mono text-[10px] shadow-xs">ESC</kbd> or click outside to dismiss</span>
                        <button type="button" 
                                @click="activeDetailModal = null" 
                                class="px-4 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl transition">
                            Close
                        </button>
                    </div>

                </div>
            </div>
        </div>

        {{-- MANUSCRIPT PDF DOCUMENT VIEWER CARD --}}
        <div class="min-card space-y-3 p-4 sm:p-5">
            <div class="flex items-center justify-between flex-wrap gap-2 pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="text-lg">📄</span>
                    <h3 class="font-extrabold text-sm text-slate-900" id="adminViewerLabel">
                        {{ $isMultiFile ? 'Continuous Combined Manuscript View (' . $attachedFiles->count() . ' Files)' : 'Manuscript Document Preview' }}
                    </h3>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" 
                            onclick="openUpdateFileModal()" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-[#0c4233] hover:bg-[#082a20] transition shadow-xs cursor-pointer"
                            title="Upload bago o re-attach na file para sa research na ito">
                        <span>📎</span> Re-attach / Update File
                    </button>
                    @if($hasAnyDocument)
                        <a href="{{ route('document.pdf', $research->id) }}" target="_blank" class="text-xs font-bold text-emerald-800 hover:text-emerald-950 transition">
                            Open in New Tab ↗
                        </a>
                    @endif
                </div>
            </div>

            @if($hasAnyDocument)
                {{-- Multi-file Tab Bar (If multi-file) --}}
                @if($isMultiFile)
                    <div class="flex items-center justify-between flex-wrap gap-2 p-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        <div class="flex items-center gap-1.5 text-xs text-slate-500 font-bold">
                            <span>📚 Attached Files:</span>
                        </div>
                        <div class="flex items-center gap-1 flex-wrap">
                            <button type="button"
                                    id="admin-tab-combined"
                                    onclick="switchAdminViewer('{{ route('document.pdf', $research->id) }}', 'admin-tab-combined', 'Continuous Combined Manuscript (Sequential Order)')"
                                    class="admin-viewer-tab px-2.5 py-1 text-xs font-bold rounded-lg transition bg-[#0c4233] text-white">
                                📑 Sequential Combined View
                            </button>
                            @foreach($attachedFiles as $idx => $f)
                                <button type="button"
                                        id="admin-tab-file-{{ $f->id }}"
                                        onclick="switchAdminViewer('{{ route('document.pdf', $research->id) }}?file_id={{ $f->id }}', 'admin-tab-file-{{ $f->id }}', 'File #{{ $idx + 1 }}: {{ addslashes($f->original_name) }}')"
                                        class="admin-viewer-tab px-2.5 py-1 text-xs font-bold rounded-lg transition bg-white text-slate-700 border border-slate-200 hover:bg-slate-100">
                                    {{ $idx === 0 ? '🥇 #1 ' : '#' . ($idx + 1) . ' ' }}{{ Str::limit($f->original_name, 20) }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Embedded PDF Viewer iframe --}}
                <div class="w-full rounded-xl overflow-hidden border border-slate-200 bg-white" style="height: 80vh; min-height: 720px;">
                    <iframe id="adminDocIframe"
                            src="{{ route('document.pdf', $research->id) }}?embedded=1#toolbar=0&navpanes=0&scrollbar=1" 
                            style="width: 100%; height: 100%; border: none; background: #ffffff;"
                            class="w-full h-full border-0">
                    </iframe>
                </div>

                {{-- Multi-file files list --}}
                @if($isMultiFile)
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1.5">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Uploaded Files Breakdown:</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                            @foreach($attachedFiles as $idx => $f)
                                <div class="p-2.5 bg-white border border-slate-200 rounded-lg flex items-center justify-between gap-2 shadow-xs">
                                    <div class="flex items-center gap-1.5 truncate text-xs">
                                        <span class="font-bold text-slate-800 truncate" title="{{ $f->original_name }}">{{ $f->original_name }}</span>
                                    </div>
                                    <a href="{{ route('files.show', ['path' => $f->file_path]) }}" target="_blank" class="px-2 py-0.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold rounded transition">
                                        Open
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @else
                <div class="py-12 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200 space-y-1">
                    <span class="text-2xl block">📁</span>
                    <p class="text-xs text-slate-400 font-medium">No manuscript PDF attached to this entry.</p>
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
                <p id="modalResearchTitle" class="text-xs font-bold text-slate-800 bg-slate-50 p-2.5 rounded-lg border border-slate-200 leading-relaxed"></p>
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

{{-- RE-ATTACH / UPDATE MANUSCRIPT MODAL --}}
<div class="modal-backdrop-custom" id="updateFileModal">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden my-auto" onclick="event.stopPropagation()">
        
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-emerald-50/50">
            <div class="flex items-center gap-2.5">
                <span class="text-xl">📎</span>
                <div>
                    <h3 class="font-extrabold text-sm text-slate-900">Update / Re-attach Manuscript File</h3>
                    <p class="text-[11px] text-slate-500 font-medium">I-upload ang file nang hindi madodoble ang record sa TiDB Cloud</p>
                </div>
            </div>
            <button type="button" onclick="closeUpdateFileModal()" class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-full transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- Form --}}
        <form action="{{ route('admin.researches.updateFiles', $research) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4" onsubmit="document.getElementById('btnSubmitUpdateFile').disabled = true; document.getElementById('btnSubmitUpdateFile').innerText = 'Nag-a-upload...';">
            @csrf

            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Target Research Record:</span>
                <p class="text-xs font-bold text-slate-900 truncate" title="{{ $research->title }}">{{ $research->title }}</p>
                <p class="text-[11px] text-slate-400">ID: #{{ $research->id }} &bull; Year: {{ $research->year }}</p>
            </div>

            <div class="space-y-1.5">
                <label for="modal_update_files" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Pumili ng Bago o Re-attached File(s) <span class="text-rose-500">*</span>
                </label>
                <input type="file" 
                       name="files[]" 
                       id="modal_update_files" 
                       multiple 
                       accept=".pdf,.docx" 
                       required
                       class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-800 hover:file:bg-emerald-100 border border-slate-200 rounded-xl cursor-pointer bg-white p-2">
                <p class="text-[11px] text-slate-400">Tumatanggap ng PDF (.pdf) o Word (.docx) hanggang 30MB bawat file. Puwede ring higit sa isang file.</p>
            </div>

            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-[11px] text-amber-900 space-y-0.5">
                <span class="font-bold flex items-center gap-1">ℹ️ Paalala / Note:</span>
                <p>Direktang ikakabit ang file sa kasalukuyang record. <strong>Hindi magbabago o madodoble ang record sa TiDB Cloud</strong> (mananatili ang Project Context, Objective, Abstract, at Keywords).</p>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeUpdateFileModal()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition cursor-pointer">
                    Kanselahin
                </button>
                <button type="submit" id="btnSubmitUpdateFile" class="px-5 py-2 text-xs font-bold text-white bg-[#0c4233] hover:bg-[#082a20] rounded-xl transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <span>📤</span> I-upload at I-save ang File
                </button>
            </div>
        </form>

    </div>
</div>

<script>
function openUpdateFileModal() {
    const m = document.getElementById('updateFileModal');
    if (m) m.classList.add('active');
}
function closeUpdateFileModal() {
    const m = document.getElementById('updateFileModal');
    if (m) m.classList.remove('active');
}
function openRejectModal(id, title) {
    document.getElementById('modalResearchTitle').textContent = title;
    document.getElementById('rejectForm').action = '{{ url("admin/researches") }}/' + id + '/reject';
    document.getElementById('rejectModal').classList.add('active');
}
function closeRejectModal() {
    document.getElementById('rejectModal').classList.remove('active');
    document.getElementById('rejection_reason').value = '';
}
function switchAdminViewer(url, activeTabId, label) {
    const iframe = document.getElementById('adminDocIframe');
    const labelEl = document.getElementById('adminViewerLabel');
    if (iframe) {
        iframe.src = url + (url.includes('?') ? '&' : '?') + 'embedded=1#toolbar=0&navpanes=0&scrollbar=1';
    }
    if (labelEl && label) {
        labelEl.textContent = label;
    }
    document.querySelectorAll('.admin-viewer-tab').forEach(btn => {
        btn.classList.remove('active', 'bg-[#0c4233]', 'text-white');
        btn.classList.add('bg-white', 'text-slate-700', 'border', 'border-slate-200');
    });
    const activeTab = document.getElementById(activeTabId);
    if (activeTab) {
        activeTab.classList.remove('bg-white', 'text-slate-700', 'border', 'border-slate-200');
        activeTab.classList.add('active', 'bg-[#0c4233]', 'text-white');
    }
}
</script>
@endsection
