@extends('layouts.admin')
@section('title', 'Upload Capstone — Admin Catalog')
@section('breadcrumb', 'Upload Capstone')

@section('content')
<style>
    .admin-form-card {
        background-color: var(--guest-bg-surface, #ffffff);
        border: 1px solid var(--guest-border, #e2e8f0);
        border-radius: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }
    .admin-form-subtle {
        background-color: var(--guest-bg-input, #f8fafc);
        border: 1px solid var(--guest-border, #e2e8f0);
        border-radius: 0.75rem;
    }
    .admin-title {
        color: var(--guest-text-headings, #0f172a) !important;
    }
    .admin-muted {
        color: var(--guest-text-muted, #64748b) !important;
    }
    .admin-border {
        border-color: var(--guest-border, #e2e8f0) !important;
    }
    .admin-input {
        background-color: var(--guest-bg-input, #f8fafc);
        border: 1px solid var(--guest-border, #cbd5e1);
        border-radius: 0.75rem;
        padding: 0.65rem 0.95rem;
        font-size: 0.875rem;
        width: 100%;
        color: var(--guest-text-headings, #0f172a);
        outline: none;
        transition: all 0.2s ease;
    }
    .admin-input:focus {
        border-color: #0c4233;
        box-shadow: 0 0 0 3px rgba(12, 66, 51, 0.1);
        background-color: var(--guest-bg-surface, #ffffff);
    }
    .custom-repo-cb {
        accent-color: #0c4233;
        width: 16px;
        height: 16px;
        border-radius: 4px;
        cursor: pointer;
    }
    .spinner {
        display: inline-block;
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Dark and OLED Adaptations */
    html[data-guest-theme="dark"] .admin-form-card,
    html[data-guest-theme="oled"] .admin-form-card,
    html[data-admin-theme="dark"] .admin-form-card,
    html[data-admin-theme="oled"] .admin-form-card {
        background: #111827 !important;
        border-color: #1f2937 !important;
    }

    html[data-guest-theme="dark"] .admin-input,
    html[data-guest-theme="oled"] .admin-input,
    html[data-admin-theme="dark"] .admin-input,
    html[data-admin-theme="oled"] .admin-input {
        background: #1f2937 !important;
        border-color: #374151 !important;
        color: #f8fafc !important;
    }

    html[data-guest-theme="dark"] .admin-title,
    html[data-guest-theme="oled"] .admin-title,
    html[data-admin-theme="dark"] .admin-title,
    html[data-admin-theme="oled"] .admin-title {
        color: #f8fafc !important;
    }
</style>

<div class="max-w-5xl mx-auto space-y-6 pb-16">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b admin-border">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold uppercase tracking-wider admin-muted">Admin Catalog</span>
                <span class="admin-muted opacity-40">/</span>
                <a href="{{ route('admin.researches.index') }}" class="text-xs font-bold uppercase tracking-wider admin-muted hover:text-emerald-700 transition">Manage Researches</a>
                <span class="admin-muted opacity-40">/</span>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Upload</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight admin-title">
                Upload & Publish Capstone
            </h1>
            <p class="text-xs sm:text-sm admin-muted mt-0.5">
                Directly upload, classify, and publish approved research papers to the institutional catalog.
            </p>
        </div>

        <a href="{{ route('admin.researches.index') }}"
           class="admin-form-card px-3.5 py-2 text-xs font-bold admin-title inline-flex items-center gap-1.5 hover:border-[#0c4233] transition shrink-0">
            ← Back to Researches
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-800 dark:text-emerald-300 text-xs font-bold flex items-center gap-2.5">
            <span class="text-base">✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('warning'))
        <div class="p-4 bg-amber-500/10 border border-amber-500/25 rounded-xl text-amber-900 dark:text-amber-200 text-xs font-bold flex items-center gap-2.5">
            <span class="text-base">⚠️</span>
            <span>{{ session('warning') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-rose-500/10 border border-rose-500/20 rounded-xl text-rose-800 dark:text-rose-300 text-xs font-bold flex items-center gap-2.5">
            <span class="text-base">❌</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 bg-rose-500/10 border border-rose-500/20 rounded-xl text-rose-800 dark:text-rose-300 text-xs font-semibold">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="admin-form-card p-6 sm:p-8">
        <form action="{{ route('admin.researches.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm" class="space-y-8">
            @csrf

            {{-- STEP 1: Research Details --}}
            <div class="space-y-4">
                <div class="flex items-center gap-2.5 pb-2 border-b admin-border">
                    <span class="w-6 h-6 rounded-md bg-[#0c4233] text-white font-bold text-xs inline-flex items-center justify-center">1</span>
                    <h2 class="text-base font-bold admin-title">Research Details</h2>
                </div>

                <div class="space-y-4 pt-1">
                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-xs font-bold uppercase tracking-wider admin-title mb-1.5">
                            Research Title <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}"
                               class="admin-input" placeholder="e.g. Development of Student Attendance Monitoring System" required autocomplete="off">

                        {{-- Real-time Duplicate / Existed Title Warning Alert --}}
                        <div id="duplicateTitleWarning" style="display:none;" class="mt-2.5 p-3.5 bg-amber-500/10 border border-amber-500/30 rounded-xl text-amber-900 dark:text-amber-200 text-xs shadow-xs">
                            <div class="flex items-start gap-2.5">
                                <span class="text-base leading-none shrink-0">⚠️</span>
                                <div class="space-y-1.5 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="font-bold text-amber-950 dark:text-amber-100">
                                            Warning / Alert: Mayroon nang umiiral na capstone sa repository na may pareho o katulad na pamagat!
                                        </p>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-500/20 text-amber-900 dark:text-amber-200 border border-amber-500/30 uppercase">
                                            Existed Project
                                        </span>
                                    </div>
                                    <div class="admin-form-card p-2.5 border-amber-500/20">
                                        <p class="font-semibold admin-title">📌 Existing Title: <span id="dupMatchTitle" class="text-amber-700 dark:text-amber-300 font-bold"></span></p>
                                        <p class="text-[11px] admin-muted mt-0.5">
                                            👤 Authors: <span id="dupMatchAuthors"></span> · 📅 Year: <span id="dupMatchYear" class="font-mono"></span> · Status: <span id="dupMatchStatus" class="font-bold uppercase text-[10px] px-1.5 py-0.5 rounded admin-form-subtle"></span>
                                        </p>
                                    </div>
                                    <p class="text-[11px] text-amber-800 dark:text-amber-300 font-medium">
                                        Note: Ensure the study is unique and original before publishing to the official repository.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Authors & Year --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold uppercase tracking-wider admin-title m-0">
                                    Authors / Researchers <span class="text-rose-500">*</span>
                                </label>
                                <button type="button" onclick="addAuthorRow()" class="text-[11px] text-emerald-700 dark:text-emerald-400 hover:underline font-bold flex items-center gap-1 cursor-pointer">
                                    + Add Co-Author
                                </button>
                            </div>
                            <div id="authorsListContainer" class="space-y-2">
                                <div class="flex items-center gap-2 author-row">
                                    <input type="text" name="authors[]" value="{{ old('authors.0', '') }}"
                                           class="admin-input flex-1" placeholder="Primary Author full name" required
                                           oninput="handleAuthorInput(this)">
                                </div>
                                @if(old('authors') && is_array(old('authors')) && count(old('authors')) > 1)
                                    @foreach(array_slice(old('authors'), 1) as $idx => $extraAuthor)
                                        <div class="flex items-center gap-2 author-row">
                                            <input type="text" name="authors[]" value="{{ $extraAuthor }}"
                                                   class="admin-input flex-1" placeholder="Co-author full name" required
                                                   oninput="handleAuthorInput(this)">
                                            <button type="button" onclick="removeAuthorRow(this)" class="px-2.5 py-1.5 text-rose-500 hover:text-rose-700 rounded-lg transition" title="Remove Co-Author">
                                                ✕
                                            </button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <p class="text-[11px] admin-muted mt-1">Maaaring maglagay ng higit sa isang author / miyembro ng research team.</p>
                        </div>

                        <div>
                            <label for="year" class="block text-xs font-bold uppercase tracking-wider admin-title mb-1.5">
                                Publication / Defense Year <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" name="year" id="year" value="{{ old('year', date('Y')) }}"
                                   min="1900" max="{{ date('Y') + 1 }}" class="admin-input" required>
                        </div>
                    </div>

                    {{-- Abstract --}}
                    <div>
                        <label for="abstract" class="block text-xs font-bold uppercase tracking-wider admin-title mb-1.5">
                            Abstract <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="abstract" id="abstract" rows="4" class="admin-input leading-relaxed"
                                  placeholder="Provide a comprehensive summary of the research..." required>{{ old('abstract') }}</textarea>
                    </div>

                    {{-- Project Context --}}
                    <div>
                        <label for="project_context" class="block text-xs font-bold uppercase tracking-wider admin-title mb-1.5">
                            Project Context <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="project_context" id="project_context" rows="3" class="admin-input leading-relaxed"
                                  placeholder="Describe the background, context, or community setting of the project..." required>{{ old('project_context') }}</textarea>
                    </div>

                    {{-- Objectives --}}
                    <div>
                        <label for="objectives" class="block text-xs font-bold uppercase tracking-wider admin-title mb-1.5">
                            Objectives of the Study <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="objectives" id="objectives" rows="3" class="admin-input leading-relaxed"
                                  placeholder="List the specific objectives of the research..." required>{{ old('objectives') }}</textarea>
                    </div>

                    {{-- Keywords --}}
                    <div>
                        <label for="keywords" class="block text-xs font-bold uppercase tracking-wider admin-title mb-1.5">
                            Keywords <span class="admin-muted font-normal lowercase">(optional, comma-separated)</span>
                        </label>
                        <input type="text" name="keywords" id="keywords" value="{{ old('keywords') }}"
                               class="admin-input" placeholder="e.g. machine learning, attendance, IoT">
                    </div>
                </div>
            </div>

            {{-- STEP 2: Category Classification --}}
            <div class="space-y-4 pt-4 border-t admin-border">
                <div class="flex items-center gap-2.5 pb-2 border-b admin-border">
                    <span class="w-6 h-6 rounded-md bg-[#0c4233] text-white font-bold text-xs inline-flex items-center justify-center">2</span>
                    <div>
                        <h2 class="text-base font-bold admin-title">Category Classification</h2>
                        <p class="text-xs admin-muted">Select research categories or run AI Auto-Classification to suggest matching tags.</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 pt-1">
                    <button type="button" id="classifyBtn" class="px-4 py-2.5 bg-[#0c4233] text-white hover:bg-[#115e47] rounded-xl text-xs font-bold transition inline-flex items-center justify-center gap-2 shadow-xs cursor-pointer" onclick="runClassify()">
                        <span>🤖</span>
                        <span id="classifyBtnText">Auto-Classify Capstone</span>
                    </button>
                    <button type="button" onclick="toggleManualCategories()" class="px-4 py-2.5 admin-form-subtle text-xs font-bold admin-title rounded-xl transition inline-flex items-center justify-center gap-1.5 border admin-border cursor-pointer">
                        <span>📁</span>
                        <span id="manualToggleBtnText">Hide Manual Categories ▴</span>
                    </button>
                </div>

                {{-- AI Suggestions Panel --}}
                <div id="classificationPanel" style="display:none;" class="admin-form-subtle p-5 space-y-4">
                    <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                        <span>🤖</span>
                        <span>AI Suggestions — Review & Confirm</span>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider admin-title">
                            Categories <span class="text-rose-500">*</span>
                        </label>
                        <p class="text-[11px] admin-muted">Select at least one category. Suggested items are pre-checked.</p>
                        <div id="categoryCheckboxes" class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                            {{-- Populated dynamically --}}
                        </div>
                    </div>

                    <div class="space-y-2 pt-2 border-t admin-border">
                        <label class="block text-xs font-bold uppercase tracking-wider admin-title">
                            Project Types
                        </label>
                        <div id="projectTypeCheckboxes" class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                            {{-- Populated dynamically --}}
                        </div>
                        <div id="noProjectTypes" style="display:none;" class="p-3 admin-form-card text-center text-xs admin-muted border-dashed">
                            No project types available under these categories.
                        </div>
                    </div>
                </div>

                {{-- Manual Category Selection Panel --}}
                <div id="manualPanel" style="display:block;" class="admin-form-subtle p-5 space-y-4">
                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider admin-title">
                            Categories <span class="text-rose-500">*</span>
                        </label>
                        <div id="manualCategoryCheckboxes" class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                            @foreach($categories as $cat)
                                <label class="admin-form-card p-2.5 flex items-center gap-2.5 text-xs font-semibold admin-title cursor-pointer hover:border-[#0c4233] transition">
                                    <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}" class="custom-repo-cb"
                                           {{ in_array($cat->id, old('category_ids', [])) ? 'checked' : '' }}>
                                    <span class="truncate">{{ $cat->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="space-y-2 pt-2 border-t admin-border">
                        <label class="block text-xs font-bold uppercase tracking-wider admin-title">Project Types</label>
                        <div id="manualProjectTypeCheckboxes" class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                            @foreach($categories as $cat)
                                @foreach($cat->projectTypes as $pt)
                                    <label class="admin-form-card p-2.5 flex items-center gap-2.5 text-xs font-semibold admin-title cursor-pointer hover:border-[#0c4233] transition">
                                        <input type="checkbox" name="project_type_ids[]" value="{{ $pt->id }}" class="custom-repo-cb"
                                               {{ in_array($pt->id, old('project_type_ids', [])) ? 'checked' : '' }}>
                                        <span class="truncate">{{ $pt->name }}</span>
                                        <span class="text-[10px] admin-muted">— {{ $cat->name }}</span>
                                    </label>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- STEP 3: Multiple Files Upload --}}
            <div class="space-y-4 pt-4 border-t admin-border">
                <div class="flex items-center justify-between pb-2 border-b admin-border flex-wrap gap-2">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-md bg-[#0c4233] text-white font-bold text-xs inline-flex items-center justify-center">3</span>
                        <div>
                            <h2 class="text-base font-bold admin-title">Upload Research Files <span class="text-rose-500">*</span></h2>
                            <p class="text-xs admin-muted">PDF or Word (.docx) files. The first file (#1) serves as the primary document.</p>
                        </div>
                    </div>
                    <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full admin-form-subtle admin-muted border admin-border">
                        Multiple Files Allowed (.pdf, .docx)
                    </span>
                </div>

                {{-- Dropzone Area --}}
                <div id="dropZone" class="border-2 border-dashed admin-border rounded-xl p-8 text-center hover:border-emerald-600 transition cursor-pointer admin-form-subtle" onclick="document.getElementById('multiFileInput').click()">
                    <input type="file" id="multiFileInput" multiple accept=".pdf,.docx" class="hidden" onchange="handleFileSelect(event)">
                    <input type="file" name="files[]" id="actualFileInput" multiple class="hidden">
                    
                    <div class="w-10 h-10 admin-form-card flex items-center justify-center text-xl mx-auto mb-2">
                        📂
                    </div>
                    <p class="text-xs sm:text-sm font-bold admin-title">
                        Click to select files, or drag and drop files here
                    </p>
                    <p class="text-[11px] admin-muted mt-0.5">
                        Supported: PDF (.pdf) or Word (.docx) · Maximum 15MB each
                    </p>
                </div>

                {{-- Selected Files List --}}
                <div id="fileListContainer" class="space-y-2" style="display:none;">
                    <div class="flex items-center justify-between text-xs font-bold admin-muted px-1">
                        <span>Selected Files (Viewing Sequence):</span>
                        <span id="fileCountBadge" class="text-emerald-700 dark:text-emerald-300 font-mono">0 files</span>
                    </div>
                    <div id="fileItemsList" class="space-y-2">
                        {{-- Populated by JavaScript --}}
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-end gap-3 pt-6 border-t admin-border">
                <a href="{{ route('admin.researches.index') }}" class="admin-form-card px-4 py-2.5 text-xs font-bold admin-muted hover:admin-title transition">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-[#0c4233] text-white hover:bg-[#115e47] rounded-xl text-xs font-bold transition inline-flex items-center gap-1.5 shadow-sm" id="submitBtn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <span>Upload & Publish Capstone</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const allCategories = @json($categories);

// Toggle manual categories panel
function toggleManualCategories() {
    const manualPanel = document.getElementById('manualPanel');
    const btnText = document.getElementById('manualToggleBtnText');
    if (!manualPanel) return;

    if (manualPanel.style.display === 'none' || manualPanel.style.display === '') {
        manualPanel.style.display = 'block';
        if (btnText) btnText.textContent = 'Hide Manual Categories ▴';
    } else {
        manualPanel.style.display = 'none';
        if (btnText) btnText.textContent = 'Select Categories Manually ▾';
    }
}

// AI Classification Runner
function runClassify() {
    const title = document.getElementById('title').value.trim();
    const abstract = document.getElementById('abstract').value.trim();
    const project_context = document.getElementById('project_context') ? document.getElementById('project_context').value.trim() : '';
    const objectives = document.getElementById('objectives').value.trim();

    if (!title || !abstract || !project_context || !objectives) {
        alert('Please fill in Title, Abstract, Project Context, and Objectives first before classifying.');
        return;
    }

    const btn = document.getElementById('classifyBtn');
    const btnText = document.getElementById('classifyBtnText');
    btn.disabled = true;
    btnText.innerHTML = '<span class="spinner"></span> Analyzing research...';

    fetch('{{ route("admin.researches.classify") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ title, abstract, project_context, objectives })
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btnText.innerHTML = '🤖 Re-classify Capstone';

        if (data.success) {
            document.getElementById('manualPanel').style.display = 'none';
            showAIResults(data.categories, data.project_types);
        } else {
            document.getElementById('classificationPanel').style.display = 'none';
            document.getElementById('manualPanel').style.display = 'block';
        }
    })
    .catch(() => {
        btn.disabled = false;
        btnText.innerHTML = 'Auto-Classify Capstone';
        document.getElementById('classificationPanel').style.display = 'none';
        document.getElementById('manualPanel').style.display = 'block';
    });
}

function showAIResults(suggestedCats, suggestedPts) {
    const panel = document.getElementById('classificationPanel');
    panel.style.display = 'block';

    const suggestedCategoryIds = new Set([
        ...suggestedCats.map(category => category.id),
        ...suggestedPts.map(projectType => projectType.category_id),
    ]);

    const catContainer = document.getElementById('categoryCheckboxes');
    catContainer.innerHTML = '';

    allCategories.forEach(cat => {
        const suggested = suggestedCats.find(s => s.id === cat.id);
        const isChecked = !!suggested || suggestedCategoryIds.has(cat.id);
        const label = document.createElement('label');
        label.className = 'admin-form-card p-2.5 flex items-center gap-2.5 text-xs font-semibold admin-title cursor-pointer hover:border-[#0c4233] transition';
        label.innerHTML = `
            <input type="checkbox" name="category_ids[]" value="${cat.id}" ${isChecked ? 'checked' : ''} class="custom-repo-cb">
            <span class="truncate">${cat.name}</span>
        `;
        catContainer.appendChild(label);
    });

    const ptContainer = document.getElementById('projectTypeCheckboxes');
    ptContainer.innerHTML = '';
    let hasPts = false;

    allCategories.forEach(cat => {
        if (cat.project_types && cat.project_types.length > 0) {
            cat.project_types.forEach(pt => {
                hasPts = true;
                const suggested = suggestedPts.find(s => s.id === pt.id);
                const isChecked = !!suggested;
                const label = document.createElement('label');
                label.className = 'admin-form-card p-2.5 flex items-center gap-2.5 text-xs font-semibold admin-title cursor-pointer hover:border-[#0c4233] transition';
                label.innerHTML = `
                    <input type="checkbox" name="project_type_ids[]" value="${pt.id}" ${isChecked ? 'checked' : ''} class="custom-repo-cb">
                    <span class="truncate">${pt.name}</span>
                    <span class="text-[10px] admin-muted">— ${cat.name}</span>
                `;
                ptContainer.appendChild(label);
            });
        }
    });

    document.getElementById('noProjectTypes').style.display = hasPts ? 'none' : 'block';
}

// Real-time Duplicate Title Checking
let titleDebounceTimer = null;
window.hasDuplicateTitle = false;
window.duplicateMatchTitle = '';

const titleInput = document.getElementById('title');
if (titleInput) {
    titleInput.addEventListener('input', function() {
        clearTimeout(titleDebounceTimer);
        const val = this.value.trim();
        if (val.length < 4) {
            hideDuplicateWarning();
            return;
        }

        titleDebounceTimer = setTimeout(() => {
            checkDuplicateTitle(val);
        }, 350);
    });

    if (titleInput.value.trim().length >= 4) {
        checkDuplicateTitle(titleInput.value.trim());
    }
}

function checkDuplicateTitle(title) {
    fetch('{{ route("admin.researches.check-exists") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ title: title })
    })
    .then(r => r.json())
    .then(data => {
        if (data.exists && data.research) {
            showDuplicateWarning(data.research);
        } else {
            hideDuplicateWarning();
        }
    })
    .catch(() => {});
}

function showDuplicateWarning(research) {
    window.hasDuplicateTitle = true;
    window.duplicateMatchTitle = research.title;
    const box = document.getElementById('duplicateTitleWarning');
    if (box) {
        document.getElementById('dupMatchTitle').textContent = research.title;
        document.getElementById('dupMatchAuthors').textContent = research.authors || 'Unknown';
        document.getElementById('dupMatchYear').textContent = research.year || 'N/A';
        const statusEl = document.getElementById('dupMatchStatus');
        if (statusEl) {
            statusEl.textContent = (research.status || 'pending').toUpperCase();
            if (research.status === 'approved') {
                statusEl.className = 'font-bold uppercase text-[10px] px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800';
            } else {
                statusEl.className = 'font-bold uppercase text-[10px] px-1.5 py-0.5 rounded bg-amber-100 text-amber-800';
            }
        }
        box.style.display = 'block';
    }
}

function hideDuplicateWarning() {
    window.hasDuplicateTitle = false;
    window.duplicateMatchTitle = '';
    const box = document.getElementById('duplicateTitleWarning');
    if (box) box.style.display = 'none';
}

// Multiple files queue management
let uploadedFilesArray = [];

function handleFileSelect(e) {
    const selectedFiles = Array.from(e.target.files);
    addFilesToQueue(selectedFiles);
    e.target.value = '';
}

function addFilesToQueue(files) {
    const allowed = ['.pdf', '.docx'];
    for (const f of files) {
        const ext = '.' + f.name.split('.').pop().toLowerCase();
        if (!allowed.includes(ext)) {
            alert('File "' + f.name + '" is not supported. Please select PDF or DOCX files only.');
            continue;
        }
        if (f.size > 30 * 1024 * 1024) {
            alert('File "' + f.name + '" exceeds the 30MB size limit (' + formatBytes(f.size) + '). Please choose a smaller document.');
            continue;
        }
        if (!uploadedFilesArray.some(existing => existing.name === f.name && existing.size === f.size)) {
            uploadedFilesArray.push(f);
        }
    }
    syncActualInput();
    renderFileList();
}

function removeFile(index) {
    uploadedFilesArray.splice(index, 1);
    syncActualInput();
    renderFileList();
}

function moveFile(index, direction) {
    const targetIndex = index + direction;
    if (targetIndex < 0 || targetIndex >= uploadedFilesArray.length) return;
    const temp = uploadedFilesArray[index];
    uploadedFilesArray[index] = uploadedFilesArray[targetIndex];
    uploadedFilesArray[targetIndex] = temp;
    syncActualInput();
    renderFileList();
}

function syncActualInput() {
    const dt = new DataTransfer();
    for (const f of uploadedFilesArray) {
        dt.items.add(f);
    }
    document.getElementById('actualFileInput').files = dt.files;
}

function formatBytes(bytes) {
    if (!bytes || bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

function renderFileList() {
    const container = document.getElementById('fileListContainer');
    const itemsList = document.getElementById('fileItemsList');
    const badge = document.getElementById('fileCountBadge');

    if (uploadedFilesArray.length === 0) {
        container.style.display = 'none';
        itemsList.innerHTML = '';
        return;
    }

    container.style.display = 'block';
    badge.textContent = uploadedFilesArray.length + (uploadedFilesArray.length === 1 ? ' file' : ' files');

    itemsList.innerHTML = uploadedFilesArray.map((f, i) => {
        const isFirst = i === 0;
        const ext = f.name.split('.').pop().toUpperCase();
        return `
            <div class="flex items-center justify-between p-3 rounded-xl admin-form-card transition shadow-xs ${isFirst ? 'border-emerald-500/40' : ''}">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="flex-shrink-0 text-xs font-bold px-2 py-0.5 rounded-md ${isFirst ? 'bg-[#0c4233] text-white' : 'admin-form-subtle admin-muted'}">
                        ${isFirst ? '🥇 #1 Primary' : '#' + (i + 1)}
                    </span>
                    <div class="truncate">
                        <p class="text-xs font-bold admin-title truncate">${f.name}</p>
                        <p class="text-[10px] admin-muted font-medium uppercase mt-0.5">${ext} • ${formatBytes(f.size)}</p>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 flex-shrink-0 ml-3">
                    <button type="button" onclick="moveFile(${i}, -1)" ${i === 0 ? 'disabled' : ''} class="px-2 py-1 admin-form-subtle hover:bg-slate-200 dark:hover:bg-white/10 disabled:opacity-30 disabled:cursor-not-allowed rounded-md text-xs font-bold admin-muted transition" title="Move Up">
                        ▲
                    </button>
                    <button type="button" onclick="moveFile(${i}, 1)" ${i === uploadedFilesArray.length - 1 ? 'disabled' : ''} class="px-2 py-1 admin-form-subtle hover:bg-slate-200 dark:hover:bg-white/10 disabled:opacity-30 disabled:cursor-not-allowed rounded-md text-xs font-bold admin-muted transition" title="Move Down">
                        ▼
                    </button>
                    <button type="button" onclick="removeFile(${i})" class="px-2 py-1 text-rose-500 hover:text-rose-700 rounded-md text-xs font-bold transition ml-1" title="Remove File">
                        ✕
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

// Drag & drop support
const dropZone = document.getElementById('dropZone');
['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.add('border-emerald-600');
    });
});
['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.remove('border-emerald-600');
    });
});
dropZone.addEventListener('drop', (e) => {
    const dt = e.dataTransfer;
    if (dt && dt.files && dt.files.length > 0) {
        addFilesToQueue(Array.from(dt.files));
    }
});

// Dynamic Multi-Author Management
function addAuthorRow(initialValue = '') {
    const container = document.getElementById('authorsListContainer');
    const div = document.createElement('div');
    div.className = 'flex items-center gap-2 author-row';
    div.innerHTML = `
        <input type="text" name="authors[]" value="${initialValue}"
               class="admin-input flex-1" placeholder="Co-author full name" required
               oninput="handleAuthorInput(this)">
        <button type="button" onclick="removeAuthorRow(this)" class="px-2.5 py-1.5 text-rose-500 hover:text-rose-700 rounded-lg transition" title="Remove Co-Author">
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

// Form validation & duplicate alert confirmation
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    syncActualInput();

    const checked = this.querySelectorAll('input[name="category_ids[]"]:checked');
    if (checked.length === 0) {
        e.preventDefault();
        alert('Please select at least one category before submitting.');
        const manualPanel = document.getElementById('manualPanel');
        if (manualPanel && manualPanel.style.display === 'none') {
            manualPanel.style.display = 'block';
        }
        return;
    }

    if (uploadedFilesArray.length === 0) {
        e.preventDefault();
        alert('Please upload at least one PDF or DOCX file before submitting.');
        return;
    }

    if (window.hasDuplicateTitle) {
        const proceed = confirm(
            '⚠️ BABALA / ALERT:\n\n' +
            'Ang pamagat ng capstone na ito ay umiiral na sa repository:\n"' + window.duplicateMatchTitle + '"\n\n' +
            'Nais mo pa rin bang magpatuloy sa pag-publish nito?'
        );
        if (!proceed) {
            e.preventDefault();
            const t = document.getElementById('title');
            if (t) {
                t.focus();
                t.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }
    }

    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.innerHTML = '<span class="spinner"></span> <span>Uploading & Publishing...</span>';
        setTimeout(() => {
            submitBtn.disabled = true;
        }, 100);
    }
});

// Proactive AI Wake-up & Heartbeat: Keeps Render AI service awake ONLY while user is on this upload page
(function initAIKeepAlive() {
    const aiUrl = @json(config('services.ai.url', 'https://capstone-ai-fqz3.onrender.com'));
    const warmupRoute = '{{ route("ai.warmup") }}';

    function pingAI() {
        if (aiUrl) {
            try {
                fetch(aiUrl.replace(/\/$/, '') + '/', { mode: 'no-cors', cache: 'no-cache' }).catch(function() {});
            } catch (e) {}
        }
        try {
            fetch(warmupRoute, { cache: 'no-cache' }).catch(function() {});
        } catch (e) {}
    }

    // 1. Immediate wake-up ping upon opening the upload page
    pingAI();

    // 2. Heartbeat every 3.5 minutes (210,000 ms) to keep AI awake while user remains on this page
    const heartbeatInterval = setInterval(pingAI, 210000);

    // 3. Re-ping when tab becomes visible again
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            pingAI();
        }
    });

    // 4. Clean up interval when navigating away so Render can sleep naturally after 15 mins
    window.addEventListener('beforeunload', function() {
        clearInterval(heartbeatInterval);
    });
})();
</script>
@endsection
