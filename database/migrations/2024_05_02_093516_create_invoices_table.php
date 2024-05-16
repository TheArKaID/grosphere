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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained();
            $table->string('invoice_number');
            $table->string('invoice_file')->nullable()->default(null);
            $table->decimal('price', 15, 2);
            $table->enum('currency', ['idr', 'sgd'])->default('idr');
            $table->integer('active_days');
            $table->integer('total_meeting');
            $table->date('due_date');
            $table->date('expired_date');
            $table->enum('status', ['unpaid', 'unconfirm', 'paid', 'overdue'])->default('unpaid');
            $table->string('payment_method')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
