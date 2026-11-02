<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpCodeMail;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Clean up any previous abandoned unverified registration with this email
        if ($request->filled('email')) {
            $existingUnverified = User::where('email', strtolower($request->email))
                ->whereNull('email_verified_at')
                ->first();

            if ($existingUnverified) {
                OtpCode::where('user_id', $existingUnverified->id)->delete();
                $existingUnverified->delete();
            }
        }

        $request->validate([
            'first_name'        => ['required', 'string', 'max:255'],
            'last_name'         => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->whereNotNull('email_verified_at')],
            'password'          => ['required', 'confirmed', Rules\Password::defaults()],
            'student_id_number' => ['required', 'string', 'max:50'],
            'id_picture_front'  => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'id_picture_back'   => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ], [
            'first_name.required'        => 'Please provide your first name.',
            'last_name.required'         => 'Please provide your last name.',
            'student_id_number.required' => 'Please provide your student ID number.',
            'id_picture_front.required'  => 'Please upload the front of your student ID.',
            'id_picture_front.image'     => 'The front ID must be an image file.',
            'id_picture_front.max'       => 'The front ID image must not exceed 5MB.',
            'id_picture_back.required'   => 'Please upload the back of your student ID.',
            'id_picture_back.image'      => 'The back ID must be an image file.',
            'id_picture_back.max'        => 'The back ID image must not exceed 5MB.',
        ]);

        // Build full name from first and last
        $fullName = trim($request->first_name . ' ' . $request->last_name);

        // Store ID pictures
        $frontPath = $request->file('id_picture_front')->store('id-pictures', 'local');
        $backPath  = $request->file('id_picture_back')->store('id-pictures', 'local');

        // Create user with 'pending' registration status (needs manual admin review)
        $user = User::create([
            'name'                  => $fullName,
            'first_name'            => $request->first_name,
            'last_name'             => $request->last_name,
            'email'                 => $request->email,
            'password'              => Hash::make($request->password),
            'student_id_number'     => $request->student_id_number,
            'id_picture_front'      => $frontPath,
            'id_picture_back'       => $backPath,
            'registration_status'   => 'pending',
        ]);

        Log::info("Registered user {$fullName} with pending registration status.");

        // Generate a 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'user_id'    => $user->id,
            'code'       => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send OTP via email (wrapped in try-catch so registration still succeeds)
        try {
            Mail::to($user->email)->send(new OtpCodeMail($otp, $user->name));
            Log::info('OTP email sent successfully to ' . $user->email);
        } catch (\Throwable $e) {
            Log::error('Failed to send OTP email to ' . $user->email . ': ' . $e->getMessage());
        }

        Auth::login($user);

        return redirect()->route('verification.otp.show');
    }
}
