@extends('layouts.admin')
@section('title', $category->name . ' — Category Details')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-12">
    {{-- TOP BREADCRUMB / ACTION HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.categories.index') }}" class="text-xs font-semibold text-slate-500 hover:text-emerald-700 transition inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Categories
                </a>
                <span class="text-slate-300 text-xs">/</span>
                <span class="text-xs font-semibold text-slate-500">Details</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <span>{{ $category->name }}</span>
            </h1>
            @if($category->description)
                <p class="text-slate-600 text-xs font-medium mt-1 leading-relaxed max-w-2xl">
                    {{ $category->description }}
                </p>
            @else
                <p class="text-slate-400 text-xs font-medium mt-0.5">
                    Category overview and approved capstone manuscript records.
                </p>
            @endif
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('admin.categories.index') }}" 
               class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-xl border border-slate-200 shadow-xs transition inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Categories
            </a>

            <a href="{{ route('admin.categories.edit', $category) }}" 
               class="px-4 py-2 bg-[#0c4233] hover:bg-[#082d23] text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Category
            </a>
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl text-xs font-semibold">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-sm font-bold">✕</button>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center justify-between p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl text-xs font-semibold">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-sm font-bold">✕</button>
        </div>
    @endif

    {{-- SUMMARY STATS STRIP --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200/90 p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200/60 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Approved Papers</span>
                <span class="text-xl font-extrabold text-slate-900">{{ number_format($researches->total()) }}</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/90 p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-slate-50 text-slate-600 border border-slate-200/60 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                </svg>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Category Name</span>
                <span class="text-sm font-extrabold text-slate-900 truncate block max-w-[200px]" title="{{ $category->name }}">{{ $category->name }}</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/90 p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-slate-50 text-slate-600 border border-slate-200/60 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Created Date</span>
                <span class="text-sm font-bold text-slate-700">{{ $category->created_at ? $category->created_at->format('M d, Y') : 'N/A' }}</span>
            </div>
        </div>
    </div>

    {{-- APPROVED RESEARCHES TABLE CARD --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="font-bold text-sm text-slate-900">Approved Capstone Papers</h2>
                <p class="text-slate-400 text-[11px] font-medium mt-0.5">Manuscripts classified under this category</p>
            </div>
            <span class="text-xs font-bold text-slate-500 bg-slate-100 border border-slate-200 px-3 py-1 rounded-xl">
                Total: {{ $researches->total() }} {{ $researches->total() === 1 ? 'Paper' : 'Papers' }}
            </span>
        </div>

        <div class="overflow-x-auto min-h-[220px]">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-6 w-12">#</th>
                        <th class="py-3 px-6">Title</th>
                        <th class="py-3 px-6">Authors</th>
                        <th class="py-3 px-6">Year</th>
                        <th class="py-3 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($researches as $research)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-6 text-slate-400 font-medium">
                                {{ $loop->iteration + ($researches->currentPage() - 1) * $researches->perPage() }}
                            </td>
                            <td class="py-3.5 px-6 max-w-md">
                                <a href="{{ route('admin.researches.show', $research) }}" 
                                   class="font-bold text-slate-900 hover:text-emerald-700 transition leading-snug line-clamp-2">
                                    {{ $research->title }}
                                </a>
                            </td>
                            <td class="py-3.5 px-6 text-slate-600 font-medium">
                                {{ $research->authors }}
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="px-2.5 py-0.5 bg-slate-100 text-slate-700 text-[11px] font-bold rounded-full border border-slate-200">
                                    {{ $research->year }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-right whitespace-nowrap">
                                <a href="{{ route('admin.researches.show', $research) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-bold text-xs transition">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span>View Paper</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 text-xs font-medium">
                                No approved capstone papers currently classified under this category.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($researches->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $researches->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
