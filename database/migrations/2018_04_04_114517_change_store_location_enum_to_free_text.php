<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeStoreLocationEnumToFreeText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('lokasi_toko');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->string('lokasi_toko')->nullable()->after('tipe_transaksi_2');
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
            $table->dropColumn('lokasi_toko');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->enum('lokasi_toko', ['Mall', 'ITC', 'Pasar'])->nullable()->after('tipe_transaksi_2');
        });
    }
}
