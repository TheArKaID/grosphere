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
        Schema::create('teacher_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->string('name')->nullable()->default(null);
            $table->mediumText('content')->nullable()->default(null);
            $table->string('content_type')->nullable()->default(null);
            $table->string('file_path')->nullable()->default(null);
            $table->string('file_name')->nullable()->default(null);
            $table->string('file_extension')->nullable()->default(null);
            $table->string('file_size')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_files');
    }
};
