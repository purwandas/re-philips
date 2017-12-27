<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDedicateToSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('summary_display_shares', function (Blueprint $table) {
            $table->enum('dedicate', ['DA', 'PC', 'MCC', 'HYBRID'])->after('store_id');
        });
        Schema::table('summary_free_products', function (Blueprint $table) {
            $table->enum('dedicate', ['DA', 'PC', 'MCC', 'HYBRID'])->after('store_id');
        });
        Schema::table('summary_ret_consuments', function (Blueprint $table) {
            $table->enum('dedicate', ['DA', 'PC', 'MCC', 'HYBRID'])->after('store_id');
        });
        Schema::table('summary_ret_distributors', function (Blueprint $table) {
            $table->enum('dedicate', ['DA', 'PC', 'MCC', 'HYBRID'])->after('store_id');
        });
        Schema::table('summary_sell_ins', function (Blueprint $table) {
            $table->enum('dedicate', ['DA', 'PC', 'MCC', 'HYBRID'])->after('store_id');
        });
        Schema::table('summary_sell_outs', function (Blueprint $table) {
            $table->enum('dedicate', ['DA', 'PC', 'MCC', 'HYBRID'])->after('store_id');
        });
        Schema::table('summary_sohs', function (Blueprint $table) {
            $table->enum('dedicate', ['DA', 'PC', 'MCC', 'HYBRID'])->after('store_id');
        });
        Schema::table('summary_target_actuals', function (Blueprint $table) {
            $table->enum('dedicate', ['DA', 'PC', 'MCC', 'HYBRID'])->after('store_id');
        });
        Schema::table('summary_tbats', function (Blueprint $table) {
            $table->enum('dedicate', ['DA', 'PC', 'MCC', 'HYBRID'])->after('store_id');
        });
        Schema::table('summary_tbats', function (Blueprint $table) {
            $table->enum('destination_dedicate', ['DA', 'PC', 'MCC', 'HYBRID'])->after('store_destination_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('summary_display_shares', function (Blueprint $table) {
            $table->dropColumn('dedicate');
        });
        Schema::table('summary_free_products', function (Blueprint $table) {
            $table->dropColumn('dedicate');
        });
        Schema::table('summary_ret_consuments', function (Blueprint $table) {
            $table->dropColumn('dedicate');
        });
        Schema::table('summary_ret_distributors', function (Blueprint $table) {
            $table->dropColumn('dedicate');
        });
        Schema::table('summary_sell_ins', function (Blueprint $table) {
            $table->dropColumn('dedicate');
        });
        Schema::table('summary_sell_outs', function (Blueprint $table) {
            $table->dropColumn('dedicate');
        });
        Schema::table('summary_sohs', function (Blueprint $table) {
            $table->dropColumn('dedicate');
        });
        Schema::table('summary_target_actuals', function (Blueprint $table) {
            $table->dropColumn('dedicate');
        });
        Schema::table('summary_tbats', function (Blueprint $table) {
            $table->dropColumn('dedicate');
            $table->dropColumn('destination_dedicate');
        });

    }
}
