<x-guest-layout>
    <div class="auth-center-card">
        <!-- Center Emblem / Logo -->
        <div class="auth-emblem-badge" aria-hidden="true">
            <img src="{{ asset('images/scs.png') }}" alt="SCS Logo">
        </div>

        <h1 class="auth-heading">Reset Password</h1>
        <p class="auth-subheading">
            Your verification succeeded. Please choose a strong new password for your account.
        </p>

        <form method="POST" action="{{ route('password.reset.update') }}" class="auth-form-body">
            @csrf

            <!-- Email Address (read-only) -->
            <div class="auth-input-group">
                <label for="email" class="auth-input-label">Email Address</label>
                <div class="auth-input-wrap">
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        value="{{ $email }}" 
                        readonly 
                        required 
                        class="auth-field"
                        style="opacity: 0.75; cursor: not-allowed;"
                    >
                </div>
                @error('email')
                    <p class="auth-field-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password -->
            <div class="auth-input-group">
                <label for="password" class="auth-input-label">New Password</label>
                <div class="auth-input-wrap">
                    <input 
                        id="password" 
                        type="password" 
                        name="password" 
                        required 
                        autocomplete="new-password"
                        placeholder="••••••••" 
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

            <!-- Confirm New Password -->
            <div class="auth-input-group">
                <label for="password_confirmation" class="auth-input-label">Confirm Password</label>
                <div class="auth-input-wrap">
                    <input 
                        id="password_confirmation" 
                        type="password" 
                        name="password_confirmation" 
                        required 
                        autocomplete="new-password"
                        placeholder="••••••••" 
                        class="auth-field has-eye"
                    >
                    <button type="button" class="auth-eye-btn" onclick="togglePasswordVisibility('password_confirmation', this)" aria-label="Toggle password confirmation visibility">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                @error('password_confirmation')
                    <p class="auth-field-error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="auth-btn-submit">
                Save New Password
            </button>
        </form>
    </div>

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
    </script>
</x-guest-layout>
