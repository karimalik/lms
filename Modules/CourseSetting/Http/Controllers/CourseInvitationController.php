<?php

namespace Modules\CourseSetting\Http\Controllers;

use App\User;
use App\Jobs\SendInvitation;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use App\Notifications\EmailNotification;
use Yajra\DataTables\Facades\DataTables;
use App\Notifications\GeneralNotification;
use Modules\CourseSetting\Entities\Course;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Notification;

class CourseInvitationController extends Controller
{

    public function courseStatistics()
    {
        try {
            $courses = Course::with('enrollUsers','enrolls','enrolls.user')->where('type', 1)->get();
            return view('coursesetting::statistics', compact('courses'));
        } catch (\Exception $e) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }


    public function enrolled_students($course_id)
    {
        try {
            $course = Course::find($course_id);
            $students = [];
            return view('coursesetting::student_list', compact('students', 'course'));

        } catch (\Exception $e) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }


    public function getAllStudentData(Request $request, $course_id)
    {

        $course = Course::find($course_id);
        $query = $course->enrollUsers;

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('image', function ($query) {
                return " <div class=\"profile_info\"><img src='" . getStudentImage($query->image) . "'   alt='" . $query->name . " image'></div>";
            })->addColumn('student_name', function ($query) {
                return '<a class="dropdown-item" target="_blank" href="' . route('student.courses', $query->id) . '" data-id="' . $query->id . '" type="button">' . $query->name . '</a>';

            })->editColumn('email', function ($query) {
                return $query->email;

            })
            ->editColumn('phone', function ($query) {
                return $query->phone;

            })
            ->addColumn('progressbar', function ($query) use ($course) {
                return "  <div class='progress_percent flex-fill text-right'>
                                                    <div class='progress theme_progressBar '>
                                                        <div class='progress-bar' role='progressbar'
                                                             style='width:" . round($course->userTotalPercentage($query->id, $course->id)) . "%'
                                                             aria-valuenow='25'
                                                             aria-valuemin='0' aria-valuemax='100'></div>
                                                    </div>
                                                    <p class='font_14 f_w_400'>" . round($course->userTotalPercentage($query->id, $course->id)) . "% Complete</p>
                                                </div>";

            })
            ->editColumn('dob', function ($query) {
                return showDate($query->dob);

            })
            ->addColumn('start_working_date', function ($query) {
                if (isModuleActive('Org')) {
                    return showDate($query->start_working_date);
                } else {
                    return '';
                }

            })
            ->editColumn('country', function ($query) {
                return $query->userCountry->name;

            })
            ->addColumn('status', function ($query) {

                $checked = $query->status == 1 ? "checked" : "";
                $view = '<label class="switch_toggle" for="active_checkbox' . $query->id . '">
                                                    <input type="checkbox" class="status_enable_disable"
                                                           id="active_checkbox' . $query->id . '" value="' . $query->id . '"
                                                             ' . $checked . '><i class="slider round"></i></label>';

                return $view;
            })->addColumn('notify_user', function ($query) use ($course) {
                if (round($course->userTotalPercentage($query->id, $course->id)) < 100) {
                    $link = '<a class="" href="' . route('course.courseStudentNotify', [$course->id, $query->id]) . '" data-id="' . $query->id . '" type="button">Notify</a>';
                } else {
                    $link = '';

                }
                return $link;


            })->rawColumns(['status', 'progressbar', 'image', 'notify_user', 'action', 'student_name'])
            ->make(true);
    }


    public function courseStudentNotify($course_id, $student_id)
    {
        try {
            $course = Course::find($course_id);
            $user = User::find($student_id);
            $percentage = round($course->userTotalPercentage($student_id, $course_id));
            $message = "You have complete " . $percentage . "% of " . $course->title . ". Please complete as soon as possible";
            $details = [
                'title' => 'Incomplete course reminder',
                'body' => $message,
                'actionText' => 'Visit',
                'actionURL' => route('courseDetailsView', $course->slug),
            ];
            Notification::send($user, new GeneralNotification($details));
            Toastr::success('Operation Done Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }

    }


}
