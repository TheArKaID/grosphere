<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCourseCategoryIdToCourseWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_works', function (Blueprint $table) {
            $table->foreignId('course_category_id')->nullable()->after('class_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_works', function (Blueprint $table) {
            $table->dropConstrainedForeignId('course_category_id');
        });
    }
}
