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
        Schema::create('feed_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('feed_id')->constrained('feeds')->cascadeOnDelete();
            $table->string('url')->default('');
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
        Schema::dropIfExists('feed_images');
    }
};
