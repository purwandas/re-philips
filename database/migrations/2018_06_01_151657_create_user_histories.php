<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('nik')->nullable();
            $table->string('name');
            $table->string('email');
            $table->string('password')->nullable();
            $table->integer('grading_id')->unsigned();
            $table->foreign('grading_id')->references('id')->on('gradings');
            $table->text('certificate')->nullable();
            $table->integer('role_id')->unsigned();
            $table->foreign('role_id')->references('id')->on('roles');            
            $table->enum('status', ['stay', 'mobile'])->nullable();
            $table->date('join_date')->nullable();
            $table->text('photo')->nullable();
            $table->rememberToken();
            $table->string('fcm_token')->nullable();
            $table->enum('status_login', ['Login', 'Logout'])->nullable();
            $table->string('jenis_hp')->nullable();
            $table->string('hp_id')->nullable();
            $table->integer('is_resign');
            $table->string('alasan_resign')->nullable();            
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
        Schema::dropIfExists('user_histories');
    }
}
