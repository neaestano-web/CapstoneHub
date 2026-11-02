<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Research;
use App\Models\User;

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
}
