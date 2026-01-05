<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToSoThich extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('so_thich')) {
            return;
        }

        Schema::table('so_thich', function (Blueprint $table) {
            // Add text columns to store CSV ids and human-readable names if they don't exist
            if (!Schema::hasColumn('so_thich', 'tien_ich_id')) {
                $table->text('tien_ich_id')->nullable()->after('dia_chi_id');
            }

            if (!Schema::hasColumn('so_thich', 'dich_vu_id')) {
                $table->text('dich_vu_id')->nullable()->after('tien_ich_id');
            }

            if (!Schema::hasColumn('so_thich', 'ten_tien_ich')) {
                $table->text('ten_tien_ich')->nullable()->after('dich_vu_id');
            }

            if (!Schema::hasColumn('so_thich', 'ten_dich_vu')) {
                $table->text('ten_dich_vu')->nullable()->after('ten_tien_ich');
            }
        });
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
            if (Schema::hasColumn('so_thich', 'ten_dich_vu')) {
                $table->dropColumn('ten_dich_vu');
            }
            if (Schema::hasColumn('so_thich', 'ten_tien_ich')) {
                $table->dropColumn('ten_tien_ich');
            }
            if (Schema::hasColumn('so_thich', 'dich_vu_id')) {
                $table->dropColumn('dich_vu_id');
            }
            if (Schema::hasColumn('so_thich', 'tien_ich_id')) {
                $table->dropColumn('tien_ich_id');
            }
        });
    }
}
