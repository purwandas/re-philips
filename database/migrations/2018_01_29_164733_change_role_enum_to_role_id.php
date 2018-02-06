<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeRoleEnumToRoleId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('role_id')->nullable()->after('password')->unsigned();
            $table->foreign('role_id')->references('id')->on('roles');
        });

        Schema::table('quiz_targets', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('quiz_targets', function (Blueprint $table) {
            $table->integer('role_id')->nullable()->after('id')->unsigned();
            $table->foreign('role_id')->references('id')->on('roles');
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
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'Driver', 'Helper', 'ACT', 'PPE', 'BDT', 'Salesman Explorer', 'PCE', 'RE Executive', 'RE Support', 'Supervisor', 'Trainer', 'Head Trainer', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'Supervisor Hybrid', 'SMD Additional', 'ASC', 'DM', 'RSM', 'Admin', 'Master']);
        });

        Schema::table('quiz_targets', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        Schema::table('quiz_targets', function (Blueprint $table) {
            $table->enum('role', ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'Driver', 'Helper', 'ACT', 'PPE', 'BDT', 'Salesman Explorer', 'PCE', 'RE Executive', 'RE Support', 'Supervisor', 'Trainer', 'Head Trainer', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'Supervisor Hybrid', 'SMD Additional', 'ASC', 'DM', 'RSM', 'Admin', 'Master']);
        });
    }
}
