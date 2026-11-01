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
        // If download is requested, redirect to unified single PDF download (merging all attached files)
        if (request()->has('download')) {
            $filename = basename($path);
            $research = Research::where('pdf_path', $path)
                ->orWhere('pdf_path', 'researches/' . $filename)
                ->orWhere('pdf_path', 'like', '%' . $filename)
                ->orWhereHas('files', function ($q) use ($path, $filename) {
                    $q->where('file_path', $path)
                      ->orWhere('file_path', 'like', '%' . $filename);
                })
                ->first();

            if ($research) {
                return redirect()->route('repository.download', $research);
            }
        }

        // pdf_path is already stored with "researches/" prefix
        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $file = Storage::disk('public')->get($path);
        $mimeType = Storage::disk('public')->mimeType($path);

        return response($file, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
            'Content-Length' => strlen($file),
        ]);
    }
}