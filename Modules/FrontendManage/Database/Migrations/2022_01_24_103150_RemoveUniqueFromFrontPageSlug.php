<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUniqueFromFrontPageSlug extends Migration
{

    public function up()
    {
        try {
            \Illuminate\Support\Facades\DB::unprepared("ALTER TABLE `front_pages` DROP INDEX `front_pages_slug_unique`;");
            \Illuminate\Support\Facades\DB::unprepared("ALTER TABLE `front_pages` CHANGE `slug` `slug` VARCHAR(191) NULL;");
        }catch (\Exception $e){
            \Illuminate\Support\Facades\Log::alert($e->getMessage());
        }
    }


    public function down()
    {
        //
    }
}
