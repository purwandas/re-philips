<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDataPemilikTokoToStores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('kepemilikan_toko')->nullable()->after('classification');
            $table->string('no_telp_pemilik_toko')->nullable()->after('classification');
            $table->string('no_telp_toko')->nullable()->after('classification');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['no_telp_toko', 'no_telp_pemilik_toko', 'kepemilikan_toko']);
        });
    }
}
