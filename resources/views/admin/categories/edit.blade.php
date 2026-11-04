@extends('layouts.admin')
@section('title', 'Edit Category — JHCSC Capstone Portal')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    .admin-category-form-body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #2d2b56;
    }

    .form-card-admin {
        background: #ffffff;
        border-radius: 28px;
        border: 1.5px solid #e2e8f0;
        padding: 32px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
    }
</style>

<div class="admin-category-form-body space-y-6 max-w-3xl mx-auto">
    <div class="flex items-center justify-between pb-4 border-b border-slate-200/80">
        <div>
            <h1 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.85rem;" class="font-extrabold text-[#2d2b56] tracking-tight leading-tight">
                Edit <span class="text-[#0c4233]">Category</span>
            </h1>
            <p class="text-slate-500 text-xs font-medium mt-1">
                Update category details and manage sub-project type classifications.
            </p>
        </div>

        <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs rounded-full transition">
            ← Back to Categories
        </a>
    </div>

    @if($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-bold space-y-1">
            @foreach($errors->all() as $error)
                <p>⚠️ {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="form-card-admin space-y-6">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')
            <input type="hidden" name="redirect_to" value="{{ url()->previous() !== url()->current() ? url()->previous() : route('admin.categories.index') }}">

            <div>
                <label for="name" class="block text-xs font-extrabold text-[#2d2b56] uppercase tracking-wider mb-2">
                    Category Name *
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
                       class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3.5 text-xs font-semibold text-[#2d2b56] outline-none focus:border-[#0c4233] focus:bg-white transition">
            </div>

            <div>
                <label for="description" class="block text-xs font-extrabold text-[#2d2b56] uppercase tracking-wider mb-2">
                    Category Description
                </label>
                <textarea name="description" id="description" rows="4"
                          placeholder="Brief summary of research scope for this category..."
                          class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3.5 text-xs font-medium text-[#2d2b56] outline-none focus:border-[#0c4233] focus:bg-white transition">{{ old('description', $category->description) }}</textarea>
            </div>

            {{-- Existing Sub-Project Types --}}
            <div class="pt-4 border-t border-slate-100 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-xs font-extrabold text-[#2d2b56] uppercase tracking-wider">
                            Sub-Project Types
                        </label>
                        <p class="text-[11px] text-slate-400 font-medium">Modify existing project types or append new subcategories</p>
                    </div>
                    <button type="button" onclick="addProjectType()" class="px-3.5 py-1.5 bg-emerald-50 text-[#0c4233] hover:bg-emerald-100 font-extrabold text-xs rounded-full transition">
                        + Add Type
                    </button>
                </div>

                @if($category->projectTypes->count() > 0)
                    <div class="space-y-2">
                        <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Existing Project Types</span>
                        @foreach($category->projectTypes as $pt)
                            <div class="flex items-center gap-3 bg-slate-50 p-2.5 rounded-xl border border-slate-200/80">
                                <input type="hidden" name="existing_project_types[{{ $pt->id }}][id]" value="{{ $pt->id }}">
                                <input type="text" name="existing_project_types[{{ $pt->id }}][name]" value="{{ $pt->name }}" required
                                       class="flex-1 bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-xs font-semibold text-[#2d2b56] outline-none focus:border-[#0c4233]">
                                <label class="inline-flex items-center gap-1.5 text-xs font-extrabold text-rose-600 cursor-pointer">
                                    <input type="checkbox" name="delete_project_types[]" value="{{ $pt->id }}" class="rounded text-rose-600">
                                    <span>Delete</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div id="project-types-container" class="space-y-2 pt-2">
                    <!-- Dynamic new project types append here -->
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.categories.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs rounded-full transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-[#0c4233] hover:bg-[#082d23] text-white font-extrabold text-xs rounded-full shadow-xs transition">
                    Update Category
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function addProjectType() {
    const container = document.getElementById('project-types-container');
    const div = document.createElement('div');
    div.className = 'flex items-center gap-2';
    div.innerHTML = `
        <input type="text" name="new_project_types[]" placeholder="e.g. Mobile Application" class="flex-1 bg-slate-50 border border-emerald-300 rounded-xl p-2.5 text-xs font-semibold text-[#2d2b56] outline-none focus:border-[#0c4233]" required>
        <button type="button" onclick="this.parentElement.remove()" class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center font-bold text-xs">✕</button>
    `;
    container.appendChild(div);
}
</script>
@endsection