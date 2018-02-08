<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeGradingEnumToGradingId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('grading');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('grading_id')->nullable()->after('password')->unsigned();
            $table->foreign('grading_id')->references('id')->on('gradings');
        });

        Schema::table('quiz_targets', function (Blueprint $table) {
            $table->dropColumn('grading');
        });

        Schema::table('quiz_targets', function (Blueprint $table) {
            $table->integer('grading_id')->nullable()->after('id')->unsigned();
            $table->foreign('grading_id')->references('id')->on('gradings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['grading_id']);
            $table->dropColumn('grading_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('grading', ['Associate', 'Starfour', 'Non-Starfour']);
        });

        Schema::table('quiz_targets', function (Blueprint $table) {
            $table->dropForeign(['grading_id']);
            $table->dropColumn('grading_id');
        });

        Schema::table('quiz_targets', function (Blueprint $table) {
            $table->enum('grading', ['Associate', 'Starfour', 'Non-Starfour']);
        });
    }
}
