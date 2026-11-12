<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationApprovedMail;
use App\Mail\RegistrationRejectedMail;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $role = $request->input('role', 'all');

        // Strictly exclude pending registrations.
        // User Accounts page only displays active accounts: Admins and Approved Students.
        // Pending student accounts are reviewed under the Registrations page.
        $query = User::withCount('researches')
            ->where(function ($q) {
                $q->where('role', 'admin')
                  ->orWhere(function ($sq) {
                      $sq->where('role', 'student')
                         ->where('registration_status', 'approved');
                  });
            });

        if ($search !== '') {
            $query->where(function ($searchQuery) use ($search) {
                $searchQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('student_id_number', 'like', "%{$search}%");
            });
        }

        if ($role === 'student') {
            $query->where('role', 'student');
        } elseif ($role === 'admin') {
            $query->where('role', 'admin');
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $approvedCount = User::where('role', 'student')->where('registration_status', 'approved')->count();
        $adminCount = User::where('role', 'admin')->count();
        $allCount = $approvedCount + $adminCount;

        // Pending count for the notification link to Registrations reviewer
        $pendingCount = User::where('role', 'student')
            ->whereNotNull('email_verified_at')
            ->where('registration_status', 'pending')
            ->count();

        return view('admin.users.index', compact('users', 'search', 'role', 'allCount', 'approvedCount', 'adminCount', 'pendingCount'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:student,admin',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'registration_status' => 'approved', // Admin manually created user
            'email_verified_at' => now(), // Auto-verify for admin-created users
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        // If student is still pending review, redirect to Registrations reviewer
        if ($user->role === 'student' && $user->registration_status === 'pending') {
            return redirect()->route('admin.pending-registrations.show', $user);
        }

        $researches = $user->researches()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.users.show', compact('user', 'researches'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:student,admin',
            'registration_status' => 'nullable|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string|max:500',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $oldStatus = $user->registration_status;
        $newStatus = $request->input('registration_status', $user->registration_status);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($user->role === 'student' && $request->filled('registration_status')) {
            $data['registration_status'] = $newStatus;
            if ($newStatus === 'rejected') {
                $data['registration_rejection_reason'] = $request->rejection_reason;
            } elseif ($newStatus === 'approved') {
                $data['registration_rejection_reason'] = null;
            }
        }

        // Update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Send email notification if registration status was changed by the admin
        if ($user->role === 'student' && $oldStatus !== $newStatus) {
            if ($newStatus === 'approved') {
                try {
                    Mail::to($user->email)->send(new RegistrationApprovedMail($user));
                    Log::info("Registration approval notification email sent to {$user->email} from User Accounts edit.");
                } catch (\Throwable $e) {
                    Log::error("Failed to send approval email to {$user->email}: " . $e->getMessage());
                }
            } elseif ($newStatus === 'rejected') {
                try {
                    Mail::to($user->email)->send(new RegistrationRejectedMail($user, $request->rejection_reason));
                    Log::info("Registration rejection notification email sent to {$user->email} from User Accounts edit.");
                } catch (\Throwable $e) {
                    Log::error("Failed to send rejection email to {$user->email}: " . $e->getMessage());
                }
            }
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.' . ($oldStatus !== $newStatus ? ' Status notification email sent to student.' : ''));
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot delete your own account.');
        }

        // Check if user has researches
        $researchesCount = $user->researches()->count();
        
        if ($researchesCount > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', "Cannot delete user. They have {$researchesCount} associated researches. Delete the researches first.");
        }

        $email = $user->email;
        $userId = $user->id;
        $user->delete();

        \App\Models\AuditLog::record('user.trashed', 'user', $userId, $email);

        return redirect()->route('admin.users.index')
            ->with('success', 'User moved to Recycle Bin successfully.');
    }

    /**
     * Toggle user status (active/inactive).
     */
    public function toggleStatus(User $user)
    {
        // Prevent admin from deactivating themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot change your own status.');
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        
        return back()->with('success', "User {$status} successfully.");
    }
}
