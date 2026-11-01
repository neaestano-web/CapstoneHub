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
        Schema::table('users', function (Blueprint $table) {
            $table->string('student_id_number')->nullable()->after('role');
            $table->string('id_picture_front')->nullable()->after('student_id_number');
            $table->string('id_picture_back')->nullable()->after('id_picture_front');
            $table->enum('registration_status', ['approved', 'pending', 'rejected'])->default('approved')->after('id_picture_back');
            $table->string('registration_rejection_reason')->nullable()->after('registration_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'student_id_number',
                'id_picture_front',
                'id_picture_back',
                'registration_status',
                'registration_rejection_reason',
            ]);
        });
    }
};
