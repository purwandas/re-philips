<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFbCategoryAndFbQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('feedback_categories', function (Blueprint $table) {
            $table->enum('type', ['PK', 'POG', 'POSM']);
        });
        Schema::table('feedback_questions', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('feedback_categories', function (Blueprint $table) {
            //
        });
        Schema::table('feedback_questions', function (Blueprint $table) {
            //
        });
    }
}
