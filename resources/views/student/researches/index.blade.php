@extends('layouts.app')
@section('title', 'My Submissions — JHCSC Capstone Portal')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    .student-submissions-body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #2d2b56;
    }

    .table-container-card {
        background: #ffffff;
        border-radius: 24px;
        border: 1.5px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
    }

    .btn-upload-primary {
        background: #0c4233;
        color: #ffffff;
        padding: 10px 24px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 13px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.25s ease;
        box-shadow: 0 4px 14px rgba(12, 66, 51, 0.25);
    }

    .btn-upload-primary:hover {
        background: #082d23;
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(12, 66, 51, 0.35);
        color: #ffffff;
    }
</style>

<div class="student-submissions-body space-y-6 max-w-5xl mx-auto">
    {{-- HEADER SECTION --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-slate-200/80">
        <div>
            <h1 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 2rem;" class="font-extrabold text-[#2d2b56] tracking-tight leading-tight">
                My Capstone <span class="text-[#0c4233]">Submission</span>
            </h1>
            <p class="text-slate-500 text-xs font-medium mt-1">
                Manage your submitted capstone manuscript or upload a new paper.
            </p>
        </div>

        @if($researches->total() >= 1)
            <button disabled class="px-5 py-2.5 bg-slate-200 text-slate-400 font-extrabold text-xs rounded-full cursor-not-allowed flex items-center gap-2 shrink-0 border border-slate-300" title="1 Active Submission Limit Reached">
                🔒 Limit Reached (1/1)
            </button>
        @else
            <a href="{{ route('student.researches.create') }}" class="btn-upload-primary shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Upload Capstone
            </a>
        @endif
    </div>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <span class="text-base">✅</span>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-sm font-black">✕</button>
        </div>
    @endif

    @if(session('warning'))
        <div class="p-4 bg-amber-50 border border-amber-300 text-amber-900 rounded-2xl text-xs font-bold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <span class="text-base">⚠️</span>
                <span>{{ session('warning') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-amber-600 hover:text-amber-900 text-sm font-black">✕</button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-bold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <span class="text-base">⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-sm font-black">✕</button>
        </div>
    @endif

    {{-- SUBMISSION LIMIT NOTICE --}}
    @if($researches->total() >= 1)
        <div class="p-4 bg-amber-50/90 border border-amber-200 text-amber-950 rounded-2xl text-xs font-bold flex items-center gap-3 shadow-xs">
            <span class="text-xl shrink-0">ℹ️</span>
            <p class="text-xs text-amber-900 font-semibold leading-relaxed">
                Mayroon ka nang 1 active capstone submission. Upang mag-submit ng panibagong papel, burahin muna ang iyong kasalukuyang na-upload gamit ang <strong>Delete</strong> button sa ibaba.
            </p>
        </div>
    @endif

    {{-- SUBMISSION TABLE CONTAINER --}}
    <div class="table-container-card">
        <div class="p-5 border-b border-slate-100 bg-slate-50/80 flex items-center justify-between">
            <h3 class="font-extrabold text-sm text-[#2d2b56] flex items-center gap-2">
                <span>📑</span> Submitted Manuscript
            </h3>
            <span class="text-xs font-bold text-slate-400">{{ $researches->total() }} of 1 Allowed Submission</span>
        </div>

        @if($researches->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                            <th class="py-4 px-6">Manuscript Title</th>
                            <th class="py-4 px-6">Category</th>
                            <th class="py-4 px-6">Status</th>
                            <th class="py-4 px-6">Submitted Date</th>
                            <th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-semibold">
                        @foreach($researches as $research)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="space-y-1 max-w-md">
                                        <a href="{{ route('student.researches.show', $research) }}" class="font-extrabold text-[#2d2b56] hover:text-[#5840bb] transition line-clamp-2 leading-snug">
                                            {{ $research->title }}
                                        </a>
                                        <p class="text-[10px] text-slate-400 truncate">
                                            Authors: {{ $research->authors }}
                                        </p>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex px-3 py-1 rounded-full text-[11px] font-extrabold bg-purple-50 text-purple-800 border border-purple-100">
                                        {{ $research->category->name ?? 'Uncategorized' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    @if($research->status == 'pending')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                            Pending Review
                                        </span>
                                    @elseif($research->status == 'approved')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800">
                                            Approved
                                        </span>
                                    @elseif($research->status == 'rejected')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-rose-100 text-rose-800">
                                            ✕ Rejection
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-slate-500">
                                    {{ $research->created_at->format('M d, Y') }}
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('student.researches.show', $research) }}" 
                                           class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-[#2d2b56] rounded-full font-extrabold text-xs transition">
                                            View
                                        </a>

                                        @if($research->status == 'pending')
                                            <a href="{{ route('student.researches.edit', $research) }}" 
                                               class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-full font-extrabold text-xs transition">
                                                Edit
                                            </a>
                                        @endif

                                        <form action="{{ route('student.researches.destroy', $research) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this research submission? Doing so will allow you to upload a new manuscript.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-full font-extrabold text-xs transition">
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
            <div class="py-16 px-6 text-center space-y-4">
                <div class="w-16 h-16 bg-purple-50 text-[#5840bb] rounded-full flex items-center justify-center font-bold text-3xl mx-auto border border-purple-100">
                    📂
                </div>
                <div>
                    <h4 class="font-extrabold text-base text-[#2d2b56]">
                        No Submission Uploaded
                    </h4>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                        You have not submitted any capstone manuscripts yet. Upload your project to initiate faculty review.
                    </p>
                </div>
                <a href="{{ route('student.researches.create') }}" class="btn-upload-primary inline-flex">
                    Upload Your Capstone
                </a>
            </div>
        @endif
    </div>
</div>
@endsection