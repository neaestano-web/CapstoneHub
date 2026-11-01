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
        Schema::table('research_embeddings', function (Blueprint $table) {

            // Drop the old single embedding column
            if (Schema::hasColumn('research_embeddings', 'embedding')) {
                $table->dropColumn('embedding');
            }

            // Drop objective_embedding and full_embedding if they still exist
            if (Schema::hasColumn('research_embeddings', 'objective_embedding')) {
                $table->dropColumn('objective_embedding');
            }

            if (Schema::hasColumn('research_embeddings', 'full_embedding')) {
                $table->dropColumn('full_embedding');
            }

            // Add/reintroduce title_embedding if missing
            if (!Schema::hasColumn('research_embeddings', 'title_embedding')) {
                $table->longText('title_embedding');
            }

            // Add content_embedding column
            if (!Schema::hasColumn('research_embeddings', 'content_embedding')) {
                $table->longText('content_embedding');
            }
        });
    }

    public function down(): void
    {
        Schema::table('research_embeddings', function (Blueprint $table) {

            if (Schema::hasColumn('research_embeddings', 'title_embedding')) {
                $table->dropColumn('title_embedding');
            }

            if (Schema::hasColumn('research_embeddings', 'content_embedding')) {
                $table->dropColumn('content_embedding');
            }

            if (!Schema::hasColumn('research_embeddings', 'embedding')) {
                $table->longText('embedding');
            }
        });
    }

};