<?php

namespace Modules\SystemSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Modules\Setting\Model\GeneralSetting;
use Modules\SystemSetting\Entities\EmailSetting;
use Modules\SystemSetting\Entities\EmailTemplate;


class SystemSettingController extends Controller
{
    public function sendTestMail(Request $request)
    {
        $request->validate([
            'type' => "required",
            'testMailAddress' => "required",
        ]);
        try {

            $email = $request->get('testMailAddress');
            $type = $request->get('type');
            $config = EmailSetting::findOrFail($type);

            if ($config->email_engine_type == 'php') {

                $status = send_php_mail($email, '', $config->from_name, "Test Mail", "Test Mail");

                if ($status) {
                    Toastr::success('Email Sent Successfully', 'Success');
                } else {
                    Toastr::error('Something Went Wrong', "Error");
                }
                return redirect()->back();

            } elseif ($config->email_engine_type == 'smtp') {

                send_smtp_mail($config, $email, 'Tester', $config->from_email, $config->from_name, 'Test Mail', 'This is a test mail');
                Toastr::success('Email Sent Successfully', 'Success');
                return redirect()->back();

            } elseif ($config->email_engine_type == 'sendgrid') {
                $data['body'] = "Test Mail";
                $emailSendGrid = new \SendGrid\Mail\Mail();
                $emailSendGrid->setFrom($config->from_email, $config->from_name);
                $emailSendGrid->setSubject("Test mail");
                $emailSendGrid->addTo($email, $email);
                $emailSendGrid->addContent(
                    "text/html", (string)view('partials.email', $data)
                );
                $sendgrid = new \SendGrid($config->api_key);
                $response = $sendgrid->send($emailSendGrid);

                if ($response->statusCode() == 202) {
                    Toastr::success('Email Sent successful', 'Success');
                    return redirect()->back();
                } else {
                    $area = json_decode($response->body(), true);
                    $msg = str_replace("'", " ", $area['errors'][0]['message']);

                    Toastr::error($msg, 'Failed');
                    return redirect()->back();
                }
            }
        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }


    }

