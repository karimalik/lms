<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLmsIdToAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $key => $table_name) {
            $table_name = json_encode(array_values(get_object_vars($table_name)));
            $table_name = str_replace(['["', '"]'], '', $table_name);
            Schema::table($table_name, function (Blueprint $table) use ($table_name) {
                if (!Schema::hasColumn($table_name, 'lms_id')) {
                    $table->tinyInteger('lms_id')->default(1);
                }
            });
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
