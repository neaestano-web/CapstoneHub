<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public const MAX_ATTEMPTS = 5;
    public const LOCKOUT_SECONDS = 1800; // 30 minutes

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey(), self::LOCKOUT_SECONDS);
            RateLimiter::hit($this->emailThrottleKey(), self::LOCKOUT_SECONDS);

            if ($this->isRateLimited()) {
                $this->persistLockoutState();
                $this->throwLockoutException();
            }

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        RateLimiter::clear($this->emailThrottleKey());
        Cache::forget(self::ipLockCacheKey($this->ip()));
        if ($this->hasSession()) {
            $this->session()->forget('login_lockout_email');
        }
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! $this->isRateLimited()) {
            return;
        }

        $this->persistLockoutState();
        $this->throwLockoutException();
    }

    private function isRateLimited(): bool
    {
        return RateLimiter::tooManyAttempts($this->throttleKey(), self::MAX_ATTEMPTS)
            || RateLimiter::tooManyAttempts($this->emailThrottleKey(), self::MAX_ATTEMPTS);
    }

    private function persistLockoutState(): void
    {
        $email = Str::lower(trim((string) $this->string('email')));
        if ($email === '') {
            return;
        }

        Cache::put(self::ipLockCacheKey($this->ip()), ['email' => $email], self::LOCKOUT_SECONDS);

        if ($this->hasSession()) {
            $this->session()->put('login_lockout_email', $email);
        }
    }

    /**
     * @throws ValidationException
     */
    private function throwLockoutException(): void
    {
        event(new Lockout($this));

        $seconds = max(
            RateLimiter::availableIn($this->throttleKey()),
            RateLimiter::availableIn($this->emailThrottleKey())
        );
        $minutes = (int) ceil($seconds / 60);

        $message = $seconds >= 60
            ? "Too many failed login attempts. Please try again in {$minutes} " . Str::plural('minute', $minutes) . '.'
            : trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => $minutes,
            ]);

        throw ValidationException::withMessages([
            'email' => $message,
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request (email + IP).
     */
    public function throttleKey(): string
    {
        return self::makeIpEmailKey((string) $this->string('email'), (string) $this->ip());
    }

    /**
     * Get the persistent email-only throttle key so IP shifts or browser refreshes cannot bypass lockout.
     */
    public function emailThrottleKey(): string
    {
        return self::makeEmailKey((string) $this->string('email'));
    }

    public static function makeIpEmailKey(string $email, string $ip): string
    {
        return Str::transliterate(Str::lower(trim($email)) . '|' . $ip);
    }

    public static function makeEmailKey(string $email): string
    {
        return 'login_email_lock:' . sha1(Str::lower(trim($email)));
    }

    public static function ipLockCacheKey(?string $ip): string
    {
        return 'login_ip_last_lock:' . sha1((string) $ip);
    }
}
