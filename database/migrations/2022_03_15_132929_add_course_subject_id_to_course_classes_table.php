<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCourseSubjectIdToCourseClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_classes', function (Blueprint $table) {
            $table->foreignId('course_subject_id')->nullable()->after('class_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_classes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('course_subject_id');
        });
    }
}
