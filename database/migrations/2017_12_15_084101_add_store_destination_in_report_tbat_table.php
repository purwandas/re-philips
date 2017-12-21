<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStoreDestinationInReportTbatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('summary_tbats', function (Blueprint $table) {
            $table->integer('store_destination_id');
            $table->string('store_destination_name_1');
            $table->string('store_destination_name_2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('summary_tbats', function (Blueprint $table) {
            $table->dropColumn('store_destination_id');
            $table->dropColumn('store_destination_name_1');
            $table->dropColumn('store_destination_name_2');
        });
    }
}
