<?php

namespace Tests\Feature\Auth;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LoginLockoutTest extends TestCase
{
    public function test_five_failed_login_attempts_lock_for_thirty_minutes_and_persist_across_refresh(): void
    {
        config(['cache.default' => 'array']);
        Auth::shouldReceive('attempt')->times(5)->andReturn(false);

        $email = 'student@jhcsc.edu.ph';
        $ip = '127.0.0.1';

        RateLimiter::clear(LoginRequest::makeIpEmailKey($email, $ip));
        RateLimiter::clear(LoginRequest::makeEmailKey($email));
        Cache::forget(LoginRequest::ipLockCacheKey($ip));

        $makeRequest = function (string $clientIp) use ($email): LoginRequest {
            $req = LoginRequest::create('/login', 'POST', [
                'email' => $email,
                'password' => 'wrong-password',
            ]);
            $req->server->set('REMOTE_ADDR', $clientIp);
            return $req;
        };

        // Attempts 1-4 fail with standard credentials error
        for ($i = 0; $i < 4; $i++) {
            try {
                $makeRequest($ip)->authenticate();
                $this->fail('Expected ValidationException on failed login attempt.');
            } catch (ValidationException $e) {
                $this->assertArrayHasKey('email', $e->errors());
                $this->assertStringNotContainsString('30 minutes', $e->errors()['email'][0]);
            }
        }

        // 5th failed attempt immediately triggers 30-minute lockout
        try {
            $makeRequest($ip)->authenticate();
            $this->fail('Expected 30-minute lockout on 5th failed attempt.');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('30 minutes', $e->errors()['email'][0] ?? '');
        }

        // Simulate browser refresh (GET /login without old input or session errors)
        $getRequest = Request::create('/login', 'GET');
        $getRequest->server->set('REMOTE_ADDR', $ip);
        $view = (new AuthenticatedSessionController())->create($getRequest);
        $data = $view->getData();

        $this->assertSame($email, $data['lockoutEmail']);
        $this->assertGreaterThan(1700, $data['lockoutSeconds']);
        $this->assertLessThanOrEqual(1800, $data['lockoutSeconds']);

        // Simulate submitting locked email from a completely different IP after refresh
        try {
            $makeRequest('203.0.113.55')->authenticate();
            $this->fail('Expected lockout to persist across IP changes.');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('30 minutes', $e->errors()['email'][0] ?? '');
        }
    }
}
