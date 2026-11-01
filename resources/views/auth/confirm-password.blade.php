<x-guest-layout>
    <div class="auth-center-card">
        <div class="auth-emblem-badge" aria-hidden="true">
            <img src="{{ asset('images/scs.png') }}" alt="SCS Logo">
        </div>

        <h1 class="auth-heading">Confirm Password</h1>
        <p class="auth-subheading">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </p>

        <form method="POST" action="{{ route('password.confirm') }}" class="auth-form-body">
            @csrf

            <!-- Password -->
            <div class="auth-input-group">
                <label for="password" class="auth-label">{{ __('Password') }}</label>
                <div class="auth-input-wrap">
                    <input id="password" class="auth-field"
                           type="password"
                           name="password"
                           placeholder="••••••••••••"
                           required autocomplete="current-password" />
                </div>
                @error('password')
                    <p class="auth-field-error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="auth-btn-submit">
                {{ __('Confirm') }}
            </button>
        </form>
    </div>
</x-guest-layout>
