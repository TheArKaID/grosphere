<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateCalendarView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('DROP VIEW IF EXISTS `calendar_view`');
        DB::statement("CREATE VIEW calendar_view AS
            SELECT a.id AS agenda_id, 0 as liveclass_id, a.user_id, a.detail, a.date, 1 as type FROM agendas a 
            UNION
            SELECT 0, lc.id, 0, c.name, lc.start_time, 2 FROM live_classes lc JOIN classes c ON lc.class_id = c.id;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS `calendar_view`');
    }
}
