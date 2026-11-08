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

