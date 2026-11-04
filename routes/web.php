<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ResearchController as AdminResearchController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PendingRegistrationController;
use App\Http\Controllers\Student\ResearchController;
use App\Http\Controllers\SimilarityController;
use App\Http\Controllers\SemanticSearchController;
use App\Http\Controllers\RepositoryController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\FileViewController;
use App\Http\Controllers\DocumentViewController;
use App\Http\Controllers\DownloadRequestController;
use App\Http\Controllers\Admin\DownloadRequestController as AdminDownloadRequestController;

Route::get('/', function () {
    session()->forget('current_viewed_research_id');
    $totalCount = \App\Models\Research::where('status', 'approved')->count();
    $categories = \App\Models\ResearchCategory::withCount(['researches' => function ($q) {
        $q->where('status', 'approved');
    }])
    ->orderByDesc('researches_count')
    ->orderBy('name')
    ->get();

    // Showcase active disciplines (those with approved research papers first)
    $disciplines = $categories->where('researches_count', '>', 0);
    if ($disciplines->count() < 4) {
        $extra = $categories->where('researches_count', 0)->take(4 - $disciplines->count());
        $disciplines = $disciplines->concat($extra);
    } else {
        $disciplines = $disciplines->take(6);
    }

    return view('welcome', compact('totalCount', 'categories', 'disciplines'));
});

Route::get('/search', [SearchController::class, 'index'])->name('search.index');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Registration status page for pending/rejected students
Route::middleware('auth')->get('/registration-status', function () {
    $user = auth()->user();

    // Must verify email OTP first!
    if (!$user->hasVerifiedEmail()) {
        return redirect()->route('verification.otp.show');
    }

    // Only show this page to non-approved students
    if ($user->role !== 'student' || $user->isApproved()) {
        return redirect()->route('dashboard');
    }

    return view('auth.registration-status', ['user' => $user]);
})->name('registration.status');

// Student Researcher Routes
Route::middleware(['auth', 'registration_approved'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [ResearchController::class, 'dashboard'])->name('dashboard');
    Route::post('/researches/classify', [ResearchController::class, 'classify'])->name('researches.classify');
    Route::post('/researches/check-exists', [ResearchController::class, 'checkExists'])->name('researches.check-exists');
    Route::resource('researches', ResearchController::class);
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', CategoryController::class);
    Route::post('/categories/{category}/project-types', [CategoryController::class, 'storeProjectType'])->name('categories.projectTypes.store');
    Route::delete('/categories/{category}/project-types/{projectType}', [CategoryController::class, 'destroyProjectType'])->name('categories.projectTypes.destroy');

    Route::get('/researches', [AdminResearchController::class, 'manageIndex'])->name('researches.index');
    Route::get('/researches/create', [AdminResearchController::class, 'create'])->name('researches.create');
    Route::post('/researches/classify', [AdminResearchController::class, 'classify'])->name('researches.classify');
    Route::post('/researches/check-exists', [AdminResearchController::class, 'checkExists'])->name('researches.check-exists');
    Route::post('/researches', [AdminResearchController::class, 'store'])->name('researches.store');
    Route::get('/researches/{research}', [AdminResearchController::class, 'show'])->name('researches.show');
    Route::patch('/researches/{research}/approve', [AdminResearchController::class, 'approve'])->name('researches.approve');
    Route::patch('/researches/{research}/reject', [AdminResearchController::class, 'reject'])->name('researches.reject');
    Route::patch('/researches/{research}/category', [AdminResearchController::class, 'updateCategory'])->name('researches.updateCategory');
    Route::post('/researches/{research}/update-files', [AdminResearchController::class, 'updateFiles'])->name('researches.updateFiles');
    Route::delete('/researches/{research}', [AdminResearchController::class, 'destroy'])->name('researches.destroy');

    Route::resource('users', UserController::class);

    // Pending registrations review
    Route::get('/pending-registrations', [PendingRegistrationController::class, 'index'])->name('pending-registrations.index');
    Route::get('/pending-registrations/{user}', [PendingRegistrationController::class, 'show'])->name('pending-registrations.show');
    Route::get('/pending-registrations/{user}/id-picture/{side}', [PendingRegistrationController::class, 'idPicture'])->name('pending-registrations.id-picture');
    Route::patch('/pending-registrations/{user}/approve', [PendingRegistrationController::class, 'approve'])->name('pending-registrations.approve');
    Route::patch('/pending-registrations/{user}/reject', [PendingRegistrationController::class, 'reject'])->name('pending-registrations.reject');
    Route::delete('/pending-registrations/{user}', [PendingRegistrationController::class, 'destroy'])->name('pending-registrations.destroy');

    // Download Requests Management
    Route::get('/download-requests', [AdminDownloadRequestController::class, 'index'])->name('download-requests.index');
    Route::patch('/download-requests/{downloadRequest}/approve', [AdminDownloadRequestController::class, 'approve'])->name('download-requests.approve');
    Route::patch('/download-requests/{downloadRequest}/reject', [AdminDownloadRequestController::class, 'reject'])->name('download-requests.reject');

});

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user) {
        // Must verify email OTP first!
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.otp.show');
        }

        // Check if student registration is not approved
        if ($user->role === 'student' && !$user->isApproved()) {
            return redirect()->route('registration.status');
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'student') {
            return redirect()->route('student.dashboard');
        }
    }
    return redirect('/');
})->middleware('auth')->name('dashboard');

Route::redirect('/title-checker', '/similarity-checker');

Route::get('/similarity-checker', [SimilarityController::class, 'index'])->name('similarity-checker');
Route::post('/similarity-checker', [SimilarityController::class, 'check'])->name('similarity-checker.check');
Route::get('/ai-warmup', [SimilarityController::class, 'warmup'])->name('ai.warmup');

Route::middleware('auth')->group(function () {
    Route::get('/semantic-search', [SemanticSearchController::class, 'index'])->name('semantic-search');
    Route::post('/semantic-search', [SemanticSearchController::class, 'search'])->name('semantic-search.search');
});

Route::get('/repository', [RepositoryController::class, 'index'])->name('repository.index');
Route::get('/browse', [RepositoryController::class, 'index'])->name('browse');
Route::redirect('/repository/search', '/repository');
Route::get('/repository/{research}', [RepositoryController::class, 'show'])->name('repository.show');

// Public Manuscript Preview (Page 1 only of first uploaded file for guests)
Route::get('/repository/{research}/preview', [DocumentViewController::class, 'preview'])->name('document.preview');

// Manuscript viewing and downloading routes (Protected: requires active auth and approved registration)
Route::middleware(['auth', 'registration_approved'])->group(function () {
    Route::get('/repository/{research}/full', [RepositoryController::class, 'show'])->name('repository.full');
    Route::post('/repository/{research}/request-download', [DownloadRequestController::class, 'store'])->name('repository.request-download');

    // Serve research files inline (PDF opens in browser, DOCX downloads)
    Route::get('/files/researches/{path}', [FileViewController::class, 'show'])->where('path', '.*')->name('files.show');

    // Document viewer page (converts DOCX to HTML for viewing)
    Route::get('/view-document/{research}', [App\Http\Controllers\Admin\ResearchController::class, 'viewDocument'])->name('document.view');

    // View document as inline PDF (converts DOCX to PDF on-demand)
    Route::get('/repository/{research}/pdf', [DocumentViewController::class, 'show'])->name('document.pdf');

    // Download complete manuscript as single PDF (all files merged sequentially)
    Route::get('/repository/{research}/download', [DocumentViewController::class, 'download'])->name('repository.download');
});

require __DIR__.'/auth.php';
