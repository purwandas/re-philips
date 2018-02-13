<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('role');
            $table->enum('role_group', ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'Driver', 'Helper', 'ACT', 'PPE', 'BDT', 'Salesman Explorer', 'PCE', 'RE Executive', 'RE Support', 'Supervisor', 'Trainer', 'Head Trainer', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'Supervisor Hybrid', 'SMD Additional', 'ASC', 'DM', 'RSM', 'Admin', 'Master']);
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
        Schema::dropIfExists('roles');
    }
}
