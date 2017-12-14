<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedbackAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedback_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('assessor_id')->unsigned();
            $table->foreign('assessor_id')->references('id')->on('users');
            $table->integer('promoter_id')->unsigned();
            $table->foreign('promoter_id')->references('id')->on('users');
            $table->integer('feedbackQuestion_id')->unsigned();
            $table->foreign('feedbackQuestion_id')->references('id')->on('feedback_questions');
            $table->integer('answer');
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
        Schema::dropIfExists('feedback_answers');
    }
}
