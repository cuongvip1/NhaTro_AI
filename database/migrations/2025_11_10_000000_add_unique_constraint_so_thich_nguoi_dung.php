<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddUniqueConstraintSoThichNguoiDung extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration will:
     *  - remove duplicate `so_thich` rows per `nguoi_dung_id` keeping the row with the MAX(id)
     *  - add a unique index on `nguoi_dung_id`
     *
     * Note: remove-then-add approach is used to avoid index creation failure when duplicates exist.
     */
    public function up()
    {
        // Defensive: only operate if table exists
        if (!Schema::hasTable('so_thich')) {
            return;
        }

        // Delete duplicates: keep the row with the greatest id for each nguoi_dung_id
        // Use a subquery wrapper to satisfy MySQL restrictions about modifying the same table
        DB::statement(<<<'SQL'
            DELETE FROM so_thich
            WHERE id NOT IN (
                SELECT keep_id FROM (
                    SELECT MAX(id) AS keep_id FROM so_thich GROUP BY nguoi_dung_id
                ) AS keepers
            )
        SQL
        );

        // Add unique index if it doesn't already exist.
        // We avoid Doctrine here (may not be installed) and use a raw ALTER TABLE wrapped
        // in a try/catch so the migration won't fail if the index already exists.
        try {
            DB::statement('ALTER TABLE `so_thich` ADD UNIQUE INDEX `so_thich_nguoi_dung_unique` (`nguoi_dung_id`)');
        } catch (\Exception $e) {
            // If index already exists or other non-fatal error, ignore and continue.
            // This keeps the migration idempotent-ish for environments with differing state.
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (!Schema::hasTable('so_thich')) {
            return;
        }

        Schema::table('so_thich', function (Blueprint $table) {
            $table->dropUnique('so_thich_nguoi_dung_unique');
        });
    }
}
