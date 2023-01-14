<?php

namespace Modules\Quiz\Http\Controllers;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Quiz\Entities\QuestionGroup;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        try {
            $user = Auth::user();
            if ($user->role_id == 1) {
                $groups = QuestionGroup::latest()->get();
            } else {
                $groups = QuestionGroup::where('user_id', $user->id)->latest()->get();
            }
            return view('quiz::index', compact('groups'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function store(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $rules = [
            'title' => ['required', Rule::unique('question_groups', 'title')->when(isModuleActive('LmsSaas'), function ($q) {
                return $q->where('lms_id', app('institute')->id);
            })],
        ];
        $this->validate($request, $rules, validationMessage($rules));

        try {
            $group = new QuestionGroup();
            $group->title = $request->title;
            $group->user_id = Auth::user()->id;
            $result = $group->save();
            if ($result) {
                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->back();
            } else {
                Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
                return redirect()->back();
                // return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
            }
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            // $group = QuestionGroup::find($id);

            $group = QuestionGroup::find($id);

            $groups = QuestionGroup::get();
            return view('quiz::index', compact('groups', 'group'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function update(Request $request, $id)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $rules = [
            'title' => ['required', Rule::unique('question_groups', 'title')->ignore($request->title, 'title')->where('id', $request->id)->when(isModuleActive('LmsSaas'), function ($q) {
                return $q->where('lms_id', app('institute')->id);
            })],
        ];
        $this->validate($request, $rules, validationMessage($rules));

        try {

            $group = QuestionGroup::find($request->id);

            $group->title = $request->title;
            $result = $group->save();
            if ($result) {
                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect('quiz/question-group');
            } else {
                Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
                return redirect()->back();
            }
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        if (demoCheck()) {
            return redirect()->back();
        }


        try {

            // $group = QuestionGroup::destroy($id);

            $group = QuestionGroup::destroy($id);

            if ($group) {
                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect('quiz/question-group');
            } else {
                Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
                return redirect()->back();
            }


        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }
}
