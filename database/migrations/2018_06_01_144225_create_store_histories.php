<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_id')->unsigned();
            $table->foreign('store_id')->references('id')->on('stores');
            // $table->string('store_re_id');
            // $table->string('store_name_1');
            // $table->string('store_name_2')->nullable();
            // $table->enum('dedicate', ['DA', 'PC', 'MCC', 'HYBRID'])->nullable();
            // $table->integer('classification_id')->nullable()->unsigned();
            // $table->foreign('classification_id')->references('id')->on('classifications');
            // $table->double('longitude')->nullable();
            // $table->double('latitude')->nullable();
            // $table->text('address')->nullable();
            // $table->integer('subchannel_id')->nullable()->unsigned();
            // $table->foreign('subchannel_id')->references('id')->on('sub_channels');
            // $table->integer('district_id')->unsigned();
            // $table->foreign('district_id')->references('id')->on('districts');
            // $table->integer('user_id')->nullable()->unsigned();
            // $table->foreign('user_id')->references('id')->on('users');            
            // $table->string('no_telp_toko')->nullable();
            // $table->string('no_telp_pemilik_toko')->nullable();
            // $table->enum('kepemilikan_toko', ['Milik Sendiri', 'Sewa'])->nullable();
            // $table->enum('kondisi_toko', ['Ada AC', 'Tidak Ada AC'])->nullable();
            // $table->enum('tipe_transaksi', ['Transaksi via Mesin/Kasir', 'Nota Manual'])->nullable();
            // $table->enum('tipe_transaksi_2', ['Konsumen Langsung', 'Online', 'Grosir'])->nullable();
            // $table->string('lokasi_toko')->nullable();
            // $table->text('photo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_histories');
    }
}
