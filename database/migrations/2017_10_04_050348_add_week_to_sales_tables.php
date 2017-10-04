<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWeekToSalesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sell_ins', function (Blueprint $table) {
            $table->integer('week')->after('store_id');
        });

        Schema::table('sell_outs', function (Blueprint $table) {
            $table->integer('week')->after('store_id');
        });

        Schema::table('ret_distributors', function (Blueprint $table) {
            $table->integer('week')->after('store_id');
        });

        Schema::table('ret_consuments', function (Blueprint $table) {
            $table->integer('week')->after('store_id');
        });

        Schema::table('free_products', function (Blueprint $table) {
            $table->integer('week')->after('store_id');
        });

        Schema::table('tbats', function (Blueprint $table) {
            $table->integer('week')->after('store_id');
        });
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
