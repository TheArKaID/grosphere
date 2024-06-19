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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('student_id')->constrained();
            $table->foreignUuid('course_work_id')->constrained();
            $table->decimal('price', 15, 2);
            $table->enum('currency', ['idr', 'sgd'])->default('idr');
            $table->integer('active_days');
            $table->date('active_until')->nullable()->default(null);
            $table->integer('total_meeting');
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
