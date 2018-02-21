<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('store_id');
            $table->string('product_id');
            $table->integer('month_minus_3_value');
            $table->integer('month_minus_2_value');
            $table->integer('month_minus_1_value');
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
        Schema::dropIfExists('apms');
    }
}
