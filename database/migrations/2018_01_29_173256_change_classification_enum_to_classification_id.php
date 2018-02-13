<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeClassificationEnumToClassificationId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('classification');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->integer('classification_id')->nullable()->after('dedicate')->unsigned();
            $table->foreign('classification_id')->references('id')->on('classifications');
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
            $table->dropForeign(['classification_id']);
            $table->dropColumn('classification_id');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->enum('classification', ['Don`t have classification', 'New Store', 'Gold', 'Platinum', 'Silver', 'Bronze', 'Risna'])->after('user_id')->nullable();
        });
    }
}
