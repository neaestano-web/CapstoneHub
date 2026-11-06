<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Research;
use App\Models\User;
use App\Services\SystemCleanupService;

class DashboardController extends Controller
{
    public function index()
    {
        $totalResearches = Research::count();
        $approvedResearches = Research::where('status', 'approved')->count();
        $pendingResearches = Research::where('status', 'pending')->count();
        $totalUsers = User::where(function ($q) {
            $q->where('role', 'admin')
              ->orWhere(function ($sq) {
                  $sq->where('role', 'student')->whereNotNull('email_verified_at');
              });
        })->count();

        return view('admin.dashboard', compact(
            'totalResearches',
            'approvedResearches',
            'pendingResearches',
            'totalUsers'
        ));
    }

    /**
     * Delete all projects and non-admin users, keeping only administrators.
     */
    public function wipeData(SystemCleanupService $cleanup)
    {
        $result = $cleanup->wipeDataKeepAdmins();

        return redirect()->route('admin.dashboard')->with(
            'success',
            "All projects ({$result['researches']}) and non-admin users ({$result['users']}) have been deleted successfully. The repository has been reset for a fresh start."
        );
    }
}
