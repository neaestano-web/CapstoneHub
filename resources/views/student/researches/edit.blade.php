@extends('layouts.app')

@section('content')
<style>
    .upload-hero{background:linear-gradient(135deg,#0f2e0f 0%,#166534 50%,#15803d 100%);padding:36px 0 48px;border-radius:0 0 24px 24px;position:relative;overflow:hidden}
    .upload-hero::before{content:'';position:absolute;top:-50%;right:-15%;width:300px;height:300px;background:rgba(255,255,255,.04);border-radius:50%}
    .upload-hero *{position:relative;z-index:1}
    .form-card{background:white;border-radius:20px;border:1px solid #e5e7eb;box-shadow:0 8px 30px rgba(0,0,0,.04);overflow:hidden}
    .form-card .accent-bar{height:5px;background:linear-gradient(90deg,#166534,#22c55e,#86efac)}
    .form-input{border:1.5px solid #e2e8f0;border-radius:12px;padding:14px 18px;font-size:14px;transition:all .3s;width:100%;outline:none;background:#f8fafc}
    .form-input:focus{border-color:#22c55e;box-shadow:0 0 0 4px rgba(34,197,94,.12);background:white}
    .form-label{display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:8px}
    .form-label .required{color:#dc2626;margin-left:2px}
    .check-card{display:flex;align-items:center;gap:12px;padding:12px 16px;background:white;border:1.5px solid #e2e8f0;border-radius:12px;cursor:pointer;transition:all .2s;user-select:none}
    .check-card:hover{border-color:#22c55e;background:#f0fdf4}
    .check-card.is-checked{border-color:#22c55e;background:#f0fdf4}
    .check-card input[type=checkbox]{width:18px;height:18px;accent-color:#22c55e;cursor:pointer;flex-shrink:0}
    .section-divider{border:none;border-top:2px dashed #e5e7eb;margin:28px 0}
</style>

<div class="mb-6 pb-4 border-b border-slate-100">
    <a href="{{ route('student.researches.index') }}"
       class="inline-flex items-center gap-2 mb-4 px-4 py-1.5 rounded-full text-xs font-bold text-[#5840bb] bg-purple-50 hover:bg-purple-100 border border-purple-100 transition">
        ← Back to My Submissions
    </a>
    <h1 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 2.15rem;" class="font-extrabold text-[#2d2b56] tracking-tight leading-tight">
        Edit Capstone <span class="text-[#0c4233]">Submission</span>
    </h1>
    <p class="text-slate-500 text-xs font-medium mt-1">Update your pending manuscript details or replace attached document file.</p>
</div>

<div class="max-w-4xl mx-auto space-y-6 pb-12">
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="form-card">
        <div class="accent-bar"></div>
        <div class="p-8">
            <form action="{{ route('student.researches.update', $research) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="space-y-5">
                    <div>
                        <label for="title" class="form-label">Title <span class="required">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title', $research->title) }}" class="form-input" required>
                    </div>
                    @php
                        $currentAuthors = old('authors')
                            ? (is_array(old('authors')) ? old('authors') : explode(',', old('authors')))
                            : explode(',', $research->authors ?? '');
                        $currentAuthors = array_values(array_filter(array_map('trim', $currentAuthors)));
                        if (empty($currentAuthors)) {
                            $currentAuthors = [auth()->user()->name];
                        }
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="form-label m-0">Authors / Researchers <span class="required">*</span></label>
                                <button type="button" onclick="addAuthorRow()" class="text-xs text-emerald-700 hover:text-emerald-800 font-extrabold flex items-center gap-1 bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1 rounded-full border border-emerald-200 transition cursor-pointer">
                                    + Add Co-Author
                                </button>
                            </div>
                            <div id="authorsListContainer" class="space-y-2">
                                @foreach($currentAuthors as $idx => $authorName)
                                    <div class="flex items-center gap-2 author-row">
                                        <input type="text" name="authors[]" value="{{ $authorName }}"
                                               class="form-input flex-1" placeholder="Author full name" required
                                               oninput="handleAuthorInput(this)">
                                        @if($idx > 0)
                                            <button type="button" onclick="removeAuthorRow(this)" class="px-2.5 py-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition cursor-pointer" title="Remove Co-Author">
                                                ✕
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1.5">
                                Maaaring maglagay ng higit sa isang author / miyembro ng research team.
                            </p>
                        </div>
                        <div>
                            <label for="year" class="form-label">Publication / Defense Year <span class="required">*</span></label>
                            <input type="number" name="year" id="year" value="{{ old('year', $research->year) }}" min="1900" max="{{ date('Y')+1 }}" class="form-input" required>
                        </div>
                    </div>
                    <div>
                        <label for="abstract" class="form-label">Abstract <span class="required">*</span></label>
                        <textarea name="abstract" id="abstract" rows="4" class="form-input" required>{{ old('abstract', $research->abstract) }}</textarea>
                    </div>
                    <div>
                        <label for="project_context" class="form-label">Project Context <span class="required">*</span></label>
                        <textarea name="project_context" id="project_context" rows="4" class="form-input" placeholder="Describe the background, context, or community/organizational setting of the project..." required>{{ old('project_context', $research->project_context) }}</textarea>
                    </div>
                    <div>
                        <label for="objectives" class="form-label">Objectives of the Study <span class="required">*</span></label>
                        <textarea name="objectives" id="objectives" rows="4" class="form-input" required>{{ old('objectives', $research->objectives) }}</textarea>
                    </div>
                    <div>
                        <label for="keywords" class="form-label">Keywords <span style="font-weight:400;color:#94a3b8">(Optional)</span></label>
                        <input type="text" name="keywords" id="keywords" value="{{ old('keywords', $research->keywords) }}" class="form-input">
                    </div>
                </div>

                <hr class="section-divider">

                {{-- Categories --}}
                @php $selectedCatIds = old('category_ids', $research->categories->pluck('id')->toArray()); @endphp
                <div style="margin-bottom:20px">
                    <label class="form-label">📂 Categories <span class="required">*</span></label>
                    <div class="space-y-2">
                        @foreach($categories as $cat)
                            <label class="check-card {{ in_array($cat->id, $selectedCatIds) ? 'is-checked' : '' }}"
                                   onclick="this.classList.toggle('is-checked', this.querySelector('input').checked)">
                                <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}"
                                       {{ in_array($cat->id, $selectedCatIds) ? 'checked' : '' }}
                                       onchange="this.parentElement.classList.toggle('is-checked', this.checked)">
                                <span style="font-size:14px;font-weight:600;color:#374151">{{ $cat->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Project Types --}}
                @php $selectedPtIds = old('project_type_ids', $research->projectTypes->pluck('id')->toArray()); @endphp
                @php $hasProjectTypes = $categories->sum(fn($c) => $c->projectTypes->count()) > 0; @endphp
                @if($hasProjectTypes)
                <div style="margin-bottom:20px">
                    <label class="form-label">🏷 Project Types</label>
                    <div class="space-y-2">
                        @foreach($categories as $cat)
                            @foreach($cat->projectTypes as $pt)
                                <label class="check-card {{ in_array($pt->id, $selectedPtIds) ? 'is-checked' : '' }}"
                                       onclick="this.classList.toggle('is-checked', this.querySelector('input').checked)">
                                    <input type="checkbox" name="project_type_ids[]" value="{{ $pt->id }}"
                                           {{ in_array($pt->id, $selectedPtIds) ? 'checked' : '' }}
                                           onchange="this.parentElement.classList.toggle('is-checked', this.checked)">
                                    <span style="font-size:14px;font-weight:600;color:#374151">{{ $pt->name }}</span>
                                    <span style="font-size:11px;color:#6b7280">— {{ $cat->name }}</span>
                                </label>
                            @endforeach
                        @endforeach
                    </div>
                </div>
                @endif

                <hr class="section-divider">

                <div>
                    <label class="form-label">Research Files (Multiple)</label>
                    <input type="file" name="files[]" id="files" multiple accept=".pdf,.docx" class="form-input">
                    <p class="mt-1 text-xs text-gray-500">Leave blank to keep current files. Uploading new files will replace existing ones (Ang unang file ang unang lilitaw sa viewer).</p>
                    
                    @if($research->files && $research->files->count() > 0)
                        <div class="mt-3 space-y-2">
                            <p class="text-xs font-bold text-slate-700">Kasalukuyang mga File (Viewing sequence):</p>
                            @foreach($research->files as $rf)
                                <div class="flex items-center justify-between p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded font-extrabold {{ $rf->sort_order === 1 ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700' }}">
                                            {{ $rf->sort_order === 1 ? '🥇 #1 Primary' : '#' . $rf->sort_order }}
                                        </span>
                                        <span class="font-semibold text-slate-800">{{ $rf->original_name }}</span>
                                    </div>
                                    <a href="{{ route('repository.show', $research->id) }}" target="_blank" class="text-emerald-700 hover:underline font-bold">
                                        View
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @elseif($research->pdf_path)
                        <a href="{{ route('repository.show', $research->id) }}" target="_blank"
                           class="text-sm text-green-700 hover:underline mt-2 inline-block">📎 View Current File</a>
                    @endif
                </div>

                <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100 mt-6">
                    <a href="{{ route('student.researches.index') }}"
                       style="padding:12px 28px;background:white;color:#475569;border:1.5px solid #e2e8f0;border-radius:14px;font-size:14px;font-weight:600;text-decoration:none">Cancel</a>
                    <button type="submit"
                            style="padding:12px 28px;background:linear-gradient(135deg,#166534,#15803d);color:white;border:none;border-radius:14px;font-size:14px;font-weight:700;cursor:pointer">
                        Update Research
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Dynamic Multi-Author Management
function addAuthorRow(initialValue = '') {
    const container = document.getElementById('authorsListContainer');
    const div = document.createElement('div');
    div.className = 'flex items-center gap-2 author-row';
    div.innerHTML = `
        <input type="text" name="authors[]" value="${initialValue}"
               class="form-input flex-1" placeholder="Co-author full name" required
               oninput="handleAuthorInput(this)">
        <button type="button" onclick="removeAuthorRow(this)" class="px-2.5 py-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition cursor-pointer" title="Remove Co-Author">
            ✕
        </button>
    `;
    container.appendChild(div);
    const input = div.querySelector('input');
    if (input && !initialValue) input.focus();
}

function removeAuthorRow(btn) {
    const row = btn.closest('.author-row');
    if (row) {
        row.remove();
    }
}

function handleAuthorInput(input) {
    const val = input.value;
    if (val.includes(',')) {
        const parts = val.split(',').map(s => s.trim()).filter(s => s.length > 0);
        if (parts.length > 1) {
            input.value = parts[0];
            for (let i = 1; i < parts.length; i++) {
                addAuthorRow(parts[i]);
            }
        }
    }
}
</script>
@endsection