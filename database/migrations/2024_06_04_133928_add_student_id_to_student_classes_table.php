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
        Schema::table('student_classes', function (Blueprint $table) {
            $table->foreignUuid('student_id')->after('course_student_id')->references('id')->on('students');
            $table->foreignUuid('course_student_id')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_classes', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropColumn('student_id');

            $table->foreignUuid('course_student_id')->nullable(false)->change();
        });
    }
};
