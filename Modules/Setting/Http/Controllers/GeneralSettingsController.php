<?php

namespace Modules\Setting\Http\Controllers;

use App\Traits\ImageStore;
use App\Traits\SendMail;
use App\Traits\SendSMS;
use App\Traits\UploadTheme;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Intervention\Image\Facades\Image;
use Modules\Localization\Entities\Language;
use Modules\Setting\Model\Currency;
use Modules\Setting\Model\DateFormat;
use Modules\Setting\Model\TimeZone;
use Modules\Setting\Repositories\GeneralSettingRepositoryInterface;
use Modules\SystemSetting\Entities\EmailTemplate;


class GeneralSettingsController extends Controller
{
    use ImageStore, SendSMS, SendMail, UploadTheme;

    protected $generalsettingRepository;

    public function __construct(GeneralSettingRepositoryInterface $generalsettingRepository)
    {
        $this->generalsettingRepository = $generalsettingRepository;
    }

    public function update(Request $request)
    {

        if (appMode()) {
            return 2;
        }


        if ($request->site_logo != null) {
            $site_log_sizes = [
                ['640', '1136'],
                ['750', '1334'],
                ['828', '1792'],
                ['1125', '2436'],
                ['1242', '2208'],
                ['1242', '2688'],
                ['1536', '2048'],
                ['1668', '2224'],
                ['1668', '2388'],
                ['2048', '2732'],
            ];
            $url1 = $this->saveImage($request->site_logo);
            if ($request->file('site_logo')->extension() != "svg") {
                foreach ($site_log_sizes as $size) {
                    $rowImage = Image::canvas($size[0], $size[1], '#fff');
                    $rowImage->insert($request->file('site_logo'), 'center');
                    $rowImage->save(public_path("images/icons/splash-{$size[0]}x{$size[1]}.png"));
                }
            }


            $request->merge(["logo" => $url1]);
        }
        if ($request->site_logo2 != null) {
            $url2 = $this->saveImage($request->site_logo2);
            $request->merge(["logo2" => $url2]);
        }
        if ($request->favicon_logo != null) {
            $fav_icon_sizes = [72, 96, 128, 144, 152, 192, 384, 512];
            if ($request->file('favicon_logo')->extension() == "svg") {
                $file3 = $request->file('favicon_logo');
                $fileName3 = md5(rand(0, 9999) . '_' . time()) . '.' . $file3->clientExtension();
                $url = 'public/uploads/settings/' . $fileName3;
                $file3->move(public_path('uploads/settings'), $fileName3);


                foreach ($fav_icon_sizes as $size) {
                    $file3->move(public_path('images/icons/'), "icon-{$size}x{$size}.svg");
                }

            } else {
                $url = $this->saveImage($request->favicon_logo);

                foreach ($fav_icon_sizes as $size) {
                    Image::make($request->file('favicon_logo'))->resize($size, $size)->save(public_path("images/icons/icon-{$size}x{$size}.png"));
                }


            }
            $request->merge(["favicon" => $url]);


        }
        if ($request->address != null) {
            $this->generalsettingRepository->address = $request->address;

            $this->generalsettingRepository->update(['address' => $request->address]);

        }

        $key1 = 'TIME_ZONE';

        if ($request->time_zone_id) {
            $time_zone = TimeZone::find($request->time_zone_id);
            $value1 = $time_zone->code ?? 'Asia/Dhaka';
            UpdateGeneralSetting('active_time_zone', $value1);

            $path = base_path() . "/.env";
            $TIME_ZONE = env($key1);
            if (file_exists($path)) {
                file_put_contents($path, str_replace(
                    "$key1=" . $TIME_ZONE,
                    "$key1=" . $value1,
                    file_get_contents($path)
                ));
            }
        }


        if ($request->site_title) {
            putEnvConfigration('APP_NAME', $request->site_title);
        }

        try {
            $this->generalsettingRepository->update($request->except("_token", "favicon_logo", "site_logo", "site_logo2"));
            $language = Language::find(Settings('language_id'));
            if ($language) {
                UpdateGeneralSetting('language_code', $language->code);
                UpdateGeneralSetting('language_name', $language->name);
                UpdateGeneralSetting('language_rtl', $language->rtl);
            }
            $currency = Currency::find(Settings('currency_id'));
            if ($currency) {
                UpdateGeneralSetting('currency_symbol', $currency->symbol);
                UpdateGeneralSetting('currency_code', $currency->code);
            }
            $date_format = DateFormat::find(Settings('date_format_id'));
            if ($date_format) {
                UpdateGeneralSetting('active_date_format', $date_format->format);
            }

            $user = Auth::user();
            $user->language_id = Settings('language_id');
            $user->language_name = Settings('language_name');
            $user->language_code = Settings('language_code');
            $user->language_rtl = Settings('language_rtl');
            $user->save();


            session()->forget('settings');


            return 1;
        } catch (\Exception $e) {
            return 0;
        }
    }


