<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\SystemSetting\Entities\EmailTemplate;

class AddCourseRefundEmailTemplete extends Migration
{

    public function up()
    {
        EmailTemplate::insert([
            'act' => 'Enroll_Refund',
            'name' => 'Course Enroll Refund By Admin',
            'subj' => 'Course Enroll Refund By Admin',
            'email_body' => 'You have enrolled {{course}} on this course . Admin refund your enrollment   at {{time}}. {{footer}} ',
            'shortcodes' => '{"course":"Course Name","time":"Refund Time"}',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('role_email_templates')->insert([
            'role_id' => 1,
            'template_act' => 'Enroll_Refund',
            'status' => 1
        ]);

        DB::table('role_email_templates')->insert([
            'role_id' => 2,
            'template_act' => 'Enroll_Refund',
            'status' => 1
        ]);

        DB::table('role_email_templates')->insert([
            'role_id' => 3,
            'template_act' => 'Enroll_Refund',
            'status' => 1
        ]);
    }


    public function down()
    {
        //
    }
}
