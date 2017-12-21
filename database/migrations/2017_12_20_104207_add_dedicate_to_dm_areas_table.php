<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDedicateToDmAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dm_areas', function (Blueprint $table) {
            $table->enum('dedicate', ['DA', 'PC', 'MCC', 'HYBRID'])->after('area_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dm_areas', function (Blueprint $table) {
            $table->dropColumn('dedicate');
        });
    }
}
