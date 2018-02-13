<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeKepemilikanTokoToEnumOnStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('kepemilikan_toko');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->enum('kepemilikan_toko', ['Milik Sendiri', 'Sewa'])->nullable()->after('no_telp_pemilik_toko');
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
            $table->dropColumn('kepemilikan_toko');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->string('kepemilikan_toko')->nullable()->after('no_telp_pemilik_toko');
        });
    }
}
