<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeTenColumnsToText extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('so_thich')) {
            return;
        }

        // Use raw ALTER TABLE to avoid requiring doctrine/dbal for column type changes.
        try {
            DB::statement('ALTER TABLE `so_thich` MODIFY `ten_tien_ich` TEXT NULL');
        } catch (\Exception $e) {
            // ignore if fails (maybe already text)
        }

        try {
            DB::statement('ALTER TABLE `so_thich` MODIFY `ten_dich_vu` TEXT NULL');
        } catch (\Exception $e) {
            // ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // We won't revert to int safely; leave as-is.
    }
}
