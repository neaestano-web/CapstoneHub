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
     * AJAX: Check if a research manuscript with a matching/similar title already exists.
     */
    public function checkExists(Request $request)
    {
        $title = $request->input('title', '');
        $existing = Research::findExistingByTitle($title);

        if ($existing) {
            return response()->json([
                'exists' => true,
                'research' => [
                    'id' => $existing->id,
                    'title' => $existing->title,
                    'authors' => $existing->authors ?? 'Unknown Authors',
                    'year' => $existing->year,
                    'status' => $existing->status,
                ],
            ]);
        }

        return response()->json(['exists' => false]);
    }

    /**
     * Store a newly created research (admin upload).
     */
    public function store(Request $request, AIService $ai)
    {
        try {
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
                'files.*'            => 'file|mimes:pdf,docx,zip|extensions:pdf,docx|max:30720',
                'pdf'                => 'nullable|file|mimes:pdf,docx,zip|extensions:pdf,docx|max:30720',
            ], [
                'files.*.uploaded'   => 'The document could not be uploaded. It may exceed the 30MB file upload limit or was rejected by the server. Try compressing or selecting a smaller file.',
                'files.*.max'        => 'File is too large. Maximum size is 30MB per file.',
                'files.*.mimes'      => 'Only PDF (.pdf) or Word (.docx) files are supported.',
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

            // Check if an existing research with the same or matching title already exists
            $existing = Research::findExistingByTitle($request->title);

            $categoryIds = array_values(array_unique($request->category_ids));
            $primaryCategoryId = $categoryIds[0] ?? null;
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
            $research->categories()->sync($categoryIds);
            if ($request->project_type_ids) {
                $research->projectTypes()->sync(array_values(array_unique($request->project_type_ids)));
            }

            // Generate embeddings if publishing immediately (fail-fast: does not block or fail upload if AI is cold or offline)
            if ($shouldPublish) {
                try {
                    @set_time_limit(120);
                    $titleEmbedding = $ai->generateEmbedding($research->title);
                    if (!empty($titleEmbedding['embedding'])) {
                        $content = $research->title . ' ' . ($research->project_context ? $research->project_context . ' ' : '') . $research->objectives . ' ' . $research->abstract;
                        $contentEmbedding = $ai->generateEmbedding($content);
                        $objectiveEmbedding = $ai->generateEmbedding($research->objectives);
                        $contextEmbedding = !empty($research->project_context) ? $ai->generateEmbedding($research->project_context) : null;

                        ResearchEmbedding::updateOrCreate(
                            ['research_id' => $research->id],
                            [
                                'title_embedding'     => !empty($titleEmbedding['embedding']) ? json_encode($titleEmbedding['embedding']) : null,
                                'content_embedding'   => !empty($contentEmbedding['embedding']) ? json_encode($contentEmbedding['embedding']) : null,
                                'objective_embedding' => !empty($objectiveEmbedding['embedding']) ? json_encode($objectiveEmbedding['embedding']) : null,
                                'context_embedding'   => !empty($contextEmbedding['embedding']) ? json_encode($contextEmbedding['embedding']) : null,
                            ]
                        );
                    }
                } catch (\Throwable $e) {
                    \Log::warning('AI embedding skipped during admin upload: ' . $e->getMessage());
                }
            }

            $catNames = \App\Models\ResearchCategory::whereIn('id', $categoryIds)->pluck('name')->implode(', ');
            $fileCount = count($uploadedFiles);

            // If title already existed in repository, attach warning alert to the session
            if ($existing) {
                session()->flash('warning', "⚠️ Note: The uploaded and published capstone has a title similar to an existing record in the repository: \"{$existing->title}\" ({$existing->year}).");
            }

            return redirect()->route('admin.researches.index')
                ->with('success', "Capstone uploaded and published successfully with {$fileCount} file(s)! Classified under: {$catNames}.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error('Admin research upload failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    public function approve(
        Research $research,
        AIService $ai
    )
    {
        try {
            @set_time_limit(120);
            $titleEmbedding = $ai->generateEmbedding($research->title);

            if (!empty($titleEmbedding['embedding'])) {
                $content = $research->title . ' ' .
                    ($research->project_context ? $research->project_context . ' ' : '') .
                    $research->objectives . ' ' .
                    $research->abstract;

                $contentEmbedding = $ai->generateEmbedding($content);
                $objectiveEmbedding = $ai->generateEmbedding($research->objectives);
                $contextEmbedding = !empty($research->project_context)
                    ? $ai->generateEmbedding($research->project_context)
                    : null;

                ResearchEmbedding::updateOrCreate(
                    ['research_id' => $research->id],
                    [
                        'title_embedding'     => !empty($titleEmbedding['embedding']) ? json_encode($titleEmbedding['embedding']) : null,
                        'content_embedding'   => !empty($contentEmbedding['embedding']) ? json_encode($contentEmbedding['embedding']) : null,
                        'objective_embedding' => !empty($objectiveEmbedding['embedding']) ? json_encode($objectiveEmbedding['embedding']) : null,
                        'context_embedding'   => !empty($contextEmbedding['embedding']) ? json_encode($contextEmbedding['embedding']) : null,
                    ]
                );
            }
        } catch (\Throwable $e) {
            \Log::warning('AI embedding skipped during approve: ' . $e->getMessage());
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
     * Update project cards details (Project Context, Objective, Abstract, Keywords).
     */
    public function updateDetails(Request $request, Research $research, AIService $ai)
    {
        $request->validate([
            'project_context' => 'nullable|string',
            'objectives'      => 'nullable|string',
            'abstract'        => 'nullable|string',
            'keywords'        => 'nullable|string',
        ]);

        $dataToUpdate = [];
        if ($request->has('project_context')) {
            $dataToUpdate['project_context'] = $request->input('project_context');
        }
        if ($request->has('objectives')) {
            $dataToUpdate['objectives'] = $request->input('objectives');
        }
        if ($request->has('abstract')) {
            $dataToUpdate['abstract'] = $request->input('abstract');
        }
        if ($request->has('keywords')) {
            $dataToUpdate['keywords'] = $request->input('keywords');
        }

        if (!empty($dataToUpdate)) {
            $research->update($dataToUpdate);

            // Re-sync embeddings so semantic search stays accurate
            try {
                if ($ai) {
                    $content = $research->title . ' ' . ($research->project_context ? $research->project_context . ' ' : '') . $research->objectives . ' ' . $research->abstract;
                    $contentEmbedding = $ai->generateEmbedding($content);
                    $objectiveEmbedding = $ai->generateEmbedding($research->objectives);
                    $contextEmbedding = !empty($research->project_context) ? $ai->generateEmbedding($research->project_context) : null;

                    ResearchEmbedding::updateOrCreate(
                        ['research_id' => $research->id],
                        [
                            'content_embedding'   => !empty($contentEmbedding['embedding']) ? json_encode($contentEmbedding['embedding']) : null,
                            'objective_embedding' => !empty($objectiveEmbedding['embedding']) ? json_encode($objectiveEmbedding['embedding']) : null,
                            'context_embedding'   => !empty($contextEmbedding['embedding']) ? json_encode($contextEmbedding['embedding']) : null,
                        ]
                    );
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('AI Embedding update on details edit failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Project details updated successfully!');
    }

    /**
     * Re-attach / Update the manuscript file(s) for an existing research record without creating duplicates.
     */
    public function updateFiles(Request $request, Research $research)
    {
        $request->validate([
            'files'   => 'nullable|array|min:1',
            'files.*' => 'file|mimes:pdf,docx,zip|extensions:pdf,docx|max:30720',
            'pdf'     => 'nullable|file|mimes:pdf,docx,zip|extensions:pdf,docx|max:30720',
        ], [
            'files.*.uploaded' => 'The document could not be uploaded. It may exceed the 30MB file upload limit or was rejected by the server. Try compressing or selecting a smaller file.',
            'files.*.max'      => 'File is too large. Maximum size is 30MB per file.',
            'files.*.mimes'    => 'Only PDF (.pdf) or Word (.docx) files are supported.',
        ]);

        $uploadedFiles = [];
        if ($request->hasFile('files')) {
            $uploadedFiles = (array) $request->file('files');
        } elseif ($request->hasFile('pdf')) {
            $uploadedFiles = [$request->file('pdf')];
        }

        if (empty($uploadedFiles)) {
            return back()->with('error', 'Please select at least one PDF or Word file to attach.');
        }

        // 1. Delete old physical files and related cache
        foreach ($research->files as $f) {
            if ($f->file_path && \Storage::disk('public')->exists($f->file_path)) {
                \Storage::disk('public')->delete($f->file_path);
            }
            $cachedPdf = 'researches/pdf_cache/' . pathinfo($f->file_path, PATHINFO_FILENAME) . '.pdf';
            if (\Storage::disk('public')->exists($cachedPdf)) {
                \Storage::disk('public')->delete($cachedPdf);
            }
            $f->delete();
        }

        if ($research->pdf_path && \Storage::disk('public')->exists($research->pdf_path)) {
            \Storage::disk('public')->delete($research->pdf_path);
        }

        // Clean any merged or preview cached PDFs for this research
        $pdfCacheFiles = \Storage::disk('public')->files('researches/pdf_cache');
        foreach ($pdfCacheFiles as $cf) {
            if (str_contains($cf, "page1_preview_{$research->id}_") || str_contains($cf, "merged_{$research->id}_")) {
                \Storage::disk('public')->delete($cf);
            }
        }

        // 2. Store newly uploaded files
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

        // 3. Update research primary pdf_path
        $research->update([
            'pdf_path' => $firstPath,
        ]);

        $count = count($uploadedFiles);
        return back()->with(
            'success',
            "Successfully attached and updated {$count} manuscript file(s) for \"{$research->title}\" without duplicating database records!"
        );
    }

    /**
     * Re-order existing attached files for an already uploaded research.
     */
    public function reorderFiles(Request $request, Research $research)
    {
        $request->validate([
            'order' => 'required|array|min:1',
            'order.*' => 'exists:research_files,id',
        ]);

        $firstPath = null;
        foreach ($request->order as $newIndex => $fileId) {
            $file = $research->files()->where('id', $fileId)->first();
            if ($file) {
                $file->update(['sort_order' => $newIndex + 1]);
                if ($newIndex === 0) {
                    $firstPath = $file->file_path;
                }
            }
        }

        if ($firstPath) {
            $research->update(['pdf_path' => $firstPath]);
        }

        // Clean cached merged and preview PDFs so new order takes effect immediately
        $pdfCacheFiles = \Storage::disk('public')->files('researches/pdf_cache');
        foreach ($pdfCacheFiles as $cf) {
            if (str_contains($cf, "page1_preview_{$research->id}_") || str_contains($cf, "merged_{$research->id}_")) {
                \Storage::disk('public')->delete($cf);
            }
        }

        return back()->with('success', 'Manuscript file sequence updated successfully!');
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
