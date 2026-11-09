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
        if (!Schema::hasTable('research_file_chunks')) {
            Schema::create('research_file_chunks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('research_file_id')->constrained('research_files')->cascadeOnDelete();
                $table->unsignedInteger('chunk_order')->default(0);
                $table->longText('chunk_data');
                $table->timestamps();

                $table->index(['research_file_id', 'chunk_order']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_file_chunks');
    }
};

