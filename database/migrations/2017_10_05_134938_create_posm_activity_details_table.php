<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePosmActivityDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posm_activity_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('posmactivity_id')->unsigned();
            $table->foreign('posmactivity_id')->references('id')->on('posm_activities');
            $table->integer('posm_id')->unsigned();
            $table->foreign('posm_id')->references('id')->on('posms');
            $table->integer('quantity');
            $table->text('photo');
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
        Schema::dropIfExists('posm_activity_details');
    }
}
