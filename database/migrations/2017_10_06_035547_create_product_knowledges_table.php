<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductKnowledgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_knowledges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->enum('type', ['Product Knowledge', 'Planogram', 'POSM']);
            $table->string('from');
            $table->string('subject');
            $table->datetime('date');
            $table->string('filename');
            $table->text('file')->nullable();
            $table->enum('target_type', ['All', 'Area', 'Store', 'Promoter']);
            $table->string('target_detail')->nullable();
            $table->integer('total_read');
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
        Schema::dropIfExists('product_knowledges');
    }
}
