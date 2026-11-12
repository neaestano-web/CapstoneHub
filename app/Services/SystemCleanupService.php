<?php

namespace App\Services;

use App\Models\DownloadRequest;
use App\Models\Research;
use App\Models\ResearchFile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SystemCleanupService
{
    /**
     * Delete all projects/researches and all non-admin users, preserving only administrators.
     *
     * @return array Summary of deleted records
     */
    public function wipeDataKeepAdmins(): array
    {
        $deletedResearches = 0;
        $deletedFiles = 0;
        $deletedUsers = 0;
        $deletedDownloadRequests = 0;
        $deletedEmbeddings = 0;

        DB::transaction(function () use (
            &$deletedResearches,
            &$deletedFiles,
            &$deletedUsers,
            &$deletedDownloadRequests,
            &$deletedEmbeddings
        ) {
            // 1. Delete all download requests
            $deletedDownloadRequests = DownloadRequest::count();
            DownloadRequest::query()->delete();

            // 2. Delete all research files records
            $deletedFiles = ResearchFile::count();
            ResearchFile::query()->delete();

            // 3. Delete all embeddings
            if (DB::getSchemaBuilder()->hasTable('research_embeddings')) {
                $deletedEmbeddings = DB::table('research_embeddings')->count();
                DB::table('research_embeddings')->delete();
            }

            // 4. Delete pivot table records
            if (DB::getSchemaBuilder()->hasTable('category_research')) {
                DB::table('category_research')->delete();
            }
            if (DB::getSchemaBuilder()->hasTable('project_type_research')) {
                DB::table('project_type_research')->delete();
            }

            // 5. Delete search and similarity logs
            if (DB::getSchemaBuilder()->hasTable('search_logs')) {
                DB::table('search_logs')->delete();
            }
            if (DB::getSchemaBuilder()->hasTable('similarity_logs')) {
                DB::table('similarity_logs')->delete();
            }

            // 6. Delete all researches / projects (including soft-deleted ones)
            $deletedResearches = Research::withTrashed()->count();
            Research::withTrashed()->forceDelete();

            // 7. Delete all users EXCEPT admins (including soft-deleted ones)
            $nonAdminUsers = User::withTrashed()->where('role', '!=', 'admin')->get();
            $deletedUsers = $nonAdminUsers->count();

            // Delete OTP codes for non-admin users if table exists
            if (DB::getSchemaBuilder()->hasTable('otp_codes')) {
                $nonAdminIds = $nonAdminUsers->pluck('id');
                DB::table('otp_codes')->whereIn('user_id', $nonAdminIds)->delete();
            }

            User::withTrashed()->where('role', '!=', 'admin')->forceDelete();
        });

        \App\Models\AuditLog::record('system.wiped', 'system', null, 'wipeDataKeepAdmins', [
            'researches' => $deletedResearches,
            'files' => $deletedFiles,
            'users' => $deletedUsers,
            'download_requests' => $deletedDownloadRequests,
            'embeddings' => $deletedEmbeddings,
        ]);

        // 8. Delete all research files and ID pictures from storage
        try {
            Storage::disk('public')->deleteDirectory('researches');
            Storage::disk('public')->makeDirectory('researches');
            Storage::disk('public')->makeDirectory('researches/pdf_cache');

            Storage::disk('public')->deleteDirectory('id-pictures');
            Storage::disk('public')->makeDirectory('id-pictures');
        } catch (\Throwable $e) {
            Log::warning('Storage cleanup error during data wipe: ' . $e->getMessage());
        }

        return [
            'researches' => $deletedResearches,
            'files' => $deletedFiles,
            'users' => $deletedUsers,
            'download_requests' => $deletedDownloadRequests,
            'embeddings' => $deletedEmbeddings,
        ];
    }
}

