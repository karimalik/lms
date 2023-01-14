<?php

namespace Modules\Quiz\Http\Controllers;

use App\User;
use App\TableList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Modules\Quiz\Entities\QuizTest;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

use Modules\CourseSetting\Entities\Category;
use Modules\CourseSetting\Entities\Course;
use Modules\Quiz\Entities\OnlineExamQuestionAssign;

use Modules\Quiz\Entities\OnlineQuiz;
use Modules\Quiz\Entities\QuizeSetup;
use Modules\Quiz\Entities\QuizMarking;
use Modules\Quiz\Entities\QuestionBank;
use Modules\Quiz\Entities\QuestionGroup;
use Modules\CourseSetting\Entities\Lesson;

use Modules\Quiz\Entities\QuizTestDetails;
use Modules\CourseSetting\Entities\Chapter;
use Modules\Quiz\Entities\StudentTakeOnlineQuiz;

class OnlineQuizController extends Controller
{
    public function index()
    {

        try {
            $user = Auth::user();
            if ($user->role_id == 1) {
                $online_exams = OnlineQuiz::with('subCategory', 'category')->latest()->get();
            } else {
                $online_exams = OnlineQuiz::with('subCategory', 'category')->where('created_by', $user->id)->latest()->get();
            }
            $categories = Category::get();

            $present_date_time = date("Y-m-d H:i:s");
            $present_time = date("H:i:s");
            $quiz_setup = QuizeSetup::getData();
            return view('quiz::online_quiz', compact('quiz_setup', 'online_exams', 'categories', 'present_date_time', 'present_time'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }

    public function CourseQuizStore(Request $request)
    {

        // return $request;
        if (demoCheck()) {
            return redirect()->back();
        }
        if ($request->type == 2) {
            $rules = [
                'title' => 'required',
                'category' => 'required',
                'percentage' => 'required',
                'instruction' => 'required'
            ];
            $this->validate($request, $rules, validationMessage($rules));
            // return OnlineQuiz::get();


            try {
                DB::beginTransaction();
                $sub = $request->sub_category;
                if (empty($sub)) {
                    $sub = null;
                }
                $online_exam = new OnlineQuiz();
                $online_exam->title = $request->title;
                $online_exam->category_id = $request->category;
                $online_exam->sub_category_id = $sub;
                $online_exam->course_id = $request->course_id;
                $online_exam->percentage = $request->percentage;
                $online_exam->instruction = $request->instruction;
                $online_exam->status = 0;
                $online_exam->created_by = Auth::user()->id;
                $result = $online_exam->save();

                $user = Auth::user();
                if ($user->role_id == 2) {
                    $course = Course::where('id', $request->course_id)->where('user_id', Auth::id())->first();
                } else {
                    $course = Course::where('id', $request->course_id)->first();
                }
                $chapter = Chapter::find($request->chapterId);

                if (isset($course) && isset($chapter)) {

                    $lesson = new Lesson();
                    $lesson->course_id = $request->course_id;
                    $lesson->chapter_id = $request->chapterId;
                    $lesson->quiz_id = $online_exam->id;
                    $lesson->is_quiz = $request->is_quiz;
                    $lesson->is_lock = $request->lock;
                    $lesson->save();
                    $quiz = OnlineQuiz::find($online_exam->id);


                    if (isset($course->enrollUsers) && !empty($course->enrollUsers)) {
                        foreach ($course->enrollUsers as $user) {
                            if (UserEmailNotificationSetup('Course_Quiz_Added', $user)) {
                                send_email($user, 'Course_Quiz_Added', [
                                    'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                                    'course' => $course->title,
                                    'chapter' => $chapter->name,
                                    'quiz' => $quiz->title,
                                ]);

                            }
                            if (UserBrowserNotificationSetup('Course_Quiz_Added', $user)) {

                                send_browser_notification($user, $type = 'Course_Quiz_Added', $shortcodes = [
                                    'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                                    'course' => $course->title,
                                    'chapter' => $chapter->name,
                                    'quiz' => $quiz->title,
                                ],
                                    '',//actionText
                                    ''//actionUrl
                                );
                            }
                        }
                    }
                    $courseUser = $course->user;
                    if (UserEmailNotificationSetup('Course_Quiz_Added', $courseUser)) {
                        send_email($courseUser, 'Course_Quiz_Added', [
                            'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                            'course' => $course->title,
                            'chapter' => $chapter->name,
                            'quiz' => $quiz->title,
                        ]);

                    }
                    if (UserBrowserNotificationSetup('Course_Quiz_Added', $courseUser)) {

                        send_browser_notification($courseUser, $type = 'Course_Quiz_Added', $shortcodes = [
                            'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                            'course' => $course->title,
                            'chapter' => $chapter->name,
                            'quiz' => $quiz->title,
                        ],
                            '',//actionText
                            ''//actionUrl
                        );
                    }
                    DB::commit();
                    Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                    return redirect()->back();
                } else {
                    Toastr::error('Invalid Access !', 'Failed');
                    return redirect()->back();
                }



            } catch (\Exception $e) {
                Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
                return redirect()->back();
            }
        } else {
            $rules = [
                'quiz' => 'required',
                'chapterId' => 'required',
                'lock' => 'required',

            ];
            $this->validate($request, $rules, validationMessage($rules));
            try {
                $user = Auth::user();
                if ($user->role_id == 2) {
                    $course = Course::where('id', $request->course_id)->where('user_id', Auth::id())->first();
                } else {
                    $course = Course::where('id', $request->course_id)->first();
                }
                $chapter = Chapter::find($request->chapterId);

                if (isset($course) && isset($chapter)) {

                    $lesson = new Lesson();
                    $lesson->course_id = $request->course_id;
                    $lesson->chapter_id = $request->chapterId;
                    $lesson->quiz_id = $request->quiz;
                    $lesson->is_quiz = $request->is_quiz;
                    $lesson->is_lock = $request->lock;
                    $lesson->save();
                    $quiz = OnlineQuiz::find($request->quiz);


                    if (isset($course->enrollUsers) && !empty($course->enrollUsers)) {
                        foreach ($course->enrollUsers as $user) {
                            if (UserEmailNotificationSetup('Course_Quiz_Added', $user)) {
                                send_email($user, 'Course_Quiz_Added', [
                                    'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                                    'course' => $course->title,
                                    'chapter' => $chapter->name,
                                    'quiz' => $quiz->title,
                                ]);

                            }
                            if (UserBrowserNotificationSetup('Course_Quiz_Added', $user)) {

                                send_browser_notification($user, $type = 'Course_Quiz_Added', $shortcodes = [
                                    'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                                    'course' => $course->title,
                                    'chapter' => $chapter->name,
                                    'quiz' => $quiz->title,
                                ],
                                    '',//actionText
                                    ''//actionUrl
                                );
                            }
                        }
                    }
                    $courseUser = $course->user;
                    if (UserEmailNotificationSetup('Course_Quiz_Added', $courseUser)) {
                        send_email($courseUser, 'Course_Quiz_Added', [
                            'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                            'course' => $course->title,
                            'chapter' => $chapter->name,
                            'quiz' => $quiz->title,
                        ]);

                    }
                    if (UserBrowserNotificationSetup('Course_Quiz_Added', $courseUser)) {

                        send_browser_notification($courseUser, $type = 'Course_Quiz_Added', $shortcodes = [
                            'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                            'course' => $course->title,
                            'chapter' => $chapter->name,
                            'quiz' => $quiz->title,
                        ],
                            '',//actionText
                            ''//actionUrl
                        );
                    }
                    Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                    return redirect()->back();
                }

                Toastr::error('Invalid Access !', 'Failed');
                return redirect()->back();

            } catch (Exception $e) {
                // dd($e);
                Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
                return redirect()->back();
            }
        }


    }

    public function CourseQuizUpdate(Request $request)
    {

        if (demoCheck()) {
            return redirect()->back();
        }
        $rules = [
            'title' => 'required',
            'category' => 'required',
            'percentage' => 'required',
            'instruction' => 'required'
        ];
        $this->validate($request, $rules, validationMessage($rules));

        DB::beginTransaction();
        try {
            $sub = $request->sub_category;
            if (empty($sub)) {
                $sub = null;
            }
            $online_exam = OnlineQuiz::find($request->quiz_id);
            $online_exam->title = $request->title;
            $online_exam->category_id = $request->category;
            $online_exam->sub_category_id = $sub;
            $online_exam->percentage = $request->percentage;
            $online_exam->instruction = $request->instruction;
            $online_exam->status = 0;
            $online_exam->created_by = Auth::user()->id;
            $result = $online_exam->save();


            DB::commit();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->route('courseDetails', $request->course_id);
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

        $rules = [
            'title' => 'required',
            'category' => 'required',
            'percentage' => 'required',
            'instruction' => 'required'
        ];
        $this->validate($request, $rules, validationMessage($rules));


        try {
            DB::beginTransaction();
            $sub = $request->sub_category;
            if (empty($sub)) {
                $sub = null;
            }
            $online_exam = new OnlineQuiz();
            $online_exam->title = $request->title;
            $online_exam->category_id = $request->category;
            $online_exam->sub_category_id = $sub;
            $online_exam->course_id = $request->course;
            $online_exam->percentage = $request->percentage;
            $online_exam->instruction = $request->instruction;
            $online_exam->status = 1;
            $online_exam->created_by = Auth::user()->id;


            if ($request->change_default_settings == 0) {
                $setup = QuizeSetup::getData();
                $online_exam->random_question = $setup->random_question == 1 ? 1 : 0;
                $online_exam->question_time_type = $setup->set_per_question_time == 1 ? 0 : 1;
                $online_exam->question_time = $setup->set_per_question_time == 1 ? $setup->time_per_question : $setup->time_total_question;
                $online_exam->question_review = $setup->question_review == 1 ? 1 : 0;
                $online_exam->show_result_each_submit = $setup->show_result_each_submit == 1 ? 1 : 0;
                $online_exam->multiple_attend = $setup->multiple_attend == 1 ? 1 : 0;
            } else {
                $online_exam->random_question = $request->random_question == 1 ? 1 : 0;
                $online_exam->question_time_type = $request->type == 1 ? 1 : 0;
                $online_exam->question_time = $request->question_time;
                $online_exam->question_review = $request->question_review == 1 ? 1 : 0;
                $online_exam->show_result_each_submit = $request->show_result_each_submit == 1 ? 1 : 0;
                $online_exam->multiple_attend = $request->multiple_attend == 1 ? 1 : 0;
            }


            $result = $online_exam->save();

            if ($request->set_random_question == 1) {
                $total = $request->random_question;

                $query = QuestionBank::query();
                if (Auth::user()->role_id != 1) {
                    $query->where('user_id', Auth::user()->id);
                }
                if (!empty($request->category)) {
                    $query->where('category_id', $request->category);

                }
                if (!empty($sub)) {
                    $query->where('sub_category_id', $sub);
                }
                $questions = $query->inRandomOrder()->limit($total)->get();

                foreach ($questions as $question) {
                    $assign = new OnlineExamQuestionAssign();
                    $assign->online_exam_id = $online_exam->id;
                    $assign->question_bank_id = $question->id;
                    $assign->save();

                }

            }


            if ($result) {

                DB::commit();
                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->back();
            } else {
                Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
                return redirect()->back();
            }
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function edit($id)
    {
        try {
            $user = Auth::user();
            if ($user->role_id == 1) {
                $online_exams = OnlineQuiz::latest()->get();
            } else {
                $online_exams = OnlineQuiz::where('created_by', $user->id)->latest()->get();
            }

            $categories = Category::get();
            $online_exam = OnlineQuiz::find($id);

            $present_date_time = date("Y-m-d H:i:s");
            $present_time = date("H:i:s");
            $quiz_setup = QuizeSetup::getData();

            return view('quiz::online_quiz', compact('quiz_setup', 'online_exams', 'categories', 'online_exam', 'present_date_time', 'present_time'));
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
            'title' => 'required',
            'category' => 'required',
            'percentage' => 'required',
            'instruction' => 'required'
        ];

        $this->validate($request, $rules, validationMessage($rules));

        DB::beginTransaction();
        try {

            $sub = $request->sub_category;
            if (empty($sub)) {
                $sub = null;
            }
            $online_exam = OnlineQuiz::find($id);
            $online_exam->title = $request->title;
            $online_exam->category_id = $request->category;
            $online_exam->sub_category_id = $sub;
            $online_exam->course_id = $request->course;
            $online_exam->percentage = $request->percentage;
            $online_exam->instruction = $request->instruction;


            $online_exam->random_question = $request->random_question == 1 ? 1 : 0;
            $online_exam->question_time_type = $request->type == 1 ? 1 : 0;
            $online_exam->question_time = $request->question_time;
            $online_exam->question_review = $request->question_review == 1 ? 1 : 0;
            $online_exam->show_result_each_submit = $request->show_result_each_submit == 1 ? 1 : 0;
            $online_exam->multiple_attend = $request->multiple_attend == 1 ? 1 : 0;


            $result = $online_exam->save();
            if ($result) {

                DB::commit();
                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->back();
            } else {
                Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
                return redirect()->back();
            }
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function delete(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            $id_key = 'online_exam_id';

            $tables = TableList::getTableList($id_key, $request->id);

            try {
                if ($tables == null) {
                    $delete_query = OnlineQuiz::destroy($request->id);

                    if ($delete_query) {
                        Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                        return redirect()->back();
                    } else {
                        Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
                        return redirect()->back();

                    }
                } else {
                    $msg = 'This data already used in  : ' . $tables . ' Please remove those data first';
                    Toastr::error($msg, 'Failed');
                    return redirect()->back();
                }


            } catch (\Illuminate\Database\QueryException $e) {
                $msg = 'This data already used in  : ' . $tables . ' Please remove those data first';
                Toastr::error($msg, 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function manageOnlineExamQuestion($id, Request $request)
    {

        try {
            $user = Auth::user();
            $online_exam = OnlineQuiz::findOrFail($id);

            $online_exam->total_marks = $online_exam->totalMarks() ?? 0;
            $online_exam->total_questions = $online_exam->totalQuestions() ?? 0;

            if (empty($request->get('group'))) {
                $searchGroup = '';
                $query = QuestionBank::where('category_id', $online_exam->category_id);
                if ($online_exam->sub_category_id != null) {
                    $query->where('sub_category_id', $online_exam->sub_category_id);
                }

                if ($user->role_id != 1) {
                    $query->where('user_id', $user->id);
                }

                $question_banks = $query->with('questionGroup', 'questionMu')->get();
            } else {
                $searchGroup = $request->get('group');
                $query = QuestionBank::where('category_id', $online_exam->category_id);
                if ($online_exam->sub_category_id != null) {
                    $query->where('sub_category_id', $online_exam->sub_category_id);
                }
                if ($user->role_id != 1) {
                    $query->where('user_id', $user->id);
                }
                $question_banks = $query->where('q_group_id', $request->get('group'))
                    ->with('questionGroup', 'questionMu')
                    ->get();

            }

            if ($user->role_id == 1) {
                $groups = QuestionGroup::where('active_status', 1)->latest()->get();
            } else {
                $groups = QuestionGroup::where('user_id', $user->id)->where('active_status', 1)->latest()->get();
            }
            $assigned_questions = OnlineExamQuestionAssign::with('questionBank')->where('online_exam_id', $id)->get();
            $already_assigned = [];
            foreach ($assigned_questions as $assigned_question) {
                $already_assigned[] = $assigned_question->question_bank_id;
            }


            return view('quiz::manage_quiz', compact('searchGroup', 'groups', 'online_exam', 'question_banks', 'already_assigned'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function onlineExamPublish($id)
    {
        try {
            $publish = OnlineQuiz::find($id);
            $publish->status = 1;
            $publish->save();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function quizSetup()
    {
        $quiz_setup = QuizeSetup::getData();
        return view('quiz::quiz_setup', compact('quiz_setup'));
    }

    public function SaveQuizSetup(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {

            if ($request->set_per_question_time == 1) {
                if (empty($request->set_time_per_question)) {
                    Toastr::error('Per question time required', trans('common.Failed'));
                    return redirect()->back();
                }
            } else {
                if (empty($request->set_time_total_question)) {
                    Toastr::error('Total questions time required', trans('common.Failed'));
                    return redirect()->back();
                }
            }

            $setup = QuizeSetup::firstOrCreate(['id' => 1]);
            $setup->random_question = $request->random_question;
            $setup->set_per_question_time = $request->set_per_question_time;
            $setup->multiple_attend = $request->multiple_attend ?? 0;
            if ($request->set_per_question_time == 1) {
                $setup->time_per_question = $request->set_time_per_question;
                $setup->time_total_question = null;
            } else {
                $setup->time_per_question = null;
                $setup->time_total_question = $request->set_time_total_question;
            }
            $setup->question_review = $request->question_review;
            if ($request->question_review == 1) {
                $setup->show_result_each_submit = null;
            } else {
                $setup->show_result_each_submit = $request->show_result_each_submit;
            }
            $setup->save();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }


    }

    public function onlineExamMarksRegister($id)
    {
        try {
            $online_exam_question = OnlineQuiz::find($id);
            $students = User::where('role_id', 3)->get();
            $present_students = [];
            foreach ($students as $student) {
                $take_exam = StudentTakeOnlineQuiz::where('student_id', $student->id)->where('online_exam_id', $online_exam_question->id)->first();
                if ($take_exam != "") {
                    $present_students[] = $student->id;
                }
            }

            return view('quiz::online_exam_marks_register', compact('online_exam_question', 'students', 'present_students'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function onlineExamQuestionAssign(Request $request)
    {
        try {
            OnlineExamQuestionAssign::where('online_exam_id', $request->online_exam_id)->delete();
            if (isset($request->questions)) {
                foreach ($request->questions as $question) {
                    $assign = new OnlineExamQuestionAssign();
                    $assign->online_exam_id = $request->online_exam_id;
                    $assign->question_bank_id = $question;
                    $assign->save();
                }
                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->back();
            }
            Toastr::error('No question is assigned', 'Failed');
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function onlineExamQuestionAssignByAjax(Request $request)
    {
        try {

            $online_exam = OnlineQuiz::findOrFail($request->online_exam_id);

            if (saasPlanCheck('quiz',$online_exam->totalQuestions())) {
                    return response()->json([
                    'success' => 'You have no permission to add more quiz',
                    'totalQus' => $online_exam->total_marks,
                    'totalMarks' => $online_exam->total_questions,
                ], 200);
            }
            OnlineExamQuestionAssign::where('online_exam_id', $request->online_exam_id)->delete();

            if (isset($request->questions)) {
                foreach ($request->questions as $question) {
                    $assign = new OnlineExamQuestionAssign();
                    $assign->online_exam_id = $request->online_exam_id;
                    $assign->question_bank_id = $question;
                    $assign->save();
                }

                $totalMarks = $online_exam->total_marks = $online_exam->totalMarks() ?? 0;
                $totalQus = $online_exam->total_questions = $online_exam->totalQuestions() ?? 0;
                return response()->json([
                    'success' => 'Operation successful',
                    'totalQus' => $totalQus,
                    'totalMarks' => $totalMarks,
                ], 200);
            }

            return response()->json([
                'success' => 'Operation successful',
                'totalQus' => 0,
                'totalMarks' => 0,
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Something Went Wrong'], 500);
        }
    }

    public function viewOnlineQuestionModal($id)
    {

        try {
            $question_bank = QuestionBank::find($id);
            return view('quiz::online_eaxm_question_view_modal', compact('question_bank'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function quizResult(Request $request)
    {
        $category = $request->get('category');
        $sub_category = $request->get('sub_category');
        $quiz_id = $request->get('quiz');


        try {

            $categories = Category::all();

            if ($request->category) {
                $category_search = $request->category;
            } else {
                $category_search = '';

            }

            if ($request->sub_category) {
                $subcategory_search = $request->sub_category;
            } else {
                $subcategory_search = '';
            }

            if ($request->course) {
                $course_search = $request->course;
            } else {
                $course_search = '';
            }


            $query = QuizTest::with('details', 'user');

            if (Auth::user()->role_id != 1 && isModuleActive('OrgInstructorPolicy')) {
                $ids = [];
                $code = [];
                $user_qurey = DB::table('users')->where('role_id', 3)->where('teach_via', 1);
                if (Auth::user()->policy) {
                    $branches = Auth::user()->policy->branches;
                    foreach ($branches as $branch) {
                        $code[] = $branch->branch->code;
                    }
                    $user_qurey->whereIn('org_chart_code', $code);
                }
                $ids = $user_qurey->select('id')->pluck('id');


                $query->whereIn('user_id', $ids);
            }

            $allReports = $query->latest()->get();


            $reports = [];
            foreach ($allReports as $key => $report) {
                $quiz = OnlineQuiz::with('category', 'subCategory')->where('id', $report->quiz_id)->first();
                if ($quiz) {
                    if ((empty($category) || $quiz->category_id == $category) &&
                        (empty($sub_category) || $quiz->sub_category_id == $sub_category) &&
                        (empty($quiz_id) || $quiz->id == $quiz_id)
                    ) {

                        $reports[$key]['date'] = showDate($report->start_at) ?? "";
                        $reports[$key]['status'] = $report->publish ?? "";
                        $reports[$key]['pass'] = $report->pass ?? "";
                        $reports[$key]['user_name'] = $report->user->name ?? "";
                        $reports[$key]['category'] = $quiz->category->name ?? "";
                        $reports[$key]['subCategory'] = $quiz->subCategory->name ?? "";
                        $reports[$key]['quiz'] = $quiz->title ?? "";


                        $totalCorrect = $report->details->where('status', 1)->sum('mark');
                        $totalMark = $quiz->totalMarks();

                        $reports[$key]['totalMarks'] = $totalMark;
                        $reports[$key]['marks'] = $totalCorrect;


                    }
                }

            }

            return view('quiz::online_exam_report', compact('course_search', 'subcategory_search', 'category_search', 'categories', 'reports'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }


    public function quizMarkingStore(Request $request)
    {

        try {
            $test = QuizTest::where('id', $request->quizTestId)->with('details', 'user')->first();

            if ($test->publish == 1) {
                Toastr::error('Marks Already Given', trans('common.Failed'));
                return redirect()->back();
            }
            DB::beginTransaction();

            foreach ($request->question as $key => $question) {
                if ($request->mark[$question] > $request->question_marks[$question]) {
                    Toastr::error('Given Marks Should not greater than question marks', trans('common.Failed'));
                    return redirect()->back();
                } else {
                    $quizDetails = QuizTestDetails::where('quiz_test_id', $test->id)->where('qus_id', $question)->first();
                    if ($request->mark[$question] > 0) {
                        $quizDetails->status = 1;
                    }
                    if ($request->question_type[$question] != 'M') {
                        $quizDetails->mark = $request->mark[$question];
                        $quizDetails->save();
                    }

                }

            }
            $question_given_marks = QuizTestDetails::where('quiz_test_id', $test->id)->sum('mark');
            $quiz_marking = QuizMarking::where('quiz_test_id', $test->id)->where('student_id', $test->user_id)->where('quiz_id', $test->quiz_id)->first();

            $quiz_marking->marked_by = Auth::user()->id ?? 1;
            $quiz_marking->marking_status = 1;
            $quiz_marking->marks = $question_given_marks;
            $quiz_marking->save();

            $quiz = OnlineQuiz::find($test->quiz_id);
            $totalScore = totalQuizMarks($quiz->id);



            $result['passMark'] = $quiz->percentage ?? 0;
            $result['mark'] = $question_given_marks > 0 ? round($question_given_marks / $totalScore * 100) : 0;
            $result['status'] = $result['mark'] >= $result['passMark'] ? "Passed" : "Failed";
            $result['text_color'] = $result['mark'] >= $result['passMark'] ? "success_text" : "error_text";
            $test->pass = $result['mark'] >= $result['passMark'] ? "1" : "0";
            $test->publish = 1;
            $test->save();
            DB::commit();

            if (UserEmailNotificationSetup('QUIZ_RESULT_TEMPLATE', $test->user)) {
                send_email($test->user, 'QUIZ_RESULT_TEMPLATE', [
                    'quiz' => $quiz->title,
                    'mark' => $question_given_marks,
                    'total' => $totalScore,
                    'status' => $test->pass == 1 ? 'Passed' : 'Failed',
                ]);
            }
            if (UserBrowserNotificationSetup('QUIZ_RESULT_TEMPLATE', $test->user)) {

                send_browser_notification($test->user, $type = 'QUIZ_RESULT_TEMPLATE', $shortcodes = [
                    'quiz' => $quiz->title,
                    'mark' => $question_given_marks,
                    'total' => $totalScore,
                    'status' => $test->pass == 1 ? 'Passed' : 'Failed',
                ],
                    '',//actionText
                    ''//actionUrl
                );
            }
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect('quiz/quiz-enrolled-student/' . $test->quiz_id);
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function enrolledStudent($id, Request $request)
    {
        try {
            $quiz_type = '';
            if ($request->type) {
                $type = $request->type;
                if ($type == "Course") {
                    $quiz_type = 1;
                } elseif ($type == "Quiz") {
                    $quiz_type = 2;

                }

            } else {
                $type = '';
            }

            $quiz = OnlineQuiz::find($id);
            if (empty($quiz_type)) {
                $quizTests = QuizTest::where('quiz_id', $quiz->id)->with('details', 'quiz', 'user')->get();
            } else {
                $quizTests = QuizTest::where('quiz_id', $quiz->id)->where('quiz_type', $quiz_type)->with('details', 'quiz', 'user')->get();
            }
            $student_details = [];
            foreach ($quizTests as $key => $test) {
                $student_details[$key]['id'] = $test->user->id;
                $student_details[$key]['role_id'] = $test->user->role_id;
                $student_details[$key]['date'] = showDate($test->start_at);
                $student_details[$key]['name'] = $test->user->name;
                $student_details[$key]['quiz_id'] = $id;
                $student_details[$key]['course_id'] = $test->course_id;
                $student_details[$key]['status'] = $test->publish;
                $student_details[$key]['pass'] = $test->pass;
                $student_details[$key]['duration'] = $test->duration;
                $student_details[$key]['test_id'] = $test->id;
                $student_details[$key]['quizDetails'] = $test->details;

            }
            return view('quiz::online_exam_enrolled', compact('type', 'quiz', 'student_details'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }


    public function markingScript($quiz_test_id)
    {
        try {
            $quizTest = QuizTest::where('id', $quiz_test_id)->with('details', 'user')->first();
            $data = [];

            $user = $quizTest->user->id;

            $questions = [];

            if (Auth::check() && $quizTest->user_id == $user) {

                $quizSetup = QuizeSetup::getData();

                $course = Course::with('quiz')
                    ->where('courses.id', $quizTest->course_id)->first();

                $quiz = OnlineQuiz::with('assign', 'assign.questionBank', 'assign.questionBank.questionMu')->findOrFail($quizTest->quiz_id);

                foreach (@$quiz->assign as $key => $assign) {

                    $questions[$key]['qus_id'] = $assign->questionBank->id;
                    $questions[$key]['qus'] = $assign->questionBank->question;
                    $questions[$key]['type'] = $assign->questionBank->type;
                    $questions[$key]['image'] = $assign->questionBank->image;
                    $questions[$key]['question_marks'] = $assign->questionBank->marks;
                    $test_answer = QuizTestDetails::where('quiz_test_id', $quizTest->id)->where('qus_id', $assign->questionBank->id)->first();
                    if ($test_answer) {
                        $test_ans_mark = $test_answer->mark;
                        $test_ans_answer = $test_answer->answer;
                    } else {
                        $test_ans_mark = 0;
                        $test_ans_answer = '';
                    }

                    $questions[$key]['mark'] = $test_ans_mark;
                    if ($assign->questionBank->type != 'M') {
                        $questions[$key]['answer'] = $test_ans_answer;
                    } else {
                        foreach (@$assign->questionBank->questionMuInSerial as $key2 => $option) {
                            $questions[$key]['option'][$key2]['title'] = $option->title;
                            $questions[$key]['option'][$key2]['right'] = $option->status == 1 ? true : false;

                        }

                        $test = QuizTestDetails::where('quiz_test_id', $quizTest->id)->where('qus_id', $assign->questionBank->id)->first();
                        if ($test) {
                            $questions[$key]['isSubmit'] = true;
                            if ($test->status == 0) {
                                $questions[$key]['option'][$key2]['wrong'] = $test->status == 0 ? true : false;
                                $questions[$key]['isWrong'] = true;
                            }
                        }
                    }


                }


                return view('quiz::online_exam_marking', compact('questions', 'quizSetup', 'course', 'data', 'quizTest'));

            } else {
                Toastr::error('Permission Denied', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }

    public function getTotalQuizNumbers(Request $request)
    {
        if (Auth::check()) {
            $query = QuestionBank::query();
            if (Auth::user()->role_id != 1) {
                $query->where('user_id', Auth::user()->id);
            }
            if (!empty($request->category_id)) {
                $query->where('category_id', $request->category_id);

            }
            if (!empty($request->subcategory_id)) {
                $query->where('sub_category_id', $request->subcategory_id);

            }
            return $query->count();
        } else {
            return 0;
        }
    }
}
