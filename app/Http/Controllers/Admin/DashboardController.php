<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Research;
use App\Models\ResearchCategory;
use App\Models\SearchLog;
use App\Models\User;
use App\Services\SystemCleanupService;
use Illuminate\Support\Str;

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

        // 1. Category Distribution (Approved Researches per Category)
        $categoryDistribution = ResearchCategory::withCount([
            'classifiedResearches as approved_researches_count' => fn ($query) => $query->where('status', 'approved')
        ])
            ->having('approved_researches_count', '>', 0)
            ->orderByDesc('approved_researches_count')
            ->take(6)
            ->get();

        if ($categoryDistribution->isEmpty()) {
            $categoryDistribution = ResearchCategory::withCount([
                'researches as approved_researches_count' => fn ($query) => $query->where('status', 'approved')
            ])
                ->having('approved_researches_count', '>', 0)
                ->orderByDesc('approved_researches_count')
                ->take(6)
                ->get();
        }

        $catPalette = [
            '#0c4233', '#10b981', '#3b82f6', '#8b5cf6', '#f59e0b', '#ec4899', '#06b6d4', '#6366f1'
        ];

        $catLabels = $categoryDistribution->pluck('name')->toArray();
        $catValues = $categoryDistribution->pluck('approved_researches_count')->toArray();
        $totalApprovedCategorized = array_sum($catValues);

        // 3. Top Read / Most Viewed Capstone Manuscripts
        $topStudies = Research::where('status', 'approved')
            ->with('category')
            ->orderByDesc('views_count')
            ->orderByDesc('approved_at')
            ->take(5)
            ->get();

        $topStudyLabels = $topStudies->map(fn($s) => Str::limit($s->title, 32))->toArray();
        $topStudyFullTitles = $topStudies->pluck('title')->toArray();
        $topStudyViews = $topStudies->pluck('views_count')->toArray();

        // 4. Popular Search Topics
        $mostSearched = SearchLog::selectRaw('query, count(*) as total')
            ->where('query', '!=', '')
            ->groupBy('query')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $searchLabels = $mostSearched->pluck('query')->toArray();
        $searchValues = $mostSearched->pluck('total')->toArray();

        return view('admin.dashboard', compact(
            'totalResearches',
            'approvedResearches',
            'pendingResearches',
            'totalUsers',
            'categoryDistribution',
            'catLabels',
            'catValues',
            'catPalette',
            'totalApprovedCategorized',
            'topStudies',
            'topStudyLabels',
            'topStudyFullTitles',
            'topStudyViews',
            'mostSearched',
            'searchLabels',
            'searchValues'
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
