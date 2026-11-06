@extends('layouts.app')
@section('title', 'My Submissions — Student Portal')

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
</style>

<div class="space-y-6 max-w-5xl mx-auto pb-16">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pb-4 border-b sd-border">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold uppercase tracking-wider sd-muted">Student Portal</span>
                <span class="sd-muted opacity-40">/</span>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">My Submissions</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight sd-title">
                Capstone Submission
            </h1>
            <p class="text-xs sm:text-sm sd-muted mt-0.5">
                Manage your submitted capstone manuscript or track review progress.
            </p>
        </div>

        @if($researches->total() >= 1)
            <span class="px-3.5 py-2 sd-card-subtle sd-muted text-xs font-bold rounded-xl flex items-center gap-2 shrink-0 border sd-border" title="1 Active Submission Limit Reached">
                🔒 Limit Reached (1/1)
            </span>
        @else
            <a href="{{ route('student.researches.create') }}" class="px-4 py-2 bg-[#0c4233] hover:bg-[#115e47] text-white text-xs font-bold rounded-xl transition inline-flex items-center gap-2 shadow-sm shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Upload Capstone</span>
            </a>
        @endif
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-800 dark:text-emerald-300 rounded-xl text-xs font-bold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <span class="text-base">✅</span>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 text-sm font-bold">✕</button>
        </div>
    @endif

    @if(session('warning'))
        <div class="p-4 bg-amber-500/10 border border-amber-500/25 text-amber-900 dark:text-amber-200 rounded-xl text-xs font-bold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <span class="text-base">⚠️</span>
                <span>{{ session('warning') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-amber-600 hover:text-amber-900 text-sm font-bold">✕</button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-800 dark:text-rose-300 rounded-xl text-xs font-bold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <span class="text-base">⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-sm font-bold">✕</button>
        </div>
    @endif

    {{-- Submission Limit Notice --}}
    @if($researches->total() >= 1)
        <div class="p-3.5 sd-card-subtle border sd-border rounded-xl text-xs flex items-center gap-2.5">
            <span class="text-base shrink-0">ℹ️</span>
            <p class="sd-text leading-relaxed">
                You currently have 1 active capstone submission. To submit a new paper, delete your existing upload using the <strong>Delete</strong> button below.
            </p>
        </div>
    @endif

    {{-- Submission Table Card --}}
    <div class="sd-card overflow-hidden">
        <div class="p-4 sm:p-5 border-b sd-border flex items-center justify-between bg-slate-50/50 dark:bg-white/[0.02]">
            <h3 class="font-bold text-xs uppercase tracking-wider sd-title flex items-center gap-2">
                <span>📑</span>
                <span>Submitted Manuscript</span>
            </h3>
            <span class="text-xs font-semibold sd-muted">{{ $researches->total() }} of 1 Allowed</span>
        </div>

        @if($researches->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b sd-border text-[11px] font-bold sd-muted uppercase tracking-wider bg-slate-50/30 dark:bg-white/[0.01]">
                            <th class="py-3 px-5">Manuscript Title</th>
                            <th class="py-3 px-5">Category</th>
                            <th class="py-3 px-5">Status</th>
                            <th class="py-3 px-5">Submitted Date</th>
                            <th class="py-3 px-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y sd-border text-xs">
                        @foreach($researches as $research)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="py-4 px-5">
                                    <div class="space-y-1 max-w-md">
                                        <a href="{{ route('student.researches.show', $research) }}" class="font-bold sd-title hover:text-emerald-600 transition line-clamp-2 leading-snug">
                                            {{ $research->title }}
                                        </a>
                                        <p class="text-[11px] sd-muted truncate">
                                            Authors: {{ $research->authors }}
                                        </p>
                                    </div>
                                </td>
                                <td class="py-4 px-5">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20">
                                        {{ $research->category->name ?? 'Uncategorized' }}
                                    </span>
                                </td>
                                <td class="py-4 px-5">
                                    @if($research->status == 'pending')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Pending Review
                                        </span>
                                    @elseif($research->status == 'approved')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20">
                                            Approved
                                        </span>
                                    @elseif($research->status == 'rejected')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-500/10 text-rose-700 dark:text-rose-300 border border-rose-500/20">
                                            Needs Revision
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-5 sd-muted text-xs">
                                    {{ $research->created_at->format('M d, Y') }}
                                </td>
                                <td class="py-4 px-5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('student.researches.show', $research) }}" 
                                           class="px-2.5 py-1 sd-card-subtle hover:sd-card text-xs font-bold sd-title rounded-lg transition border sd-border">
                                            View
                                        </a>

                                        @if($research->status == 'pending')
                                            <a href="{{ route('student.researches.edit', $research) }}" 
                                               class="px-2.5 py-1 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 rounded-lg font-bold text-xs transition border border-emerald-500/20">
                                                Edit
                                            </a>
                                        @endif

                                        <form action="{{ route('student.researches.destroy', $research) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this submission? Once deleted, you will be able to upload a new manuscript.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 rounded-lg font-bold text-xs transition border border-rose-500/20">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-14 px-6 text-center space-y-3">
                <div class="w-12 h-12 sd-card-subtle rounded-full flex items-center justify-center text-2xl mx-auto border sd-border">
                    📂
                </div>
                <div>
                    <h4 class="font-bold text-sm sd-title">
                        No Submission Uploaded
                    </h4>
                    <p class="text-xs sd-muted mt-0.5 max-w-sm mx-auto">
                        You have not submitted any capstone manuscripts yet. Upload your paper to initiate review.
                    </p>
                </div>
                <a href="{{ route('student.researches.create') }}" class="px-4 py-2 bg-[#0c4233] hover:bg-[#115e47] text-white text-xs font-bold rounded-xl transition inline-flex items-center gap-1.5 shadow-sm">
                    Upload Your Capstone
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
