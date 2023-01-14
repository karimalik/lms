<?php

namespace Modules\FrontendManage\Http\Controllers;

use App\Traits\ImageStore;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\FrontendManage\Entities\LoginPage;

class LoginPageController extends Controller
{
    use ImageStore;

    public function index()
    {
        $page = LoginPage::getData();
        return view('frontendmanage::loginpage', compact('page'));
    }


    public function store(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $page = LoginPage::first();
        $page->title = $request->title;
        if ($request->banner != null) {

            if ($request->file('banner')->extension() == "svg") {
                $file = $request->file('banner');
                $fileName = md5(rand(0, 9999) . '_' . time()) . '.' . $file->clientExtension();
                $url = 'public/uploads/settings/' . $fileName;
                $file->move(public_path('uploads/settings'), $fileName);
            } else {
                $url = $this->saveImage($request->banner);
            }

            $page->banner = $url;
        }

        $page->slogans1 = $request->slogan1;
        $page->slogans2 = $request->slogan2;
        $page->slogans3 = $request->slogan3;




        $page->reg_title = $request->reg_title;
        if ($request->reg_banner != null) {

            if ($request->file('reg_banner')->extension() == "svg") {
                $file = $request->file('reg_banner');
                $fileName = md5(rand(0, 9999) . '_' . time()) . '.' . $file->clientExtension();
                $url = 'public/uploads/settings/' . $fileName;
                $file->move(public_path('uploads/settings'), $fileName);
            } else {
                $url = $this->saveImage($request->reg_banner);
            }

            $page->reg_banner = $url;
        }

        $page->reg_slogans1 = $request->reg_slogan1;
        $page->reg_slogans2 = $request->reg_slogan2;
        $page->reg_slogans3 = $request->reg_slogan3;



        $page->forget_title = $request->forget_title;
        if ($request->forget_banner != null) {

            if ($request->file('forget_banner')->extension() == "svg") {
                $file = $request->file('forget_banner');
                $fileName = md5(rand(0, 9999) . '_' . time()) . '.' . $file->clientExtension();
                $url = 'public/uploads/settings/' . $fileName;
                $file->move(public_path('uploads/settings'), $fileName);
            } else {
                $url = $this->saveImage($request->forget_banner);
            }

            $page->forget_banner = $url;
        }

        $page->forget_slogans1 = $request->forget_slogan1;
        $page->forget_slogans2 = $request->forget_slogan2;
        $page->forget_slogans3 = $request->forget_slogan3;
        $page->save();

        Toastr::success(trans('common.Operation successful'), trans('common.Success'));
        return redirect()->back();
    }


}
