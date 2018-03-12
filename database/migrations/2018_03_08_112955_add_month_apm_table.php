<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMonthApmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `apms` MODIFY `month_minus_1_value` DOUBLE;');
        DB::statement('ALTER TABLE `apms` MODIFY `month_minus_2_value` DOUBLE;');
        DB::statement('ALTER TABLE `apms` MODIFY `month_minus_3_value` DOUBLE;');

        Schema::table('apms', function (Blueprint $table) {
            $table->double('month_minus_4_value')->after('product_id');
            $table->double('month_minus_5_value')->after('product_id');
            $table->double('month_minus_6_value')->after('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('apms', function (Blueprint $table) {
            //
        });
    }
}
