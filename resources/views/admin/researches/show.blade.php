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
        <div class="min-card space-y-5" x-data="{ editingTitleYear: false }">

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
                    <button type="button" 
                            @click="editingTitleYear = !editingTitleYear" 
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-emerald-800 hover:text-emerald-950 bg-emerald-50 hover:bg-emerald-100/80 border border-emerald-200/80 transition cursor-pointer">
                        <span x-show="!editingTitleYear">✏️ Edit Title & Year</span>
                        <span x-show="editingTitleYear" x-cloak>✕ Cancel</span>
                    </button>
                </div>

                {{-- Views Count pill --}}
                <span class="text-xs font-semibold text-slate-400">
                    👁 {{ number_format($research->views_count) }} views
                </span>
            </div>

            {{-- Main Title (Normal View) --}}
            <div x-show="!editingTitleYear">
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-snug tracking-tight">
                    {{ $research->title }}
                </h1>
            </div>

            {{-- Inline Edit Form for Title and Year --}}
            <div x-show="editingTitleYear" x-cloak class="p-4 sm:p-5 bg-slate-50/80 rounded-xl border border-slate-200 space-y-3">
                <form action="{{ route('admin.researches.updateDetails', $research) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PATCH')
                    
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div class="sm:col-span-3 space-y-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Research Title</label>
                            <input type="text" name="title" value="{{ old('title', $research->title) }}" required
                                   placeholder="Enter Research Title..."
                                   class="w-full bg-white border border-slate-200 rounded-lg px-3.5 py-2 text-xs font-semibold text-slate-900 focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-none transition">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Academic Year</label>
                            <input type="number" name="year" value="{{ old('year', $research->year) }}" min="1990" max="{{ date('Y') + 1 }}" required
                                   placeholder="e.g. 2026"
                                   class="w-full bg-white border border-slate-200 rounded-lg px-3.5 py-2 text-xs font-semibold text-slate-900 focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-none transition">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
                        <button type="button" @click="editingTitleYear = false" class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-lg transition cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-1.5 bg-[#0c4233] hover:bg-[#082a20] text-white font-bold text-xs rounded-lg transition shadow-xs flex items-center gap-1 cursor-pointer">
                            ✓ Save Changes
                        </button>
                    </div>
                </form>
            </div>

            {{-- 3-Column Key Info Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-3 border-t border-slate-100">
                <div class="min-meta-block">
                    <span class="min-meta-label">Researchers / Authors</span>
                    <span class="min-meta-val truncate" title="{{ $research->authors }}">{{ $research->authors ?? 'SCS Researchers' }}</span>
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
            activeEditCard: null,
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
                            <h3 class="text-xs font-bold text-slate-900 group-hover:text-emerald-800 transition-colors uppercase tracking-wider line-clamp-1">
                                Project Context
                            </h3>
                            <span class="text-sm text-slate-400 group-hover:text-emerald-700 transition">🏢</span>
                        </div>

                        <p class="text-xs text-slate-500 leading-relaxed line-clamp-3 font-normal">
                            {{ count($contextBlocks) > 0 ? $contextBlocks[0]['text'] : ($research->project_context ?: 'No context provided for this research project.') }}
                        </p>
                    </div>

                    <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <button type="button" 
                                @click.stop="activeEditCard = 'context'" 
                                class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-600 hover:text-emerald-700 bg-slate-100 hover:bg-emerald-50 px-2 py-0.5 rounded-md border border-slate-200 transition cursor-pointer"
                                title="Edit Project Context">
                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span>Edit</span>
                        </button>
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
                            <h3 class="text-xs font-bold text-slate-900 group-hover:text-emerald-800 transition-colors uppercase tracking-wider line-clamp-1">
                                Objective
                            </h3>
                            <span class="text-sm text-slate-400 group-hover:text-emerald-700 transition">🎯</span>
                        </div>

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
                        <button type="button" 
                                @click.stop="activeEditCard = 'objectives'" 
                                class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-600 hover:text-emerald-700 bg-slate-100 hover:bg-emerald-50 px-2 py-0.5 rounded-md border border-slate-200 transition cursor-pointer"
                                title="Edit Objective">
                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span>Edit</span>
                        </button>
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
                            <h3 class="text-xs font-bold text-slate-900 group-hover:text-emerald-800 transition-colors uppercase tracking-wider line-clamp-1">
                                Abstract
                            </h3>
                            <span class="text-sm text-slate-400 group-hover:text-emerald-700 transition">📝</span>
                        </div>

                        <p class="text-xs text-slate-500 leading-relaxed line-clamp-3 font-normal">
                            {{ count($abstractBlocks) > 0 ? $abstractBlocks[0]['text'] : ($research->abstract ?: 'No abstract available for this project.') }}
                        </p>
                    </div>

                    <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <button type="button" 
                                @click.stop="activeEditCard = 'abstract'" 
                                class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-600 hover:text-emerald-700 bg-slate-100 hover:bg-emerald-50 px-2 py-0.5 rounded-md border border-slate-200 transition cursor-pointer"
                                title="Edit Abstract">
                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span>Edit</span>
                        </button>
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
                            <h3 class="text-xs font-bold text-slate-900 group-hover:text-emerald-800 transition-colors uppercase tracking-wider line-clamp-1">
                                Keywords
                            </h3>
                            <span class="text-sm text-slate-400 group-hover:text-emerald-700 transition">🏷️</span>
                        </div>

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
                        <button type="button" 
                                @click.stop="activeEditCard = 'keywords'" 
                                class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-600 hover:text-emerald-700 bg-slate-100 hover:bg-emerald-50 px-2 py-0.5 rounded-md border border-slate-200 transition cursor-pointer"
                                title="Edit Keywords">
                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span>Edit</span>
                        </button>
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
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" 
                                    @click="activeEditCard = activeDetailModal; activeDetailModal = null" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 rounded-xl border border-emerald-200 transition cursor-pointer"
                                    title="Edit this card">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span>Edit Card</span>
                            </button>
                            <button type="button" 
                                    @click="activeDetailModal = null" 
                                    class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-full transition shrink-0"
                                    title="Close (Esc)">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
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

            {{-- EDIT DETAIL CARD MODAL --}}
            <div x-show="activeEditCard !== null" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @keydown.escape.window="activeEditCard = null"
                 class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-slate-900/60 backdrop-blur-md overflow-y-auto"
                 style="display: none;">

                <div x-show="activeEditCard !== null"
                     x-transition:enter="transition ease-out duration-250"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-3"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-3"
                     @click.outside="activeEditCard = null"
                     class="centered-detail-modal bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-slate-200 flex flex-col overflow-hidden my-auto"
                     style="max-height: 90vh; height: auto;">
                    
                    <form action="{{ route('admin.researches.updateDetails', $research) }}" method="POST" class="flex flex-col flex-1 min-h-0">
                        @csrf
                        @method('PATCH')

                        {{-- Modal Header --}}
                        <div class="flex items-center justify-between px-6 py-4 sm:px-7 border-b border-slate-100 shrink-0 bg-white">
                            <div class="flex items-center gap-3 min-w-0 pr-2">
                                <span class="text-2xl shrink-0" x-text="modalIcons[activeEditCard]"></span>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 inline-block mb-1">
                                        Edit Mode
                                    </span>
                                    <h3 class="font-extrabold text-base sm:text-lg text-slate-900 truncate">
                                        Edit <span x-text="modalTitles[activeEditCard]"></span>
                                    </h3>
                                </div>
                            </div>
                            <button type="button" 
                                    @click="activeEditCard = null" 
                                    class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-full transition shrink-0"
                                    title="Close (Esc)">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        {{-- Quick Tabs to switch between the 4 cards --}}
                        <div class="px-6 py-2.5 bg-slate-50 border-b border-slate-100 flex items-center gap-1.5 overflow-x-auto shrink-0">
                            <button type="button" 
                                    @click="activeEditCard = 'context'" 
                                    :class="activeEditCard === 'context' ? 'bg-[#0c4233] text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100'"
                                    class="px-3 py-1 rounded-lg text-xs font-bold transition shrink-0 border border-slate-200/80 cursor-pointer">
                                🏢 Context
                            </button>
                            <button type="button" 
                                    @click="activeEditCard = 'objectives'" 
                                    :class="activeEditCard === 'objectives' ? 'bg-[#0c4233] text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100'"
                                    class="px-3 py-1 rounded-lg text-xs font-bold transition shrink-0 border border-slate-200/80 cursor-pointer">
                                🎯 Objective
                            </button>
                            <button type="button" 
                                    @click="activeEditCard = 'abstract'" 
                                    :class="activeEditCard === 'abstract' ? 'bg-[#0c4233] text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100'"
                                    class="px-3 py-1 rounded-lg text-xs font-bold transition shrink-0 border border-slate-200/80 cursor-pointer">
                                📝 Abstract
                            </button>
                            <button type="button" 
                                    @click="activeEditCard = 'keywords'" 
                                    :class="activeEditCard === 'keywords' ? 'bg-[#0c4233] text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100'"
                                    class="px-3 py-1 rounded-lg text-xs font-bold transition shrink-0 border border-slate-200/80 cursor-pointer">
                                🏷️ Keywords
                            </button>
                        </div>

                        {{-- Modal Body: Inputs for all cards (active one shown, others kept as inputs so nothing is lost) --}}
                        <div class="p-6 sm:p-7 overflow-y-auto flex-1 space-y-4">
                            {{-- Context Form --}}
                            <div x-show="activeEditCard === 'context'" class="space-y-2">
                                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                                    Project Context
                                </label>
                                <p class="text-[11px] text-slate-500">Edit the project background, problem statement, or context.</p>
                                <textarea name="project_context" rows="10"
                                          placeholder="Enter Project Context..."
                                          class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-xs text-slate-800 outline-none focus:border-emerald-600 focus:bg-white transition leading-relaxed font-normal">{{ old('project_context', $research->project_context) }}</textarea>
                            </div>

                            {{-- Objectives Form --}}
                            <div x-show="activeEditCard === 'objectives'" class="space-y-2">
                                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                                    Objective
                                </label>
                                <p class="text-[11px] text-slate-500">Specify the general and specific objectives of the study.</p>
                                <textarea name="objectives" rows="10"
                                          placeholder="Enter Objectives..."
                                          class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-xs text-slate-800 outline-none focus:border-emerald-600 focus:bg-white transition leading-relaxed font-normal">{{ old('objectives', $research->objectives) }}</textarea>
                            </div>

                            {{-- Abstract Form --}}
                            <div x-show="activeEditCard === 'abstract'" class="space-y-2">
                                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                                    Abstract
                                </label>
                                <p class="text-[11px] text-slate-500">Comprehensive summary of the study, methodology, technologies, and results.</p>
                                <textarea name="abstract" rows="10"
                                          placeholder="Enter Abstract..."
                                          class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-xs text-slate-800 outline-none focus:border-emerald-600 focus:bg-white transition leading-relaxed font-normal">{{ old('abstract', $research->abstract) }}</textarea>
                            </div>

                            {{-- Keywords Form --}}
                            <div x-show="activeEditCard === 'keywords'" class="space-y-2">
                                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                                    Keywords
                                </label>
                                <p class="text-[11px] text-slate-500">Separate each keyword with commas (e.g., Web Application, SMS Notification, Food Ordering).</p>
                                <textarea name="keywords" rows="5"
                                          placeholder="e.g. Web-Based System, Online Ordering, SMS Notification"
                                          class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-xs text-slate-800 outline-none focus:border-emerald-600 focus:bg-white transition leading-relaxed font-normal">{{ old('keywords', $research->keywords) }}</textarea>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="px-6 py-3.5 sm:px-7 border-t border-slate-100 flex items-center justify-between text-xs shrink-0 bg-slate-50/70 rounded-b-2xl">
                            <button type="button" 
                                    @click="activeEditCard = null" 
                                    class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl transition cursor-pointer">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-5 py-2 text-xs font-bold text-white bg-[#0c4233] hover:bg-[#082a20] rounded-xl transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Save Changes</span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        {{-- MANUSCRIPT PDF DOCUMENT VIEWER CARD --}}
        <div class="min-card space-y-3 p-4 sm:p-5">
            <div class="flex items-center justify-between flex-wrap gap-2 pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="text-lg">📄</span>
                    <h3 class="font-extrabold text-sm text-slate-900" id="adminViewerLabel">
                        {{ $isMultiFile ? 'Continuous Combined Manuscript (Sequential Order)' : 'Manuscript Document Preview' }}
                    </h3>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" 
                            onclick="openUpdateFileModal()" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-[#0c4233] hover:bg-[#082a20] transition shadow-xs cursor-pointer"
                            title="Upload new or re-attach files for this research">
                        <span>📎</span> Re-attach / Update File
                    </button>
                    @if($hasAnyDocument)
                        <a href="{{ route('document.pdf', $research->id) }}{{ $isMultiFile ? '?combined=1' : '' }}" target="_blank" class="text-xs font-bold text-emerald-800 hover:text-emerald-950 transition">
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
                            @foreach($attachedFiles as $idx => $f)
                                <button type="button"
                                        id="admin-tab-file-{{ $f->id }}"
                                        onclick="switchAdminViewer('{{ route('document.pdf', $research->id) }}?file_id={{ $f->id }}', 'admin-tab-file-{{ $f->id }}', 'File #{{ $idx + 1 }}: {{ addslashes($f->original_name) }}')"
                                        class="admin-viewer-tab px-2.5 py-1 text-xs font-bold rounded-lg transition bg-white text-slate-700 border border-slate-200 hover:bg-slate-100">
                                    {{ $idx === 0 ? '🥇 #1 ' : '#' . ($idx + 1) . ' ' }}{{ Str::limit($f->original_name, 20) }}
                                </button>
                            @endforeach
                            <button type="button"
                                    id="admin-tab-combined"
                                    onclick="switchAdminViewer('{{ route('document.pdf', $research->id) }}?combined=1', 'admin-tab-combined', 'Continuous Combined Manuscript (Sequential Order)')"
                                    class="admin-viewer-tab px-2.5 py-1 text-xs font-bold rounded-lg transition bg-[#0c4233] text-white">
                                📑 Sequential Combined View
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Embedded PDF Viewer iframe --}}
                <div class="w-full rounded-xl overflow-hidden border border-slate-200 bg-white" style="height: 80vh; min-height: 720px;">
                    <iframe id="adminDocIframe"
                            src="{{ route('document.pdf', $research->id) }}{{ $isMultiFile ? '?combined=1&' : '?' }}embedded=1#toolbar=0&navpanes=0&scrollbar=1" 
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

{{-- RE-ATTACH / UPDATE MANUSCRIPT MODAL (MINIMAL UI) --}}
<div class="modal-backdrop-custom" id="updateFileModal">
    <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-slate-200/90 overflow-hidden my-auto max-h-[90vh] flex flex-col" onclick="event.stopPropagation()">
        
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-white shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0 border border-slate-200/60">
                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-sm text-slate-900">Manage & Upload Manuscript File(s)</h3>
                    <p class="text-[11px] text-slate-500 font-medium mt-0.5">Arrange file sequence to select which document appears first in the viewer</p>
                </div>
            </div>
            <button type="button" onclick="closeUpdateFileModal()" class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-full transition cursor-pointer" title="Close">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- Optional Modern Segmented Tabs (shown if research already has attached files) --}}
        @if($isMultiFile)
            <div class="px-6 pt-3.5 pb-1 shrink-0">
                <div class="flex items-center gap-1.5 p-1 bg-slate-100/90 rounded-xl border border-slate-200/60">
                    <button type="button" 
                            id="btnTabUpload" 
                            onclick="switchUpdateTab('upload')" 
                            class="flex-1 py-1.5 px-3 rounded-lg text-xs font-bold transition bg-white text-slate-900 shadow-xs cursor-pointer flex items-center justify-center gap-1.5 border border-slate-200/60">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <span>Re-upload / New Files</span>
                    </button>
                    <button type="button" 
                            id="btnTabReorder" 
                            onclick="switchUpdateTab('reorder')" 
                            class="flex-1 py-1.5 px-3 rounded-lg text-xs font-bold transition text-slate-600 hover:text-slate-900 cursor-pointer flex items-center justify-center gap-1.5 border border-transparent">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <span>Reorder Existing Files ({{ $attachedFiles->count() }})</span>
                    </button>
                </div>
            </div>
        @endif

        {{-- Modal Scrollable Body --}}
        <div class="p-6 overflow-y-auto flex-1 space-y-4">

            {{-- TAB 1: UPLOAD / RE-ATTACH NEW FILES WITH RE-ORDERING QUEUE --}}
            <div id="updateTabUpload" class="space-y-4">
                <form action="{{ route('admin.researches.updateFiles', $research) }}" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      id="formUploadNewFiles" 
                      class="space-y-4" 
                      onsubmit="return validateAndSubmitUploadForm(event)">
                    @csrf

                    <div class="px-3.5 py-2.5 bg-slate-50/80 rounded-xl border border-slate-200/80 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Target Research</span>
                            <p class="text-xs font-bold text-slate-800 truncate" title="{{ $research->title }}">{{ $research->title }}</p>
                        </div>
                        <span class="shrink-0 text-[11px] font-semibold text-slate-600 bg-white px-2 py-0.5 rounded-md border border-slate-200">
                            ID #{{ $research->id }} &bull; {{ $research->year }}
                        </span>
                    </div>

                    {{-- Dropzone / Picker Area (Minimal Clean) --}}
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                            Select Manuscript Files (PDF / Word) <span class="text-rose-500">*</span>
                        </label>
                        <div id="modalDropZone" 
                             onclick="document.getElementById('modalPickerInput').click()" 
                             class="border-2 border-dashed border-slate-200 hover:border-emerald-500 rounded-2xl p-6 text-center cursor-pointer transition-all duration-200 bg-slate-50/50 hover:bg-emerald-50/20 group">
                             <div class="w-11 h-11 rounded-xl bg-white text-slate-400 group-hover:text-emerald-600 group-hover:scale-105 transition-all shadow-xs flex items-center justify-center mx-auto mb-2 border border-slate-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <p class="text-xs font-bold text-slate-800 group-hover:text-emerald-900 transition-colors">Click to browse file(s) or drag & drop here</p>
                            <p class="text-[11px] text-slate-400 mt-0.5 font-medium">Accepts PDF (.pdf) or Word (.docx) up to 30MB each</p>
                        </div>
                        <input type="file" id="modalPickerInput" multiple accept=".pdf,.docx" class="hidden" onchange="handleModalFilesSelected(this.files)">
                        <input type="file" name="files[]" id="modalActualFilesInput" multiple class="hidden">
                    </div>

                    {{-- File Queue & Arrangement List --}}
                    <div id="modalFileListContainer" style="display: none;" class="space-y-2 pt-1">
                        <div class="flex items-center justify-between pb-1">
                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                                </svg>
                                <span>Arranged File Sequence:</span>
                            </span>
                            <span id="modalFileCountBadge" class="text-[10px] font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200"></span>
                        </div>

                        <div class="p-2.5 bg-slate-50 border border-slate-200/80 rounded-xl text-[11px] text-slate-600 flex items-start gap-2">
                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>The file marked <strong>#1 Primary View</strong> will be displayed first in the viewer and guest preview. Use <strong>▲</strong> and <strong>▼</strong> to arrange the sequence.</span>
                        </div>

                        <div id="modalFileListItems" class="space-y-2 max-h-60 overflow-y-auto pr-1"></div>

                        <div class="pt-1">
                            <button type="button" 
                                    onclick="document.getElementById('modalPickerInput').click()" 
                                    class="w-full py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200/80 rounded-xl text-xs font-bold text-slate-700 transition flex items-center justify-center gap-1.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>Add More Files to Queue</span>
                            </button>
                        </div>
                    </div>

                    {{-- Minimal Note Strip --}}
                    <div class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-50 border border-slate-200/80 text-[11px] text-slate-600 leading-relaxed">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <strong class="text-slate-800 font-bold">Note:</strong> Existing database records in TiDB Cloud will not be duplicated. This only updates file attachments while preserving Project Context, Objectives, Abstract, and Keywords.
                        </div>
                    </div>

                    {{-- Footer Buttons --}}
                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" onclick="closeUpdateFileModal()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" id="btnSubmitUpdateFile" disabled class="px-5 py-2 text-xs font-bold text-white bg-[#0c4233] hover:bg-[#082a20] disabled:opacity-40 disabled:cursor-not-allowed rounded-xl transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            <span>Upload & Save Files</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- TAB 2: REORDER EXISTING ATTACHED FILES (IF ALREADY MULTI-FILE) --}}
            @if($isMultiFile)
                <div id="updateTabReorder" style="display: none;" class="space-y-4">
                    <form action="{{ route('admin.researches.reorderFiles', $research) }}" method="POST" class="space-y-3" onsubmit="const b = document.getElementById('btnSubmitReorder'); if(b){ b.style.pointerEvents='none'; b.innerText='Saving order...'; setTimeout(function(){ b.disabled = true; }, 300); }">
                        @csrf

                        <div class="p-2.5 bg-slate-50 border border-slate-200/80 rounded-xl text-[11px] text-slate-600 flex items-start gap-2">
                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                            </svg>
                            <span>Click <strong>▲ (Move Up)</strong> or <strong>▼ (Move Down)</strong> to change which file is viewed first (#1). No re-upload required.</span>
                        </div>

                        <div class="space-y-2 max-h-72 overflow-y-auto pr-1" id="existingFilesContainer">
                            @foreach($attachedFiles as $idx => $f)
                                <div class="existing-file-row flex items-center justify-between p-2.5 rounded-xl border {{ $idx === 0 ? 'border-emerald-500 bg-emerald-50/30 shadow-xs' : 'border-slate-200/80 bg-white hover:bg-slate-50/50' }} transition" data-file-id="{{ $f->id }}">
                                    <input type="hidden" name="order[]" value="{{ $f->id }}">
                                    <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                        <span class="existing-file-badge shrink-0 text-[10px] font-extrabold px-2 py-0.5 rounded-md {{ $idx === 0 ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                            {{ $idx === 0 ? '🥇 #1 Primary View' : '#' . ($idx + 1) }}
                                        </span>
                                        <p class="text-xs font-bold text-slate-900 truncate" title="{{ $f->original_name }}">{{ $f->original_name }}</p>
                                    </div>
                                    <div class="flex items-center gap-1 shrink-0">
                                        <button type="button" onclick="moveExistingFile(this, -1)" class="w-7 h-7 inline-flex items-center justify-center bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-lg text-xs font-bold transition cursor-pointer" title="Move Up">
                                            ▲
                                        </button>
                                        <button type="button" onclick="moveExistingFile(this, 1)" class="w-7 h-7 inline-flex items-center justify-center bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-lg text-xs font-bold transition cursor-pointer" title="Move Down">
                                            ▼
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                            <button type="button" onclick="closeUpdateFileModal()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition cursor-pointer">
                                Cancel
                            </button>
                            <button type="submit" id="btnSubmitReorder" class="px-5 py-2 text-xs font-bold text-white bg-[#0c4233] hover:bg-[#082a20] rounded-xl transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Save File Order</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

        </div>

    </div>
</div>

<script>
let modalFilesArray = [];

function openUpdateFileModal() {
    const m = document.getElementById('updateFileModal');
    if (m) {
        m.classList.add('active');
        initModalDropzone();
    }
}

function closeUpdateFileModal() {
    const m = document.getElementById('updateFileModal');
    if (m) m.classList.remove('active');
}

function switchUpdateTab(tabName) {
    const tabUpload = document.getElementById('updateTabUpload');
    const tabReorder = document.getElementById('updateTabReorder');
    const btnUpload = document.getElementById('btnTabUpload');
    const btnReorder = document.getElementById('btnTabReorder');

    const activeClass = 'flex-1 py-1.5 px-3 rounded-lg text-xs font-bold transition bg-white text-slate-900 shadow-xs cursor-pointer flex items-center justify-center gap-1.5 border border-slate-200/60';
    const inactiveClass = 'flex-1 py-1.5 px-3 rounded-lg text-xs font-bold transition text-slate-600 hover:text-slate-900 cursor-pointer flex items-center justify-center gap-1.5 border border-transparent';

    if (tabName === 'upload') {
        if (tabUpload) tabUpload.style.display = 'block';
        if (tabReorder) tabReorder.style.display = 'none';
        if (btnUpload) btnUpload.className = activeClass;
        if (btnReorder) btnReorder.className = inactiveClass;
    } else {
        if (tabUpload) tabUpload.style.display = 'none';
        if (tabReorder) tabReorder.style.display = 'block';
        if (btnReorder) btnReorder.className = activeClass;
        if (btnUpload) btnUpload.className = inactiveClass;
    }
}

function handleModalFilesSelected(files) {
    if (!files || files.length === 0) return;
    for (let i = 0; i < files.length; i++) {
        modalFilesArray.push(files[i]);
    }
    const picker = document.getElementById('modalPickerInput');
    if (picker) picker.value = '';
    syncModalActualInput();
    renderModalFileList();
}

function moveModalFile(index, direction) {
    const target = index + direction;
    if (target < 0 || target >= modalFilesArray.length) return;
    const temp = modalFilesArray[index];
    modalFilesArray[index] = modalFilesArray[target];
    modalFilesArray[target] = temp;
    syncModalActualInput();
    renderModalFileList();
}

function removeModalFile(index) {
    modalFilesArray.splice(index, 1);
    syncModalActualInput();
    renderModalFileList();
}

function syncModalActualInput() {
    const dt = new DataTransfer();
    for (const file of modalFilesArray) {
        dt.items.add(file);
    }
    const input = document.getElementById('modalActualFilesInput');
    if (input) {
        input.files = dt.files;
    }
}

function formatModalSize(bytes) {
    if (!bytes || bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

function renderModalFileList() {
    const container = document.getElementById('modalFileListContainer');
    const list = document.getElementById('modalFileListItems');
    const badge = document.getElementById('modalFileCountBadge');
    const submitBtn = document.getElementById('btnSubmitUpdateFile');

    if (!container || !list) return;

    if (modalFilesArray.length === 0) {
        container.style.display = 'none';
        list.innerHTML = '';
        if (submitBtn) submitBtn.disabled = true;
        return;
    }

    container.style.display = 'block';
    if (badge) badge.textContent = modalFilesArray.length + (modalFilesArray.length === 1 ? ' file' : ' files');
    if (submitBtn) submitBtn.disabled = false;

    list.innerHTML = modalFilesArray.map((f, i) => {
        const isFirst = i === 0;
        const ext = f.name.split('.').pop().toUpperCase();
        return `
            <div class="flex items-center justify-between p-2.5 rounded-xl border ${isFirst ? 'border-emerald-500 bg-emerald-50/30 shadow-xs' : 'border-slate-200/80 bg-white hover:bg-slate-50/50'} transition">
                <div class="flex items-center gap-2.5 min-w-0 pr-2">
                    <span class="shrink-0 text-[10px] font-extrabold px-2 py-0.5 rounded-md ${isFirst ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-600 border border-slate-200'}">
                        ${isFirst ? '🥇 #1 Primary View' : '#' + (i + 1)}
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-900 truncate" title="${f.name}">${f.name}</p>
                        <p class="text-[10px] text-slate-400 font-semibold uppercase">${ext} &bull; ${formatModalSize(f.size)}</p>
                    </div>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    <button type="button" 
                            onclick="moveModalFile(${i}, -1)" 
                            ${i === 0 ? 'disabled' : ''} 
                            class="w-7 h-7 inline-flex items-center justify-center rounded-lg text-xs font-bold transition ${i === 0 ? 'opacity-25 cursor-not-allowed bg-slate-100 text-slate-400' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 cursor-pointer'}" 
                            title="Move Up">
                        ▲
                    </button>
                    <button type="button" 
                            onclick="moveModalFile(${i}, 1)" 
                            ${i === modalFilesArray.length - 1 ? 'disabled' : ''} 
                            class="w-7 h-7 inline-flex items-center justify-center rounded-lg text-xs font-bold transition ${i === modalFilesArray.length - 1 ? 'opacity-25 cursor-not-allowed bg-slate-100 text-slate-400' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 cursor-pointer'}" 
                            title="Move Down">
                        ▼
                    </button>
                    <button type="button" 
                            onclick="removeModalFile(${i})" 
                            class="w-7 h-7 inline-flex items-center justify-center rounded-lg text-xs font-bold text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition cursor-pointer ml-1" 
                            title="Remove">
                        ✕
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

function validateAndSubmitUploadForm(e) {
    if (modalFilesArray.length === 0) {
        if (e) e.preventDefault();
        alert('Please select at least one PDF or Word file before uploading.');
        return false;
    }
    syncModalActualInput();
    const btn = document.getElementById('btnSubmitUpdateFile');
    if (btn) {
        btn.style.pointerEvents = 'none';
        btn.style.opacity = '0.75';
        btn.innerHTML = `
            <svg class="w-3.5 h-3.5 animate-spin inline-block mr-1.5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Uploading & Saving Files...</span>
        `;
        // Delay disabling so Chrome does not abort the active form submit with ERR_FAILED
        setTimeout(function() {
            btn.disabled = true;
        }, 300);
    }
    return true;
}

function moveExistingFile(btn, direction) {
    const row = btn.closest('.existing-file-row');
    if (!row) return;
    const parent = row.parentElement;
    if (direction === -1 && row.previousElementSibling) {
        parent.insertBefore(row, row.previousElementSibling);
    } else if (direction === 1 && row.nextElementSibling) {
        parent.insertBefore(row.nextElementSibling, row);
    }
    updateExistingFileBadges();
}

function updateExistingFileBadges() {
    const rows = document.querySelectorAll('.existing-file-row');
    rows.forEach((r, idx) => {
        const badge = r.querySelector('.existing-file-badge');
        if (badge) {
            if (idx === 0) {
                badge.className = 'existing-file-badge shrink-0 text-[11px] font-bold px-2 py-0.5 rounded-md bg-[#0c4233] text-white';
                badge.textContent = '🥇 #1 Primary View';
                r.className = 'existing-file-row flex items-center justify-between p-3 rounded-xl border border-emerald-500 bg-emerald-50/40 shadow-xs transition';
            } else {
                badge.className = 'existing-file-badge shrink-0 text-[11px] font-bold px-2 py-0.5 rounded-md bg-slate-200 text-slate-700';
                badge.textContent = '#' + (idx + 1);
                r.className = 'existing-file-row flex items-center justify-between p-3 rounded-xl border border-slate-200 bg-slate-50/60 transition';
            }
        }
    });
}

let dropzoneInitialized = false;
function initModalDropzone() {
    if (dropzoneInitialized) return;
    const mDrop = document.getElementById('modalDropZone');
    if (mDrop) {
        ['dragenter', 'dragover'].forEach(evt => {
            mDrop.addEventListener(evt, (e) => {
                e.preventDefault();
                e.stopPropagation();
                mDrop.classList.add('border-emerald-600', 'bg-emerald-50/50');
            });
        });
        ['dragleave', 'drop'].forEach(evt => {
            mDrop.addEventListener(evt, (e) => {
                e.preventDefault();
                e.stopPropagation();
                mDrop.classList.remove('border-emerald-600', 'bg-emerald-50/50');
            });
        });
        mDrop.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            if (dt && dt.files && dt.files.length > 0) {
                handleModalFilesSelected(dt.files);
            }
        });
        dropzoneInitialized = true;
    }
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
