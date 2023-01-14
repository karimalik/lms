<?php

namespace App\View\Components;

use Carbon\Carbon;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;
use Modules\CourseSetting\Entities\Course;
use Modules\Setting\Entities\StudentSetup;
use Modules\CourseSetting\Entities\CourseEnrolled;

class MyDashboardPageSection extends Component
{

    public function __construct()
    {
        //
    }

    public function render()
    {
        $data['user'] = Auth::user();
        $enrolledByUser = CourseEnrolled::where('user_id', Auth::user()->id)->orderBy('last_view_at', 'desc');

        $total_spent = $enrolledByUser->sum('purchase_price');
        $total_purchase = $enrolledByUser->count() ?? 0;


        $Hour = date('G');

        if ($Hour >= 5 && $Hour <= 11) {
            $wish_string = trans("student.Good Morning");
        } else if ($Hour >= 12 && $Hour <= 18) {
            $wish_string = trans("student.Good Afternoon");
        } else if ($Hour >= 19 || $Hour <= 4) {
            $wish_string = trans("student.Good Evening");
        }
        $date = Carbon::now(Settings('active_time_zone'))->format("jS F Y \, l");

        $mycourse = $enrolledByUser
            ->whereHas('course', function ($query) {
                $query->where('type', '=', 1);
            })
            ->with('course', 'course.lessons', 'course.activeReviews', 'course.completeLessons', 'course.completeLessons')->take(3)->get();

        $student_setup = StudentSetup::getData();
        $courses = Course::where('type', 1)->where('status', 1)->inRandomOrder()->limit(3)->with('lessons', 'enrollUsers', 'cartUsers', 'user', 'reviews', 'BookmarkUsers', 'courseLevel')
            ->whereDoesntHave('enrolls', function ($q) {
                $q->where('user_id', '=', Auth::id());
            })
            ->get();
        $quizzes = Course::where('type', 2)->where('status', 1)->inRandomOrder()->limit(3)->with('quiz', 'quiz.assign', 'enrollUsers', 'cartUsers', 'user', 'reviews', 'BookmarkUsers', 'courseLevel')
            ->whereDoesntHave('enrolls', function ($q) {
                $q->where('user_id', '=', Auth::id());
            })->get();

        $withForClass = ['activeReviews', 'enrollUsers', 'cartUsers', 'class', 'class.zoomMeetings', 'user', 'reviews', 'BookmarkUsers', 'courseLevel'];
        if (isModuleActive('BBB')) {
            $withForClass[] = 'class.bbbMeetings';
        }
        if (isModuleActive('Jisti')) {
            $withForClass[] = 'class.jitsiMeetings';
        }
        $classes = Course::where('type', 3)->where('status', 1)->inRandomOrder()->limit(3)->with($withForClass)
            ->whereDoesntHave('enrolls', function ($q) {
                $q->where('user_id', '=', Auth::id());
            })->get();

        return view(theme('components.my-dashboard-page-section'), compact('quizzes', 'courses', 'classes', 'data', 'mycourse', 'wish_string', 'date', 'total_purchase', 'student_setup', 'total_spent'));
    }
}
