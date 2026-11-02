<x-guest-layout>
    <div class="auth-center-card auth-card-wide">
        <!-- Center Emblem / Logo -->
        <div class="auth-emblem-badge" aria-hidden="true">
            <img src="{{ asset('images/scs.png') }}" alt="SCS Logo">
        </div>

        <!-- Title & Subtitle Matching Reference Style -->
        <h1 class="auth-heading">Sign up</h1>
        <p class="auth-subheading">
            Create an account to submit and explore capstone projects!
        </p>

        @if (session('status'))
            <div class="w-full mb-4 p-3.5 text-xs font-semibold text-emerald-300 bg-emerald-950/80 border border-emerald-500/50 rounded-xl flex items-center gap-2">
                <span>✅</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="w-full mb-4 p-3.5 text-xs font-semibold text-rose-300 bg-rose-950/80 border border-rose-500/50 rounded-xl flex items-center gap-2">
                <span>⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="w-full mb-4 p-3.5 text-xs font-semibold text-rose-300 bg-rose-950/80 border border-rose-500/50 rounded-xl space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="flex items-center gap-1.5">
                        <span class="text-rose-400">⚠️</span>
                        <span>{{ $error }}</span>
                    </p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="auth-form-body" id="registerForm">
            @csrf

            <!-- First Name & Last Name (2-Column Grid) -->
            <div class="auth-grid-2col">
                <div class="auth-input-group">
                    <div class="auth-input-wrap">
                        <input 
                            id="first_name" 
                            type="text" 
                            name="first_name" 
                            value="{{ old('first_name') }}" 
                            required 
                            autofocus 
                            autocomplete="given-name"
                            placeholder="First Name" 
                            class="auth-field"
                        >
                    </div>
                    @error('first_name')
                        <p class="auth-field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="auth-input-group">
                    <div class="auth-input-wrap">
                        <input 
                            id="last_name" 
                            type="text" 
                            name="last_name" 
                            value="{{ old('last_name') }}" 
                            required 
                            autocomplete="family-name"
                            placeholder="Last Name" 
                            class="auth-field"
                        >
                    </div>
                    @error('last_name')
                        <p class="auth-field-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Student ID Number -->
            <div class="auth-input-group">
                <div class="auth-input-wrap">
                    <input 
                        id="student_id_number" 
                        type="text" 
                        name="student_id_number" 
                        value="{{ old('student_id_number') }}" 
                        required 
                        placeholder="Student ID Number (e.g. 2024-00123)" 
                        class="auth-field"
                    >
                </div>
                @error('student_id_number')
                    <p class="auth-field-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="auth-input-group">
                <div class="auth-input-wrap">
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autocomplete="username"
                        placeholder="Email Address" 
                        class="auth-field"
                    >
                </div>
                @error('email')
                    <p class="auth-field-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password & Confirm Password (2-Column Grid) -->
            <div class="auth-grid-2col">
                <div class="auth-input-group">
                    <div class="auth-input-wrap">
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required 
                            autocomplete="new-password"
                            placeholder="Password" 
                            class="auth-field has-eye"
                        >
                        <button type="button" class="auth-eye-btn" onclick="togglePasswordVisibility('password', this)" aria-label="Toggle password visibility">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="auth-field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="auth-input-group">
                    <div class="auth-input-wrap">
                        <input 
                            id="password_confirmation" 
                            type="password" 
                            name="password_confirmation" 
                            required 
                            autocomplete="new-password"
                            placeholder="Confirm Password" 
                            class="auth-field has-eye"
                        >
                        <button type="button" class="auth-eye-btn" onclick="togglePasswordVisibility('password_confirmation', this)" aria-label="Toggle password confirmation visibility">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Student ID Pictures Upload (Front & Back) -->
            <div class="auth-input-group">
                <span class="auth-input-label">Student ID Pictures Verification</span>
                <div class="auth-grid-2col">
                    <!-- Front ID Card -->
                    <div class="auth-upload-card" id="frontUploadCard">
                        <input 
                            type="file" 
                            name="id_picture_front" 
                            id="id_picture_front" 
                            accept="image/*" 
                            required
                            class="auth-file-input" 
                            onchange="previewIdImage(this, 'frontPreview', 'frontPlaceholder', 'frontUploadCard')"
                        >
                        <div class="auth-upload-placeholder" id="frontPlaceholder">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#00e676" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <polyline points="21 15 16 10 5 21"/>
                            </svg>
                            <span class="upload-title">Front ID Photo</span>
                            <span class="upload-hint">Click or drop image</span>
                        </div>
                        <img id="frontPreview" class="auth-preview-img" style="display:none;" alt="Front ID Preview">
                        <button type="button" class="auth-remove-img-btn" onclick="removeIdImage('id_picture_front', 'frontPreview', 'frontPlaceholder', 'frontUploadCard')" style="display:none;" aria-label="Remove image">✕</button>
                    </div>

                    <!-- Back ID Card -->
                    <div class="auth-upload-card" id="backUploadCard">
                        <input 
                            type="file" 
                            name="id_picture_back" 
                            id="id_picture_back" 
                            accept="image/*" 
                            required
                            class="auth-file-input" 
                            onchange="previewIdImage(this, 'backPreview', 'backPlaceholder', 'backUploadCard')"
                        >
                        <div class="auth-upload-placeholder" id="backPlaceholder">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#00e676" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <line x1="3" y1="9" x2="21" y2="9"/>
                            </svg>
                            <span class="upload-title">Back ID Photo</span>
                            <span class="upload-hint">Click or drop image</span>
                        </div>
                        <img id="backPreview" class="auth-preview-img" style="display:none;" alt="Back ID Preview">
                        <button type="button" class="auth-remove-img-btn" onclick="removeIdImage('id_picture_back', 'backPreview', 'backPlaceholder', 'backUploadCard')" style="display:none;" aria-label="Remove image">✕</button>
                    </div>
                </div>
                @error('id_picture_front')
                    <p class="auth-field-error">{{ $message }}</p>
                @enderror
                @error('id_picture_back')
                    <p class="auth-field-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Primary Green Submit Button -->
            <button type="submit" class="auth-btn-submit" id="submitBtn">
                Create Account
            </button>

            <!-- Switch to Login Link -->
            <p class="auth-switch-text">
                Already have an account? <a href="{{ route('login') }}">Sign in</a>
            </p>
        </form>
    </div>

    <style>
        .auth-grid-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.85rem;
            width: 100%;
        }

        @media (max-width: 540px) {
            .auth-grid-2col {
                grid-template-columns: 1fr;
                gap: 0.85rem;
            }
        }

        .auth-upload-card {
            position: relative;
            background: var(--input-bg);
            border: 1.5px dashed rgba(0, 230, 118, 0.4);
            border-radius: 14px;
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            overflow: hidden;
            transition: all 0.25s ease;
        }

        .auth-upload-card:hover {
            border-color: var(--emerald-accent);
            background: rgba(0, 230, 118, 0.08);
            box-shadow: 0 0 16px rgba(0, 230, 118, 0.2);
        }

        .auth-file-input {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            z-index: 5;
            width: 100%;
            height: 100%;
        }

        .auth-upload-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            pointer-events: none;
            padding: 4px;
        }

        .upload-title {
            font-size: 0.78rem;
            font-weight: 700;
            color: #ffffff;
        }

        .upload-hint {
            font-size: 0.65rem;
            color: #7b94ac;
            font-family: 'JetBrains Mono', monospace;
        }

        .auth-preview-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            inset: 0;
            z-index: 2;
        }

        .auth-remove-img-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            z-index: 10;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #ef4444;
            color: #ffffff;
            border: none;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
            transition: transform 0.2s;
        }

        .auth-remove-img-btn:hover {
            transform: scale(1.15);
        }
    </style>

    <script>
        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            if (!input) return;
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            btn.innerHTML = isPassword
                ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>'
                : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
        }

        function previewIdImage(input, previewId, placeholderId, cardId) {
            const preview = document.getElementById(previewId);
            const placeholder = document.getElementById(placeholderId);
            const card = document.getElementById(cardId);
            const removeBtn = card.querySelector('.auth-remove-img-btn');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                    removeBtn.style.display = 'flex';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeIdImage(inputId, previewId, placeholderId, cardId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            const placeholder = document.getElementById(placeholderId);
            const card = document.getElementById(cardId);
            const removeBtn = card.querySelector('.auth-remove-img-btn');

            input.value = '';
            preview.src = '';
            preview.style.display = 'none';
            placeholder.style.display = 'flex';
            removeBtn.style.display = 'none';
        }
    </script>
</x-guest-layout>