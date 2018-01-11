<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NullableSubchannelIdOnStores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('stores', function (Blueprint $table) {
            $table->dropForeign(['subchannel_id']);
            $table->dropColumn('subchannel_id');
        });
        Schema::table('stores', function (Blueprint $table) {
            $table->integer('subchannel_id')->nullable()->unsigned()->after('address');
            $table->foreign('subchannel_id')->references('id')->on('sub_channels');
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
            $table->dropForeign(['subchannel_id']);
            $table->dropColumn('subchannel_id');
        });
        Schema::table('stores', function (Blueprint $table) {
            $table->integer('subchannel_id')->unsigned()->after('address');
            $table->foreign('subchannel_id')->references('id')->on('sub_channels');
        });
    }
}
