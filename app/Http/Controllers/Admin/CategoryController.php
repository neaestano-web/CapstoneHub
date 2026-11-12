<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ResearchCategory;
use App\Models\ProjectType;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = ResearchCategory::withCount([
            'classifiedResearches as classified_researches_count' => fn ($researches) => $researches->where('status', 'approved'),
            'projectTypes',
        ])
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:research_categories,name',
            'description' => 'nullable|string|max:1000',
        ]);

        $category = ResearchCategory::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->has('project_types') && is_array($request->project_types)) {
            foreach ($request->project_types as $ptName) {
                if (!empty(trim($ptName))) {
                    $category->projectTypes()->create(['name' => trim($ptName)]);
                }
            }
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified category with its project types.
     */
    public function show(ResearchCategory $category)
    {
        $category->load('projectTypes');
        $researches = $category->classifiedResearches()
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.categories.show', compact('category', 'researches'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(ResearchCategory $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, ResearchCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:research_categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $categoryData = ['name' => $request->name];
        if ($request->has('description')) {
            $categoryData['description'] = $request->description;
        }

        $category->update($categoryData);

        // Delete project types if any
        if ($request->has('delete_project_types') && is_array($request->delete_project_types)) {
            $category->projectTypes()->whereIn('id', $request->delete_project_types)->delete();
        }

        // Update existing project types
        if ($request->has('existing_project_types') && is_array($request->existing_project_types)) {
            foreach ($request->existing_project_types as $ptId => $ptData) {
                if (isset($ptData['name']) && !empty(trim($ptData['name']))) {
                    // Make sure it belongs to this category before updating
                    $pt = $category->projectTypes()->find($ptId);
                    if ($pt) {
                        $pt->update(['name' => trim($ptData['name'])]);
                    }
                }
            }
        }

        // Add new project types
        if ($request->has('new_project_types') && is_array($request->new_project_types)) {
            foreach ($request->new_project_types as $ptName) {
                if (!empty(trim($ptName))) {
                    $category->projectTypes()->create(['name' => trim($ptName)]);
                }
            }
        }

        return redirect($request->input('redirect_to', route('admin.categories.index')))
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(ResearchCategory $category)
    {
        $researchesCount = $category->classifiedResearches()->count();

        if ($researchesCount > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', "Cannot delete category. It has {$researchesCount} associated researches.");
        }

        $catName = $category->name;
        $catId = $category->id;
        $category->delete();

        \App\Models\AuditLog::record('category.trashed', 'category', $catId, $catName);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category moved to Recycle Bin successfully.');
    }

    /**
     * Store a project type under a category.
     */
    public function storeProjectType(Request $request, ResearchCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $category->projectTypes()->create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return back()->with('success', "Project type \"{$request->name}\" added successfully.");
    }

    /**
     * Delete a project type.
     */
    public function destroyProjectType(ResearchCategory $category, ProjectType $projectType)
    {
        if ($projectType->research_category_id !== $category->id) {
            abort(403);
        }

        $name = $projectType->name;
        $projectType->delete();

        return back()->with('success', "Project type \"{$name}\" deleted.");
    }
}
