<?php

namespace Tests\Feature\Auth;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LoginLockoutTest extends TestCase
{
    public function test_five_failed_login_attempts_trigger_thirty_minute_lockout(): void
    {
        config(['cache.default' => 'array']);
        Auth::shouldReceive('attempt')->times(5)->andReturn(false);

        $request = LoginRequest::create('/login', 'POST', [
            'email' => 'student@jhcsc.edu.ph',
            'password' => 'wrong-password',
        ]);
        $request->server->set('REMOTE_ADDR', '127.0.0.1');

        RateLimiter::clear($request->throttleKey());

        for ($i = 0; $i < 5; $i++) {
            try {
                $request->authenticate();
                $this->fail('Expected ValidationException on failed login attempt.');
            } catch (ValidationException $e) {
                $this->assertArrayHasKey('email', $e->errors());
            }
        }

        // 6th attempt must trigger 30-minute lockout
        try {
            $request->authenticate();
            $this->fail('Expected 30-minute lockout ValidationException.');
        } catch (ValidationException $e) {
            $msg = $e->errors()['email'][0] ?? '';
            $this->assertStringContainsString('30 minutes', $msg);
        }

        $remaining = RateLimiter::availableIn($request->throttleKey());
        $this->assertGreaterThan(1700, $remaining);
        $this->assertLessThanOrEqual(1800, $remaining);
    }
}
