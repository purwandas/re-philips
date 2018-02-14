<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHpidAndStatusloginToUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('hp_id')->nullable()->after('remember_token');
            $table->string('jenis_hp')->nullable()->after('remember_token');
            $table->enum('status_login', ['Login', 'Logout'])->nullable()->after('remember_token');
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
            $table->dropColumn('jenis_hp');
            $table->dropColumn('hp_id');
            $table->dropColumn('status_login');
        });
    }
}
