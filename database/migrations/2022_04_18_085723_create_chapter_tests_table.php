<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChapterTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chapter_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_chapter_id')->constrained();
            $table->string('title');
            $table->integer('duration');
            $table->integer('attempt')->default(1);
            $table->dateTime('available_at')->nullable();
            $table->dateTime('available_until')->nullable();
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('chapter_tests');
    }
}
