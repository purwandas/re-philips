<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSummaryTargetActualsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('summary_target_actuals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('region_id');
            $table->string('area_id');
            $table->string('district_id');
            $table->string('storeId');
            $table->string('user_id');
            $table->string('region');
            $table->string('area');
            $table->string('district');
            $table->string('nik');
            $table->string('promoter_name');
            $table->string('account_type');
            $table->string('title_of_promoter');
            $table->string('classification_store');
            $table->string('account');
            $table->string('store_id');
            $table->string('store_name_1');
            $table->string('store_name_2');
            $table->string('spv_name');
            $table->string('trainer');
            $table->integer('target_dapc');
            $table->integer('actual_dapc');
            $table->integer('target_da');
            $table->integer('actual_da');
            $table->integer('target_pc');
            $table->integer('actual_pc');
            $table->integer('target_mcc');
            $table->integer('actual_mcc');
            $table->integer('target_pf_da');
            $table->integer('actual_pf_da');
            $table->integer('target_pf_pc');
            $table->integer('actual_pf_pc');
            $table->integer('target_pf_mcc');
            $table->integer('actual_pf_mcc');
            $table->integer('sum_target_store');
            $table->integer('sum_actual_store');
            $table->integer('sum_target_area');
            $table->integer('sum_actual_area');
            $table->integer('sum_target_region');
            $table->integer('sum_actual_region');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('summary_target_actuals');
    }
}
