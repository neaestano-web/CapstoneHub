<?php

namespace App\Http\Controllers;

use App\Models\DownloadRequest;
use App\Models\Research;
use App\Services\AI\AIService;
use Illuminate\Support\Facades\Storage;

class DocumentViewController extends Controller
{
    /**
     * View a research document as PDF inline in the browser.
     *
     * If multiple files exist: combines them in sequence (first uploaded file is first).
     * If specific file requested via ?file_id=X: serves that specific file.
     * For PDF files: serves directly.
     * For DOCX files: converts to PDF via the AI service (cached), then serves.
     */
    public function show(Research $research, AIService $ai)
    {
        if (request()->has('download')) {
            return $this->download($research, $ai);
        }

        $files = $research->files()->orderBy('sort_order', 'asc')->get();

        // If specific file requested via ?file_id=X
        $fileId = request()->query('file_id');
        if ($fileId && $specificFile = $files->firstWhere('id', $fileId)) {
            return $this->serveSingleFile($research, $specificFile->file_path, $ai, $specificFile->original_name);
        }

        // If multiple files exist, merge them sequentially (first uploaded file is first)
        if ($files->count() > 1) {
            $pdfData = $this->getMergedOrConvertedPdf($research, $ai);
            if ($pdfData) {
                return response($pdfData['content'], 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $pdfData['filename'] . '"',
                    'Content-Length' => strlen($pdfData['content']),
                ]);
            }
        }

        // Single file or fallback: use first file or legacy research->pdf_path
        $primaryPath = $files->first()?->file_path ?? $research->pdf_path;
        if (!$primaryPath || !Storage::disk('public')->exists($primaryPath)) {
            abort(404, 'Document not found.');
        }

        return $this->serveSingleFile($research, $primaryPath, $ai);
    }

    /**
     * Download the research manuscript as a single PDF containing all uploaded files.
     */
    public function download(Research $research, AIService $ai)
    {
        if (!$this->canDownload($research)) {
            return redirect()->route('repository.show', $research)
                ->with('error', 'Download permission required. Please request access from Admin.');
        }

        $pdfData = $this->getMergedOrConvertedPdf($research, $ai);

        if (!$pdfData) {
            return redirect()->route('repository.show', $research)
                ->with('error', 'Unable to prepare download file. Please try again later.');
        }

        return response($pdfData['content'], 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $pdfData['filename'] . '"',
            'Content-Length' => strlen($pdfData['content']),
        ]);
    }

    /**
     * Check if the authenticated user has permission to download this research paper.
     */
    public function canDownload(Research $research): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        // Admin can always download
        if ($user->role === 'admin') {
            return true;
        }

        // The student author/uploader can always download their own paper
        if ($research->user_id && (int)$research->user_id === (int)$user->id) {
            return true;
        }

        // Check approved download request
        return DownloadRequest::where('user_id', $user->id)
            ->where('research_id', $research->id)
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Get or generate the single merged PDF file content (and safe filename) containing all files.
     */
    public function getMergedOrConvertedPdf(Research $research, AIService $ai): ?array
    {
        $files = $research->files()->orderBy('sort_order', 'asc')->get();
        $safeTitle = preg_replace('/[^\w\s\-\.\(\)]/u', '_', $research->title);

        // 1. Multiple files: merge into single PDF in sequential order
        if ($files->count() > 1) {
            $inputPaths = [];
            foreach ($files as $f) {
                if (Storage::disk('public')->exists($f->file_path)) {
                    $inputPaths[] = Storage::disk('public')->path($f->file_path);
                }
            }

            if (!empty($inputPaths)) {
                $hash = substr(md5(implode('|', $inputPaths)), 0, 10);
                $mergedRelativePath = 'researches/pdf_cache/merged_' . $research->id . '_' . $hash . '.pdf';
                $absoluteOutputPath = Storage::disk('public')->path($mergedRelativePath);

                if (!Storage::disk('public')->exists($mergedRelativePath)) {
                    try {
                        $ai->mergeDocuments($inputPaths, $absoluteOutputPath);
                    } catch (\Exception $e) {
                        // fallback below
                    }
                }

                if (Storage::disk('public')->exists($mergedRelativePath)) {
                    $fileContent = Storage::disk('public')->get($mergedRelativePath);
                    return [
                        'content' => $fileContent,
                        'filename' => $safeTitle . ' (Complete Combined Manuscript).pdf',
                    ];
                }
            }
        }

        // 2. Single file: PDF or converted DOCX
        $primaryPath = $files->first()?->file_path ?? $research->pdf_path;
        if (!$primaryPath || !Storage::disk('public')->exists($primaryPath)) {
            return null;
        }

        $extension = strtolower(pathinfo($primaryPath, PATHINFO_EXTENSION));

        if ($extension === 'pdf') {
            return [
                'content' => Storage::disk('public')->get($primaryPath),
                'filename' => $safeTitle . '.pdf',
            ];
        }

        // DOCX conversion to PDF
        $pdfRelativePath = 'researches/pdf_cache/' . pathinfo($primaryPath, PATHINFO_FILENAME) . '.pdf';
        $absolutePdfPath = Storage::disk('public')->path($pdfRelativePath);
        $absoluteDocxPath = Storage::disk('public')->path($primaryPath);

        if (!Storage::disk('public')->exists($pdfRelativePath)) {
            try {
                $converted = $ai->convertDocxToPdf($absoluteDocxPath, $absolutePdfPath);
                if (!$converted) {
                    return null;
                }
            } catch (\Exception $e) {
                return null;
            }
        }

        if (Storage::disk('public')->exists($pdfRelativePath)) {
            return [
                'content' => Storage::disk('public')->get($pdfRelativePath),
                'filename' => $safeTitle . '.pdf',
            ];
        }

        return null;
    }

    /**
     * Serve a single file inline as PDF (converting DOCX if needed).
     */
    private function serveSingleFile(Research $research, string $path, AIService $ai, ?string $originalName = null)
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $absoluteDocxPath = Storage::disk('public')->path($path);
        $displayName = $originalName ?: ($research->title . '.' . $extension);

        if ($extension === 'pdf') {
            $file = Storage::disk('public')->get($path);
            return response($file, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $displayName . '"',
                'Content-Length' => strlen($file),
            ]);
        }

        // For DOCX files, convert to PDF and cache
        $pdfRelativePath = 'researches/pdf_cache/' . pathinfo($path, PATHINFO_FILENAME) . '.pdf';
        $absolutePdfPath = Storage::disk('public')->path($pdfRelativePath);

        if (!Storage::disk('public')->exists($pdfRelativePath)) {
            try {
                $converted = $ai->convertDocxToPdf($absoluteDocxPath, $absolutePdfPath);
                if (!$converted) {
                    return $this->serveFallback($research);
                }
            } catch (\Exception $e) {
                return $this->serveFallback($research);
            }
        }

        $file = Storage::disk('public')->get($pdfRelativePath);
        return response($file, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . pathinfo($displayName, PATHINFO_FILENAME) . '.pdf"',
            'Content-Length' => strlen($file),
        ]);
    }

    /**
     * Fallback: redirect to the original file if PDF conversion fails.
     */
    private function serveFallback(Research $research)
    {
        return redirect()->route('document.view', $research)
            ->with('error', 'PDF conversion is temporarily unavailable. Showing document viewer instead.');
    }
}
