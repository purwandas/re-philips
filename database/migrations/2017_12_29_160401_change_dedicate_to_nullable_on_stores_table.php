<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDedicateToNullableOnStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('dedicate');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->enum('dedicate', ['DA', 'PC', 'MCC', 'HYBRID'])->after('store_name_2')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('dedicate');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->enum('dedicate', ['DA', 'PC', 'MCC', 'HYBRID'])->after('store_name_2');
        });
    }
}
