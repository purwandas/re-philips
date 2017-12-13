<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedbackQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        Schema::create('feedback_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('feedbackCategory_id')->unsigned();
            $table->foreign('feedbackCategory_id')->references('id')->on('feedback_categories');
            $table->string('question');
            $table->enum('type', ['PK', 'POG', 'POSM']);
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
        Schema::dropIfExists('feedback_questions');
    }
}
