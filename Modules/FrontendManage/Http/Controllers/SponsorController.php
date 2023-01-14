<?php

namespace Modules\FrontendManage\Http\Controllers;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Modules\FrontendManage\Entities\Sponsor;

class SponsorController extends Controller
{
    public function index()
    {
        try {
            $sponsors = Sponsor::all();
            return view('frontendmanage::sponsors', compact('sponsors'));
        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function create()
    {
        return view('frontendmanage::create');
    }

    public function store(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $rules = [
            'title' => 'required',
            'image' => 'required',
        ];
        $this->validate($request, $rules, validationMessage($rules));

        try {
            $sponsor = new Sponsor();
            $sponsor->title = $request->title;
            if ($request->file('image') != "") {
                $name = md5($request->title . rand(0, 1000)) . '.' . 'png';
                $img = Image::make($request->image);
//                $img->resize(100 * 100);
                $upload_path = 'public/uploads/sponsor/';
                $img->save($upload_path . $name);
                $sponsor->image = 'public/uploads/sponsor/' . $name;
            }
            $sponsor->save();
            Toastr::success(trans('sponsor.Sponsor Saved Successfully'));
            return back();
        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function show($id)
    {
        return view('frontendmanage::show');
    }

    public function edit($id)
    {
        try {
            $sponsors = Sponsor::all();

            $sponsor = Sponsor::findOrFail($id);
            return view('frontendmanage::sponsors', compact('sponsors', 'sponsor'));
        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function update(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $rules = [
            'title' => 'required|unique:sponsors,title,' . $request->id,
        ];
        $this->validate($request, $rules, validationMessage($rules));

        try {
            $sponsor = Sponsor::find($request->id);
            $sponsor->title = $request->title;
            if ($request->file('image') != "") {
                $name = md5($request->title . rand(0, 1000)) . '.' . 'png';
                $img = Image::make($request->image);
//                $img->resize(100 * 100);
                $upload_path = 'public/uploads/sponsor/';
                $img->save($upload_path . $name);
                $sponsor->image = 'public/uploads/sponsor/' . $name;
            }
            $sponsor->save();
            Toastr::success(trans('sponsor.Sponsor Updated Successfully'));
            return redirect()->route('frontend.sponsors.index');
        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function destroy($id)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            Sponsor::destroy($id);
            Toastr::success(trans('sponsor.Sponsor Deleted Successfully'));
            return redirect()->route('frontend.sponsors.index');
        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }
}
