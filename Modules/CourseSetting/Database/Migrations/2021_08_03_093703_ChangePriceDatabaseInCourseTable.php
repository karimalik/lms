<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePriceDatabaseInCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
           \Illuminate\Support\Facades\DB::statement("ALTER TABLE `courses` CHANGE `price` `price` DOUBLE(255,2) NOT NULL DEFAULT '0.00', CHANGE `discount_price` `discount_price` DOUBLE(255,2) NULL DEFAULT NULL;");
        });

        Schema::table(' virtual_classes', function (Blueprint $table) {
           \Illuminate\Support\Facades\DB::statement("ALTER TABLE `virtual_classes` CHANGE `fees` `fees` DOUBLE(255,2) NOT NULL DEFAULT '0.00';");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
