<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->increments('id');
            $table->string('store_id');
            $table->string('store_name_1');
            $table->string('store_name_2')->nullable();
            $table->double('longitude')->nullable();
            $table->double('latitude')->nullable();
            $table->enum('channel', ['Modern Retail', 'Traditional Retail', 'Mother Child & Care']);
            $table->integer('account_id')->unsigned();
            $table->foreign('account_id')->references('id')->on('accounts');
            $table->integer('areaapp_id')->unsigned();
            $table->foreign('areaapp_id')->references('id')->on('area_apps');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('stores');
    }
}
