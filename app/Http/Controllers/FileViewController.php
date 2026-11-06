<?php

namespace App\Http\Controllers;

use App\Models\DownloadRequest;
use App\Models\Research;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileViewController extends Controller
{
    /**
     * Serve a file for inline viewing (PDF opens in browser, DOCX downloads).
     * If download attachment is requested, redirect to repository.download to serve the complete merged PDF.
     */
    public function show($path)
    {
        $filename = basename($path);
        $research = Research::where('pdf_path', $path)
            ->orWhere('pdf_path', 'researches/' . $filename)
            ->orWhere('pdf_path', 'like', '%' . $filename)
            ->orWhereHas('files', function ($q) use ($path, $filename) {
                $q->where('file_path', $path)
                  ->orWhere('file_path', 'like', '%' . $filename);
            })
            ->first();

        // If download is requested, redirect to unified single PDF download (merging all attached files)
        if (request()->has('download') && $research) {
            return redirect()->route('repository.download', $research);
        }

        // For student users attempting direct navigation/download to raw files, enforce approved download request
        if ($research && auth()->check() && auth()->user()->role !== 'admin') {
            $isFetchForViewer = request()->ajax()
                || request()->header('Sec-Fetch-Dest') === 'empty'
                || request()->header('X-Requested-With') === 'XMLHttpRequest';

            if (!$isFetchForViewer && !app(DocumentViewController::class)->canDownload($research)) {
                return redirect()->route('repository.show', $research)
                    ->with('error', 'Download permission required. Please submit a download request and wait for faculty approval.');
            }
        }

        // Support paths with or without "researches/" prefix
        $actualPath = $path;
        if (!Storage::disk('public')->exists($actualPath)) {
            if (Storage::disk('public')->exists('researches/' . $path)) {
                $actualPath = 'researches/' . $path;
            } elseif (str_starts_with($path, 'researches/') && Storage::disk('public')->exists(substr($path, 11))) {
                $actualPath = substr($path, 11);
            } else {
                abort(404, 'File not found in storage.');
            }
        }

        $file = Storage::disk('public')->get($actualPath);
        $mimeType = Storage::disk('public')->mimeType($actualPath);

        return response($file, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($actualPath) . '"',
            'Content-Length' => strlen($file),
        ]);
    }
}