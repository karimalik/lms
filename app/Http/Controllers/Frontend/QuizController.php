<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\LessonComplete;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\CourseSetting\Entities\Course;
use Modules\CourseSetting\Entities\CourseComment;
use Modules\CourseSetting\Entities\CourseEnrolled;
use Modules\CourseSetting\Entities\Lesson;
use Modules\Quiz\Entities\OnlineExamQuestionAssign;
use Modules\Quiz\Entities\QuestionBankMuOption;
use Modules\Quiz\Entities\QuizTestDetailsAnswer;
use Modules\Quiz\Entities\QuizMarking;
use Modules\Quiz\Entities\QuizTest;
use Modules\Quiz\Entities\QuizTestDetails;

class QuizController extends Controller
{
    public function __construct()
    {
        $this->middleware('maintenanceMode');
    }

    public function quizzes(Request $request)
    {
        try {
            return view(theme('pages.quizzes'), compact('request'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function quizDetails($slug, Request $request)
    {


        try {

            $course = Course::select(
                'courses.id',
                'courses.type',
                'courses.slug',
                'courses.image',
                'courses.trailer_link',
                'courses.thumbnail',
                'courses.title',
                'courses.level',
                'courses.host',
                'courses.host',
                'courses.status',
                'courses.about',
                'courses.quiz_id',
                'courses.reveiw',
                'courses.duration',
                'courses.type',
                'courses.total_enrolled',
                'courses.special_commission',
                'courses.duration',
                'courses.slug',
                'courses.user_id',
                'courses.price',
                'courses.discount_price',
                'users.name as userName'
            )->leftJoin('users', 'courses.user_id', 'users.id')
                ->where('courses.slug', $slug)->first();
            if (!$course) {
                Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
                return redirect()->back();
            }
            if (isModuleActive('OrgSubscription') && Auth::check()) {
                if (!orgSubscriptionCourseValidity($course->id)) {
                    Toastr::warning('Your Subscription Expire');
                    return back();
                }
                if (!orgSubscriptionCourseSequence($course->id)) {
                    Toastr::warning('You Can Not Continue This . Pls Complete Previous Course');
                    return back();
                }
            }

            if (!isViewable($course)) {
                Toastr::error(trans('common.Access Denied'), trans('common.Failed'));
                return redirect()->to(route('quizzes'));
            }

            if (empty($course->quiz->id)) {
                Toastr::error('No Quiz Assign', trans('common.Failed'));
                return \redirect()->back();
            }
            if (Auth::check()) {
                $isEnrolled = $course->isLoginUserEnrolled;
            } else {
                $isEnrolled = false;
            }
            if ($isEnrolled) {
                $enroll = CourseEnrolled::where('user_id', Auth::id())->where('course_id', $course->id)->first();
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
            $data = '';
            $reviews = DB::table('course_reveiws')
                ->select(
                    'course_reveiws.id',
                    'course_reveiws.star',
                    'course_reveiws.comment',
                    'course_reveiws.instructor_id',
                    'course_reveiws.created_at',
                    'users.id as userId',
                    'users.name as userName',
                )
                ->join('users', 'users.id', '=', 'course_reveiws.user_id')
                ->where('course_reveiws.course_id', $course->id)->paginate(10);

            if ($request->ajax()) {
                if ($request->type == "review") {
                    foreach ($reviews as $review) {
                        $data .= view(theme('partials._single_review'), ['review' => $review, 'isEnrolled' => $isEnrolled, 'course' => $course])->render();
                    }
                    if (count($reviews) == 0) {
                        $data .= '';
                    }
                    return $data;
                }
            }
            $comments = CourseComment::where('course_id', $course->id)->with('replies', 'replies.user', 'user')->paginate(10);

            if ($request->ajax()) {
                if ($request->type == "comment") {
                    foreach ($comments as $comment) {
                        $data .= view(theme('partials._single_comment'), ['comment' => $comment, 'isEnrolled' => $isEnrolled, 'course' => $course])->render();
                    }
                    return $data;
                }

            }
            $course->view = $course->view + 1;
            $course->save();
            return view(theme('pages.quizDetails'), compact('course', 'request', 'isEnrolled'));

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function quizStart($id, $quiz_id, $slug)
    {
        try {
            $course = Course::where('courses.id', $id)->first();
            if (Auth::check() && $course->isLoginUserEnrolled) {
                return view(theme('pages.quizStart'), compact('course', 'quiz_id'));
            } else {
                Toastr::error('Permission Denied', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function quizSubmit(Request $request)
    {
        // return $request;
        try {
            $allAns = $request->ans;
            $userId = Auth::id();
            $courseId = $request->get('courseId');
            $quizId = $request->get('quizId');
            $questions = $request->get('question');
            $quiz_test = QuizTest::with('quiz', 'details')->find($request->quiz_test_id);

            if ($quiz_test->quiz_id) {
                $marking = QuizMarking::where('quiz_id', $quiz_test->quiz_id)->where('quiz_test_id', $quiz_test->id)->where('student_id', $userId)->first();
            }

            if ($marking) {
                $quiz_marking = $marking;
            } else {
                $quiz_marking = new QuizMarking();
            }

            $quiz_marking->quiz_id = $quiz_test->quiz_id;
            $quiz_marking->quiz_test_id = $quiz_test->id;
            $quiz_marking->student_id = $userId;

            if (in_array('L', $request->type) || in_array('S', $request->type)) {
                $quiz_marking->marking_status = 0;
                $quiz_test->publish = 0;
            } else {
                $totalCorrect = 0;
                $score = 0;
                if ($quiz_test->details) {
                    foreach ($quiz_test->details as $test) {
                        $score += $test->mark ?? 1;
                    }
                }
                $quiz_marking->marked_by = 0;
                $quiz_marking->marking_status = 1;
                $quiz_marking->marks = $score;
                $quiz_test->publish = 1;
            }
            $quiz_marking->save();
            $quiz_test->save();
            Toastr::success('Successfully submitted', 'Success');
            if ($request->from == "course") {
                $previousUrl = app('url')->previous();
                return redirect()->to($previousUrl . '?' . http_build_query(['quiz_result_id' => $quiz_test->id]));

            } else {
                return redirect()->route('getQuizResult', $quiz_test->id);
            }

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function quizResult($id)
    {
        try {

            $user = Auth::user();

            $quiz = QuizTest::with('quiz')->findOrFail($id);
            if ($quiz->user_id == $user->id) {
                $course = Course::findOrFail($quiz->course_id);
                return view(theme('pages.quizResult'), compact('quiz', 'user', 'course'));
            } else {
                Toastr::error('Permission Denied', 'Failed');
                return redirect()->back();
            }

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function quizResultPreview($id)
    {
        $quizTest = QuizTest::findOrFail($id);
        try {
            $user = Auth::user();


            if (Auth::check() && $quizTest->user_id == $user->id) {
                $course = Course::with('quiz')
                    ->where('courses.id', $quizTest->course_id)->first();
                return view(theme('pages.quizResultPreview'), compact('user', 'quizTest', 'course'));

            } else {
                Toastr::error('Permission Denied', 'Failed');
                return redirect()->back();
            }

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function quizTestStart(Request $request)
    {
        try {
            $userId = Auth::id();
            $courseId = $request->get('courseId');
            $quizId = $request->get('quizId');
            $quizType = $request->get('quizType');
            $quiz = new QuizTest();
            $quiz->user_id = $userId;
            $quiz->course_id = $courseId;
            $quiz->quiz_id = $quizId;
            $quiz->quiz_type = $quizType;
            $quiz->start_at = now();
            $quiz->end_at = null;
            $quiz->duration = 0.00;

            $quiz->save();

            $return['result'] = true;
            $return['data'] = $quiz;

        } catch (\Exception $e) {
            $return['result'] = true;
            $return['data'] = null;
        }

        return $return;
    }

    public function singleQuizSubmit(Request $request)
    {
        try {
            $answer = $request->ans;
            $userId = Auth::id();
            $type = $request->get('type');
            $assign_id = $request->get('assign_id');
            $quiz_test_id = $request->get('quiz_test_id');
            $assign = OnlineExamQuestionAssign::with('questionBank')->find($assign_id);
            $qus = $assign->question_bank_id;
            $quizTest = QuizTest::find($quiz_test_id);


            $start_at = Carbon::parse($quizTest->start_at);
            $end_at = Carbon::now();


            $quizTest->end_at = $end_at;
            $quizTest->duration = number_format(abs(strtotime($start_at) - strtotime($end_at)) / 60, 2) ?? 0.00;
            $quizTest->save();

            $check_details = QuizTestDetails::where('quiz_test_id', $quiz_test_id)->where('qus_id', $qus)->first();
            if ($check_details) {
                $quizDetails = $check_details;
            } else {
                $quizDetails = new QuizTestDetails();
                $quizDetails->quiz_test_id = $quiz_test_id;
                $quizDetails->qus_id = $qus;
                $quizDetails->status = 0;
                $quizDetails->mark = $assign->questionBank->marks;
                $quizDetails->save();
            }

            if ($type == "M") {

                $alreadyAns = QuizTestDetailsAnswer::where('quiz_test_details_id', $quizDetails->id)->get();
                $totalCorrectAns = QuestionBankMuOption::where('status', 1)->where('question_bank_id', $assign->question_bank_id)->count();

                foreach ($alreadyAns as $already) {
                    $already->delete();
                }
                $wrong = 0;
                $userCorrectAns = 0;
                if (!empty($answer)) {
                    foreach ($answer as $ans) {
                        $setAns = new QuizTestDetailsAnswer();
                        $option = QuestionBankMuOption::with('question')->find($ans);
                        if ($option) {
                            $setAns->quiz_test_details_id = $quizDetails->id;
                            $setAns->ans_id = $ans;
                            $setAns->status = $option->status;
                            $setAns->save();

                            if ($setAns->status == 0) {
                                $wrong++;
                            } elseif ($setAns->status == 1) {
                                $userCorrectAns++;
                            }
                        }
                    }
                    if ($wrong == 0) {
                        if ($userCorrectAns == $totalCorrectAns) {
                            $quizDetails->status = 1;
                        } else {
                            $quizDetails->status = 0;
                        }
                    } else {
                        $quizDetails->status = 0;
                    }
                    $quizDetails->save();
                }

            } else {

                $quizDetails->quiz_test_id = $quiz_test_id;
                $quizDetails->qus_id = $qus;
                $quizDetails->answer = $answer;
                $quizDetails->status = 0;
                $quizDetails->mark = 0;

                $quizDetails->save();
            }

            return true;

        } catch (\Exception $e) {
            return false;
        }
    }
}
