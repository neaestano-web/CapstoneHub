<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationApprovedMail;
use App\Mail\RegistrationRejectedMail;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PendingRegistrationController extends Controller
{
    /**
     * Display a listing of pending registrations.
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'pending');

        // Show student applications whose email has been verified via OTP
        $query = User::where('role', 'student')
            ->whereNotNull('email_verified_at')
            ->whereIn('registration_status', ['pending', 'rejected']);

        if ($filter === 'pending') {
            $query->where('registration_status', 'pending');
        } elseif ($filter === 'rejected') {
            $query->where('registration_status', 'rejected');
        }

        $search = trim($request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($searchQuery) use ($search) {
                $searchQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('student_id_number', 'like', "%{$search}%");
            });
        }

        $registrations = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $pendingCount = User::where('role', 'student')->whereNotNull('email_verified_at')->where('registration_status', 'pending')->count();
        $rejectedCount = User::where('role', 'student')->whereNotNull('email_verified_at')->where('registration_status', 'rejected')->count();
        $allCount = $pendingCount + $rejectedCount;

        return view('admin.pending-registrations.index', compact('registrations', 'filter', 'search', 'pendingCount', 'rejectedCount', 'allCount'));
    }

    /**
     * Show details of a pending registration.
     */
    public function show(User $user)
    {
        if ($user->role !== 'student') {
            abort(404);
        }

        return view('admin.pending-registrations.show', compact('user'));
    }

    /**
     * Serve a student's ID picture reliably to administrators.
     */
    public function idPicture(User $user, string $side = 'front')
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        if ($user->role !== 'student') {
            abort(404);
        }

        $rawPath = $side === 'back' ? $user->id_picture_back : $user->id_picture_front;

        if (empty($rawPath)) {
            return $this->servePlaceholderSvg($user, $side, 'No ID photo uploaded for this student');
        }

        // 1. Handle base64 Data URI if stored directly in database
        if (str_starts_with($rawPath, 'data:image/')) {
            [$meta, $data] = explode(',', $rawPath, 2);
            preg_match('#data:(image/[^;]+)#', $meta, $matches);
            $mime = $matches[1] ?? 'image/jpeg';
            return response(base64_decode($data), 200, [
                'Content-Type' => $mime,
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        // 2. Normalize path and search all candidate disk locations
        $cleanPath = ltrim(str_replace('\\', '/', $rawPath), '/');
        $basename = basename($cleanPath);

        $candidatePaths = [
            Storage::disk('local')->path($cleanPath),
            Storage::disk('public')->path($cleanPath),
            storage_path('app/private/' . $cleanPath),
            storage_path('app/public/' . $cleanPath),
            storage_path('app/' . $cleanPath),
            public_path('storage/' . $cleanPath),
            public_path($cleanPath),
            storage_path('app/private/id-pictures/' . $basename),
            storage_path('app/public/id-pictures/' . $basename),
            storage_path('app/id-pictures/' . $basename),
            public_path('storage/id-pictures/' . $basename),
        ];

        foreach ($candidatePaths as $candidate) {
            if (!empty($candidate) && file_exists($candidate) && is_file($candidate)) {
                $mime = mime_content_type($candidate) ?: 'image/jpeg';
                return response()->file($candidate, [
                    'Content-Type' => $mime,
                    'Cache-Control' => 'public, max-age=86400',
                ]);
            }
        }

        // 3. Fallback SVG if file was wiped during cloud host container reboot
        return $this->servePlaceholderSvg($user, $side, 'ID photo unavailable on disk (Server restarted)');
    }

    /**
     * Generate an SVG placeholder badge when an ID photo is missing on disk.
     */
    protected function servePlaceholderSvg(User $user, string $side, string $statusText = 'Photo Unavailable')
    {
        $name = htmlspecialchars($user->name ?: 'Student', ENT_QUOTES, 'UTF-8');
        $idNum = htmlspecialchars($user->student_id_number ?: 'N/A', ENT_QUOTES, 'UTF-8');
        $sideLabel = ucfirst($side) . ' Side';
        $status = htmlspecialchars($statusText, ENT_QUOTES, 'UTF-8');

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="600" height="380" viewBox="0 0 600 380">
  <defs>
    <linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#0f172a" />
      <stop offset="100%" stop-color="#1e293b" />
    </linearGradient>
    <linearGradient id="cardGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#1e293b" />
      <stop offset="100%" stop-color="#334155" />
    </linearGradient>
  </defs>
  <rect width="100%" height="100%" fill="url(#bg)" />
  <rect x="20" y="20" width="560" height="340" rx="20" fill="url(#cardGrad)" stroke="#475569" stroke-width="2" />
  
  <!-- Header bar -->
  <rect x="20" y="20" width="560" height="50" rx="20" fill="#0c4233" />
  <rect x="20" y="55" width="560" height="15" fill="#0c4233" />
  <text x="40" y="52" fill="#a7f3d0" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="14" font-weight="bold" letter-spacing="1">INSTITUTIONAL STUDENT ID — {$sideLabel}</text>
  
  <!-- Icon -->
  <circle cx="100" cy="180" r="48" fill="#0c4233" stroke="#10b981" stroke-width="2"/>
  <text x="100" y="196" fill="#ecfdf5" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="44" text-anchor="middle">🪪</text>
  
  <!-- Student info -->
  <text x="180" y="145" fill="#94a3b8" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="12" font-weight="bold">STUDENT NAME</text>
  <text x="180" y="172" fill="#ffffff" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="20" font-weight="800">{$name}</text>
  
  <text x="180" y="210" fill="#94a3b8" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="12" font-weight="bold">STUDENT ID NUMBER</text>
  <rect x="180" y="222" width="220" height="32" rx="8" fill="#0c4233" />
  <text x="192" y="244" fill="#34d399" font-family="monospace" font-size="16" font-weight="bold">{$idNum}</text>
  
  <!-- Footer notice -->
  <rect x="40" y="295" width="520" height="42" rx="10" fill="#451a03" stroke="#b45309" stroke-width="1"/>
  <text x="60" y="321" fill="#fde68a" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="12" font-weight="bold">⚠ {$status}</text>
</svg>
SVG;

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'no-cache, private',
        ]);
    }

    /**
     * Approve a pending registration.
     */
    public function approve(User $user)
    {
        if ($user->role !== 'student') {
            abort(404);
        }

        $user->registration_status = 'approved';
        $user->registration_rejection_reason = null;
        $user->save();

        // Send approval notification email to student
        $emailSuccess = false;
        try {
            Mail::to($user->email)->send(new RegistrationApprovedMail($user));
            Log::info("Registration approval email sent to {$user->email}");
            $emailSuccess = true;
        } catch (\Throwable $e) {
            Log::error("Failed to send registration approval email to {$user->email}: " . $e->getMessage());
        }

        $msg = "Registration for {$user->name} has been approved.";
        if ($emailSuccess) {
            $msg .= " Notification email sent to {$user->email}.";
        } else {
            $msg .= " (Note: Notification email could not be delivered to {$user->email}. Check logs/mail settings).";
        }

        return back()->with('success', $msg);
    }

    /**
     * Reject a pending registration.
     */
    public function reject(Request $request, User $user)
    {
        if ($user->role !== 'student') {
            abort(404);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $user->registration_status = 'rejected';
        $user->registration_rejection_reason = $request->rejection_reason;
        $user->save();

        // Send rejection notification email to student
        $emailSuccess = false;
        try {
            Mail::to($user->email)->send(new RegistrationRejectedMail($user, $request->rejection_reason));
            Log::info("Registration rejection email sent to {$user->email}");
            $emailSuccess = true;
        } catch (\Throwable $e) {
            Log::error("Failed to send registration rejection email to {$user->email}: " . $e->getMessage());
        }

        $msg = "Registration for {$user->name} has been rejected.";
        if ($emailSuccess) {
            $msg .= " Notification email sent to {$user->email}.";
        } else {
            $msg .= " (Note: Notification email could not be delivered to {$user->email}. Check logs/mail settings).";
        }

        return back()->with('success', $msg);
    }

    /**
     * Delete a pending or rejected registration permanently.
     */
    public function destroy(User $user)
    {
        if ($user->role !== 'student') {
            abort(404);
        }

        if ($user->researches()->count() > 0) {
            return back()->with('error', "Cannot delete student. They have associated researches. Delete the researches first.");
        }

        // Delete uploaded ID pictures
        if ($user->id_picture_front && !str_starts_with($user->id_picture_front, 'data:image/')) {
            Storage::disk('local')->delete($user->id_picture_front);
            Storage::disk('public')->delete($user->id_picture_front);
        }
        if ($user->id_picture_back && !str_starts_with($user->id_picture_back, 'data:image/')) {
            Storage::disk('local')->delete($user->id_picture_back);
            Storage::disk('public')->delete($user->id_picture_back);
        }

        $userName = $user->name;
        $userEmail = $user->email;
        $userId = $user->id;
        OtpCode::where('user_id', $user->id)->delete();
        $user->forceDelete();

        \App\Models\AuditLog::record('user.force_deleted', 'user', $userId, $userEmail, ['context' => 'pending_registration']);

        return redirect()->route('admin.pending-registrations.index')
            ->with('success', "Registration application for {$userName} has been permanently deleted.");
    }
}
