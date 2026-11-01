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
        Schema::create('research_embeddings', function (Blueprint $table) {

            $table->id();

            $table->foreignId('research_id')
                ->constrained('researches')
                ->cascadeOnDelete();

            $table->longText('embedding');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_embeddings');
    }
};
