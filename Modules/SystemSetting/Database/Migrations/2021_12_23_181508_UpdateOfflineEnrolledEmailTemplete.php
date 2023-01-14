<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOfflineEnrolledEmailTemplete extends Migration
{

    public function up()
    {
        $check = \Modules\SystemSetting\Entities\EmailTemplate::where('act', 'Offline_Enrolled')->first();
        if ($check) {
            $check->shortcodes = '{"email":"Email Address","password":"Password"}';

            $subject = 'Login Credentials';
            $br = "<br/>";
            $body = ' Please login to ' . url('/') . ' with email {{email}} and password {{password}} ' . $br . "{{footer}}";
            $check->email_body = htmlPart($subject, $body);
            $check->save();
        }
    }

    public function down()
    {
        //
    }
}
