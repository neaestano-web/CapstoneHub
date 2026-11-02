<x-guest-layout>
    <div class="auth-center-card">
        <!-- Center Emblem / Logo -->
        <div class="auth-emblem-badge" aria-hidden="true">
            <img src="{{ asset('images/scs.png') }}" alt="SCS Logo">
        </div>

        <h1 class="auth-heading">Verify OTP Code</h1>
        <p class="auth-subheading">
            We have sent a 6-digit verification code to your email. Please enter it below to activate your account.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="w-full mb-3 p-3 text-xs font-semibold text-emerald-300 bg-emerald-950/70 border border-emerald-500/40 rounded-xl text-center">
                A new OTP code has been sent to your email address.
            </div>
        @endif

        @if (isset($attemptsLeft) && $attemptsLeft <= 0)
            <div class="w-full mb-3 p-3 text-xs font-semibold text-amber-300 bg-amber-950/70 border border-amber-500/40 rounded-xl text-center">
                Maximum OTP request limit reached (3/3 attempts). Please check your email inbox or spam folder for previous codes, or wait 1 hour to try again.
            </div>
        @endif

        <form method="POST" action="{{ route('verification.otp.verify') }}" class="auth-form-body">
            @csrf

            <div class="auth-input-group">
                <label for="otp" class="auth-label" style="text-align: center; display: block;">
                    Enter 6-Digit OTP Code
                </label>
                <div class="auth-input-wrap">
                    <input 
                        id="otp" 
                        type="text" 
                        name="otp" 
                        value="{{ old('otp') }}"
                        maxlength="6"
                        inputmode="numeric"
                        pattern="[0-9]{6}"
                        placeholder="••••••" 
                        required 
                        autofocus
                        class="auth-field"
                        style="text-align: center; letter-spacing: 0.35em; font-size: 1.35rem; font-family: 'JetBrains Mono', monospace; font-weight: 700;"
                    >
                </div>
                @error('otp')
                    <p class="auth-field-error" style="text-align:center;">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="auth-btn-submit">
                Verify Account
            </button>
        </form>

        <div class="flex items-center justify-between w-full mt-4 text-xs pt-3 border-t border-slate-800/60">
            <div>
                @if (isset($attemptsLeft) && $attemptsLeft <= 0)
                    <span class="text-slate-500 font-semibold cursor-not-allowed text-xs flex items-center gap-1.5 opacity-70">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Limit reached (3/3)
                    </span>
                    <div class="text-[10px] text-slate-500 mt-0.5">
                        0 requests left (1-hour cooldown)
                    </div>
                @else
                    <form method="POST" action="{{ route('verification.otp.resend') }}" id="resendOtpForm">
                        @csrf
                        <button 
                            type="submit" 
                            id="resendBtn" 
                            class="text-emerald-400 font-bold hover:underline bg-transparent border-0 cursor-pointer p-0 text-xs transition-all flex items-center gap-1.5 disabled:text-slate-500 disabled:no-underline disabled:cursor-not-allowed disabled:opacity-75"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span id="resendBtnText">Resend Code</span>
                        </button>
                    </form>
                    <div class="text-[10px] text-slate-400 mt-0.5">
                        Attempts remaining: <span class="text-emerald-400 font-bold">{{ $attemptsLeft ?? 3 }}/3</span>
                    </div>
                @endif
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-slate-400 font-semibold hover:text-slate-200 bg-transparent border-0 cursor-pointer p-0">
                    Log Out
                </button>
            </form>
        </div>
    </div>

    @if (!isset($attemptsLeft) || $attemptsLeft > 0)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const resendForm = document.getElementById('resendOtpForm');
            const resendBtn = document.getElementById('resendBtn');
            const resendBtnText = document.getElementById('resendBtnText');
            if (!resendBtn || !resendBtnText) return;

            const storageKey = 'reg_otp_cooldown_until';
            let serverCooldown = {{ (int) ($cooldownSeconds ?? 60) }};
            const now = Math.floor(Date.now() / 1000);
            const storedUntil = parseInt(sessionStorage.getItem(storageKey), 10);

            let targetUntil = 0;
            if (storedUntil && storedUntil > now) {
                targetUntil = Math.max(storedUntil, now + serverCooldown);
            } else if (serverCooldown > 0) {
                targetUntil = now + serverCooldown;
                sessionStorage.setItem(storageKey, targetUntil.toString());
            }

            function tick() {
                const currentNow = Math.floor(Date.now() / 1000);
                const remaining = Math.max(0, targetUntil - currentNow);

                if (remaining > 0) {
                    resendBtn.disabled = true;
                    resendBtnText.textContent = `Resend Code in ${remaining}s`;
                } else {
                    resendBtn.disabled = false;
                    resendBtnText.textContent = "Resend Code";
                    sessionStorage.removeItem(storageKey);
                    if (intervalId) clearInterval(intervalId);
                }
            }

            let intervalId = null;
            if (targetUntil > now) {
                tick();
                intervalId = setInterval(tick, 1000);
            } else {
                resendBtn.disabled = false;
                resendBtnText.textContent = "Resend Code";
            }

            if (resendForm) {
                resendForm.addEventListener('submit', function () {
                    resendBtn.disabled = true;
                    resendBtnText.textContent = 'Sending...';
                    sessionStorage.setItem(storageKey, (Math.floor(Date.now() / 1000) + 60).toString());
                });
            }
        });
    </script>
    @endif
</x-guest-layout>
