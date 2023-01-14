<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Modules\Homework\Entities\InfixHomework;
use Modules\CourseSetting\Entities\CourseEnrolled;
use Modules\Homework\Entities\InfixAssignHomework;
use Modules\Homework\Http\Controllers\AssignmentController;

class StudyMaterialController extends Controller
{
    public function myHomework()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        try {
            $enrolleds = CourseEnrolled::where('user_id', Auth::user()->id)->where('status', 1)->get();
            foreach ($enrolleds as $key => $enrolled) {
                foreach ($enrolled->course->courseStudyMaterials as $key => $studyMaterial) {
                    AssignmentController::assignHomework($studyMaterial->id, Auth::user()->id, $studyMaterial->assign_info[0]->id);
                }

            }


            return view(theme('pages.myHomework'));
        } catch (\Exception $e) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }

    public function myHomeworkDetails($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        try {
            return view(theme('pages.homework_details'), compact('id'));
        } catch (\Exception $e) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }
}
