<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompetitorActivityDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competitor_activity_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('competitoractivity_id')->unsigned();
            $table->foreign('competitoractivity_id')->references('id')->on('competitor_activities');
            $table->integer('groupcompetitor_id')->unsigned();
            $table->foreign('groupcompetitor_id')->references('id')->on('group_competitors');
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
        Schema::dropIfExists('competitor_activity_details');
    }
}
