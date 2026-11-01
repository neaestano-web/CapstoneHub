@extends('layouts.admin')
@section('title', 'Manage Categories — JHCSC Capstone Portal')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    .admin-categories-body {
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

    .btn-create-primary {
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

    .btn-create-primary:hover {
        background: #082d23;
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(12, 66, 51, 0.35);
        color: #ffffff;
    }
</style>

<div class="admin-categories-body space-y-6 max-w-7xl mx-auto">
    {{-- HEADER SECTION --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-slate-200/80">
        <div>
            <h1 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 2rem;" class="font-extrabold text-[#2d2b56] tracking-tight leading-tight">
                Manage <span class="text-[#0c4233]">Categories</span>
            </h1>
            <p class="text-slate-500 text-xs font-medium mt-1">
                Organize and classify research papers into academic categories and sub-project types.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('admin.categories.create') }}" class="btn-create-primary">
                <span>➕</span> Add New Category
            </a>
        </div>
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

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-bold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <span class="text-base">⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-sm font-black">✕</button>
        </div>
    @endif

    {{-- TABLE CONTAINER CARD --}}
    <div class="table-container-card">
        <div class="p-5 border-b border-slate-100 bg-slate-50/80 flex items-center justify-between">
            <h3 class="font-extrabold text-sm text-[#2d2b56] flex items-center gap-2">
                <span>📂</span> Research Category Classifications
            </h3>
            <span class="text-xs font-bold text-slate-400">Total: {{ $categories->total() }} Categories</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">#</th>
                        <th class="py-4 px-6">Category Name</th>
                        <th class="py-4 px-6">Description</th>
                        <th class="py-4 px-6">Assigned Papers</th>
                        <th class="py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-semibold">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-6 text-slate-400 font-bold">
                                {{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-bold shrink-0">
                                        📂
                                    </div>
                                    <a href="{{ route('admin.categories.show', $category) }}" class="font-extrabold text-sm text-[#2d2b56] hover:text-[#5840bb] transition">
                                        {{ $category->name }}
                                    </a>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-slate-500 max-w-sm">
                                {{ Str::limit($category->description, 70) ?? 'No description provided' }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-3 py-1 bg-purple-50 text-purple-800 border border-purple-200 text-xs font-extrabold rounded-full">
                                    {{ number_format($category->classified_researches_count) }} papers
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.categories.show', $category) }}" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-[#2d2b56] rounded-full font-extrabold text-xs transition">
                                        View Details
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="px-3.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-full font-extrabold text-xs transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Permanently delete this category?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-3.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-full font-extrabold text-xs transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center space-y-3">
                                <div class="w-14 h-14 bg-emerald-50 text-[#0c4233] rounded-full flex items-center justify-center font-bold text-2xl mx-auto">
                                    📂
                                </div>
                                <h4 class="font-extrabold text-base text-[#2d2b56]">No Categories Found</h4>
                                <p class="text-xs text-slate-400 max-w-sm mx-auto">Start organizing capstone papers by creating your first category.</p>
                                <a href="{{ route('admin.categories.create') }}" class="btn-create-primary inline-flex mt-2">
                                    ➕ Create Category
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
