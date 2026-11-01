<?php

namespace App\Http\Controllers;

use App\Models\DownloadRequest;
use App\Models\Research;
use Illuminate\Http\Request;

class DownloadRequestController extends Controller
{
    /**
     * Store a new manuscript download request from a student.
     */
    public function store(Request $request, Research $research)
    {
        if ($research->status !== 'approved') {
            abort(404);
        }

        $user = auth()->user();

        // Check if student already has a pending or approved request
        $existingRequest = DownloadRequest::where('user_id', $user->id)
            ->where('research_id', $research->id)
            ->latest()
            ->first();

        if ($existingRequest) {
            if ($existingRequest->isApproved()) {
                return redirect()->back()->with('status', 'You already have access to download this manuscript.');
            }
            if ($existingRequest->isPending()) {
                return redirect()->back()->with('status', 'Your download request is currently pending admin review.');
            }
            // If rejected, allow re-submitting with a new reason
            $existingRequest->update([
                'reason' => $request->input('reason'),
                'status' => 'pending',
                'actioned_by' => null,
                'actioned_at' => null,
            ]);

            return redirect()->back()->with('success', 'Your download request has been re-submitted for admin review.');
        }

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        DownloadRequest::create([
            'user_id' => $user->id,
            'research_id' => $research->id,
            'reason' => $request->input('reason'),
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Download request submitted successfully! Admin will review your request.');
    }
}
