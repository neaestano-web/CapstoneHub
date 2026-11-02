<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationApprovedMail;
use App\Mail\RegistrationRejectedMail;
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

        $query = User::where('role', 'student')
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

        return view('admin.pending-registrations.index', compact('registrations', 'filter', 'search'));
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

        $path = $side === 'back' ? $user->id_picture_back : $user->id_picture_front;

        if (!$path) {
            abort(404, 'No ID photo specified.');
        }

        // 1. Try secure Storage local disk (private)
        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->response($path);
        }

        // 2. Fallback for legacy uploads on public disk
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->response($path);
        }

        abort(404, 'ID photo not found.');
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
}
