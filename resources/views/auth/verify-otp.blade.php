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

        <form method="POST" action="{{ route('verification.otp.verify') }}" class="auth-form-body">
            @csrf

            <div class="auth-input-group">
                <div class="auth-input-wrap">
                    <input 
                        id="otp" 
                        type="text" 
                        name="otp" 
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

        <div class="flex items-center justify-between w-full mt-4 text-xs">
            <form method="POST" action="{{ route('verification.otp.resend') }}">
                @csrf
                <button type="submit" class="text-emerald-400 font-bold hover:underline bg-transparent border-0 cursor-pointer p-0">
                    Resend Code
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-slate-400 font-semibold hover:text-slate-200 bg-transparent border-0 cursor-pointer p-0">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
