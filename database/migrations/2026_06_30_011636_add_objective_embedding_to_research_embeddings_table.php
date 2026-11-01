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
            $table->longText('objective_embedding')->nullable()->after('content_embedding');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('research_embeddings', function (Blueprint $table) {
            $table->dropColumn('objective_embedding');
        });
    }
};
