<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsReadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_reads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('news_id')->unsigned();
            $table->foreign('news_id')->references('id')->on('news');            
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news_reads');
    }
}
