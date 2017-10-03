<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFieldKategoriGroupCompetitors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_competitors', function (Blueprint $table) {
            //
            $table->dropColumn('kategori');
        });

        Schema::table('group_competitors', function (Blueprint $table) {
            //
            $table->enum('kategori', ['Male Grooming', 'Beauty'])->nullable()->after('groupproduct_id');
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
