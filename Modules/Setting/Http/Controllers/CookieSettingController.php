<?php

namespace Modules\Setting\Http\Controllers;

use App\Traits\ImageStore;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Setting\Entities\CookieSetting;
use Modules\Setting\Model\GeneralSetting;

class CookieSettingController extends Controller
{
    use ImageStore;

    public function index()
    {
        $setting = CookieSetting::getData();
        return view('setting::cookie_setting', compact('setting'));
    }


    public function store(Request $request)
    {

        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            $cookie = CookieSetting::first();
            if ($cookie) {
                $cookie->allow = $request->allow;
                $cookie->btn_text = $request->btn_text;
                $cookie->bg_color = $request->bg_color;
                $cookie->text_color = $request->text_color;
                $cookie->details = $request->details;

                if ($request->image != null) {
                    if ($request->file('image')->extension() == "svg") {
                        $file = $request->file('image');
                        $fileName = md5(rand(0, 9999) . '_' . time()) . '.' . $file->clientExtension();
                        $url1 = 'public/uploads/settings/' . $fileName;
                        $file->move(public_path('uploads/settings'), $fileName);
                    } else {
                        $url1 = $this->saveImage($request->image);
                    }
                    $cookie->image = $url1;
                }
                $cookie->save();

                UpdateGeneralSetting('cookie_status', $request->allow ?? 0);
            }
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {

            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }


    }

}
