<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDoubleAllFieldSummary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `salesman_summary_sales` MODIFY `unit_price` DOUBLE;');
        DB::statement('ALTER TABLE `salesman_summary_sales` MODIFY `value` DOUBLE;');
        DB::statement('ALTER TABLE `salesman_summary_sales` MODIFY `value_pf` DOUBLE;');

        DB::statement('ALTER TABLE `summary_free_products` MODIFY `unit_price` DOUBLE;');
        DB::statement('ALTER TABLE `summary_free_products` MODIFY `value` DOUBLE;');
        DB::statement('ALTER TABLE `summary_free_products` MODIFY `value_pf_mr` DOUBLE;');
        DB::statement('ALTER TABLE `summary_free_products` MODIFY `value_pf_tr` DOUBLE;');
        DB::statement('ALTER TABLE `summary_free_products` MODIFY `value_pf_ppe` DOUBLE;');
        
        DB::statement('ALTER TABLE `summary_ret_consuments` MODIFY `unit_price` DOUBLE;');
        DB::statement('ALTER TABLE `summary_ret_consuments` MODIFY `value` DOUBLE;');
        DB::statement('ALTER TABLE `summary_ret_consuments` MODIFY `value_pf_mr` DOUBLE;');
        DB::statement('ALTER TABLE `summary_ret_consuments` MODIFY `value_pf_tr` DOUBLE;');
        DB::statement('ALTER TABLE `summary_ret_consuments` MODIFY `value_pf_ppe` DOUBLE;');

        DB::statement('ALTER TABLE `summary_ret_distributors` MODIFY `unit_price` DOUBLE;');
        DB::statement('ALTER TABLE `summary_ret_distributors` MODIFY `value` DOUBLE;');
        DB::statement('ALTER TABLE `summary_ret_distributors` MODIFY `value_pf_mr` DOUBLE;');
        DB::statement('ALTER TABLE `summary_ret_distributors` MODIFY `value_pf_tr` DOUBLE;');
        DB::statement('ALTER TABLE `summary_ret_distributors` MODIFY `value_pf_ppe` DOUBLE;');

        DB::statement('ALTER TABLE `summary_sell_ins` MODIFY `unit_price` DOUBLE;');
        DB::statement('ALTER TABLE `summary_sell_ins` MODIFY `value` DOUBLE;');
        DB::statement('ALTER TABLE `summary_sell_ins` MODIFY `value_pf_mr` DOUBLE;');
        DB::statement('ALTER TABLE `summary_sell_ins` MODIFY `value_pf_tr` DOUBLE;');
        DB::statement('ALTER TABLE `summary_sell_ins` MODIFY `value_pf_ppe` DOUBLE;');

        DB::statement('ALTER TABLE `summary_sell_outs` MODIFY `unit_price` DOUBLE;');
        DB::statement('ALTER TABLE `summary_sell_outs` MODIFY `value` DOUBLE;');
        DB::statement('ALTER TABLE `summary_sell_outs` MODIFY `value_pf_mr` DOUBLE;');
        DB::statement('ALTER TABLE `summary_sell_outs` MODIFY `value_pf_tr` DOUBLE;');
        DB::statement('ALTER TABLE `summary_sell_outs` MODIFY `value_pf_ppe` DOUBLE;');

        DB::statement('ALTER TABLE `summary_sohs` MODIFY `unit_price` DOUBLE;');
        DB::statement('ALTER TABLE `summary_sohs` MODIFY `value` DOUBLE;');
        DB::statement('ALTER TABLE `summary_sohs` MODIFY `value_pf_mr` DOUBLE;');
        DB::statement('ALTER TABLE `summary_sohs` MODIFY `value_pf_tr` DOUBLE;');
        DB::statement('ALTER TABLE `summary_sohs` MODIFY `value_pf_ppe` DOUBLE;');

        DB::statement('ALTER TABLE `summary_sos` MODIFY `unit_price` DOUBLE;');
        DB::statement('ALTER TABLE `summary_sos` MODIFY `value` DOUBLE;');
        DB::statement('ALTER TABLE `summary_sos` MODIFY `value_pf_mr` DOUBLE;');
        DB::statement('ALTER TABLE `summary_sos` MODIFY `value_pf_tr` DOUBLE;');
        DB::statement('ALTER TABLE `summary_sos` MODIFY `value_pf_ppe` DOUBLE;');

        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `target_dapc` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `actual_dapc` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `target_da` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `actual_da` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `target_pc` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `actual_pc` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `target_mcc` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `actual_mcc` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `target_pf_da` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `actual_pf_da` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `target_pf_pc` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `actual_pf_pc` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `target_pf_mcc` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `actual_pf_mcc` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `target_da_w1` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `actual_da_w1` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `target_da_w2` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `actual_da_w2` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `target_da_w3` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `actual_da_w3` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `target_da_w4` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `actual_da_w4` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `target_da_w5` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `actual_da_w5` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `target_pc_w1` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `actual_pc_w1` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `target_pc_w2` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `actual_pc_w2` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `target_pc_w3` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `actual_pc_w3` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `target_pc_w4` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `actual_pc_w4` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `target_pc_w5` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `actual_pc_w5` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `target_mcc_w1` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `actual_mcc_w1` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `target_mcc_w2` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `actual_mcc_w2` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `target_mcc_w3` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `actual_mcc_w3` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `target_mcc_w4` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `actual_mcc_w4` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `target_mcc_w5` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `actual_mcc_w5` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `sum_target_store` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `sum_actual_store` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `sum_pf_target_store` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `sum_pf_actual_store` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `sum_target_store_promo` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `sum_actual_store_promo` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `sum_pf_target_store_promo` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `sum_pf_actual_store_promo` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `sum_target_store_demo` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `sum_actual_store_demo` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `sum_pf_target_store_demo` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `sum_pf_actual_store_demo` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `sum_target_area` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `sum_actual_area` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `sum_pf_target_area` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `sum_pf_actual_area` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `sum_target_region` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `sum_actual_region` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `sum_pf_target_region` DOUBLE;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `sum_pf_actual_region` DOUBLE;');

        DB::statement('ALTER TABLE `summary_tbats` MODIFY `unit_price` DOUBLE;');
        DB::statement('ALTER TABLE `summary_tbats` MODIFY `value` DOUBLE;');
        DB::statement('ALTER TABLE `summary_tbats` MODIFY `value_pf_mr` DOUBLE;');
        DB::statement('ALTER TABLE `summary_tbats` MODIFY `value_pf_tr` DOUBLE;');
        DB::statement('ALTER TABLE `summary_tbats` MODIFY `value_pf_ppe` DOUBLE;');

        DB::statement('ALTER TABLE `targets` MODIFY `target_da` DOUBLE;');
        DB::statement('ALTER TABLE `targets` MODIFY `target_pf_da` DOUBLE;');
        DB::statement('ALTER TABLE `targets` MODIFY `target_pc` DOUBLE;');
        DB::statement('ALTER TABLE `targets` MODIFY `target_pf_pc` DOUBLE;');
        DB::statement('ALTER TABLE `targets` MODIFY `target_mcc` DOUBLE;');
        DB::statement('ALTER TABLE `targets` MODIFY `target_pf_mcc` DOUBLE;');

        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
