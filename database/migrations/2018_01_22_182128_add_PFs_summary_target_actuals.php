<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPFsSummaryTargetActuals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('summary_target_actuals', function (Blueprint $table) {
            $table->integer('sum_pf_actual_store')->after('sum_actual_store');
            $table->integer('sum_pf_target_store')->after('sum_actual_store');

            $table->integer('sum_pf_actual_area')->after('sum_actual_area');
            $table->integer('sum_pf_target_area')->after('sum_actual_area');

            $table->integer('sum_pf_actual_region')->after('sum_actual_region');
            $table->integer('sum_pf_target_region')->after('sum_actual_region');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('summary_target_actuals', function (Blueprint $table) {
            $table->dropColumn('sum_pf_actual_store');
            $table->dropColumn('sum_pf_target_store');
            $table->dropColumn('sum_pf_actual_area');
            $table->dropColumn('sum_pf_target_area');
            $table->dropColumn('sum_pf_actual_region');
            $table->dropColumn('sum_pf_target_region');
        });
    }
}
