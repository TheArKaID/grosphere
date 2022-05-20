<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveHostCodeAndUserCodeFromLiveClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('live_classes', function (Blueprint $table) {
            $table->dropColumn('host_code');
            $table->dropColumn('user_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('live_classes', function (Blueprint $table) {
            $table->string('host_code', 50)->default(null)->nullable()->after('start_time');
            $table->string('user_code', 50)->default(null)->nullable()->after('host_code');
        });
    }
}
