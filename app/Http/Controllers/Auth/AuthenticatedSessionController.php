<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        $lockoutEmail = null;
        $lockoutSeconds = 0;

        $candidateEmail = trim((string) (
            old('email')
            ?: ($request->hasSession() ? $request->session()->get('login_lockout_email') : '')
            ?: (Cache::get(LoginRequest::ipLockCacheKey($request->ip()))['email'] ?? '')
        ));

        if ($candidateEmail !== '') {
            $ipEmailKey = LoginRequest::makeIpEmailKey($candidateEmail, (string) $request->ip());
            $emailKey = LoginRequest::makeEmailKey($candidateEmail);

            if (RateLimiter::tooManyAttempts($ipEmailKey, LoginRequest::MAX_ATTEMPTS)
                || RateLimiter::tooManyAttempts($emailKey, LoginRequest::MAX_ATTEMPTS)) {
                $lockoutSeconds = max(
                    RateLimiter::availableIn($ipEmailKey),
                    RateLimiter::availableIn($emailKey)
                );
                if ($lockoutSeconds > 0) {
                    $lockoutEmail = Str::lower($candidateEmail);
                }
            }
        }

        return view('auth.login', compact('lockoutEmail', 'lockoutSeconds'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Redirect based on user role
        $user = $request->user();

        // Never allow an unverified account to proceed, even after registration approval.
        // This also covers users who closed the browser during OTP verification.
        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.otp.show');
        }
        
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        } elseif ($user->role === 'student') {
            // Check if student registration is approved
            if (!$user->isApproved()) {
                return redirect()->route('registration.status');
            }
            return redirect()->intended(route('student.dashboard', absolute: false));
        }
        
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($cookieName = config('session.cookie')) {
            Cookie::queue(Cookie::forget($cookieName));
        }

        return redirect('/')->with('status', 'You have been successfully logged out.');
    }
}
