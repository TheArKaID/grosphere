<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentTestAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_test_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_test_id')->constrained();
            $table->foreignId('test_question_id')->constrained();
            $table->text('answer');
            $table->tinyInteger('is_correct')->nullable()->default(null);
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
        Schema::dropIfExists('student_test_answers');
    }
}
