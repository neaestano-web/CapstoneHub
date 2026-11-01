@extends('layouts.admin')
@section('title', $category->name . ' — Category Details')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    .admin-category-show-body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #2d2b56;
    }

    .category-hero-admin {
        background: linear-gradient(135deg, #0c4233 0%, #082d23 100%);
        border-radius: 28px;
        padding: 32px;
        color: #ffffff;
        box-shadow: 0 14px 40px rgba(12, 66, 51, 0.25);
    }

    .card-detail-section {
        background: #ffffff;
        border-radius: 24px;
        border: 1.5px solid #e2e8f0;
        padding: 28px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
    }
</style>

<div class="admin-category-show-body space-y-6 max-w-7xl mx-auto">
    {{-- HERO HEADER --}}
    <div class="category-hero-admin space-y-4">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-full font-extrabold text-xs text-white transition inline-flex items-center gap-2">
                ← Back to Categories
            </a>

            <a href="{{ route('admin.categories.edit', $category) }}" class="px-4 py-2 bg-amber-400 text-amber-950 font-extrabold text-xs rounded-full shadow-xs transition">
                ✏️ Edit Category
            </a>
        </div>

        <div class="space-y-1">
            <span class="inline-flex items-center gap-2 px-3 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-emerald-900/80 text-emerald-200 border border-emerald-700/50">
                📂 Category Detail
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight">
                {{ $category->name }}
            </h1>
            <p class="text-xs text-emerald-100 font-medium">
                {{ $category->description ?? 'No description assigned for this category.' }}
            </p>
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold flex items-center justify-between">
            <span>✅ {{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 font-black">✕</button>
        </div>
    @endif

    {{-- PROJECT TYPES CARD --}}
    <div class="card-detail-section space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-base text-[#2d2b56] flex items-center gap-2">
                <span>🏷</span> Sub-Project Types / Subcategories
            </h3>
            <span class="text-xs font-bold text-slate-400">Total: {{ $category->projectTypes->count() }} Types</span>
        </div>

        {{-- Add Project Type Form --}}
        <form action="{{ route('admin.categories.projectTypes.store', $category) }}" method="POST" class="p-4 bg-slate-50 border border-slate-200/80 rounded-2xl flex flex-col md:flex-row items-end gap-3">
            @csrf
            <div class="flex-1 w-full">
                <label class="block text-xs font-extrabold text-[#2d2b56] uppercase tracking-wider mb-1">Project Type Name *</label>
                <input type="text" name="name" required placeholder="e.g., Web Application, Mobile App..." 
                       class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-semibold text-[#2d2b56] outline-none focus:border-[#0c4233]">
            </div>
            <div class="flex-1 w-full">
                <label class="block text-xs font-extrabold text-[#2d2b56] uppercase tracking-wider mb-1">Description <span class="text-slate-400 font-normal lowercase">(optional)</span></label>
                <input type="text" name="description" placeholder="Short description..." 
                       class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-semibold text-[#2d2b56] outline-none focus:border-[#0c4233]">
            </div>
            <button type="submit" class="px-5 py-2 bg-[#0c4233] text-white font-extrabold text-xs rounded-xl shadow-xs shrink-0 w-full md:w-auto">
                ➕ Add Subtype
            </button>
        </form>

        {{-- Project Types Grid --}}
        @if($category->projectTypes->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 pt-2">
                @foreach($category->projectTypes as $pt)
                    <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-2xl flex items-center justify-between gap-3 hover:border-emerald-300 transition">
                        <div>
                            <span class="font-extrabold text-xs text-[#2d2b56] block">🏷 {{ $pt->name }}</span>
                            @if($pt->description)
                                <p class="text-[11px] text-slate-400 font-medium leading-tight mt-0.5">{{ $pt->description }}</p>
                            @endif
                            <span class="text-[10px] font-bold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full inline-block mt-2">
                                {{ $pt->researches()->count() }} researches
                            </span>
                        </div>
                        <form action="{{ route('admin.categories.projectTypes.destroy', [$category, $pt]) }}" method="POST" onsubmit="return confirm('Delete sub-project type {{ addslashes($pt->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center text-xs font-bold">
                                🗑
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-8 text-center bg-slate-50 border border-dashed border-slate-200 rounded-2xl space-y-1">
                <span class="text-2xl block">🏷</span>
                <p class="text-xs font-bold text-slate-500">No project types added yet.</p>
                <p class="text-[11px] text-slate-400">Use the form above to add specific project subtypes under {{ $category->name }}.</p>
            </div>
        @endif
    </div>

    {{-- APPROVED RESEARCHES IN THIS CATEGORY --}}
    <div class="card-detail-section space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-extrabold text-base text-[#2d2b56] flex items-center gap-2">
                <span>📋</span> Approved Capstone Papers in {{ $category->name }}
            </h3>
            <span class="text-xs font-bold text-slate-400">Total: {{ $researches->total() }} Papers</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">#</th>
                        <th class="py-4 px-6">Title</th>
                        <th class="py-4 px-6">Authors</th>
                        <th class="py-4 px-6">Year</th>
                        <th class="py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-semibold">
                    @forelse($researches as $research)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-6 text-slate-400 font-bold">
                                {{ $loop->iteration + ($researches->currentPage() - 1) * $researches->perPage() }}
                            </td>
                            <td class="py-4 px-6 max-w-sm">
                                <a href="{{ route('admin.researches.show', $research) }}" class="font-extrabold text-[#2d2b56] hover:text-[#5840bb] transition leading-snug line-clamp-2">
                                    {{ $research->title }}
                                </a>
                            </td>
                            <td class="py-4 px-6 text-slate-600 font-bold">
                                {{ $research->authors }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-800 text-[11px] font-extrabold rounded-full">
                                    {{ $research->year }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('admin.researches.show', $research) }}" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-[#2d2b56] rounded-full font-extrabold text-xs transition">
                                    View Paper
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
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $researches->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
