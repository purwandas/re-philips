<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupCompetitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_competitors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('kategori')->nullable();
            $table->integer('groupproduct_id')->unsigned();
            $table->foreign('groupproduct_id')->references('id')->on('group_products');
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
        Schema::dropIfExists('group_competitors');
    }
}
