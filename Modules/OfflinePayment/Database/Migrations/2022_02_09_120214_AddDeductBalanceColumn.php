<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\SystemSetting\Entities\EmailTemplate;

class AddDeductBalanceColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offline_payments', function ($table) {
            if (!Schema::hasColumn('offline_payments', 'type')) {
                $table->string('type')->default('Add');
            }
        });


        $template = EmailTemplate::where('act', 'Deduct_Payment')->first();
        if (!$template) {
            $template = new EmailTemplate();
            $template->act = 'Deduct_Payment';
        }

        $shortCode = '{"amount":"Amount","time":"Deduct Time"}';
        $subject = 'Deduct Amount From Fund';
        $br = "<br/>";
        $body = "Subject: {{subject}} " . $br . "{{amount}} has deduct successfully from your fund at {{time}} By Admin." . $br . "{{footer}}";
        $template->name = $subject;
        $template->subj = $subject;
        $template->shortcodes = $shortCode;
        $template->status = 1;

        $template->email_body = htmlPart($subject, $body);
        $template->save();


        DB::statement("INSERT INTO `role_email_templates` (`role_id`, `template_act`, `created_at`, `updated_at`) VALUE
                        (3,'Deduct_Payment', now(), now()),(2,'Deduct_Payment', now(), now())");

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
