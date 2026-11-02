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
use Illuminate\Support\Facades\Storage;
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
        Log::info('GET /register page accessed from IP: ' . request()->ip());
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        Log::info('POST /register submitted', [
            'email' => $request->input('email'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'student_id' => $request->input('student_id_number'),
            'has_id_front' => $request->hasFile('id_picture_front'),
            'has_id_back' => $request->hasFile('id_picture_back'),
        ]);

        // Clean up any previous unverified OR rejected student registration with this email so they can re-register cleanly
        if ($request->filled('email')) {
            $existingStale = User::where('email', strtolower($request->email))
                ->where(function ($q) {
                    $q->whereNull('email_verified_at')
                      ->orWhere('registration_status', 'rejected');
                })
                ->first();

            // Only clean up if student and has no existing researches
            if ($existingStale && $existingStale->role === 'student' && $existingStale->researches()->count() === 0) {
                if ($existingStale->id_picture_front && !str_starts_with($existingStale->id_picture_front, 'data:image/')) {
                    Storage::disk('local')->delete($existingStale->id_picture_front);
                    Storage::disk('public')->delete($existingStale->id_picture_front);
                }
                if ($existingStale->id_picture_back && !str_starts_with($existingStale->id_picture_back, 'data:image/')) {
                    Storage::disk('local')->delete($existingStale->id_picture_back);
                    Storage::disk('public')->delete($existingStale->id_picture_back);
                }
                OtpCode::where('user_id', $existingStale->id)->delete();
                $existingStale->delete();
                Log::info("Cleaned up previous stale/rejected registration for email: " . $request->email);
            }
        }

        try {
            $request->validate([
                'first_name'        => ['required', 'string', 'max:255'],
                'last_name'         => ['required', 'string', 'max:255'],
                'email'             => [
                    'required',
                    'string',
                    'lowercase',
                    'email',
                    'max:255',
                    Rule::unique(User::class)->where(function ($q) {
                        $q->whereNotNull('email_verified_at')
                          ->where('registration_status', '!=', 'rejected');
                    }),
                ],
                'password'          => ['required', 'confirmed', Rules\Password::defaults()],
                'student_id_number' => ['required', 'string', 'max:50'],
                'id_picture_front'  => ['required', 'image', 'mimes:jpeg,png,jpg,webp,gif,bmp', 'max:10240'],
                'id_picture_back'   => ['required', 'image', 'mimes:jpeg,png,jpg,webp,gif,bmp', 'max:10240'],
            ], [
                'first_name.required'        => 'Please provide your first name.',
                'last_name.required'         => 'Please provide your last name.',
                'student_id_number.required' => 'Please provide your student ID number.',
                'id_picture_front.required'  => 'Please upload the front of your student ID.',
                'id_picture_front.image'     => 'The front ID must be an image file.',
                'id_picture_front.max'       => 'The front ID image must not exceed 10MB.',
                'id_picture_back.required'   => 'Please upload the back of your student ID.',
                'id_picture_back.image'      => 'The back ID must be an image file.',
                'id_picture_back.max'        => 'The back ID image must not exceed 10MB.',
            ]);
        } catch (ValidationException $e) {
            Log::warning('POST /register validation FAILED for ' . $request->input('email'), [
                'errors' => $e->errors(),
            ]);
            throw $e;
        }

        // Build full name from first and last
        $fullName = trim($request->first_name . ' ' . $request->last_name);

        // Store ID pictures on private disk
        $frontPath = $request->file('id_picture_front')->store('id-pictures', 'local');
        $backPath  = $request->file('id_picture_back')->store('id-pictures', 'local');

        // Mirror to public disk for fallback
        try {
            $request->file('id_picture_front')->storeAs('id-pictures', basename($frontPath), 'public');
            $request->file('id_picture_back')->storeAs('id-pictures', basename($backPath), 'public');
        } catch (\Throwable $e) {
            Log::warning('Could not mirror ID pictures to public disk: ' . $e->getMessage());
        }

        // Generate persistent Base64 Data URI so photos survive cloud container rebuilds
        $frontDbValue = $frontPath;
        $backDbValue  = $backPath;
        try {
            $frontBase64 = $this->compressAndEncodeImage($request->file('id_picture_front'));
            $backBase64  = $this->compressAndEncodeImage($request->file('id_picture_back'));
            if ($frontBase64) {
                $frontDbValue = $frontBase64;
            }
            if ($backBase64) {
                $backDbValue = $backBase64;
            }
        } catch (\Throwable $e) {
            Log::warning('Could not encode ID pictures to base64: ' . $e->getMessage());
        }

        // Create user with 'pending' registration status (needs manual admin review)
        try {
            $user = User::create([
                'name'                  => $fullName,
                'first_name'            => $request->first_name,
                'last_name'             => $request->last_name,
                'email'                 => trim(strtolower($request->email)),
                'password'              => Hash::make($request->password),
                'student_id_number'     => $request->student_id_number,
                'id_picture_front'      => $frontDbValue,
                'id_picture_back'       => $backDbValue,
                'registration_status'   => 'pending',
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Fallback to relative file path if column has not yet migrated to LONGTEXT
            Log::warning('Base64 storage query failed, falling back to disk paths: ' . $e->getMessage());
            $user = User::create([
                'name'                  => $fullName,
                'first_name'            => $request->first_name,
                'last_name'             => $request->last_name,
                'email'                 => trim(strtolower($request->email)),
                'password'              => Hash::make($request->password),
                'student_id_number'     => $request->student_id_number,
                'id_picture_front'      => $frontPath,
                'id_picture_back'       => $backPath,
                'registration_status'   => 'pending',
            ]);
        }

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

    /**
     * Compress and encode an uploaded image to a compact base64 Data URI (max ~150-250KB).
     */
    protected function compressAndEncodeImage($file): ?string
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        $realPath = $file->getRealPath();
        if (!$realPath || !file_exists($realPath)) {
            return null;
        }

        $mime = $file->getMimeType() ?: 'image/jpeg';
        $contents = file_get_contents($realPath);

        // If GD is available, resize large images down to max 1200px and compress
        if (function_exists('imagecreatefromstring')) {
            $img = @imagecreatefromstring($contents);
            if ($img !== false) {
                $width = imagesx($img);
                $height = imagesy($img);
                $maxDim = 1200;

                if ($width > $maxDim || $height > $maxDim) {
                    $ratio = min($maxDim / $width, $maxDim / $height);
                    $newWidth = (int) round($width * $ratio);
                    $newHeight = (int) round($height * $ratio);

                    $resized = imagecreatetruecolor($newWidth, $newHeight);
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                    imagecopyresampled($resized, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    imagedestroy($img);
                    $img = $resized;
                }

                ob_start();
                imagejpeg($img, null, 82);
                $compressedData = ob_get_clean();
                imagedestroy($img);

                if (!empty($compressedData)) {
                    return 'data:image/jpeg;base64,' . base64_encode($compressedData);
                }
            }
        }

        return 'data:' . $mime . ';base64,' . base64_encode($contents);
    }
}