    public function updateEmailSetting(Request $request)
    {

        if (demoCheck()) {
            return redirect()->back();
        }
        // return $request;
        $request->validate([
            'id' => "required",
            'api_key' => "required_if:mail_driver,sendgrid",
            'from_name' => "required",
            'from_email' => "required|email",
            'mail_driver' => "required_if:mail_driver,smtp",
            'mail_host' => "required_if:mail_driver,smtp",
            'mail_port' => "required_if:mail_driver,smtp|nullable|numeric",
            'mail_username' => "required_if:mail_driver,smtp",
            'mail_password' => "required_if:mail_driver,smtp",
            'mail_encryption' => "required_if:mail_driver,smtp",
            'active_status' => "required",
        ]);

        DB::beginTransaction();

        try {
            if (Config::get('app.app_sync')) {
                Toastr::error('For demo version you can not change this !', 'Failed');
                return redirect()->back();
            } else {
                switch ($request->mail_driver) {
                    case 'php':
                        $email_setting = EmailSetting::firstOrNew(array('mail_driver' => $request->mail_driver));
                        $email_setting->email_engine_type = $request->mail_driver;
                        $email_setting->from_name = $request->from_name;
                        $email_setting->from_email = $request->from_email;
                        $email_setting->save();
                        SaasEnvSetting(SaasDomain(), 'MAIL_DRIVER', 'sendmail');
                        break;
                    case 'sendgrid':
                        $email_setting = EmailSetting::firstOrNew(array('mail_driver' => $request->mail_driver));
                        $email_setting->email_engine_type = $request->mail_driver;
                        $email_setting->from_name = $request->from_name;
                        $email_setting->from_email = $request->from_email;
                        $email_setting->api_key = $request->api_key;
                        $email_setting->save();
                        break;
                    case 'smtp':
                        $key1 = 'MAIL_USERNAME';
                        $key2 = 'MAIL_PASSWORD';
                        $key3 = 'MAIL_ENCRYPTION';
                        $key4 = 'MAIL_PORT';
                        $key5 = 'MAIL_HOST';
                        $key6 = 'MAIL_DRIVER';
                        $key7 = 'MAIL_FROM_ADDRESS';

                        $value1 = $request->mail_username;
                        $value2 = $request->mail_password;
                        $value3 = $request->mail_encryption;
                        $value4 = $request->mail_port;
                        $value5 = $request->mail_host;
                        $value6 = $request->mail_driver;
                        $value7 = $request->from_email;

                        SaasEnvSetting(SaasDomain(), $key1, $value1);
                        SaasEnvSetting(SaasDomain(), $key2, $value2);
                        SaasEnvSetting(SaasDomain(), $key3, $value3);
                        SaasEnvSetting(SaasDomain(), $key4, $value4);
                        SaasEnvSetting(SaasDomain(), $key5, $value5);
                        SaasEnvSetting(SaasDomain(), $key6, $value6);
                        SaasEnvSetting(SaasDomain(), $key7, $value7);

                        $emailSettData = EmailSetting::firstOrNew(array('mail_driver' => $request->mail_driver));
                        $emailSettData->from_name = $request->from_name;
                        $emailSettData->from_email = $request->from_email;
                        $emailSettData->email_engine_type = $request->mail_driver;

                        $emailSettData->mail_driver = $request->mail_driver;
                        $emailSettData->mail_host = $request->mail_host;
                        $emailSettData->mail_port = $request->mail_port;
                        $emailSettData->mail_username = $request->mail_username;
                        $emailSettData->mail_password = $request->mail_password;
                        $emailSettData->mail_encryption = $request->mail_encryption;

                        $results = $emailSettData->save();
                        break;
                    default:
                        return response()->json(['error' => "Operation Failed"]);
                        break;
                }

                SaasEnvSetting(SaasDomain(), 'MAIL_FROM_NAME', $request->from_name ?? 'infixLMS');
                SaasEnvSetting(SaasDomain(), 'MAIL_FROM_ADDRESS', $request->from_email ?? 'admin@infixlms.com');

                if ($request->active_status == 1) {
                    EmailSetting::where('active_status', 1)->update(['active_status' => 0]);
                    EmailSetting::where('mail_driver', $request->mail_driver)->update(['active_status' => 1]);
                }

                DB::commit();

                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->back();

            }
        } catch (\Exception $e) {
            DB::rollBack();
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function footerTemplateUpdate(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $request->validate([
            'email_template' => "required"
        ]);


        try {
            if (Config::get('app.app_sync')) {
                Toastr::error('For demo version you can not change this !', 'Failed');
                return redirect()->back();
            } else {
                UpdateGeneralSetting('email_template', $request->email_template);
                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->back();
            }

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }

    public function updateEmailTemp(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $request->validate([
            'id' => "required",
            'subj' => "required",
            'email_body' => "required"
        ]);
        try {

            if (Config::get('app.app_sync')) {
                Toastr::error('For demo version you can not change this !', 'Failed');
                return redirect()->back();
            } else {
                // $success = trans('lang.Email Template').' '.trans('lang.Updated').' '.trans('lang.Successfully');

                $template = EmailTemplate::find($request->id);
                $template->subj = $request->subj;
                $template->email_body = $request->email_body;
                $template->save();

            }
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }

    public function allApi()
    {
        return view('systemsetting::api.index');
    }

    public function saveApi(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }

        if ($request->gmap_key) {
            UpdateGeneralSetting('gmap_key', $request->gmap_key);
        }
        if ($request->lat) {
            UpdateGeneralSetting('lat', $request->lat);
        }
        if ($request->lng) {
            UpdateGeneralSetting('lng', $request->lng);
        }
        if ($request->fixer_key) {
            UpdateGeneralSetting('fixer_key', $request->fixer_key);
        }
        if ($request->zoom_level) {
            UpdateGeneralSetting('zoom_level', $request->zoom_level);
        }

        if ($request->fcm_key) {
            UpdateGeneralSetting('fcm_key', $request->fcm_key);
        }

        GenerateGeneralSetting(SaasDomain());

        Toastr::success(trans('setting.Api Settings Saved Successfully'));
        return back();
    }
}

