<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteGroupKategoriFieldGroupCompetitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_competitors', function (Blueprint $table) {
            $table->dropForeign(['groupproduct_id']);
            $table->dropColumn(['groupproduct_id', 'kategori']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_competitors', function (Blueprint $table) {
            //
        });
    }
}
