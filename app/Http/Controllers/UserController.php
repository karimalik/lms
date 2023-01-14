<?php

namespace App\Http\Controllers;

use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Modules\Localization\Entities\Language;
use Modules\Newsletter\Http\Controllers\AcelleController;
use Modules\Newsletter\Http\Controllers\GetResponseController;
use Modules\Newsletter\Http\Controllers\MailchimpController;
use Modules\SystemSetting\Entities\Currency;

class UserController extends Controller
{
    public function __construct()
    {

    }

    public function changePassword()
    {
        try {
            $user = User::where('id', Auth::id())->with('role')->first();
            $currencies = Currency::whereStatus('1')->get();
            $languages = Language::whereStatus('1')->get();
            $countries = DB::table('countries')->whereActiveStatus(1)->get();
            $states = DB::table('states')->where('country_id', $user->country)->where('id', $user->state)->select('id', 'name')->get();
            $cities = DB::table('spn_cities')->where('state_id', $user->state)->where('id', $user->city)->select('id', 'name')->get();
            return view('backend.changePassword', compact('cities', 'user', 'currencies', 'languages', 'countries', 'states'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => "required",
            'new_password' => "required|same:confirm_password|min:8|different:current_password",
            'confirm_password' => 'required|min:8'
        ]);

        try {
            if (demoCheck()) {
                return redirect()->back();
            }
            $user = Auth::user();
            if (Hash::check($request->current_password, $user->password)) {

                $user->password = Hash::make($request->new_password);
                $result = $user->save();

                if ($result) {
                    send_email($user, $type = 'PASS_UPDATE', $shortcodes = ['time' => now()->format(Settings('active_date_format') . ' H:i:s A')]);

                    Auth::logout();
                    session(['role_id' => '']);
                    Session::flush();
                    Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                    return redirect()->back();

                } else {
                    Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
                    return redirect()->back();

                }
            } else {
                Toastr::error('Current password not match!', 'Failed');
                return redirect()->back();

            }
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function update_user(Request $request)
    {

        $request->validate([
            'name' => "required",
            'email' => "required|unique:users,email," . Auth::id(),
            'phone' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:1|unique:users,phone,' . Auth::id(),
        ]);

        try {
            if (demoCheck()) {
                return redirect()->back();
            }
            $user = Auth::user();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->city = $request->city;
            $user->state = $request->state;
            $user->zip = $request->zip;
            $user->currency_id = $request->currency;
            $user->language_id = $request->language;

            $language = Language::find($request->language);

            $user->language_code = $language->code;
            $user->language_name = $language->name;

            $user->country = $request->country;
            $user->facebook = $request->facebook;
            $user->twitter = $request->twitter;
            $user->linkedin = $request->linkedin;
            $user->instagram = $request->instagram;
            $user->short_details = $request->short_details;

            $user->subscription_method = $request->subscription_method;
            $user->subscription_api_key = $request->subscription_api_key;
            $sub_status = false;
            if ($request->subscription_method == "Mailchimp") {
                $mailchimp = new MailchimpController();
                $mailchimp->mailchimp($request->subscription_api_key);
                $sub_status = $mailchimp->connected;

            } elseif ($request->subscription_method == "GetResponse") {
                $getResponse = new GetResponseController();
                $getResponse->getResponseApi($request->subscription_api_key);
                $sub_status = $getResponse->connected;
            } elseif ($request->subscription_method == "Acelle") {

                $acelleController = new AcelleController();
                $acelle = $acelleController->getAcelleApiResponse();
                $sub_status = $acelleController->connected;

            }

            $user->subscription_api_status = $sub_status;

            if (!empty($request->about)) {
                $user->about = $request->about;
            }
            $fileName = "";
            if ($request->file('image') != "") {
                $file = $request->file('image');
                $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/staff/', $fileName);
                $fileName = 'public/uploads/staff/' . $fileName;
                $user->image = $fileName;
            }
            $user->save();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }

    public function changeLanguage($language_code)
    {

        $language = Language::where('status', 1)->where('code', $language_code)->first();
        if ($language) {
            if (Auth::check()) {
                //set session & set user
                $user = Auth::user();
                $user->language_id = $language->id;
                $user->language_code = $language->code;
                $user->language_name = $language->name;
                $user->language_rtl = $language->rtl;
                $user->save();
            } else {
                Session::put('locale', $language->code);
                Session::put('language_name', $language->name);
                Session::put('language_rtl', $language->rtl);

            }
            App::setLocale($language->code);
            $success_msg = trans('setting.Successfully changed language');
            Toastr::success($success_msg, trans('common.Success'));
            return redirect()->back();
        } else {
            Toastr::error('Failed to change language', trans('common.Failed'));
            return redirect()->back();
        }
    }

}
