<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceToAllSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE sell_out_details ADD price DOUBLE DEFAULT 0;');
        DB::statement('ALTER TABLE sell_in_details ADD price DOUBLE DEFAULT 0;');
        DB::statement('ALTER TABLE ret_distributor_details ADD price DOUBLE DEFAULT 0;');
        DB::statement('ALTER TABLE ret_consument_details ADD price DOUBLE DEFAULT 0;');
        DB::statement('ALTER TABLE free_product_details ADD price DOUBLE DEFAULT 0;');
        DB::statement('ALTER TABLE tbat_details ADD price DOUBLE DEFAULT 0;');
        DB::statement('ALTER TABLE soh_details ADD price DOUBLE DEFAULT 0;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE sell_out_details DROP COLUMN price;');
        DB::statement('ALTER TABLE sell_in_details DROP COLUMN price DOUBLE;');
        DB::statement('ALTER TABLE ret_distributor_details DROP COLUMN price DOUBLE;');
        DB::statement('ALTER TABLE ret_consument_details DROP COLUMN price DOUBLE;');
        DB::statement('ALTER TABLE free_product_details DROP COLUMN price DOUBLE;');
        DB::statement('ALTER TABLE tbat_details DROP COLUMN price DOUBLE;');
        DB::statement('ALTER TABLE soh_details DROP COLUMN price DOUBLE;');
    }
}
