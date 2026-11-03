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
    .btn-submit{display:inline-flex;justify-content:center;align-items:center;gap:10px;padding:14px 36px;background:linear-gradient(135deg,#166534,#15803d);color:white;border:none;border-radius:14px;font-size:15px;font-weight:700;cursor:pointer;transition:all .3s}
    .btn-submit:hover{transform:translateY(-2px);box-shadow:0 10px 24px rgba(22,101,52,.25)}
    .btn-submit:disabled{opacity:.5;cursor:not-allowed;transform:none;box-shadow:none}
    .btn-cancel{display:inline-flex;align-items:center;gap:8px;padding:14px 28px;background:white;color:#475569;border:1.5px solid #e2e8f0;border-radius:14px;font-size:15px;font-weight:600;text-decoration:none;transition:all .3s}
    .btn-cancel:hover{border-color:#166534;color:#166534;background:#f0fdf4}
    .btn-classify{display:inline-flex;align-items:center;gap:10px;padding:14px 28px;background:linear-gradient(135deg,#7c3aed,#6d28d9);color:white;border:none;border-radius:14px;font-size:14px;font-weight:700;cursor:pointer;transition:all .3s;width:100%}
    .btn-classify:hover{transform:translateY(-2px);box-shadow:0 10px 24px rgba(124,58,237,.3)}
    .btn-classify:disabled{opacity:.6;cursor:not-allowed;transform:none}
    .classification-panel{background:linear-gradient(135deg,#f5f3ff,#ede9fe);border:1.5px solid #c4b5fd;border-radius:16px;padding:24px;margin-top:8px}
    .classification-panel-title{display:flex;align-items:center;gap:10px;font-size:15px;font-weight:700;color:#5b21b6;margin-bottom:16px}
    .check-card{display:flex;align-items:center;gap:12px;padding:12px 16px;background:white;border:1.5px solid #e2e8f0;border-radius:12px;cursor:pointer;transition:all .2s;user-select:none}
    .check-card:hover{border-color:#7c3aed;background:#faf5ff}
    .check-card.checked{border-color:#22c55e;background:#f0fdf4}
    .check-card input[type=checkbox]{width:18px;height:18px;accent-color:#22c55e;cursor:pointer;flex-shrink:0}
    .section-divider{border:none;border-top:2px dashed #e5e7eb;margin:28px 0}
    .step-badge{display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:50%;font-size:13px;font-weight:800;margin-right:8px;flex-shrink:0}
    .spinner{display:inline-block;width:18px;height:18px;border:3px solid rgba(255,255,255,.3);border-top-color:white;border-radius:50%;animation:spin .6s linear infinite}
    @keyframes spin{to{transform:rotate(360deg)}}
</style>

<div class="mb-6 pb-4 border-b border-slate-100">
    <a href="{{ route('student.researches.index') }}"
       class="inline-flex items-center gap-2 mb-4 px-4 py-1.5 rounded-full text-xs font-bold text-[#5840bb] bg-purple-50 hover:bg-purple-100 border border-purple-100 transition">
        ← Back to My Submissions
    </a>
    <h1 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 2.15rem;" class="font-extrabold text-[#2d2b56] tracking-tight leading-tight">
        Upload Capstone <span class="text-[#0c4233]">Manuscript</span>
    </h1>
    <p class="text-slate-500 text-xs font-medium mt-1">Submit your capstone project for review. Smart AI will assist in auto-classifying topic categories.</p>
</div>

<div class="max-w-4xl mx-auto space-y-6 pb-12">

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 font-medium text-sm flex items-center gap-3">
            <span class="text-lg">✅</span> {{ session('success') }}
        </div>
    @endif
    @if(session('warning'))
        <div class="mb-6 p-4 bg-amber-50 border border-amber-300 rounded-xl text-amber-900 font-medium text-sm flex items-center gap-3">
            <span class="text-lg">⚠️</span> {{ session('warning') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-card">
        <div class="accent-bar"></div>
        <div class="p-8">
            <form action="{{ route('student.researches.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf

                {{-- STEP 1: Research Details --}}
                <div style="margin-bottom:28px">
                    <h2 class="flex items-center text-lg font-bold text-gray-900 mb-5">
                        <span class="step-badge" style="background:#dcfce7;color:#166534">1</span>
                        Research Details
                    </h2>

                    <div class="space-y-5">
                        <div>
                            <label for="title" class="form-label">Research Title <span class="required">*</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}"
                                   class="form-input" placeholder="e.g. Development of Student Attendance Monitoring System" required autocomplete="off">

                            {{-- Real-time Duplicate / Existed Title Warning Alert --}}
                            <div id="duplicateTitleWarning" style="display:none;" class="mt-2.5 p-3.5 bg-amber-50 border border-amber-300 rounded-xl text-amber-900 text-xs shadow-xs">
                                <div class="flex items-start gap-2.5">
                                    <span class="text-lg leading-none shrink-0">⚠️</span>
                                    <div class="space-y-1 flex-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="font-bold text-amber-950">
                                                Warning / Alert: Mayroon nang umiiral na capstone sa repository na may pareho o katulad na pamagat!
                                            </p>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-200 text-amber-900 border border-amber-300 uppercase">
                                                Existed Project
                                            </span>
                                        </div>
                                        <div class="bg-white/90 p-2.5 rounded-lg border border-amber-200 mt-1">
                                            <p class="font-semibold text-slate-800">📌 Existing Title: <span id="dupMatchTitle" class="text-amber-950 font-bold"></span></p>
                                            <p class="text-[11px] text-slate-600 mt-0.5">
                                                👤 Authors: <span id="dupMatchAuthors" class="font-medium"></span> · 📅 Year: <span id="dupMatchYear" class="font-mono"></span> · Status: <span id="dupMatchStatus" class="font-bold uppercase text-[10px] px-1.5 py-0.5 rounded bg-slate-100"></span>
                                            </p>
                                        </div>
                                        <p class="text-[11px] text-amber-800 font-medium pt-0.5">
                                            Paalala: Siguraduhing natatangi at orihinal ang pag-aaral upang maiwasan ang duplication o plagiarism.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="form-label m-0">Authors / Researchers <span class="required">*</span></label>
                                    <button type="button" onclick="addAuthorRow()" class="text-xs text-emerald-700 hover:text-emerald-800 font-extrabold flex items-center gap-1 bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1 rounded-full border border-emerald-200 transition cursor-pointer">
                                        + Add Co-Author
                                    </button>
                                </div>
                                <div id="authorsListContainer" class="space-y-2">
                                    {{-- Row 1: Author (Pre-filled with logged-in student) --}}
                                    <div class="flex items-center gap-2 author-row">
                                        <input type="text" name="authors[]" value="{{ old('authors.0', auth()->user()->name) }}"
                                               class="form-input flex-1" placeholder="Author full name" required
                                               oninput="handleAuthorInput(this)">
                                    </div>
                                    @if(old('authors') && is_array(old('authors')) && count(old('authors')) > 1)
                                        @foreach(array_slice(old('authors'), 1) as $idx => $extraAuthor)
                                            <div class="flex items-center gap-2 author-row">
                                                <input type="text" name="authors[]" value="{{ $extraAuthor }}"
                                                       class="form-input flex-1" placeholder="Co-author full name" required
                                                       oninput="handleAuthorInput(this)">
                                                <button type="button" onclick="removeAuthorRow(this)" class="px-2.5 py-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition cursor-pointer" title="Remove Co-Author">
                                                    ✕
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1.5">
                                    Maaaring maglagay ng higit sa isang author / miyembro ng research team.
                                </p>
                            </div>
                            <div>
                                <label for="year" class="form-label">Publication / Defense Year <span class="required">*</span></label>
                                <input type="number" name="year" id="year" value="{{ old('year', date('Y')) }}"
                                       min="1900" max="{{ date('Y') + 1 }}" class="form-input" required>
                            </div>
                        </div>

                        <div>
                            <label for="abstract" class="form-label">Abstract <span class="required">*</span></label>
                            <textarea name="abstract" id="abstract" rows="4" class="form-input"
                                      placeholder="Provide a summary of your research..." required>{{ old('abstract') }}</textarea>
                        </div>

                        <div>
                            <label for="project_context" class="form-label">Project Context <span class="required">*</span></label>
                            <textarea name="project_context" id="project_context" rows="4" class="form-input"
                                      placeholder="Describe the background, context, or community/organizational setting of the project..." required>{{ old('project_context') }}</textarea>
                        </div>

                        <div>
                            <label for="objectives" class="form-label">Objectives of the Study <span class="required">*</span></label>
                            <textarea name="objectives" id="objectives" rows="4" class="form-input"
                                      placeholder="List the specific objectives of your research..." required>{{ old('objectives') }}</textarea>
                        </div>

                        <div>
                            <label for="keywords" class="form-label">Keywords <span style="font-weight:400;color:#94a3b8">(Optional)</span></label>
                            <input type="text" name="keywords" id="keywords" value="{{ old('keywords') }}"
                                   class="form-input" placeholder="e.g. monitoring, attendance, web application">
                        </div>
                    </div>
                </div>

                <hr class="section-divider">

                {{-- STEP 2: Automated Classification --}}
                <div style="margin-bottom:28px">
                    <h2 class="flex items-center text-lg font-bold text-gray-900 mb-3">
                        <span class="step-badge" style="background:#ede9fe;color:#7c3aed">2</span>
                        Category Classification
                    </h2>
                    <p class="text-sm text-gray-500 mb-5">Fill in the details above, then click the button below. The System will suggest the best categories and project types for your research. You can review and adjust.</p>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                        <button type="button" id="classifyBtn" class="btn-classify flex-1" onclick="runClassify()">
                            <span id="classifyBtnText">🤖 Auto-Classify My Research</span>
                        </button>
                        <button type="button" onclick="toggleManualCategories()" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition inline-flex items-center justify-center gap-1.5 cursor-pointer border border-slate-200 shrink-0">
                            <span>📁</span>
                            <span id="manualToggleBtnText">Select Categories Manually ▾</span>
                        </button>
                    </div>

                    {{-- Classification Results Panel --}}
                    <div id="classificationPanel" style="display:none" class="classification-panel">
                        <div class="classification-panel-title">
                            <span style="font-size:22px">🤖</span>
                            Suggestions — Review & Adjust
                        </div>

                        {{-- Categories --}}
                        <div style="margin-bottom:20px">
                            <label class="form-label" style="color:#5b21b6">📂 Categories <span class="required">*</span></label>
                            <p style="font-size:11px;color:#7c3aed;margin-bottom:10px">Select at least one category. Suggested items are pre-checked.</p>
                            <div id="categoryCheckboxes" class="space-y-2">
                                {{-- Dynamically populated --}}
                            </div>
                        </div>

                        {{-- Project Types --}}
                        <div>
                            <label class="form-label" style="color:#5b21b6">🏷 Project Types</label>
                            <p style="font-size:11px;color:#7c3aed;margin-bottom:10px">Select the project types that apply. Suggested items are pre-checked.</p>
                            <div id="projectTypeCheckboxes" class="space-y-2">
                                {{-- Dynamically populated --}}
                            </div>
                            <div id="noProjectTypes" style="display:none;padding:16px;background:white;border-radius:10px;text-align:center;color:#7c3aed;font-size:13px;border:1.5px dashed #c4b5fd">
                                No project types available. Admin needs to add project types under categories first.
                            </div>
                        </div>
                    </div>

                    {{-- Manual fallback --}}
                    <div id="manualPanel" style="display:none;margin-top:12px">
                        <div style="padding:16px;background:#fef3c7;border:1.5px solid #fcd34d;border-radius:12px;margin-bottom:16px">
                            <p style="font-size:13px;color:#92400e;font-weight:600">⚠️ Service unavailable. Please select categories manually below.</p>
                        </div>
                        <div style="margin-bottom:16px">
                            <label class="form-label">📂 Categories <span class="required">*</span></label>
                            <div id="manualCategoryCheckboxes" class="space-y-2">
                                @foreach($categories as $cat)
                                    <label class="check-card">
                                        <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}"
                                               {{ in_array($cat->id, old('category_ids', [])) ? 'checked' : '' }}>
                                        <span style="font-size:14px;font-weight:600;color:#374151">{{ $cat->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <label class="form-label">🏷 Project Types</label>
                            <div id="manualProjectTypeCheckboxes" class="space-y-2">
                                @foreach($categories as $cat)
                                    @foreach($cat->projectTypes as $pt)
                                        <label class="check-card">
                                            <input type="checkbox" name="project_type_ids[]" value="{{ $pt->id }}"
                                                   {{ in_array($pt->id, old('project_type_ids', [])) ? 'checked' : '' }}>
                                            <span style="font-size:14px;font-weight:600;color:#374151">{{ $pt->name }}</span>
                                            <span style="font-size:11px;color:#6b7280">— {{ $cat->name }}</span>
                                        </label>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="section-divider">

                {{-- STEP 3: Multiple Files Upload --}}
                <div style="margin-bottom:28px">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="flex items-center text-lg font-bold text-gray-900 m-0">
                            <span class="step-badge" style="background:#dbeafe;color:#1d4ed8">3</span>
                            Upload Research Files <span class="required" style="color:#dc2626;margin-left:4px">*</span>
                        </h2>
                        <span class="text-xs px-2.5 py-1 bg-blue-50 text-blue-700 font-semibold rounded-full border border-blue-200">
                            Multiple Files Allowed (.pdf, .docx)
                        </span>
                    </div>

                    <p class="text-xs text-slate-500 mb-3">
                        Maaari kang mag-upload ng higit sa isang file (hal. Main Manuscript, Appendices, Documentation). 
                        <strong>Ang unang file (#1) ang magiging Primary Document</strong> na unang bubukas sa viewer ng ibang authorized users.
                    </p>

                    {{-- Dropzone Area --}}
                    <div id="dropZone" class="border-2 border-dashed border-gray-300 rounded-2xl p-6 text-center hover:border-emerald-500 hover:bg-emerald-50/20 transition cursor-pointer relative bg-slate-50/50" onclick="document.getElementById('multiFileInput').click()">
                        <input type="file" id="multiFileInput" multiple accept=".pdf,.docx" class="hidden" onchange="handleFileSelect(event)">
                        <input type="file" name="files[]" id="actualFileInput" multiple class="hidden">
                        
                        <div class="w-12 h-12 bg-white rounded-2xl shadow-sm border border-slate-200 text-2xl flex items-center justify-center mx-auto mb-3">
                            📂
                        </div>
                        <p class="text-sm font-bold text-slate-800">
                            Click to select files, or drag and drop multiple files here
                        </p>
                        <p class="text-xs text-slate-400 mt-1">
                            Accepted: PDF (.pdf) o Word (.docx) · Maximum 15MB bawat file
                        </p>
                    </div>

                    {{-- Uploaded Files List --}}
                    <div id="fileListContainer" class="mt-4 space-y-2" style="display:none">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-600 px-1">
                            <span>Selected Files (Arranged by viewing sequence):</span>
                            <span id="fileCountBadge" class="text-emerald-700 bg-emerald-100/70 px-2 py-0.5 rounded-full">0 files</span>
                        </div>
                        <div id="fileItemsList" class="space-y-2">
                            {{-- Populated by JavaScript --}}
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100">
                    <a href="{{ route('student.researches.index') }}" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        Submit Research
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// All categories & project types data from server
const allCategories = @json($categories);

function runClassify() {
    const title = document.getElementById('title').value.trim();
    const abstract = document.getElementById('abstract').value.trim();
    const project_context = document.getElementById('project_context') ? document.getElementById('project_context').value.trim() : '';
    const objectives = document.getElementById('objectives').value.trim();

    if (!title || !abstract || !project_context || !objectives) {
        alert('Please fill in the Title, Abstract, Project Context, and Objectives first before classifying.');
        return;
    }

    const btn = document.getElementById('classifyBtn');
    const btnText = document.getElementById('classifyBtnText');
    btn.disabled = true;
    btnText.innerHTML = '<span class="spinner"></span> System is analyzing your research...';

    fetch('{{ route("student.researches.classify") }}', {
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
        btnText.innerHTML = '🤖 Re-classify My Research';

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
        btnText.innerHTML = 'Auto-Classify My Research';
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

    // Build category checkboxes
    const catContainer = document.getElementById('categoryCheckboxes');
    catContainer.innerHTML = '';

    allCategories.forEach(cat => {
        const suggested = suggestedCats.find(s => s.id === cat.id);
        const isChecked = !!suggested || suggestedCategoryIds.has(cat.id);
        const label = document.createElement('label');
        label.className = 'check-card' + (isChecked ? ' checked' : '');
        label.innerHTML = `
            <input type="checkbox" name="category_ids[]" value="${cat.id}" ${isChecked ? 'checked' : ''}
                   onchange="this.parentElement.classList.toggle('checked', this.checked)">
            <span style="font-size:14px;font-weight:600;color:#374151">${cat.name}</span>
        `;
        catContainer.appendChild(label);
    });

    // Build project type checkboxes
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
                label.className = 'check-card' + (isChecked ? ' checked' : '');
                label.innerHTML = `
                    <input type="checkbox" name="project_type_ids[]" value="${pt.id}" ${isChecked ? 'checked' : ''}
                           onchange="this.parentElement.classList.toggle('checked', this.checked)">
                    <span style="font-size:14px;font-weight:600;color:#374151">${pt.name}</span>
                    <span style="font-size:11px;color:#6b7280;margin-left:4px">— ${cat.name}</span>
                `;
                ptContainer.appendChild(label);
            });
        }
    });

    document.getElementById('noProjectTypes').style.display = hasPts ? 'none' : 'block';
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
            <div class="flex items-center justify-between p-3 rounded-xl border ${isFirst ? 'bg-emerald-50/70 border-emerald-300' : 'bg-white border-slate-200'} transition shadow-xs">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="flex-shrink-0 text-xs font-black px-2.5 py-1 rounded-lg ${isFirst ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600'}">
                        ${isFirst ? '🥇 #1 (First / Primary)' : '#' + (i + 1)}
                    </span>
                    <div class="truncate">
                        <p class="text-xs font-bold text-slate-800 truncate">${f.name}</p>
                        <p class="text-[10px] text-slate-400 font-semibold uppercase mt-0.5">${ext} • ${formatBytes(f.size)}</p>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 flex-shrink-0 ml-3">
                    <button type="button" onclick="moveFile(${i}, -1)" ${i === 0 ? 'disabled' : ''} class="px-2 py-1 bg-slate-100 hover:bg-slate-200 disabled:opacity-30 disabled:cursor-not-allowed rounded-md text-xs font-bold text-slate-700 transition" title="Move Up">
                        ▲
                    </button>
                    <button type="button" onclick="moveFile(${i}, 1)" ${i === uploadedFilesArray.length - 1 ? 'disabled' : ''} class="px-2 py-1 bg-slate-100 hover:bg-slate-200 disabled:opacity-30 disabled:cursor-not-allowed rounded-md text-xs font-bold text-slate-700 transition" title="Move Down">
                        ▼
                    </button>
                    <button type="button" onclick="removeFile(${i})" class="px-2 py-1 bg-red-50 hover:bg-red-100 text-red-600 rounded-md text-xs font-bold transition ml-1" title="Remove File">
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
        dropZone.classList.add('border-emerald-500', 'bg-emerald-50/40');
    });
});
['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.remove('border-emerald-500', 'bg-emerald-50/40');
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

// Toggle manual category panel
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

// Real-time duplicate title checking
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

    // Check on initial load if old title exists
    if (titleInput.value.trim().length >= 4) {
        checkDuplicateTitle(titleInput.value.trim());
    }
}

function checkDuplicateTitle(title) {
    fetch('{{ route("student.researches.check-exists") }}', {
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
    .catch(() => {
        // Silent catch on network interruption
    });
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

// Form validation: ensure category & at least one file selected + duplicate confirmation alert
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    const checked = this.querySelectorAll('input[name="category_ids[]"]:checked');
    if (checked.length === 0) {
        e.preventDefault();
        alert('Please select at least one category. Click "Auto-Classify" first or select manually.');
        return;
    }

    if (uploadedFilesArray.length === 0) {
        e.preventDefault();
        alert('Please upload at least one PDF or DOCX file before submitting.');
        return;
    }

    // If an existing research was detected, warn the user before submitting
    if (window.hasDuplicateTitle) {
        const proceed = confirm(
            '⚠️ BABALA / ALERT:\n\n' +
            'Ang pamagat ng capstone na iyong ina-upload ay umiiral na sa repository:\n"' + window.duplicateMatchTitle + '"\n\n' +
            'Nais mo pa rin bang magpatuloy sa pag-submit?'
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
});

// Proactive background warm-up for Render AI service while user fills upload form
(function wakeAIService() {
    const aiUrl = @json(config('services.ai.url', 'https://capstone-ai-fqz3.onrender.com'));
    if (aiUrl) {
        try {
            fetch(aiUrl.replace(/\/$/, '') + '/', { mode: 'no-cors', cache: 'no-cache' }).catch(function() {});
        } catch (e) {}
    }
    try {
        fetch('{{ route("ai.warmup") }}', { cache: 'no-cache' }).catch(function() {});
    } catch (e) {}
})();
</script>
@endsection
