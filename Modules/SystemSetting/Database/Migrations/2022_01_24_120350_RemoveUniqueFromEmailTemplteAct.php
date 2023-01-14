<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUniqueFromEmailTemplteAct extends Migration
{
    public function up()
    {
        try {
            \Illuminate\Support\Facades\DB::unprepared("ALTER TABLE `email_templates` DROP INDEX `email_templates_act_unique`;");
            \Illuminate\Support\Facades\DB::unprepared("ALTER TABLE `email_templates` CHANGE `act` `act` VARCHAR(191) NULL;");
        }catch (\Exception $e){
            \Illuminate\Support\Facades\Log::alert($e->getMessage());
        }
    }


    public function down()
    {
        //
    }
}