    public function smtp_gateway_credentials_update(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }

       UpdateGeneralSetting('mail_protocol',$request->mail_protocol);
       UpdateGeneralSetting('mail_signature',$request->mail_signature);
       UpdateGeneralSetting('mail_header',$request->mail_header);
       UpdateGeneralSetting('mail_footer',$request->mail_footer);

        session()->forget('settings');

        if ($request->mail_protocol == 'sendmail') {
            $request->merge(["MAIL_MAILER" => "smtp"]);
        } else {
            $request->merge(["MAIL_MAILER" => $request->mail_protocol]);
        }
        foreach ($request->types as $key => $type) {
            $this->overWriteEnvFile($type, $request[$type]);
        }
        // return back()->with('message-success', __('setting.SMTP Gateways Credentials has been updated Successfully'));
        Toastr::success(__('setting.SMTP Gateways Credentials has been updated Successfully'), trans('common.Success'));
        return redirect()->back();
    }

    public function test_mail_send(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        if (saasEnv('MAIL_USERNAME') != null) {
            $this->sendMailTest($request);
            // return back()->with('message-success', __('setting.Mail has been sent Successfully'));
            Toastr::success(__('setting.Mail has been sent Successfully'), trans('common.Success'));
            return redirect()->back();
        }
        // return back()->with('message-warning', __('setting.Please Configure SMTP settings first'));
        Toastr::warning(__('setting.Please Configure SMTP settings first'), 'Warning');
        return redirect()->back();
    }

    public function socialCreditional(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        if ($request->fbLogin == 1) {
            $request->validate([

                'facebook_client' => "required",
                'facebook_secret' => "required",
            ]);
        } elseif ($request->googleLogin == 1) {
            $request->validate([
                'google_client' => "required",
                'google_secret' => "required"
            ]);
        } else {
            $request->validate([
                'google_client' => "required",
                'google_secret' => "required",
                'facebook_client' => "required",
                'facebook_secret' => "required",
            ]);

        }

        try {

            if (Config::get('app.app_sync')) {
                Toastr::error('For demo version you can not change this !', 'Failed');
                return redirect()->back();
            } else {
                UpdateGeneralSetting('google_client', $request->google_client);
                UpdateGeneralSetting('google_secret', $request->google_secret);
                UpdateGeneralSetting('facebook_client', $request->facebook_client);
                UpdateGeneralSetting('facebook_secret', $request->facebook_secret);
                UpdateGeneralSetting('fbLogin', $request->fbLogin);
                UpdateGeneralSetting('googleLogin', $request->googleLogin);
                session()->forget('settings');

                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->back();
            }

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }

    public function seoSetting(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $request->validate([
            'meta_keywords' => 'required',
            'meta_description' => 'required',

        ]);
        try {
            if (Config::get('app.app_sync')) {
                Toastr::error('For demo version you can not change this !', 'Failed');
                return redirect()->back();
            } else {

                UpdateGeneralSetting('meta_keywords', $request->meta_keywords);
                UpdateGeneralSetting('meta_description', $request->meta_description);


                session()->forget('settings');

                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->back();
            }

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }

    public function footerEmailConfig()
    {
        return view('setting::emails.email_template');
    }

    public function EmailTemp()
    {

        $templates = EmailTemplate::all();
        return view('setting::emails.email_temp', compact('templates'));
    }

    public function aboutSystem()
    {
        return view('setting::aboutSystem');
    }


}
