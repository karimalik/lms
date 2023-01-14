<?php

namespace App\Http\Controllers\Frontend;

use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\CourseSetting\Entities\Course;
use Modules\Setting\Entities\InstructorSetup;
use Modules\FrontendManage\Entities\BecomeInstructor;

class InstructorController extends Controller
{
    public function __construct()
    {
        $this->middleware('maintenanceMode');
    }

    public function instructors(Request $request)
    {
        try {

            $instructors = User::where('role_id', 2)->where('status', '1')->orderBy('total_rating', 'desc')->paginate(16);

            $data = '';
            if ($request->ajax()) {
                foreach ($instructors as $instructor) {
                    if (Settings('frontend_active_theme') == "edume") {
                        $data .= view(theme('partials._single_instractor'),compact('instructor'));
                    } else {
                        $data .= '    <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="single_instractor mb_30">
                                <a href="' . route('instructorDetails', [$instructor->id, Str::slug($instructor->name, '-')]) . '"
                                   class="thumb">
                                    <img src="' . getInstructorImage($instructor->image) . '" alt="">
                                </a>
                                <a href="' . route('instructorDetails', [$instructor->id, Str::slug($instructor->name, '-')]) . '">
                                    <h4>' . $instructor->name . '</h4></a>
                                <span>' . $instructor->headline . '</span>
                            </div>
                        </div>';
                    }


                }
                return $data;
            }
            return view(theme('pages.instructors'), compact('instructors'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function instructorDetails($id, $name, Request $request)
    {
        try {

            $instructor = User::findOrFail($id);
            $InstructorSetup = InstructorSetup::getData();
            $courses = Course::where('user_id', $id)->with('enrollUsers', 'lessons','category')->where('status', 1)->orderBy('total_rating', 'desc')->paginate(12);

            $data = '';
            if ($request->ajax()) {
                foreach ($courses as $course) {
                    $data .= view(theme('partials._single_course'), ['course' => $course])->render();
                }
                return $data;
            }
            if (isModuleActive('BundleSubscription')) {
                $bundleCourse = new  \Modules\BundleSubscription\Repositories\BundleCoursePlanRepository;
                $BundleCourse = $bundleCourse->getInstructorBundle($id);
            } else {
                $BundleCourse = null;
            }
            return view(theme('pages.instructor'), compact('BundleCourse', 'instructor', 'id', 'InstructorSetup'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function becomeInstructor()
    {
        try {
            $becomeInstructor = BecomeInstructor::all();
            return view(theme('pages.becomeInstructor'), compact('becomeInstructor'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }
}
