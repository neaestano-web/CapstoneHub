<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\DownloadRequestApprovedMail;
use App\Mail\DownloadRequestRejectedMail;
use App\Models\DownloadRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DownloadRequestController extends Controller
{
    /**
     * Display a listing of manuscript download requests.
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'all');

        $query = DownloadRequest::with(['user', 'research', 'actionedBy'])
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $requests = $query->paginate(15)->withQueryString();

        $pendingCount = DownloadRequest::where('status', 'pending')->count();
        $approvedCount = DownloadRequest::where('status', 'approved')->count();
        $rejectedCount = DownloadRequest::where('status', 'rejected')->count();

        return view('admin.download-requests.index', compact(
            'requests',
            'status',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }

    /**
     * Approve a download request.
     */
    public function approve(DownloadRequest $downloadRequest)
    {
        DownloadRequest::where('user_id', $downloadRequest->user_id)
            ->where('research_id', $downloadRequest->research_id)
            ->update([
                'status' => 'approved',
                'actioned_by' => auth()->id(),
                'actioned_at' => now(),
            ]);

        // Send approval notification email to student
        $downloadRequest->loadMissing(['user', 'research']);
        if ($downloadRequest->user && !empty($downloadRequest->user->email)) {
            try {
                Mail::to($downloadRequest->user->email)->send(new DownloadRequestApprovedMail($downloadRequest));
                Log::info("Download request approval email sent to {$downloadRequest->user->email} for research #{$downloadRequest->research_id}");
            } catch (\Throwable $e) {
                Log::error("Failed to send download request approval email to {$downloadRequest->user->email}: " . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Download request granted successfully and notification email sent!');
    }

    /**
     * Reject a download request.
     */
    public function reject(DownloadRequest $downloadRequest)
    {
        DownloadRequest::where('user_id', $downloadRequest->user_id)
            ->where('research_id', $downloadRequest->research_id)
            ->update([
                'status' => 'rejected',
                'actioned_by' => auth()->id(),
                'actioned_at' => now(),
            ]);

        // Send rejection notification email to student
        $downloadRequest->loadMissing(['user', 'research']);
        if ($downloadRequest->user && !empty($downloadRequest->user->email)) {
            try {
                Mail::to($downloadRequest->user->email)->send(new DownloadRequestRejectedMail($downloadRequest));
                Log::info("Download request rejection email sent to {$downloadRequest->user->email} for research #{$downloadRequest->research_id}");
            } catch (\Throwable $e) {
                Log::error("Failed to send download request rejection email to {$downloadRequest->user->email}: " . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Download request denied and notification email sent.');
    }
}
