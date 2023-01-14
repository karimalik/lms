<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\CourseSetting\Entities\Course;
use Modules\CourseSetting\Entities\CourseComment;
use Modules\CourseSetting\Entities\CourseCommentReply;
use Modules\CourseSetting\Entities\CourseReveiw;
use Modules\CourseSetting\Entities\Notification;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('maintenanceMode');
    }


    public function saveComment(Request $request)
    {
        $request->validate([
            'course_id' => 'required',
            'comment' => 'required',
        ]);


        try {
            $course = Course::where('id', $request->course_id)->first();

            if (isset($course)) {
                $comment = new CourseComment();
                $comment->user_id = Auth::user()->id;
                $comment->course_id = $request->course_id;
                $comment->instructor_id = $course->user_id;
                $comment->comment = $request->comment;
                $comment->status = 1;
                $comment->save();


                $courseUser = $course->user;
                if (UserEmailNotificationSetup('Course_comment', $courseUser)) {
                    send_email($courseUser, 'Course_comment', [
                        'time' => Carbon::now()->format('d-M-Y, s:i A'),
                        'course' => $course->title,
                        'comment' => $comment->comment,
                    ]);

                }
                if (UserBrowserNotificationSetup('Course_comment', $courseUser)) {

                    send_browser_notification($courseUser, $type = 'Course_comment', $shortcodes = [
                        'time' => Carbon::now()->format('d-M-Y, s:i A'),
                        'course' => $course->title,
                        'comment' => $comment->comment,
                    ],
                        '',//actionText
                        ''//actionUrl
                    );
                }


                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->back();
            } else {
                Toastr::error('Invalid Action !', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function submitCommnetReply(Request $request)
    {

        $request->validate([
            'comment_id' => 'required',
            'reply' => 'required'
        ]);
        try {
            $comment = CourseComment::find($request->comment_id);
            $course = $comment->course;
            $commentUser = $comment->user;

            if (isset($course)) {
                $comment = new CourseCommentReply();
                $comment->user_id = Auth::user()->id;
                $comment->course_id = $course->id;
                if (!empty($request->reply_id)) {
                    $comment->reply_id = $request->reply_id;
                } else {
                    $comment->reply_id = null;
                }
                $comment->comment_id = $request->comment_id;
                $comment->reply = $request->reply;
                $comment->status = 1;
                $comment->save();

                if ($course->user->id != Auth::user()->id) {
                    if (UserEmailNotificationSetup('Course_comment_Reply', $course->user)) {
                        send_email($course->user, 'Course_comment_Reply', [
                            'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                            'course' => $course->title,
                            'comment' => $comment->comment,
                            'reply' => $comment->reply,
                        ]);
                    }
                    if (UserBrowserNotificationSetup('Course_comment_Reply', $course->user)) {

                        send_browser_notification($course->user, $type = 'Course_comment_Reply', $shortcodes = [
                            'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                            'course' => $course->title,
                            'comment' => $comment->comment,
                            'reply' => $comment->reply,
                        ],
                            '',//actionText
                            ''//actionUrl
                        );
                    }
                }


//                $commentUser = $comment->user;
                if (UserEmailNotificationSetup('Course_comment_Reply', $commentUser)) {
                    send_email($commentUser, 'Course_comment_Reply', [
                        'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                        'course' => $course->title,
                        'comment' => $comment->comment,
                        'reply' => $comment->reply,
                    ]);
                }
                if (UserBrowserNotificationSetup('Course_comment_Reply', $commentUser)) {

                    send_browser_notification($commentUser, $type = 'Course_comment_Reply', $shortcodes = [
                        'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                        'course' => $course->title,
                        'comment' => $comment->comment,
                        'reply' => $comment->reply,
                    ],
                        '',//actionText
                        ''//actionUrl
                    );
                }

                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->back();
            } else {
                Toastr::error('Invalid Action !', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function deleteComment($id)
    {
        try {
            $comment = CourseComment::find($id);
            $user = Auth::user();
            if ($comment->user_id == $user->id || $user->role_id == 1 || $comment->instructor_id == $user->id) {
                $comment->delete();
                if (isset($comment->replies)) {
                    foreach ($comment->replies as $replay) {
                        $replay->delete();
                    }
                }
                return true;
            } else {
                return false;
            }


        } catch (\Exception $exception) {
            return false;

        }
    }

    public function deleteReview($id)
    {
        try {
            $review = CourseReveiw::find($id);
            $course_id = $review->course_id;
            $user = Auth::user();
            if ($review->user_id == $user->id || $user->role_id == 1 || $review->instructor_id == $user->id) {
                $review->delete();

                $course = Course::find($course_id);
                $total = CourseReveiw::where('course_id', $course->id)->sum('star');
                $count = CourseReveiw::where('course_id', $course->id)->where('status', 1)->count();
                if ($total != 0) {
                    $average = $total / $count;
                } else {
                    $average = 0;
                }
                $course->reveiw = $average;
                $course->total_rating = $average;
                $course->save();


                $course_user = User::find($course->user_id);
                $user_courses = Course::where('user_id', $course_user->id)->get();
                $user_total = 0;
                $user_rating = 0;
                foreach ($user_courses as $u_course) {
                    $total = CourseReveiw::where('course_id', $u_course->id)->sum('star');
                    $count = CourseReveiw::where('course_id', $u_course->id)->where('status', 1)->count();
                    if ($total != 0) {
                        $user_total = $user_total + 1;
                        $average = $total / $count;
                        $user_rating = $user_rating + $average;
                    }
                }
                if ($user_total != 0) {
                    $user_rating = $user_rating / $user_total;
                }
                $course_user->total_rating = $user_rating;
                $course_user->save();

                $total = CourseReveiw::where('course_id', $course->id)->sum('star');
                $count = CourseReveiw::where('course_id', $course->id)->where('status', 1)->count();
                if ($total != 0) {
                    $average = $total / $count;
                } else {
                    $average = 0;
                }
                $course->reveiw = $average;
                $course->total_rating = $average;
                $course->save();


                return true;
            } else {
                return false;
            }


        } catch (\Exception $exception) {
            return false;
        }
    }

    public function deleteCommnetReply($id)
    {
        try {

            $reply = CourseCommentReply::find($id);
            $course = Course::find($reply->course_id);
            $user = Auth::user();

            if ($reply->user_id == $user->id || $user->role_id == 1 || $course->user_id == $user->id) {
                $reply->delete();
                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            } else {
                Toastr::error('Invalid access', trans('common.Failed'));
            }
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }
}
