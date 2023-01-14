<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUniqueFromCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            \Illuminate\Support\Facades\DB::unprepared("ALTER TABLE `categories` DROP INDEX `categories_name_unique`;");
            \Illuminate\Support\Facades\DB::unprepared("ALTER TABLE `categories` DROP INDEX `categories_name_index`;");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error($e);
        }
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
