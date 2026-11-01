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
        Schema::create('similarity_logs', function (Blueprint $table) {

            $table->id();

            $table->text('input_title');

            $table->longText('input_objectives')
                ->nullable();

            $table->string('matched_title');

            $table->float('title_similarity');

            $table->float('objective_similarity')
                ->nullable();

            $table->float('overall_similarity')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('similarity_logs');
    }
};
