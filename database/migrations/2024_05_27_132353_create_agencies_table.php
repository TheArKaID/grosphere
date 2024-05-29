<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 50)->nullable()->default(null);
            $table->string('email')->nullable()->default(null);
            $table->text('address')->nullable()->default(null);
            $table->string('website')->nullable()->default(null);
            $table->text('about')->nullable()->default(null);
            $table->string('sub_title')->nullable()->default(null);
            $table->string('color')->nullable()->default(null);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
        DB::table('agencies')->updateOrInsert(['id' => 1], [
            'name' => 'Grosphere X',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agencies');
    }
};
