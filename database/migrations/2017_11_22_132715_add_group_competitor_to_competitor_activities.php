<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGroupCompetitorToCompetitorActivities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('competitor_activities', function (Blueprint $table) {
            $table->integer('groupcompetitor_id')->unsigned()->after('sku');
            $table->foreign('groupcompetitor_id')->references('id')->on('group_competitors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('competitor_activities', function (Blueprint $table) {
            //
        });
    }
}
