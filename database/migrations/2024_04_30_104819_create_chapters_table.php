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
        Schema::create('chapters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('curriculum_id')->constrained();
            $table->string('name')->nullable();
            $table->text('description')->nullable()->default(null);
            $table->mediumText('content');
            $table->string('content_type');
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
        Schema::dropIfExists('chapters');
    }
};
