<?php

namespace Modules\FrontendManage\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Modules\FrontendManage\Entities\HomePageFaq;

class HomePageFaqController extends Controller
{
    public function index()
    {
        try {
            $faqs = HomePageFaq::orderBy('order', 'asc')->get();
            return view('frontendmanage::faq.index', compact('faqs'));

        } catch (\Exception $e) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();

        }
    }


    public function store(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $request->validate([
            'question' => ['required', Rule::unique('home_page_faqs', 'question')->when(isModuleActive('LmsSaas'), function ($q) {
                return $q->where('lms_id', app('institute')->id);
            })],
            'answer' => 'required'
        ]);

        try {
            $total = HomePageFaq::latest()->count();
            $faq = new HomePageFaq;
            $faq->question = $request->question;
            $faq->answer = $request->answer;
            $faq->order = $total + 1;
            $faq->save();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));

            return redirect()->back();

        } catch (\Exception $e) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));

            return redirect()->back();
        }
    }


    public function update(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $request->validate([
            'question' => ['required', Rule::unique('home_page_faqs', 'question')->ignore($request->question, 'question')->where('id', $request->id)->where('id')->when(isModuleActive('LmsSaas'), function ($q) {
                return $q->where('lms_id', app('institute')->id);
            })],
            'answer' => 'required',
        ]);
        $faq = HomePageFaq::findOrFail($request->id);

        try {
            $faq->question = $request->question;
            $faq->answer = $request->answer;
            $faq->order = $request->order;
            $faq->save();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));

            return redirect()->back();

        } catch (\Exception $e) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));

            return redirect()->back();

        }
    }


    public function destroy(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            $id = $request->id;
            $faq = HomePageFaq::find($id);
            $faq->delete();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));

            return redirect()->back();

        } catch (\Exception $e) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }

    public function changeFaqPosition(Request $request)
    {
        if (demoCheck()) {
            return false;
        }
        $ids = $request->get('ids');
        if (count($ids) != 0) {
            foreach ($ids as $key => $id) {
                $chapter = HomePageFaq::find($id);
                if ($chapter) {
                    $chapter->order = $key + 1;
                    $chapter->save();
                }
            }
        }
        return true;
    }
}
