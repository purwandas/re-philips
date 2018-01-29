<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCheckInAndOutAddresToVisitPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visit_plans', function (Blueprint $table) {
            $table->string('check_out_location')->nullable()->after('visit_status');
            $table->string('check_in_location')->nullable()->after('visit_status');
            $table->time('check_out')->nullable()->after('visit_status');
            $table->time('check_in')->nullable()->after('visit_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visit_plans', function (Blueprint $table) {
            //
        });
    }
}
