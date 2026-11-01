<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Research;
use App\Models\ResearchCategory;
use App\Models\ProjectType;
use App\Models\SearchLog;
use App\Services\AI\AIService;
use Illuminate\Support\Facades\Auth;

class ResearchController extends Controller
{
    /**
     * Display the student dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();

        $recentResearches = $user->researches()
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $userResearches = $user->researches()->with('category')->get();
        $myTotalViews = $userResearches->sum('views_count');
        $myResearchesCount = $userResearches->count();
        $myApprovedCount = $userResearches->where('status', 'approved')->count();
        $myPendingCount = $userResearches->where('status', 'pending')->count();

        // Real monthly engagement chart data for logged in student's own manuscripts
        $months = [];
        $monthlyViewsData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = now()->subMonths($i);
            $months[] = $monthDate->format('M');
            
            // Calculate total views cumulative for student's researches created on or before that month
            $viewsInMonth = $userResearches->filter(function ($r) use ($monthDate) {
                return $r->created_at <= $monthDate->endOfMonth();
            })->sum('views_count');

            $monthlyViewsData[] = $viewsInMonth;
        }

        $analytics = [
            'approved_researches' => Research::where('status', 'approved')->count(),
            'searches' => SearchLog::count(),
            'categories' => ResearchCategory::count(),
            'study_views' => Research::sum('views_count'),
            'my_total_views' => $myTotalViews,
            'my_researches_count' => $myResearchesCount,
            'my_approved_count' => $myApprovedCount,
            'my_pending_count' => $myPendingCount,
        ];

        $mostSearched = SearchLog::selectRaw('query, count(*) as total')
            ->where('query', '!=', '')
            ->groupBy('query')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $topStudies = Research::where('status', 'approved')
            ->with('category')
            ->orderByDesc('views_count')
            ->orderByDesc('approved_at')
            ->take(5)
            ->get();

        $categoryDistribution = ResearchCategory::withCount([
            'classifiedResearches as approved_researches_count' => fn ($researches) => $researches->where('status', 'approved'),
        ])
            ->having('approved_researches_count', '>', 0)
            ->orderByDesc('approved_researches_count')
            ->take(5)
            ->get();

        $mostExploredCategories = ResearchCategory::withCount('searchLogs')
            ->having('search_logs_count', '>', 0)
            ->orderByDesc('search_logs_count')
            ->take(5)
            ->get();

        return view('student.dashboard', compact(
            'recentResearches',
            'analytics',
            'mostSearched',
            'topStudies',
            'categoryDistribution',
            'mostExploredCategories',
            'userResearches',
            'myTotalViews',
            'myResearchesCount',
            'myApprovedCount',
            'myPendingCount',
            'months',
            'monthlyViewsData'
        ));
    }

    /**
     * Display a listing of the student's researches.
     */
    public function index()
    {
        $researches = Research::where('user_id', Auth::id())
            ->with(['categories', 'projectTypes', 'files'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('student.researches.index', compact('researches'));
    }

    /**
     * Show the form for creating a new research.
     */
    public function create()
    {
        $hasActiveSubmission = Research::where('user_id', Auth::id())->exists();
        if ($hasActiveSubmission) {
            return redirect()->route('student.researches.index')
                ->with('error', 'Limit Reached: Pinoproseso o nakatabi na ang iyong 1 active capstone submission. Maaari mo lamang itong palitan kapag binura mo ang kasalukuyang na-upload.');
        }

        $categories = ResearchCategory::with('projectTypes')->orderBy('id', 'asc')->get();
        return view('student.researches.create', compact('categories'));
    }

    /**
     * AJAX: AI-classify research text into categories and project types.
     */
    public function classify(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'abstract' => 'required|string',
            'project_context' => 'required|string',
            'objectives' => 'required|string',
        ]);

        $categories = ResearchCategory::with('projectTypes')->orderBy('id', 'asc')->get();
        $categoryNames = $categories->pluck('name')->toArray();

        // Build project types array with their parent category name
        $projectTypes = [];
        foreach ($categories as $cat) {
            foreach ($cat->projectTypes as $pt) {
                $projectTypes[] = [
                    'id' => $pt->id,
                    'name' => $pt->name,
                    'category' => $cat->name,
                    'category_id' => $cat->id,
                ];
            }
        }

        $combinedText = $request->title . '. ' . ($request->project_context ? $request->project_context . '. ' : '') . $request->abstract . '. ' . $request->objectives;

        try {
            $ai = app(AIService::class);
            $result = $ai->classifyCategory(
                $combinedText,
                $categoryNames,
                array_map(fn($pt) => ['name' => $pt['name'], 'category' => $pt['category']], $projectTypes)
            );

            // Map AI results back to IDs
            $suggestedCategories = [];
            foreach ($result['categories'] ?? [] as $cat) {
                $match = $categories->firstWhere('name', $cat['name']);
                if ($match && ($cat['confidence'] ?? 0) >= config('classification.minimum_confidence', 40)) {
                    $suggestedCategories[] = [
                        'id' => $match->id,
                        'name' => $match->name,
                        'confidence' => $cat['confidence'],
                    ];
                }
            }

            $suggestedProjectTypes = [];
            foreach ($result['project_types'] ?? [] as $pt) {
                $match = collect($projectTypes)->firstWhere('name', $pt['name']);
                if ($match && ($pt['confidence'] ?? 0) >= config('classification.minimum_confidence', 40)) {
                    $suggestedProjectTypes[] = [
                        'id' => $match['id'],
                        'name' => $match['name'],
                        'category' => $pt['category'],
                        'category_id' => $match['category_id'],
                        'confidence' => $pt['confidence'],
                    ];
                }
            }

            // A selected project type must always include its parent category.
            foreach ($suggestedProjectTypes as $projectType) {
                $hasParentCategory = collect($suggestedCategories)
                    ->contains('id', $projectType['category_id']);

                if (! $hasParentCategory) {
                    $parentCategory = $categories->firstWhere('id', $projectType['category_id']);

                    if ($parentCategory) {
                        $suggestedCategories[] = [
                            'id' => $parentCategory->id,
                            'name' => $parentCategory->name,
                            'confidence' => $projectType['confidence'],
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'categories' => $suggestedCategories,
                'project_types' => $suggestedProjectTypes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI service unavailable. Please select categories manually.',
                'categories' => [],
                'project_types' => [],
            ]);
        }
    }

    /**
     * Store a newly created research in storage.
     */
    public function store(Request $request)
    {
        $hasActiveSubmission = Research::where('user_id', Auth::id())->exists();
        if ($hasActiveSubmission) {
            return redirect()->route('student.researches.index')
                ->with('error', 'Limit Reached: Pinoproseso o nakatabi na ang iyong 1 active capstone submission. Maaari mo lamang itong palitan kapag binura mo ang kasalukuyang na-upload.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'abstract' => 'required|string',
            'project_context' => 'required|string',
            'objectives' => 'required|string',
            'keywords' => 'nullable|string',
            'authors' => 'required',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:research_categories,id',
            'project_type_ids' => 'nullable|array',
            'project_type_ids.*' => 'exists:project_types,id',
            'files' => 'nullable|array|min:1',
            'files.*' => 'mimes:pdf,docx|max:15360',
            'pdf' => 'nullable|mimes:pdf,docx|max:15360',
        ]);

        $rawAuthors = $request->input('authors');
        if (is_array($rawAuthors)) {
            $authorsList = array_values(array_filter(array_map('trim', $rawAuthors)));
            $authorsString = implode(', ', $authorsList);
        } else {
            $authorsString = trim((string) $rawAuthors);
        }

        if (empty($authorsString)) {
            return back()->withErrors(['authors' => 'Please provide at least one author.'])->withInput();
        }

        $uploadedFiles = $request->file('files') ?? [];
        if (empty($uploadedFiles) && $request->hasFile('pdf')) {
            $uploadedFiles = [$request->file('pdf')];
        }

        if (empty($uploadedFiles)) {
            return back()->withErrors(['files' => 'Please upload at least one PDF or DOCX file.'])->withInput();
        }

        // Use the first selected category as the primary category (backward compat)
        $primaryCategoryId = $request->category_ids[0] ?? null;

        $research = Research::create([
            'user_id' => Auth::id(),
            'research_category_id' => $primaryCategoryId,
            'title' => $request->title,
            'abstract' => $request->abstract,
            'project_context' => $request->project_context,
            'objectives' => $request->objectives,
            'keywords' => $request->keywords,
            'authors' => $authorsString,
            'year' => $request->year,
            'pdf_path' => '',
            'status' => 'pending'
        ]);

        $firstPath = null;
        foreach ($uploadedFiles as $index => $file) {
            $path = $file->store('researches', 'public');
            if ($index === 0) {
                $firstPath = $path;
            }
            $research->files()->create([
                'file_path'     => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type'     => $file->getClientMimeType(),
                'file_size'     => $file->getSize(),
                'sort_order'    => $index + 1,
            ]);
        }

        $research->update(['pdf_path' => $firstPath]);

        // Sync many-to-many relationships
        $research->categories()->sync($request->category_ids);
        if ($request->project_type_ids) {
            $research->projectTypes()->sync($request->project_type_ids);
        }

        $catNames = ResearchCategory::whereIn('id', $request->category_ids)->pluck('name')->implode(', ');
        $successMsg = "Research submitted successfully with " . count($uploadedFiles) . " file(s)! Classified under: {$catNames}. Waiting for admin approval.";

        return redirect()->route('student.researches.index')
            ->with('success', $successMsg);
    }

    /**
     * Display the specified research.
     */
    public function show(Research $research)
    {
        if ($research->user_id !== Auth::id()) {
            abort(403);
        }

        $research->load(['categories', 'projectTypes', 'files']);
        return view('student.researches.show', compact('research'));
    }

    /**
     * Show the form for editing the specified research.
     */
    public function edit(Research $research)
    {
        if ($research->user_id !== Auth::id()) {
            abort(403);
        }

        if ($research->status !== 'pending') {
            return redirect()->route('student.researches.index')
                ->with('error', 'Cannot edit research that is already approved or approved.');
        }

        $categories = ResearchCategory::with('projectTypes')->orderBy('id', 'asc')->get();
        $research->load(['categories', 'projectTypes', 'files']);
        return view('student.researches.edit', compact('research', 'categories'));
    }

    /**
     * Update the specified research in storage.
     */
    public function update(Request $request, Research $research)
    {
        if ($research->user_id !== Auth::id()) {
            abort(403);
        }

        if ($research->status !== 'pending') {
            return redirect()->route('student.researches.index')
                ->with('error', 'Cannot update research that is already approved or approved.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'abstract' => 'required|string',
            'project_context' => 'required|string',
            'objectives' => 'required|string',
            'keywords' => 'nullable|string',
            'authors' => 'required',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:research_categories,id',
            'project_type_ids' => 'nullable|array',
            'project_type_ids.*' => 'exists:project_types,id',
            'files' => 'nullable|array',
            'files.*' => 'mimes:pdf,docx|max:15360',
            'pdf' => 'nullable|mimes:pdf,docx|max:15360',
        ]);

        $rawAuthors = $request->input('authors');
        if (is_array($rawAuthors)) {
            $authorsList = array_values(array_filter(array_map('trim', $rawAuthors)));
            $authorsString = implode(', ', $authorsList);
        } else {
            $authorsString = trim((string) $rawAuthors);
        }

        if (empty($authorsString)) {
            return back()->withErrors(['authors' => 'Please provide at least one author.'])->withInput();
        }

        $data = [
            'title' => $request->title,
            'abstract' => $request->abstract,
            'project_context' => $request->project_context,
            'objectives' => $request->objectives,
            'keywords' => $request->keywords,
            'authors' => $authorsString,
            'year' => $request->year,
            'research_category_id' => $request->category_ids[0] ?? null,
        ];

        $uploadedFiles = $request->file('files') ?? [];
        if (empty($uploadedFiles) && $request->hasFile('pdf')) {
            $uploadedFiles = [$request->file('pdf')];
        }

        if (!empty($uploadedFiles)) {
            // Delete old files from storage
            foreach ($research->files as $existingFile) {
                if ($existingFile->file_path && \Storage::disk('public')->exists($existingFile->file_path)) {
                    \Storage::disk('public')->delete($existingFile->file_path);
                }
                $existingFile->delete();
            }

            $firstPath = null;
            foreach ($uploadedFiles as $index => $file) {
                $path = $file->store('researches', 'public');
                if ($index === 0) {
                    $firstPath = $path;
                }
                $research->files()->create([
                    'file_path'     => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type'     => $file->getClientMimeType(),
                    'file_size'     => $file->getSize(),
                    'sort_order'    => $index + 1,
                ]);
            }
            $data['pdf_path'] = $firstPath;
        }

        $research->update($data);
        $research->categories()->sync($request->category_ids);
        $research->projectTypes()->sync($request->project_type_ids ?? []);

        return redirect()->route('student.researches.index')
            ->with('success', 'Research updated successfully.');
    }

    /**
     * Remove the specified research from storage.
     */
    public function destroy(Research $research)
    {
        if ($research->user_id !== Auth::id()) {
            abort(403);
        }

        if ($research->status !== 'pending') {
            return redirect()->route('student.researches.index')
                ->with('error', 'Cannot delete research that is already approved or approved.');
        }

        foreach ($research->files as $f) {
            if ($f->file_path && \Storage::disk('public')->exists($f->file_path)) {
                \Storage::disk('public')->delete($f->file_path);
            }
            $f->delete();
        }

        if ($research->pdf_path && \Storage::disk('public')->exists($research->pdf_path)) {
            \Storage::disk('public')->delete($research->pdf_path);
        }

        $research->delete();

        return redirect()->route('student.researches.index')
            ->with('success', 'Research deleted successfully.');
    }
}
