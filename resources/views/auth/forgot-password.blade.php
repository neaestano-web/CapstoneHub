<x-guest-layout>
    <div class="auth-center-card">
        <!-- Center Emblem / Logo -->
        <div class="auth-emblem-badge" aria-hidden="true">
            <img src="{{ asset('images/scs.png') }}" alt="SCS Logo">
        </div>

        <h1 class="auth-heading">Forgot Password</h1>
        <p class="auth-subheading">
            Enter your registered email address and we will send you a 6-digit OTP code to reset your password.
        </p>

        <!-- Session Status Alert -->
        @if (session('status'))
            <div class="w-full mb-3 p-3 text-xs font-semibold text-emerald-300 bg-emerald-950/70 border border-emerald-500/40 rounded-xl text-center">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="auth-form-body">
            @csrf

            <!-- Email Address Field -->
            <div class="auth-input-group">
                <div class="auth-input-wrap">
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus 
                        autocomplete="email"
                        placeholder="Enter your email address" 
                        class="auth-field"
                    >
                </div>
                @error('email')
                    <p class="auth-field-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="auth-btn-submit">
                Send OTP Code
            </button>

            <!-- Back to Login Link -->
            <p class="auth-switch-text">
                Remember your password? <a href="{{ route('login') }}">Sign in</a>
            </p>
        </form>
    </div>
</x-guest-layout>
