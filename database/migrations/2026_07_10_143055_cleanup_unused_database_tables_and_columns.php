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
        // Drop unused tables
        Schema::dropIfExists('pre_registered_students');
        Schema::dropIfExists('school_id_templates');
        Schema::dropIfExists('similarity_logs');

        // Drop unused columns from users table if they exist
        Schema::table('users', function (Blueprint $table) {
            $cols = array_filter(['id_verification_status', 'id_ocr_extracted_text'], fn($col) => Schema::hasColumn('users', $col));
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-create dropped columns in users table
        Schema::table('users', function (Blueprint $table) {
            $table->enum('id_verification_status', ['verified', 'unverified', 'failed'])->default('unverified');
            $table->text('id_ocr_extracted_text')->nullable();
        });

        // Re-create similarity_logs table
        Schema::create('similarity_logs', function (Blueprint $table) {
            $table->id();
            $table->text('input_title');
            $table->longText('input_objectives')->nullable();
            $table->string('matched_title');
            $table->float('title_similarity');
            $table->float('objective_similarity')->nullable();
            $table->float('overall_similarity')->nullable();
            $table->timestamps();
        });

        // Re-create school_id_templates table
        Schema::create('school_id_templates', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['front', 'back']);
            $table->string('image_path');
            $table->text('ocr_reference_text')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Re-create pre_registered_students table
        Schema::create('pre_registered_students', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('student_id_number');
            $table->boolean('is_used')->default(false);
            $table->timestamps();
        });
    }
};
