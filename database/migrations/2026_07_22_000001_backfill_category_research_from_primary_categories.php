<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Make legacy primary-category assignments available through the current
     * multi-category relationship used by category pages and repository filters.
     */
    public function up(): void
    {
        DB::table('researches')
            ->select('id', 'research_category_id')
            ->whereNotNull('research_category_id')
            ->orderBy('id')
            ->chunkById(100, function ($researches) {
                DB::table('category_research')->insertOrIgnore(
                    $researches->map(fn ($research) => [
                        'research_id' => $research->id,
                        'research_category_id' => $research->research_category_id,
                    ])->all()
                );
            });
    }

    public function down(): void
    {
        // Backfilled links can be indistinguishable from user assignments.
    }
};
