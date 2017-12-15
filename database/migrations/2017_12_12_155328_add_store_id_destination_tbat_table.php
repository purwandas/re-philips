<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStoreIdDestinationTbatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
    {
        Schema::table('tbats', function (Blueprint $table) {
            $table->integer('store_destination_id')->unsigned()->after('store_id');
            $table->foreign('store_destination_id')->references('id')->on('stores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbats', function (Blueprint $table) {
            //
        });
    }
}
