<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesmanSummarySales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salesman_summary_sales', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sellin_detail_id');
            $table->string('region_id');
            $table->string('area_id');
            $table->string('district_id');
            $table->string('storeId');
            $table->string('user_id');
            $table->char('week', 1);
            $table->string('distributor_code');
            $table->string('distributor_name');
            $table->string('region');
            $table->string('channel');
            $table->string('sub_channel');
            $table->string('area');
            $table->string('district');
            $table->string('store_name_1');
            $table->string('store_name_2');
            $table->string('store_id');
            $table->string('nik');
            $table->string('promoter_name');
            $table->string('date');
            $table->string('model');
            $table->string('group');
            $table->string('category');
            $table->string('product_name');
            $table->integer('quantity');
            $table->integer('unit_price');
            $table->integer('value');
            $table->integer('value_pf');
            $table->string('role');
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
        Schema::dropIfExists('salesman_summary_sales');
    }
}
