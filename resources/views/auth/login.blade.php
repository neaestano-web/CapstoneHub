<x-guest-layout>
    <div class="auth-center-card">
        <!-- Center Emblem / Logo -->
        <div class="auth-emblem-badge" aria-hidden="true">
            <img src="{{ asset('images/scs.png') }}" alt="SCS Logo">
        </div>

        <!-- Title & Subtitle Matching Reference Image -->
        <h1 class="auth-heading">Sign in</h1>
        <p class="auth-subheading">
            Sign in and start managing your capstone projects!
        </p>

        <!-- Session Status Alert -->
        @if (session('status'))
            <div class="w-full mb-3 p-3 text-xs font-semibold text-emerald-300 bg-emerald-950/70 border border-emerald-500/40 rounded-xl text-center">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="auth-form-body" id="loginForm">
            @csrf

            <!-- Login / Email Input Field -->
            <div class="auth-input-group">
                <div class="auth-input-wrap">
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus 
                        autocomplete="username"
                        placeholder="Login" 
                        class="auth-field"
                    >
                </div>
                @error('email')
                    <p class="auth-field-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Input Field -->
            <div class="auth-input-group">
                <div class="auth-input-wrap">
                    <input 
                        id="password" 
                        type="password" 
                        name="password" 
                        required 
                        autocomplete="current-password"
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

            <!-- Remember Me & Forgot Password Row -->
            <div class="auth-action-row">
                <label for="remember_me" class="auth-remember-wrap">
                    <input 
                        id="remember_me" 
                        type="checkbox" 
                        name="remember" 
                        class="auth-checkbox"
                        {{ old('remember') ? 'checked' : 'checked' }}
                    >
                    <span>Remember me</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="auth-forgot-link">
                        Forgot password?
                    </a>
                @endif
            </div>

            <!-- Primary Green Submit Button -->
            <button type="submit" class="auth-btn-submit" id="submitBtn">
                Login
            </button>

            <!-- Switch to Register Link -->
            <p class="auth-switch-text">
                Don't have an account? <a href="{{ route('register') }}">Sign up</a>
            </p>
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