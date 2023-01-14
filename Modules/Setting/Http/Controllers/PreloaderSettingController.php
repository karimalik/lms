<?php

namespace Modules\Setting\Http\Controllers;

use App\Traits\ImageStore;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PreloaderSettingController extends Controller
{
    use ImageStore;

    public function index()
    {
        return view('setting::preloader.index');
    }


    public function store(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }

        UpdateGeneralSetting('preloader_status', $request->preloader_status);
        UpdateGeneralSetting('preloader_style', $request->preloader_style);
        UpdateGeneralSetting('preloader_type', $request->preloader_type);

        if ($request->hasFile('preloader_image')) {
            UpdateGeneralSetting('preloader_image', $this->saveImage($request->preloader_image));
        }

        Toastr::success(trans('common.Operation successful'), trans('common.Success'));
        return redirect()->back();
    }

}
