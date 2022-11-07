<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLiveClassStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('live_class_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_class_id')->constrained();
            $table->foreignId('student_id')->constrained();
            $table->tinyInteger('type')->default(1);
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
        Schema::dropIfExists('live_class_students');
    }
}
