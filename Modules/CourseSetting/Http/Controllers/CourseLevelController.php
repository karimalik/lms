<?php

namespace Modules\CourseSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Modules\CourseSetting\Entities\Course;
use Modules\CourseSetting\Entities\CourseLevel;


class CourseLevelController extends Controller
{

    public function index()
    {
        $levels = CourseLevel::all();
        return view('coursesetting::level', compact('levels'));
    }


    public function store(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $rules = [
            'title' => 'required',

        ];

        $this->validate($request, $rules, validationMessage($rules));


        $level = new CourseLevel();
        $level->title = $request->title;
        $level->save();

        Toastr::success(trans('common.Operation successful'), trans('common.Success'));
        return redirect()->back();
    }

    public function edit($id, Request $request)
    {
        $edit = CourseLevel::findOrFail($id);
        $levels = CourseLevel::all();
        return view('coursesetting::level', compact('levels', 'edit'));
    }

    public function update(Request $request, $id)
    {
        if (demoCheck()) {
            return redirect()->back();
        }

        $rules = [
            'title' => 'required',

        ];
        $this->validate($request, $rules, validationMessage($rules));
        $edit = CourseLevel::findOrFail($id);
        $edit->title = $request->title;
        $edit->save();

        Toastr::success(trans('common.Operation successful'), trans('common.Success'));
        return redirect()->back();

    }

    public function delete($id)
    {

        if (demoCheck()) {
            return redirect()->back();
        }
        $hasCourse = Course::where('level', $id)->count();
        if ($hasCourse != 0) {
            Toastr::error('Level is not Empty', trans('common.Failed'));
            return redirect()->back();
        }
        $level = CourseLevel::findOrFail($id);
        $level->delete();
        Toastr::success(trans('common.Operation successful'), trans('common.Success'));
        return redirect()->back();
    }
}
