<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTargetEnumToStringOnQuizsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quizs', function (Blueprint $table) {
            $table->dropColumn('target');
        });
        Schema::table('quizs', function (Blueprint $table) {
            $table->string('target');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quizs', function (Blueprint $table) {
            $table->dropColumn('target');
        });
        Schema::table('quizs', function (Blueprint $table) {
            $table->enum('target', array('Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'Salesman Explorer', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC'));
        });
    }
}
