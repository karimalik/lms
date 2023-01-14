<?php

namespace Modules\PopupContent\Http\Controllers;

use App\Traits\ImageStore;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\PopupContent\Entities\PopupContent;

class PopupContentController extends Controller
{
    use ImageStore;

    public function index()
    {
        try {
            $popup = PopupContent::getData();
            return view('popupcontent::popup_content.index', compact('popup'));

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function update(Request $request)
    {

        try {
            $popup = PopupContent::first();

            $popup->title = $request->title;
            $popup->btn_txt = $request->btn_txt;
            $popup->link = $request->link;
            $popup->status = $request->status;

            $popup->message = $request->message;
            if ($request->hasFile('file')) {
                $image = $this->saveImage($request->file);
                $popup->image = $image;
            }
            $popup->save();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));

            return back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }

    }
}
