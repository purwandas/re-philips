<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWeekToSummaryTargetActuals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('summary_target_actuals', function (Blueprint $table) {

            // Week MCC
            $table->integer('actual_mcc_w5')->after('actual_pf_mcc');
            $table->integer('target_mcc_w5')->after('actual_pf_mcc');
            $table->integer('actual_mcc_w4')->after('actual_pf_mcc');
            $table->integer('target_mcc_w4')->after('actual_pf_mcc');
            $table->integer('actual_mcc_w3')->after('actual_pf_mcc');
            $table->integer('target_mcc_w3')->after('actual_pf_mcc');
            $table->integer('actual_mcc_w2')->after('actual_pf_mcc');
            $table->integer('target_mcc_w2')->after('actual_pf_mcc');
            $table->integer('actual_mcc_w1')->after('actual_pf_mcc');
            $table->integer('target_mcc_w1')->after('actual_pf_mcc');

            // Week PC
            $table->integer('actual_pc_w5')->after('actual_pf_mcc');
            $table->integer('target_pc_w5')->after('actual_pf_mcc');
            $table->integer('actual_pc_w4')->after('actual_pf_mcc');
            $table->integer('target_pc_w4')->after('actual_pf_mcc');
            $table->integer('actual_pc_w3')->after('actual_pf_mcc');
            $table->integer('target_pc_w3')->after('actual_pf_mcc');
            $table->integer('actual_pc_w2')->after('actual_pf_mcc');
            $table->integer('target_pc_w2')->after('actual_pf_mcc');
            $table->integer('actual_pc_w1')->after('actual_pf_mcc');
            $table->integer('target_pc_w1')->after('actual_pf_mcc');

            // Week DA
            $table->integer('actual_da_w5')->after('actual_pf_mcc');
            $table->integer('target_da_w5')->after('actual_pf_mcc');
            $table->integer('actual_da_w4')->after('actual_pf_mcc');
            $table->integer('target_da_w4')->after('actual_pf_mcc');
            $table->integer('actual_da_w3')->after('actual_pf_mcc');
            $table->integer('target_da_w3')->after('actual_pf_mcc');
            $table->integer('actual_da_w2')->after('actual_pf_mcc');
            $table->integer('target_da_w2')->after('actual_pf_mcc');
            $table->integer('actual_da_w1')->after('actual_pf_mcc');
            $table->integer('target_da_w1')->after('actual_pf_mcc');

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
            //
        });
    }
}
