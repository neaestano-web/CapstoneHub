<x-guest-layout>
    <div class="auth-center-card">
        <!-- Center Emblem / Logo -->
        <div class="auth-emblem-badge" aria-hidden="true">
            <img src="{{ asset('images/scs.png') }}" alt="SCS Logo">
        </div>

        <h1 class="auth-heading">Verify OTP Code</h1>
        <p class="auth-subheading">
            {{ __('We have sent a 6-digit OTP code to your email address. Please enter it below to verify your identity and reset your password.') }}
        </p>

        @if (session('status') == 'otp-sent')
            <div class="w-full mb-3 p-3 text-xs font-semibold text-emerald-300 bg-emerald-950/70 border border-emerald-500/40 rounded-xl text-center">
                {{ __('A new OTP code has been sent to your email address.') }}
            </div>
        @endif

        <!-- OTP Verification Form -->
        <form method="POST" action="{{ route('password.otp.verify') }}" class="auth-form-body">
            @csrf

            <div class="auth-input-group">
                <label for="otp" class="auth-label" style="text-align: center; display: block;">
                    {{ __('OTP Code') }}
                </label>
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
                    <p class="auth-field-error" style="text-align: center;">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="auth-btn-submit">
                {{ __('Verify OTP') }}
            </button>
        </form>

        <div class="flex items-center justify-between w-full mt-4 text-xs pt-2 border-t border-slate-800/60">
            <form method="POST" action="{{ route('password.otp.resend') }}">
                @csrf
                <button type="submit" class="text-emerald-400 font-bold hover:underline bg-transparent border-0 cursor-pointer p-0 text-xs">
                    {{ __('Resend OTP Code') }}
                </button>
            </form>

            <a href="{{ route('password.request') }}" class="text-slate-400 font-semibold hover:text-emerald-400 hover:underline">
                {{ __('Try Again') }}
            </a>
        </div>
    </div>
</x-guest-layout>