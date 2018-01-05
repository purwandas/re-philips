<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesmanSummaryTargetActuals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salesman_summary_target_actuals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id');
            $table->string('nik');
            $table->string('salesman_name');
            $table->string('area');
            $table->integer('target_call');
            $table->integer('actual_call');
            $table->integer('target_active_outlet');
            $table->integer('actual_active_outlet');
            $table->integer('target_effective_call');
            $table->integer('actual_effective_call');
            $table->double('target_sales');
            $table->double('actual_sales');
            $table->double('target_sales_pf');
            $table->double('actual_sales_pf');
            $table->integer('sum_national_target_call');
            $table->integer('sum_national_actual_call');
            $table->integer('sum_national_target_active_outlet');
            $table->integer('sum_national_actual_active_outlet');
            $table->integer('sum_national_target_effective_call');
            $table->integer('sum_national_actual_effective_call');
            $table->double('sum_national_target_sales');
            $table->double('sum_national_actual_sales');
            $table->double('sum_national_target_sales_pf');
            $table->double('sum_national_actual_sales_pf');
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
        Schema::dropIfExists('salesman_summary_target_actuals');
    }
}
