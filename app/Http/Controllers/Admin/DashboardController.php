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
        $totalUsers = User::count();

        return view('admin.dashboard', compact(
            'totalResearches',
            'approvedResearches',
            'pendingResearches',
            'totalUsers'
        ));
    }
}
