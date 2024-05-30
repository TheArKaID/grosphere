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
            $table->enum('rating', ['1', '2', '3', '4', '5'])->nullable()->default(null);
            $table->text('remark')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_classes', function (Blueprint $table) {
            $table->dropColumn('rating');
            $table->dropColumn('remark');
        });
    }
};
