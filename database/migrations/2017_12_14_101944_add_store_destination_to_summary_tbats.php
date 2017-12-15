<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStoreDestinationToSummaryTbats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('summary_tbats', function (Blueprint $table) {
            $table->string('storeDestinationId')->after('storeId');

            $table->string('store_destination_id')->after('district');
            $table->string('store_destination_name_2')->after('district');
            $table->string('store_destination_name_1')->after('district');
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
            //
        });
    }
}
