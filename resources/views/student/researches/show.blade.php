@extends('layouts.app')
@section('title', $research->title . ' — Submission Details')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    .student-show-body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #2d2b56;
    }

    .back-btn-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 20px;
        background: #f4f4fa;
        color: #5840bb;
        border: 1px solid #e8e8f5;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.25s ease;
        margin-bottom: 16px;
    }

    .back-btn-pill:hover {
        background: #5840bb;
        color: #ffffff;
        border-color: #5840bb;
        transform: translateX(-3px);
    }

    .section-card {
        background: #ffffff;
        border-radius: 24px;
        border: 1.5px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
    }

    .btn-edit-action {
        background: #5840bb;
        color: #ffffff;
        padding: 9px 22px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 13px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.25s ease;
        box-shadow: 0 4px 14px rgba(88, 64, 187, 0.25);
    }

    .btn-edit-action:hover {
        background: #432e96;
        color: #ffffff;
        transform: translateY(-2px);
    }
</style>

<div class="student-show-body space-y-8 max-w-5xl mx-auto">
    {{-- TOP NAVIGATION & STATUS BAR --}}
    <div>
        <a href="{{ route('student.researches.index') }}" class="back-btn-pill">
            ← Back to My Submissions
        </a>

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mt-2">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3.5 py-1 rounded-full text-xs font-extrabold bg-purple-100 text-purple-800">
                        {{ $research->category->name ?? 'Uncategorized' }}
                    </span>
                    @if($research->status == 'pending')
                        <span class="px-3.5 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                            Pending Review
                        </span>
                    @elseif($research->status == 'approved')
                        <span class="px-3.5 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800">
                            Approved
                        </span>
                    @elseif($research->status == 'rejected')
                        <span class="px-3.5 py-1 rounded-full text-xs font-extrabold bg-rose-100 text-rose-800">
                            ✕ Rejection Notice
                        </span>
                    @endif
                    <span class="px-3.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                        Submitted {{ $research->created_at->format('M d, Y') }}
                    </span>
                </div>

                <h1 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 2rem;" class="font-extrabold text-[#2d2b56] leading-tight">
                    {{ $research->title }}
                </h1>
            </div>

            @if($research->status == 'pending')
                <a href="{{ route('student.researches.edit', $research) }}" class="btn-edit-action shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Submission
                </a>
            @endif
        </div>
    </div>

    {{-- REJECTION REASON CALLOUT IF REJECTED --}}
    @if($research->status == 'rejected' && $research->rejection_reason)
        <div class="p-6 bg-rose-50 border-1.5 border-rose-200 rounded-3xl space-y-2">
            <div class="flex items-center gap-2 text-rose-800 font-extrabold text-sm">
                <span class="text-base">⚠️</span>
                <span>Reviewer Remarks & Rejection Reason</span>
            </div>
            <p class="text-xs text-rose-700 leading-relaxed font-semibold pl-6">
                {{ $research->rejection_reason }}
            </p>
        </div>
    @endif

    {{-- MAIN DETAILS CONTENT GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- LEFT COLUMN: ABSTRACT & OBJECTIVES --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="section-card p-8 space-y-4">
                <h3 class="font-extrabold text-base text-[#2d2b56] flex items-center gap-2">
                    <span>📝</span> Abstract
                </h3>
                <p class="text-xs text-slate-600 leading-relaxed font-normal whitespace-pre-line">
                    {{ $research->abstract }}
                </p>
            </div>

            @if(!empty($research->project_context))
            <div class="section-card p-8 space-y-4">
                <h3 class="font-extrabold text-base text-[#2d2b56] flex items-center gap-2">
                    <span>🏢</span> Project Context
                </h3>
                <p class="text-xs text-slate-600 leading-relaxed font-normal whitespace-pre-line">
                    {{ $research->project_context }}
                </p>
            </div>
            @endif

            <div class="section-card p-8 space-y-4">
                <h3 class="font-extrabold text-base text-[#2d2b56] flex items-center gap-2">
                    <span>🎯</span> Study Objectives
                </h3>
                <p class="text-xs text-slate-600 leading-relaxed font-normal whitespace-pre-line">
                    {{ $research->objectives }}
                </p>
            </div>

            @if($research->keywords)
            <div class="section-card p-8 space-y-4">
                <h3 class="font-extrabold text-sm text-[#2d2b56]">
                    🏷️ Keywords
                </h3>
                <div class="flex flex-wrap gap-2">
                    @foreach(explode(',', $research->keywords) as $kw)
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold rounded-full">
                            {{ trim($kw) }}
                        </span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- RIGHT SIDEBAR: METADATA & FILE ACCESS --}}
        <div class="space-y-6">
            {{-- Attached File(s) Card --}}
            <div class="section-card p-6 space-y-4">
                <div class="w-12 h-12 bg-purple-50 text-[#5840bb] rounded-2xl flex items-center justify-center font-bold text-xl mx-auto border border-purple-100">
                    📂
                </div>
                <div class="text-center">
                    <h4 class="font-extrabold text-sm text-[#2d2b56]">
                        Uploaded {{ ($research->files && $research->files->count() > 1) ? 'Files (' . $research->files->count() . ')' : 'File' }}
                    </h4>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        Arranged by viewing sequence (First file is viewed first)
                    </p>
                </div>

                @if($research->files && $research->files->count() > 0)
                    <div class="space-y-2 text-left">
                        @foreach($research->files->sortBy('sort_order') as $rf)
                            <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2 truncate">
                                    <span class="px-2 py-0.5 rounded font-extrabold flex-shrink-0 {{ $rf->sort_order === 1 ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700' }}">
                                        {{ $rf->sort_order === 1 ? '🥇 #1' : '#' . $rf->sort_order }}
                                    </span>
                                    <span class="font-semibold text-slate-800 truncate" title="{{ $rf->original_name }}">{{ $rf->original_name }}</span>
                                </div>
                                <a href="{{ route('files.show', ['path' => $rf->file_path]) }}" target="_blank" class="text-emerald-700 hover:underline font-bold flex-shrink-0 ml-2">
                                    Open
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('repository.download', $research->id) }}" class="w-full py-2.5 px-4 bg-[#0c4233] hover:bg-[#082d23] text-white rounded-full text-xs font-extrabold transition inline-flex items-center justify-center gap-2 shadow-xs">
                            <span>📥</span> Download Complete PDF (All Files)
                        </a>
                    </div>
                @elseif($research->pdf_path)
                    <a href="{{ route('repository.download', $research->id) }}" class="w-full py-2.5 px-4 bg-[#0c4233] hover:bg-[#082d23] text-white rounded-full text-xs font-extrabold transition inline-flex items-center justify-center gap-2 shadow-xs">
                        <span>📥</span> Download PDF ➔
                    </a>
                @endif
            </div>

            {{-- Metadata Card --}}
            <div class="section-card p-6 space-y-3">
                <h4 class="font-extrabold text-xs text-[#2d2b56] uppercase tracking-wider">
                    Submission Details
                </h4>
                <div class="space-y-2.5 text-xs">
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span class="text-slate-400 font-medium">Authors</span>
                        <span class="font-extrabold text-[#2d2b56] text-right truncate max-w-[150px]">{{ $research->authors }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span class="text-slate-400 font-medium">Publication Year</span>
                        <span class="font-extrabold text-[#2d2b56]">{{ $research->year }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span class="text-slate-400 font-medium">Submitted On</span>
                        <span class="font-extrabold text-slate-600">{{ $research->created_at->format('M d, Y') }}</span>
                    </div>
                    @if($research->approved_at)
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span class="text-slate-400 font-medium">Approved On</span>
                        <span class="font-extrabold text-emerald-700">{{ $research->approved_at->format('M d, Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection