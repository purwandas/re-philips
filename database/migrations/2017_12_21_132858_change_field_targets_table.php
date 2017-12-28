<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFieldTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('targets', function (Blueprint $table) {
            $table->dropForeign(['groupproduct_id']);
            $table->dropColumn(['groupproduct_id', 'type', 'target']);
        });

        Schema::table('targets', function (Blueprint $table) {
            $table->integer('target_pf_mcc')->after('store_id');
            $table->integer('target_mcc')->after('store_id');
            $table->integer('target_pf_pc')->after('store_id');
            $table->integer('target_pc')->after('store_id');
            $table->integer('target_pf_da')->after('store_id');
            $table->integer('target_da')->after('store_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('targets', function (Blueprint $table) {
            //
        });
    }
}
