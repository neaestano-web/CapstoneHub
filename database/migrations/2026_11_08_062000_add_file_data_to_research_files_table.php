<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('research_files', function (Blueprint $table) {
            if (!Schema::hasColumn('research_files', 'file_data')) {
                $table->longText('file_data')->nullable()->after('file_size');
            }
        });

        // Automatically backfill existing files on disk to TiDB if present
        try {
            $files = \App\Models\ResearchFile::whereNull('file_data')->get();
            foreach ($files as $rf) {
                if ($rf->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($rf->file_path)) {
                    $rf->update([
                        'file_data' => base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($rf->file_path)),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Ignore non-critical backfill notices
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('research_files', function (Blueprint $table) {
            if (Schema::hasColumn('research_files', 'file_data')) {
                $table->dropColumn('file_data');
            }
        });
    }
};

