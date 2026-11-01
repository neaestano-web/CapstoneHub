<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Research;
use App\Models\ResearchEmbedding;
use App\Services\AI\AIService;

class ResearchController extends Controller
{
    /**
     * Show the form for creating a new research (admin upload).
     */
    public function create()
    {
        $categories = \App\Models\ResearchCategory::with('projectTypes')->orderBy('id', 'asc')->get();
        return view('admin.researches.create', compact('categories'));
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

        $categories = \App\Models\ResearchCategory::with('projectTypes')->orderBy('id', 'asc')->get();
        $categoryNames = $categories->pluck('name')->toArray();

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
     * Store a newly created research (admin upload).
     */
    public function store(Request $request, AIService $ai)
    {
        $request->validate([
            'title'              => 'required|string|max:255',
            'abstract'           => 'required|string',
            'project_context'    => 'required|string',
            'objectives'         => 'required|string',
            'keywords'           => 'nullable|string',
            'authors'            => 'required',
            'year'               => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'category_ids'       => 'required|array|min:1',
            'category_ids.*'     => 'exists:research_categories,id',
            'project_type_ids'   => 'nullable|array',
            'project_type_ids.*' => 'exists:project_types,id',
            'files'              => 'nullable|array|min:1',
            'files.*'            => 'mimes:pdf,docx|max:15360',
            'pdf'                => 'nullable|mimes:pdf,docx|max:15360',
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

        $primaryCategoryId = $request->category_ids[0] ?? null;
        // Admin uploads are published directly to the repository.
        $shouldPublish = true;

        $research = Research::create([
            'user_id'              => auth()->id(),
            'research_category_id' => $primaryCategoryId,
            'title'                => $request->title,
            'abstract'             => $request->abstract,
            'project_context'      => $request->project_context,
            'objectives'           => $request->objectives,
            'keywords'             => $request->keywords,
            'authors'              => $authorsString,
            'year'                 => $request->year,
            'pdf_path'             => '',
            'status'               => $shouldPublish ? 'approved' : 'pending',
            'approved_at'         => $shouldPublish ? now() : null,
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

        // Generate embeddings if publishing immediately
        if ($shouldPublish) {
            try {
                $titleEmbedding = $ai->generateEmbedding($research->title);
                $content = $research->title . ' ' . ($research->project_context ? $research->project_context . ' ' : '') . $research->objectives . ' ' . $research->abstract;
                $contentEmbedding = $ai->generateEmbedding($content);
                $objectiveEmbedding = $ai->generateEmbedding($research->objectives);
                $contextEmbedding = !empty($research->project_context) ? $ai->generateEmbedding($research->project_context) : null;

                ResearchEmbedding::create([
                    'research_id'         => $research->id,
                    'title_embedding'     => json_encode($titleEmbedding['embedding']),
                    'content_embedding'   => json_encode($contentEmbedding['embedding']),
                    'objective_embedding' => json_encode($objectiveEmbedding['embedding']),
                    'context_embedding'   => $contextEmbedding ? json_encode($contextEmbedding['embedding']) : null,
                ]);
            } catch (\Exception $e) {
                // AI service unavailable — semantic search won't work for this item
            }
        }

        $catNames = \App\Models\ResearchCategory::whereIn('id', $request->category_ids)->pluck('name')->implode(', ');
        $fileCount = count($uploadedFiles);

        return redirect()->route('admin.researches.index')
            ->with('success', "Capstone uploaded and published successfully with {$fileCount} file(s)! Classified under: {$catNames}.");
    }

    public function approve(
        Research $research,
        AIService $ai
    )
    {
        try {
            $titleEmbedding =
                $ai->generateEmbedding(
                    $research->title
                );

            $content =
                $research->title . ' ' .
                ($research->project_context ? $research->project_context . ' ' : '') .
                $research->objectives . ' ' .
                $research->abstract;

            $contentEmbedding =
                $ai->generateEmbedding(
                    $content
                );

            $objectiveEmbedding =
                $ai->generateEmbedding(
                    $research->objectives
                );

            $contextEmbedding = !empty($research->project_context)
                ? $ai->generateEmbedding($research->project_context)
                : null;

            ResearchEmbedding::create([

                'research_id' => $research->id,

                'title_embedding' =>
                    json_encode(
                        $titleEmbedding['embedding']
                    ),

                'content_embedding' =>
                    json_encode(
                        $contentEmbedding['embedding']
                    ),

                'objective_embedding' =>
                    json_encode(
                        $objectiveEmbedding['embedding']
                    ),

                'context_embedding' =>
                    $contextEmbedding ? json_encode($contextEmbedding['embedding']) : null
            ]);
        } catch (\Exception $e) {
            // AI service is not available, continue without embeddings
            // Semantic search will not work for this research
        }

        $research->update([

            'status' => 'approved',

            'approved_at' => now()

        ]);

        return back()->with(
            'success',
            'Research approved successfully.'
        );
    }

    /**
     * Update the category and project types of the specified research.
     */
    public function updateCategory(Request $request, Research $research)
    {
        $request->validate([
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:research_categories,id',
            'project_type_ids' => 'nullable|array',
            'project_type_ids.*' => 'exists:project_types,id',
        ]);

        $research->update([
            'research_category_id' => $request->category_ids[0] ?? null,
        ]);

        $research->categories()->sync($request->category_ids);
        $research->projectTypes()->sync($request->project_type_ids ?? []);

        return back()->with(
            'success',
            'Categories and project types updated successfully.'
        );
    }

    /**
     * Reject the specified research.
     */
    public function reject(Request $request, Research $research)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $research->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with(
            'success',
            'Research rejected successfully.'
        );
    }

    /**
     * Display the specified research details and PDF preview.
     */
    public function show(Research $research)
    {
        $research->load(['categories', 'projectTypes', 'files', 'user']);
        $categories = \App\Models\ResearchCategory::with('projectTypes')->orderBy('id', 'asc')->get();
        $allProjectTypes = \App\Models\ProjectType::with('category')->get();
        return view('admin.researches.show', compact('research', 'categories', 'allProjectTypes'));
    }

    /**
     * Display document viewer page for PDF/DOCX files.
     */
    public function viewDocument(Research $research)
    {
        $fileUrl = route('files.show', ['path' => $research->pdf_path]);
        $extension = pathinfo($research->pdf_path, PATHINFO_EXTENSION);
        
        return view('admin.researches.file-viewer', compact('research', 'fileUrl', 'extension'));
    }

    /**
     * Display a listing of all researches for admin management.
     */
    public function manageIndex(Request $request)
    {
        $status = $request->input('status');
        $search = trim($request->input('search', ''));

        // Only apply recognized workflow statuses from the management filters.
        if (! in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $status = null;
        }

        $researches = Research::with('category')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('authors', 'like', "%{$search}%")
                        ->orWhere('keywords', 'like', "%{$search}%")
                        ->orWhereHas('categories', fn ($categories) => $categories->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'all' => Research::count(),
            'pending' => Research::where('status', 'pending')->count(),
            'approved' => Research::where('status', 'approved')->count(),
            'rejected' => Research::where('status', 'rejected')->count(),
        ];

        return view('admin.researches.index', compact('researches', 'counts'));
    }

    /**
     * Remove the specified research from storage.
     */
    public function destroy(Research $research)
    {
        foreach ($research->files as $f) {
            if ($f->file_path && \Storage::disk('public')->exists($f->file_path)) {
                \Storage::disk('public')->delete($f->file_path);
            }
            $f->delete();
        }

        // Delete PDF file
        if ($research->pdf_path && \Storage::disk('public')->exists($research->pdf_path)) {
            \Storage::disk('public')->delete($research->pdf_path);
        }

        // Delete embedding if exists
        if ($research->embedding) {
            $research->embedding->delete();
        }

        if (session('current_viewed_research_id') == $research->id) {
            session()->forget('current_viewed_research_id');
        }

        $research->delete();

        return redirect()->route('admin.researches.index')->with('success', 'Research deleted successfully.');
    }
}
