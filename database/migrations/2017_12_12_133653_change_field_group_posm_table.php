<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFieldGroupPosmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posms', function (Blueprint $table) {
            $table->dropForeign(['groupproduct_id']);
            $table->dropColumn('groupproduct_id');
        });
        Schema::table('posms', function (Blueprint $table) {
            $table->integer('group_id')->unsigned()->after('name');
            $table->foreign('group_id')->references('id')->on('groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posms', function (Blueprint $table) {
            //
        });
    }
}
