<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_works', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('curriculum_id')->nullable()->constrained();
            $table->string('subject');
            $table->integer('grade');
            $table->integer('term');
            $table->integer('quota');
            $table->string('thumbnail')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_works');
    }
}
