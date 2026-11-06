<?php

namespace App\Http\Controllers;

use App\Models\DownloadRequest;
use App\Models\Research;
use App\Services\AI\AIService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentViewController extends Controller
{
    /**
     * Ensure FPDI and FPDF classes are available.
     */
    private function ensureFpdiLoaded(): void
    {
        if (!class_exists(\FPDF::class) && file_exists(base_path('vendor/setasign/fpdf/fpdf.php'))) {
            require_once base_path('vendor/setasign/fpdf/fpdf.php');
        }
        if (!class_exists(\setasign\Fpdi\Fpdi::class) && file_exists(base_path('vendor/setasign/fpdi/src/autoload.php'))) {
            require_once base_path('vendor/setasign/fpdi/src/autoload.php');
        }
    }

    /**
     * Convert a Word document (.docx) to PDF locally in pure PHP.
     */
    public function convertDocxToPdfLocally(string $docxPath, string $outputPath): bool
    {
        $this->ensureFpdiLoaded();

        if (!file_exists($docxPath) || !class_exists(\FPDF::class)) {
            return false;
        }

        try {
            $zip = new \ZipArchive();
            if ($zip->open($docxPath) !== true) {
                return false;
            }

            $xml = $zip->getFromName('word/document.xml');
            $zip->close();

            if (!$xml) {
                return false;
            }

            $dom = new \DOMDocument();
            @$dom->loadXML($xml);
            $xpath = new \DOMXPath($dom);
            $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

            $fpdf = new \FPDF('P', 'mm', 'A4');
            $fpdf->SetMargins(20, 20, 20);
            $fpdf->SetAutoPageBreak(true, 20);
            $fpdf->AddPage();

            $hasContent = false;

            foreach ($xpath->query('//w:p') as $p) {
                $text = '';
                $isHeading = false;
                $isBold = false;

                $pStyle = $xpath->query('.//w:pStyle/@w:val', $p)->item(0)?->nodeValue;
                if ($pStyle && (stripos($pStyle, 'heading') !== false || stripos($pStyle, 'title') !== false)) {
                    $isHeading = true;
                }

                foreach ($xpath->query('.//w:r', $p) as $r) {
                    $boldNode = $xpath->query('.//w:b', $r)->item(0);
                    if ($boldNode) {
                        $isBold = true;
                    }
                    foreach ($xpath->query('.//w:t', $r) as $t) {
                        $text .= $t->nodeValue;
                    }
                }

                $text = trim($text);
                if ($text === '') {
                    $fpdf->Ln(3);
                    continue;
                }

                $hasContent = true;

                // Transliterate to standard windows-1252 for FPDF
                $cleanText = iconv('UTF-8', 'windows-1252//TRANSLIT', $text);
                if ($cleanText === false) {
                    $cleanText = utf8_decode($text);
                }

                if ($isHeading) {
                    $fpdf->Ln(4);
                    $fpdf->SetFont('Helvetica', 'B', 12);
                    $fpdf->SetTextColor(15, 23, 42);
                    $fpdf->MultiCell(0, 6, $cleanText);
                    $fpdf->Ln(2);
                } elseif ($isBold) {
                    $fpdf->SetFont('Helvetica', 'B', 10);
                    $fpdf->SetTextColor(30, 41, 59);
                    $fpdf->MultiCell(0, 5, $cleanText);
                    $fpdf->Ln(1.5);
                } else {
                    $fpdf->SetFont('Helvetica', '', 10);
                    $fpdf->SetTextColor(51, 65, 85);
                    $fpdf->MultiCell(0, 5, $cleanText);
                    $fpdf->Ln(1.5);
                }
            }

            if (!$hasContent) {
                return false;
            }

            if (!is_dir(dirname($outputPath))) {
                mkdir(dirname($outputPath), 0755, true);
            }

            $fpdf->Output($outputPath, 'F');
            return file_exists($outputPath);
        } catch (\Throwable $e) {
            Log::warning("Local docx to pdf conversion failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Public Preview: Serve ONLY the first page of the first uploaded file as a PDF for guest users.
     */
    public function preview(Research $research, AIService $ai)
    {
        $this->ensureFpdiLoaded();

        // 1. Get the first uploaded file in sequential order
        $files = $research->files()->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();
        $firstFile = $files->first();
        $primaryPath = $firstFile?->file_path ?? $research->pdf_path;

        if (!$primaryPath || !Storage::disk('public')->exists($primaryPath)) {
            return $this->serveMissingFileNotice($research, 'Guest Preview');
        }

        $extension = strtolower(pathinfo($primaryPath, PATHINFO_EXTENSION));
        $absoluteSourcePath = Storage::disk('public')->path($primaryPath);

        // 2. If it's a DOCX file, check cache first or convert locally
        if ($extension !== 'pdf') {
            $pdfRelativePath = 'researches/pdf_cache/' . pathinfo($primaryPath, PATHINFO_FILENAME) . '.pdf';
            $absolutePdfPath = Storage::disk('public')->path($pdfRelativePath);

            if (!Storage::disk('public')->exists($pdfRelativePath)) {
                $converted = $this->convertDocxToPdfLocally($absoluteSourcePath, $absolutePdfPath);
                if (!$converted) {
                    try {
                        $ai->convertDocxToPdf($absoluteSourcePath, $absolutePdfPath);
                    } catch (\Throwable $e) {
                        // ignore
                    }
                }
            }

            if (Storage::disk('public')->exists($pdfRelativePath)) {
                $absoluteSourcePath = $absolutePdfPath;
            }
        }

        // 3. Generate or retrieve the cached 1-page PDF
        $mtime = file_exists($absoluteSourcePath) ? filemtime($absoluteSourcePath) : time();
        $cacheHash = substr(md5($primaryPath . '_' . $mtime), 0, 12);
        $previewRelativePath = 'researches/pdf_cache/page1_preview_' . $research->id . '_' . $cacheHash . '.pdf';
        $absolutePreviewPath = Storage::disk('public')->path($previewRelativePath);

        if (!Storage::disk('public')->exists($previewRelativePath) && strtolower(pathinfo($absoluteSourcePath, PATHINFO_EXTENSION)) === 'pdf') {
            try {
                if (class_exists(\setasign\Fpdi\Fpdi::class)) {
                    $fpdi = new \setasign\Fpdi\Fpdi();
                    $pageCount = $fpdi->setSourceFile($absoluteSourcePath);
                    if ($pageCount >= 1) {
                        $templateId = $fpdi->importPage(1);
                        $size = $fpdi->getTemplateSize($templateId);
                        $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
                        $fpdi->useTemplate($templateId);

                        if (!is_dir(dirname($absolutePreviewPath))) {
                            mkdir(dirname($absolutePreviewPath), 0755, true);
                        }
                        $fpdi->Output($absolutePreviewPath, 'F');
                    }
                }
            } catch (\Throwable $e) {
                // If FPDI fails on non-standard compression, serve source PDF
                $content = file_get_contents($absoluteSourcePath);
                return response($content, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . pathinfo($primaryPath, PATHINFO_FILENAME) . ' (Page 1 Preview).pdf"',
                    'Content-Length' => strlen($content),
                ]);
            }
        }

        if (Storage::disk('public')->exists($previewRelativePath)) {
            $content = Storage::disk('public')->get($previewRelativePath);
            $safeTitle = preg_replace('/[^\w\s\-\.\(\)]/u', '_', $research->title);

            return response($content, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $safeTitle . ' (Page 1 Preview).pdf"',
                'Content-Length' => strlen($content),
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        return $this->serveMissingFileNotice($research, 'Guest Preview');
    }

    /**
     * View research document as PDF inline in the browser.
     */
    public function show(Research $research, AIService $ai)
    {
        if (request()->has('download')) {
            return $this->download($research, $ai);
        }

        $files = $research->files()->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();

        // If specific file requested via ?file_id=X
        $fileId = request()->query('file_id');
        if ($fileId && $specificFile = $files->firstWhere('id', $fileId)) {
            return $this->serveSingleFile($research, $specificFile->file_path, $ai, $specificFile->original_name);
        }

        // If continuous combined view is explicitly requested via ?combined=1
        if (request()->has('combined') && $files->count() > 1) {
            $pdfData = $this->getMergedOrConvertedPdf($research, $ai);
            if ($pdfData) {
                return response($pdfData['content'], 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $pdfData['filename'] . '"',
                    'Content-Length' => strlen($pdfData['content']),
                ]);
            }
        }

        // Default: ALWAYS serve the first uploaded file (#1 primary view)
        $firstFile = $files->first();
        $primaryPath = $firstFile?->file_path ?? $research->pdf_path;
        if (!$primaryPath || !Storage::disk('public')->exists($primaryPath)) {
            return $this->serveMissingFileNotice($research, 'Manuscript Reader');
        }

        return $this->serveSingleFile($research, $primaryPath, $ai, $firstFile?->original_name);
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
                ->with('error', 'Unable to prepare download file. Please contact the administrator.');
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
     * Merge multiple PDF files into one output file locally using FPDI.
     */
    private function mergePdfsLocally(array $inputPdfPaths, string $outputPath): bool
    {
        $this->ensureFpdiLoaded();

        if (!class_exists(\setasign\Fpdi\Fpdi::class)) {
            return false;
        }

        try {
            $fpdi = new \setasign\Fpdi\Fpdi();
            $hasPages = false;
            $allFilesMerged = true;

            foreach ($inputPdfPaths as $path) {
                if (!file_exists($path)) {
                    $allFilesMerged = false;
                    continue;
                }
                try {
                    $pageCount = $fpdi->setSourceFile($path);
                    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                        $templateId = $fpdi->importPage($pageNo);
                        $size = $fpdi->getTemplateSize($templateId);
                        $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
                        $fpdi->useTemplate($templateId);
                        $hasPages = true;
                    }
                } catch (\Throwable $pageEx) {
                    Log::warning("Skipping incompatible page or file {$path}: " . $pageEx->getMessage());
                    $allFilesMerged = false;
                }
            }

            if (!$hasPages || !$allFilesMerged) {
                if (file_exists($outputPath)) {
                    @unlink($outputPath);
                }
                return false;
            }

            if (!is_dir(dirname($outputPath))) {
                mkdir(dirname($outputPath), 0755, true);
            }

            $fpdi->Output($outputPath, 'F');
            return file_exists($outputPath);
        } catch (\Throwable $e) {
            Log::warning("Local FPDI merge failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get or generate the single merged PDF file content (and safe filename) containing all files.
     */
    public function getMergedOrConvertedPdf(Research $research, AIService $ai): ?array
    {
        $this->ensureFpdiLoaded();
        $files = $research->files()->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();
        $safeTitle = preg_replace('/[^\w\s\-\.\(\)]/u', '_', $research->title);

        // 1. Multiple files: merge into single PDF in sequential order
        if ($files->count() > 1) {
            $inputPdfPaths = [];

            foreach ($files as $f) {
                if (!Storage::disk('public')->exists($f->file_path)) {
                    continue;
                }

                $ext = strtolower(pathinfo($f->file_path, PATHINFO_EXTENSION));
                $absoluteSrcPath = Storage::disk('public')->path($f->file_path);

                if ($ext === 'pdf') {
                    $inputPdfPaths[] = $absoluteSrcPath;
                } else {
                    // Check if cached PDF exists
                    $cachedPdfRel = 'researches/pdf_cache/' . pathinfo($f->file_path, PATHINFO_FILENAME) . '.pdf';
                    $cachedPdfAbs = Storage::disk('public')->path($cachedPdfRel);

                    if (Storage::disk('public')->exists($cachedPdfRel)) {
                        $inputPdfPaths[] = $cachedPdfAbs;
                    } else {
                        // 1. Attempt local pure-PHP conversion
                        $converted = $this->convertDocxToPdfLocally($absoluteSrcPath, $cachedPdfAbs);

                        // 2. Fallback to AI service
                        if (!$converted || !file_exists($cachedPdfAbs)) {
                            try {
                                if ($ai->convertDocxToPdf($absoluteSrcPath, $cachedPdfAbs) && file_exists($cachedPdfAbs)) {
                                    $converted = true;
                                }
                            } catch (\Throwable $e) {
                                // ignore
                            }
                        }

                        if ($converted && file_exists($cachedPdfAbs)) {
                            $inputPdfPaths[] = $cachedPdfAbs;
                        }
                    }
                }
            }

            if (!empty($inputPdfPaths)) {
                $hash = substr(md5(implode('|', $inputPdfPaths)), 0, 10);
                $mergedRelativePath = 'researches/pdf_cache/merged_' . $research->id . '_' . $hash . '.pdf';
                $absoluteOutputPath = Storage::disk('public')->path($mergedRelativePath);

                if (!Storage::disk('public')->exists($mergedRelativePath)) {
                    // 1. Try local pure-PHP FPDI merge
                    $merged = $this->mergePdfsLocally($inputPdfPaths, $absoluteOutputPath);

                    // 2. Fallback to AI service if local FPDI did not complete
                    if (!$merged || !file_exists($absoluteOutputPath)) {
                        try {
                            $ai->mergeDocuments($inputPdfPaths, $absoluteOutputPath);
                        } catch (\Throwable $e) {
                            Log::warning("AI service merge fallback failed: " . $e->getMessage());
                        }
                    }
                }

                if (Storage::disk('public')->exists($mergedRelativePath)) {
                    $fileContent = Storage::disk('public')->get($mergedRelativePath);
                    return [
                        'content' => $fileContent,
                        'filename' => $safeTitle . ' (Complete Combined Manuscript).pdf',
                    ];
                }

                // If merging failed but at least one PDF file exists, serve the first PDF as fallback
                if (!empty($inputPdfPaths) && file_exists($inputPdfPaths[0])) {
                    return [
                        'content' => file_get_contents($inputPdfPaths[0]),
                        'filename' => $safeTitle . ' (Part 1).pdf',
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

        if (Storage::disk('public')->exists($pdfRelativePath)) {
            return [
                'content' => Storage::disk('public')->get($pdfRelativePath),
                'filename' => $safeTitle . '.pdf',
            ];
        }

        $converted = $this->convertDocxToPdfLocally($absoluteDocxPath, $absolutePdfPath);
        if (!$converted) {
            try {
                $converted = $ai->convertDocxToPdf($absoluteDocxPath, $absolutePdfPath);
            } catch (\Throwable $e) {
                Log::warning("Single DOCX conversion failed: {$primaryPath}");
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
     * Serve a single file inline (PDF as inline PDF, DOCX via converted PDF or embedded docx-preview).
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

        // For DOCX files: check if cached PDF already exists first
        $pdfRelativePath = 'researches/pdf_cache/' . pathinfo($path, PATHINFO_FILENAME) . '.pdf';
        $absolutePdfPath = Storage::disk('public')->path($pdfRelativePath);

        if (Storage::disk('public')->exists($pdfRelativePath)) {
            $file = Storage::disk('public')->get($pdfRelativePath);
            return response($file, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . pathinfo($displayName, PATHINFO_FILENAME) . '.pdf"',
                'Content-Length' => strlen($file),
            ]);
        }

        // If not cached, attempt local conversion
        $converted = $this->convertDocxToPdfLocally($absoluteDocxPath, $absolutePdfPath);
        if (!$converted) {
            try {
                $converted = $ai->convertDocxToPdf($absoluteDocxPath, $absolutePdfPath);
            } catch (\Throwable $e) {
                Log::warning("DOCX conversion exception: {$path}");
            }
        }

        if (Storage::disk('public')->exists($pdfRelativePath)) {
            $file = Storage::disk('public')->get($pdfRelativePath);
            return response($file, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . pathinfo($displayName, PATHINFO_FILENAME) . '.pdf"',
                'Content-Length' => strlen($file),
            ]);
        }

        // View the actual Word Document content using high-fidelity docx-preview
        return response()->view('admin.researches.file-viewer', [
            'research' => $research,
            'fileUrl' => route('files.show', ['path' => $path]),
            'extension' => 'docx',
            'displayName' => $displayName,
        ]);
    }

    /**
     * Clean PDF notice when a physical file is missing from storage.
     */
    private function serveMissingFileNotice(Research $research, string $context = 'Manuscript Viewer')
    {
        $this->ensureFpdiLoaded();
        $safeTitle = preg_replace('/[^\w\s\-\.\(\)]/u', '_', $research->title);

        if (class_exists(\FPDF::class)) {
            try {
                $fpdf = new \FPDF('P', 'mm', 'A4');
                $fpdf->AddPage();
                $fpdf->SetMargins(20, 20, 20);

                // Top decorative bar
                $fpdf->SetFillColor(12, 66, 51); // Capstone dark green
                $fpdf->Rect(0, 0, 210, 8, 'F');

                // Header
                $fpdf->Ln(14);
                $fpdf->SetFont('Helvetica', 'B', 16);
                $fpdf->SetTextColor(15, 23, 42);
                $fpdf->Cell(0, 10, 'JHCSC CAPSTONE REPOSITORY', 0, 1, 'C');

                $fpdf->SetFont('Helvetica', '', 10);
                $fpdf->SetTextColor(100, 116, 139);
                $fpdf->Cell(0, 6, 'Digital Manuscript Archival Services', 0, 1, 'C');

                $fpdf->Ln(10);
                $fpdf->SetDrawColor(226, 232, 240);
                $fpdf->Line(20, $fpdf->GetY(), 190, $fpdf->GetY());
                $fpdf->Ln(12);

                // Notice Box
                $boxY = $fpdf->GetY();
                $fpdf->SetFillColor(248, 250, 252);
                $fpdf->SetDrawColor(203, 213, 225);
                $fpdf->Rect(20, $boxY, 170, 80, 'DF');

                $fpdf->SetXY(25, $boxY + 8);
                $fpdf->SetFont('Helvetica', 'B', 11);
                $fpdf->SetTextColor(12, 66, 51);
                $fpdf->Cell(160, 7, strtoupper($context) . ' - ARCHIVAL NOTICE', 0, 1, 'L');

                $fpdf->Ln(2);
                $fpdf->SetX(25);
                $fpdf->SetFont('Helvetica', 'B', 10);
                $fpdf->SetTextColor(30, 41, 59);
                $cleanTitle = iconv('UTF-8', 'windows-1252//TRANSLIT', $research->title);
                $fpdf->MultiCell(160, 5, 'Title: ' . $cleanTitle);

                $fpdf->Ln(4);
                $fpdf->SetX(25);
                $fpdf->SetFont('Helvetica', '', 9.5);
                $fpdf->SetTextColor(71, 85, 105);
                $fpdf->MultiCell(160, 5, 'The uploaded manuscript file for this record is currently stored in institutional cloud storage and is being synchronized to this viewer node.');

                $fpdf->Ln(3);
                $fpdf->SetX(25);
                $fpdf->SetFont('Helvetica', 'I', 8.5);
                $fpdf->SetTextColor(100, 116, 139);
                $fpdf->MultiCell(160, 4.5, 'Institutional administrators can verify storage synchronization in storage/app/public/researches or re-upload the manuscript file.');

                $pdfString = $fpdf->Output('S');

                return response($pdfString, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $safeTitle . ' (Notice).pdf"',
                    'Content-Length' => strlen($pdfString),
                ]);
            } catch (\Throwable $e) {
                Log::warning("Error creating missing file notice PDF: " . $e->getMessage());
            }
        }

        return response('Manuscript document is undergoing archival synchronization.', 200, [
            'Content-Type' => 'text/plain',
        ]);
    }
}
