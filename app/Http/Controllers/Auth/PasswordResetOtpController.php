<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpMail;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetOtpController extends Controller
{
    /**
     * Show the form to request a password reset OTP.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a password reset OTP to the user's email.
     */
    public function sendOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $request->email)->first();

        // Invalidate all previous password reset OTPs for this user
        OtpCode::where('user_id', $user->id)->update(['used' => true]);

        // Generate a 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'user_id'    => $user->id,
            'code'       => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send OTP strictly via Gmail
        try {
            Mail::to($user->email)->send(new PasswordResetOtpMail($otp, $user->name));
            Log::info("Password reset OTP sent securely to email: {$user->email}");
        } catch (\Throwable $e) {
            Log::error("Failed to send password reset OTP to {$user->email}: " . $e->getMessage());

            return back()->withInput()->withErrors([
                'email' => 'Unable to send OTP email to your Gmail account. Please ensure your email configuration is valid or try again in a few moments.',
            ]);
        }

        // Store email in session for OTP verification
        Session::put('password_reset_email', $user->email);

        return redirect()->route('password.otp.verify.form')
            ->with('status', 'otp-sent');
    }

    /**
     * Show the OTP verification form for password reset.
     */
    public function showVerifyForm(): View|RedirectResponse
    {
        if (!Session::has('password_reset_email')) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-reset-otp');
    }

    /**
     * Verify the OTP and show the password reset form.
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $email = Session::get('password_reset_email');

        if (!$email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Session expired. Please request a new OTP.']);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'User not found.']);
        }

        $otpRecord = OtpCode::where('user_id', $user->id)
            ->where('code', $request->otp)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otpRecord) {
            return back()->withErrors([
                'otp' => 'The OTP code is invalid or has expired. Please check your Gmail or request a new code.',
            ]);
        }

        // Mark OTP as used
        $otpRecord->update(['used' => true]);

        // Store verification status in session
        Session::put('password_reset_verified', true);

        return redirect()->route('password.reset.form');
    }

    /**
     * Show the password reset form.
     */
    public function showResetForm(): View|RedirectResponse
    {
        if (!Session::has('password_reset_email') || !Session::has('password_reset_verified')) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password', [
            'email' => Session::get('password_reset_email'),
        ]);
    }

    /**
     * Reset the user's password.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $email = Session::get('password_reset_email');

        if (!$email || !Session::has('password_reset_verified')) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Session expired. Please request a new OTP.']);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'User not found.']);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        // Clear session
        Session::forget(['password_reset_email', 'password_reset_verified']);

        // Auto-login the user
        Auth::login($user);

        return redirect()->route('dashboard');
    }

    /**
     * Resend a new password reset OTP.
     */
    public function resendOtp(Request $request): RedirectResponse
    {
        $email = Session::get('password_reset_email');

        if (!$email) {
            return redirect()->route('password.request');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request');
        }

        // Invalidate all previous OTPs for this user
        OtpCode::where('user_id', $user->id)->update(['used' => true]);

        // Generate a 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'user_id'    => $user->id,
            'code'       => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send strictly to Gmail
        try {
            Mail::to($user->email)->send(new PasswordResetOtpMail($otp, $user->name));
            Log::info("Password reset OTP resent securely to email: {$user->email}");
        } catch (\Throwable $e) {
            Log::error("Failed to resend password reset OTP to {$user->email}: " . $e->getMessage());

            return back()->withErrors([
                'otp' => 'Unable to resend OTP email. Please ensure your email settings are configured or try again later.',
            ]);
        }

        return back()->with('status', 'otp-sent');
    }
}
