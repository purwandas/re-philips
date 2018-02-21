<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNullableOnSummaryAndSales2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `summary_sell_ins` MODIFY `distributor_code` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_sell_ins` MODIFY `distributor_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_sell_ins` MODIFY `store_name_2` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_sell_ins` MODIFY `spv_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_sell_ins` MODIFY `dm_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_sell_ins` MODIFY `trainer_name` VARCHAR(191) NULL;');

        DB::statement('ALTER TABLE `summary_sell_outs` MODIFY `distributor_code` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_sell_outs` MODIFY `distributor_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_sell_outs` MODIFY `store_name_2` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_sell_outs` MODIFY `spv_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_sell_outs` MODIFY `dm_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_sell_outs` MODIFY `trainer_name` VARCHAR(191) NULL;');
        
        DB::statement('ALTER TABLE `summary_ret_distributors` MODIFY `distributor_code` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_ret_distributors` MODIFY `distributor_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_ret_distributors` MODIFY `store_name_2` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_ret_distributors` MODIFY `spv_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_ret_distributors` MODIFY `dm_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_ret_distributors` MODIFY `trainer_name` VARCHAR(191) NULL;');
        
        DB::statement('ALTER TABLE `summary_ret_consuments` MODIFY `distributor_code` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_ret_consuments` MODIFY `distributor_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_ret_consuments` MODIFY `store_name_2` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_ret_consuments` MODIFY `spv_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_ret_consuments` MODIFY `dm_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_ret_consuments` MODIFY `trainer_name` VARCHAR(191) NULL;');
        
        DB::statement('ALTER TABLE `summary_free_products` MODIFY `distributor_code` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_free_products` MODIFY `distributor_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_free_products` MODIFY `store_name_2` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_free_products` MODIFY `spv_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_free_products` MODIFY `dm_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_free_products` MODIFY `trainer_name` VARCHAR(191) NULL;');
        
        DB::statement('ALTER TABLE `summary_tbats` MODIFY `distributor_code` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_tbats` MODIFY `distributor_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_tbats` MODIFY `store_name_2` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_tbats` MODIFY `spv_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_tbats` MODIFY `dm_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_tbats` MODIFY `trainer_name` VARCHAR(191) NULL;');
        
        DB::statement('ALTER TABLE `summary_sohs` MODIFY `distributor_code` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_sohs` MODIFY `distributor_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_sohs` MODIFY `store_name_2` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_sohs` MODIFY `spv_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_sohs` MODIFY `dm_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_sohs` MODIFY `trainer_name` VARCHAR(191) NULL;');
        
        DB::statement('ALTER TABLE `summary_sos` MODIFY `distributor_code` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_sos` MODIFY `distributor_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_sos` MODIFY `store_name_2` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_sos` MODIFY `spv_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_sos` MODIFY `dm_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_sos` MODIFY `trainer_name` VARCHAR(191) NULL;');
        
        DB::statement('ALTER TABLE `summary_display_shares` MODIFY `distributor_code` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_display_shares` MODIFY `distributor_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_display_shares` MODIFY `store_name_2` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_display_shares` MODIFY `spv_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_display_shares` MODIFY `dm_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_display_shares` MODIFY `trainer_name` VARCHAR(191) NULL;');
        
        
        DB::statement('ALTER TABLE `salesman_summary_sales` MODIFY `distributor_code` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `salesman_summary_sales` MODIFY `distributor_name` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `salesman_summary_sales` MODIFY `store_name_2` VARCHAR(191) NULL;');

        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `store_name_2` VARCHAR(191) NULL;');
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `spv_name` VARCHAR(191) NULL;');        
        DB::statement('ALTER TABLE `summary_target_actuals` MODIFY `trainer` VARCHAR(191) NULL;');
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
