<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateInstitutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('institutes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('phone', 50)->default("");
            $table->string('email')->default("");
            $table->string('website')->default("");
            $table->string('about')->default("");
            $table->timestamps();
        });
        DB::table('institutes')->updateOrInsert(['id' => 1], [
            'name' => 'Grosphere X',
            'address' => '',
            'phone' => '',
            'email' => '',
            'website' => '',
            'about' => '',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('institutes');
    }
}
