<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPromoDemoSummaryTargetActuals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('summary_target_actuals', function (Blueprint $table) {
            // DEMONSTRATOR
            $table->integer('sum_pf_actual_store_demo')->after('sum_pf_actual_store');
            $table->integer('sum_pf_target_store_demo')->after('sum_pf_actual_store');
            $table->integer('sum_actual_store_demo')->after('sum_pf_actual_store');
            $table->integer('sum_target_store_demo')->after('sum_pf_actual_store');

            // PROMOTER
            $table->integer('sum_pf_actual_store_promo')->after('sum_pf_actual_store');
            $table->integer('sum_pf_target_store_promo')->after('sum_pf_actual_store');
            $table->integer('sum_actual_store_promo')->after('sum_pf_actual_store');
            $table->integer('sum_target_store_promo')->after('sum_pf_actual_store');
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
            $table->dropColumn('sum_pf_actual_store_demo');
            $table->dropColumn('sum_pf_target_store_demo');
            $table->dropColumn('sum_actual_store_demo');
            $table->dropColumn('sum_target_store_demo');

            $table->dropColumn('sum_pf_actual_store_promo');
            $table->dropColumn('sum_pf_target_store_promo');
            $table->dropColumn('sum_actual_store_promo');
            $table->dropColumn('sum_target_store_promo');
        });
    }
}
