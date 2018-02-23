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

        DB::statement('ALTER TABLE `summary_ret_consuments` MODIFY `unit_price` DOUBLE;');
        DB::statement('ALTER TABLE `summary_ret_consuments` MODIFY `value` DOUBLE;');
        DB::statement('ALTER TABLE `summary_ret_consuments` MODIFY `value_pf_mr` DOUBLE;');
        DB::statement('ALTER TABLE `summary_ret_consuments` MODIFY `value_pf_tr` DOUBLE;');
        DB::statement('ALTER TABLE `summary_ret_consuments` MODIFY `value_pf_ppe` DOUBLE;');
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
