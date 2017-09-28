<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nik');
            $table->string('name');
            $table->string('email');
            $table->string('password')->nullable();            
            $table->enum('role', ['Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'Driver', 'Helper', 'ACT', 'PPE', 'BDT', 'Salesman Explorer', 'PCE', 'RE Executive', 'RE Support', 'Supervisor', 'Trainer', 'Head Trainer', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'Supervisor Hybrid', 'SMD Additional', 'ASC']);
            $table->enum('status', ['stay', 'mobile'])->nullable();
            $table->text('photo')->nullable();
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
        Schema::dropIfExists('employees');
    }
}
