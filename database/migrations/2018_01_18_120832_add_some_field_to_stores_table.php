<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeFieldToStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->enum('lokasi_toko', ['Mall', 'ITC', 'Pasar'])->after('kepemilikan_toko')->nullable();
            $table->enum('tipe_transaksi_2', ['Konsumen Langsung', 'Online', 'Grosir'])->after('kepemilikan_toko')->nullable();
            $table->enum('tipe_transaksi', ['Transaksi via Mesin/Kasir', 'Nota Manual'])->after('kepemilikan_toko')->nullable();
            $table->enum('kondisi_toko', ['Ada AC', 'Tidak Ada AC'])->after('kepemilikan_toko')->nullable();
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
            $table->dropColumn('tipe_transaksi');
            $table->dropColumn('tipe_transaksi_2');
            $table->dropColumn('kondisi_toko');
        });
    }
}
