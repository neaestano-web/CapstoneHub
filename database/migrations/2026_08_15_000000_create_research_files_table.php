<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('research_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_id')->constrained('researches')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->unsignedInteger('sort_order')->default(1);
            $table->timestamps();
        });

        // Backfill existing researches that have pdf_path
        $existing = DB::table('researches')->whereNotNull('pdf_path')->get();
        foreach ($existing as $r) {
            $filename = basename($r->pdf_path);
            DB::table('research_files')->insert([
                'research_id'   => $r->id,
                'file_path'     => $r->pdf_path,
                'original_name' => $filename,
                'mime_type'     => str_ends_with(strtolower($filename), '.docx') ? 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' : 'application/pdf',
                'file_size'     => null,
                'sort_order'    => 1,
                'created_at'    => $r->created_at ?? now(),
                'updated_at'    => $r->updated_at ?? now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_files');
    }
};
