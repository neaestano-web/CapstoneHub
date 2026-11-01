<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('search_logs', function (Blueprint $table) {
            $table->foreignId('research_category_id')
                ->nullable()
                ->after('user_id')
                ->constrained('research_categories')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('search_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('research_category_id');
        });
    }
};
