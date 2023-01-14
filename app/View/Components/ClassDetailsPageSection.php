<?php

namespace App\View\Components;

use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;
use Modules\BBB\Entities\BbbMeeting;
use Modules\Certificate\Entities\Certificate;
use Modules\CourseSetting\Entities\Course;
use Modules\CourseSetting\Entities\CourseEnrolled;
use Modules\Jitsi\Entities\JitsiMeeting;
use Modules\Payment\Entities\Cart;
use Modules\StudentSetting\Entities\BookmarkCourse;
use Modules\VirtualClass\Entities\ClassComplete;
use Modules\Zoom\Entities\ZoomMeeting;

class ClassDetailsPageSection extends Component
{
    public $course, $request;

    public function __construct($course, $request)
    {
        $this->course = $course;
        $this->request = $request;
    }


    public function render()
    {
        $course_reviews = DB::table('course_reveiws')->select('user_id')->where('course_id', $this->course->id)->get();

        if (Auth::check()) {
            $isEnrolled = $this->course->isLoginUserEnrolled;
        } else {
            $isEnrolled = false;
        }


        $bookmarked = BookmarkCourse::where('user_id', Auth::id())->where('course_id', $this->course->id)->count();
        if ($bookmarked == 0) {
            $isBookmarked = false;
        } else {
            $isBookmarked = true;

        }


        if ($this->course->price == 0) {
            $isFree = true;
        } else {
            $isFree = false;
        }

        if ($isEnrolled) {
            $enroll = CourseEnrolled::where('user_id', Auth::id())->where('course_id', $this->course->id)->first();
            if ($enroll) {
                if ($enroll->subscription == 1) {
                    if (isModuleActive('Subscription')) {
                        if (!isSubscribe()) {
                            Toastr::error('Subscription has expired, Please Subscribe again.', 'Failed');
                            return redirect()->route('courseSubscription');
                        }
                    }
                }
            }
        }

        if (Auth::check() && $this->course->isLoginUserEnrolled) {


            if ($this->course->class->host == "Zoom") {
                $nextMeeting = ZoomMeeting::where('class_id', $this->course->class->id)->where('date_of_meeting', date('m/d/Y'))
                    ->orderBy('start_time', 'asc')
                    ->get();
                $this->course->nextMeeting = null;
                $hasClass = false;
                foreach ($nextMeeting as $next) {
                    if ($next->currentStatus == "closed") {
                        continue;
                    } else {
                        if (!$hasClass) {
                            $this->course->nextMeeting = $next;
                            $hasClass = true;
                        }
                    }
                }

            } elseif ($this->course->class->host == "BBB") {
                if (isModuleActive("BBB")) {

                    $nextMeeting = BbbMeeting::where('class_id', $this->course->class->id)->where('date', date('m/d/Y'))
                        ->orderBy('datetime', 'asc')->first();
                    $this->course->nextMeeting = $nextMeeting;

                } else {
                    Toastr::error('Module is not activated', 'Failed');
                }

            } elseif ($this->course->class->host == "Jitsi") {
                if (isModuleActive("Jitsi")) {

                    $nextMeeting = JitsiMeeting::where('class_id', $this->course->class->id)->where('date', date('m/d/Y'))->first();
                    if ($nextMeeting) {
                        $nextMeeting->isRunning = true;
                    }
                    $this->course->nextMeeting = $nextMeeting;

                } else {
                    Toastr::error('Module is not activated', 'Failed');
                }

            }

        }
        $reviewer_user_ids = [];
        foreach ($course_reviews as $key => $review) {
            $reviewer_user_ids[] = $review->user_id;
        }


        $course_enrolled_std = [];
        foreach ($this->course->enrolls as $key => $enroll) {
            $course_enrolled_std[] = $enroll['user_id'];
        }


        $related = Course::where('category_id', $this->course->category_id)->where('id', '!=', $this->course->id)->take(2)->get();

        $is_cart = 0;
        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->where('course_id', $this->course->id)->first();
            if ($cart) {
                $is_cart = 1;
            }
        } else {
            $sessonCartList = session()->get('cart');
            if (!empty($sessonCartList)) {
                foreach ($sessonCartList as $item) {
                    if ($item['course_id'] == $this->course->id) {
                        $is_cart = 1;
                    }
                }
            }
        }


        $class = $this->course->class;
        $last_time = Carbon::parse($class->end_date . ' ' . $class->time);
        $nowDate = Carbon::now();
        $isWaiting = $last_time->gt($nowDate);
        $certificateCanDownload = false;
        if (!$isWaiting) {
            if (Auth::check() && $isEnrolled) {
                $totalClass = $class->total_class;
                $completeClass = ClassComplete::where('course_id', $this->course->id)->where('class_id', $class->id)->count();
                if ($totalClass == $completeClass) {
                    $hasCertificate = $this->course->certificate_id;
                    if (!empty($hasCertificate)) {
                        $certificate = Certificate::find($hasCertificate);
                        if ($certificate) {
                            $certificateCanDownload = true;
                        }
                    } else {
                        $certificate = Certificate::where('for_class', 1)->first();
                        if ($certificate) {
                            $certificateCanDownload = true;
                        }
                    }
                }
            }
        }
        $userRating = userRating($this->course->user_id);
        return view(theme('components.class-details-page-section'), compact('userRating', 'certificateCanDownload', 'isWaiting', 'is_cart', 'reviewer_user_ids', 'related', 'isFree', 'isBookmarked', 'isEnrolled'));
    }
}
