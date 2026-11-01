<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_type_research', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_id')->constrained('researches')->cascadeOnDelete();
            $table->foreignId('project_type_id')->constrained('project_types')->cascadeOnDelete();
            $table->unique(['research_id', 'project_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_type_research');
    }
};
