<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpCodeMail;
use App\Models\OtpCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OtpVerificationController extends Controller
{
    /**
     * Show the OTP verification form.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Count OTP requests in the past hour
        $recentOtpCount = OtpCode::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        // Calculate remaining 60-second cooldown from the latest OTP
        $latestOtp = OtpCode::where('user_id', $user->id)->latest()->first();
        $cooldownSeconds = 0;
        if ($latestOtp && (int) $latestOtp->created_at->diffInSeconds(now()) < 60) {
            $cooldownSeconds = 60 - (int) $latestOtp->created_at->diffInSeconds(now());
        }

        $attemptsLeft = max(0, 3 - $recentOtpCount);

        return view('auth.verify-otp', [
            'cooldownSeconds' => $cooldownSeconds,
            'attemptsLeft'    => $attemptsLeft,
            'recentRequests'  => $recentOtpCount,
        ]);
    }

    /**
     * Verify the submitted OTP code.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();

        $otpRecord = OtpCode::where('user_id', $user->id)
            ->where('code', $request->otp)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otpRecord) {
            return back()->withErrors([
                'otp' => 'The OTP code is invalid or has expired.',
            ]);
        }

        // Mark OTP as used
        $otpRecord->update(['used' => true]);

        // Clean up OTP codes for this verified user
        OtpCode::where('user_id', $user->id)->delete();

        // Mark email as verified
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect()->intended(route('dashboard', absolute: false) . '?verified=1');
    }

    /**
     * Resend a new OTP code to the user.
     */
    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // 1. Check 3-request limit per hour
        $recentOtpCount = OtpCode::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($recentOtpCount >= 3) {
            return back()->withErrors([
                'otp' => 'Maximum OTP request limit reached (3/3 attempts). Please wait 1 hour before requesting another code.',
            ]);
        }

        // 2. Check 60-second cooldown
        $latestOtp = OtpCode::where('user_id', $user->id)->latest()->first();
        if ($latestOtp && (int) $latestOtp->created_at->diffInSeconds(now()) < 60) {
            $secondsLeft = 60 - (int) $latestOtp->created_at->diffInSeconds(now());
            return back()->withErrors([
                'otp' => "Please wait {$secondsLeft} seconds before requesting a new OTP.",
            ]);
        }

        // Invalidate all previous OTPs for this user
        OtpCode::where('user_id', $user->id)->update(['used' => true]);

        // Generate and send new OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'user_id' => $user->id,
            'code'    => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        try {
            Mail::to($user->email)->send(new OtpCodeMail($otp, $user->name));
            Log::info('OTP resend email sent successfully to ' . $user->email);
        } catch (\Throwable $e) {
            Log::error('Failed to resend OTP email to ' . $user->email . ': ' . $e->getMessage());
            return back()->withErrors([
                'otp' => 'Unable to resend OTP email. Please ensure your email settings are configured or try again later.',
            ]);
        }

        return back()->with('status', 'verification-link-sent');
    }
}
