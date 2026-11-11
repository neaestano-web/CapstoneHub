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
            <div class="auth-alert auth-alert-success">
                <span>✅</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- Persistent Lockout Warning (survives browser refresh) -->
        @if (($lockoutSeconds ?? 0) > 0 && !empty($lockoutEmail))
            @php $lockoutMins = (int) ceil($lockoutSeconds / 60); @endphp
            <div class="auth-alert auth-alert-error" id="lockoutBanner" style="align-items: flex-start; text-align: left;">
                <span>🔒</span>
                <span>
                    Too many failed login attempts for <strong>{{ $lockoutEmail }}</strong>.
                    Please try again in <strong id="lockoutTimerText">{{ $lockoutMins }} {{ Str::plural('minute', $lockoutMins) }}</strong>.
                </span>
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
                        value="{{ old('email', ($lockoutSeconds ?? 0) > 0 ? ($lockoutEmail ?? '') : '') }}" 
                        required 
                        autofocus 
                        autocomplete="username"
                        placeholder="Email" 
                        class="auth-field"
                    >
                </div>
                @error('email')
                    @if (($lockoutSeconds ?? 0) <= 0 || !str_contains($message, 'Too many failed login attempts'))
                        <p class="auth-field-error">{{ $message }}</p>
                    @endif
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
                        {{ old('remember') ? 'checked' : '' }}
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

        (function initLockoutCountdown() {
            const lockedEmail = @json(strtolower((string) ($lockoutEmail ?? '')));
            let remaining = {{ (int) ($lockoutSeconds ?? 0) }};

            if (!lockedEmail || remaining <= 0) return;

            const timerText = document.getElementById('lockoutTimerText');
            const banner = document.getElementById('lockoutBanner');
            const emailInput = document.getElementById('email');
            const submitBtn = document.getElementById('submitBtn');

            function formatDuration(sec) {
                const m = Math.floor(sec / 60);
                const s = sec % 60;
                return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
            }

            function syncLockoutUi() {
                if (remaining <= 0) {
                    if (banner) banner.style.display = 'none';
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.style.opacity = '';
                        submitBtn.style.cursor = '';
                        submitBtn.textContent = 'Login';
                    }
                    return;
                }

                const formatted = formatDuration(remaining);
                const mins = Math.ceil(remaining / 60);
                if (timerText) {
                    timerText.textContent = mins + ' ' + (mins === 1 ? 'minute' : 'minutes') + ' (' + formatted + ')';
                }

                const currentEmail = (emailInput ? emailInput.value : '').trim().toLowerCase();
                if (submitBtn) {
                    if (currentEmail === '' || currentEmail === lockedEmail) {
                        submitBtn.disabled = true;
                        submitBtn.style.opacity = '0.65';
                        submitBtn.style.cursor = 'not-allowed';
                        submitBtn.textContent = 'Locked (' + formatted + ')';
                    } else {
                        submitBtn.disabled = false;
                        submitBtn.style.opacity = '';
                        submitBtn.style.cursor = '';
                        submitBtn.textContent = 'Login';
                    }
                }
            }

            if (emailInput) {
                emailInput.addEventListener('input', syncLockoutUi);
            }

            const loginForm = document.getElementById('loginForm');
            if (loginForm) {
                loginForm.addEventListener('submit', function (e) {
                    const currentEmail = (emailInput ? emailInput.value : '').trim().toLowerCase();
                    if (remaining > 0 && (currentEmail === '' || currentEmail === lockedEmail)) {
                        e.preventDefault();
                        syncLockoutUi();
                    }
                });
            }

            syncLockoutUi();
            const interval = setInterval(function () {
                remaining = Math.max(0, remaining - 1);
                syncLockoutUi();
                if (remaining <= 0) {
                    clearInterval(interval);
                }
            }, 1000);
        })();
    </script>
</x-guest-layout>