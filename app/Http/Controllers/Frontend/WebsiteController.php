<?php

namespace App\Http\Controllers\Frontend;

use Modules\CourseSetting\Entities\CourseEnrolled;
use PDF;
use App\User;
use Carbon\Carbon;
use App\Subscription;
use App\LessonComplete;
use Illuminate\Http\Request;
use DrewM\MailChimp\MailChimp;
use Illuminate\Support\Facades\DB;
use Modules\Payment\Entities\Cart;
use Modules\Quiz\Entities\QuizTest;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Modules\Quiz\Entities\OnlineQuiz;
use Modules\Quiz\Entities\QuizeSetup;
use Modules\Calendar\Entities\Calendar;
use Illuminate\Support\Facades\Redirect;
use Modules\CourseSetting\Entities\Course;
use Modules\CourseSetting\Entities\Lesson;
use Modules\CourseSetting\Entities\Chapter;
use Modules\Localization\Entities\Language;
use Modules\Certificate\Entities\Certificate;
use Modules\FrontendManage\Entities\FrontPage;
use Modules\CourseSetting\Entities\CourseLevel;
use Modules\Quiz\Entities\QuestionBankMuOption;
use Modules\VirtualClass\Entities\VirtualClass;
use Modules\FrontendManage\Entities\PrivacyPolicy;
use Modules\Newsletter\Entities\NewsletterSetting;
use Modules\Certificate\Entities\CertificateRecord;
use Modules\BundleSubscription\Entities\BundleCoursePlan;
use Modules\Newsletter\Http\Controllers\AcelleController;
use Modules\Certificate\Http\Controllers\CertificateController;
use Modules\Subscription\Http\Controllers\CourseSubscriptionController;

class WebsiteController extends Controller
{
    public function __construct()
    {
        $this->middleware('maintenanceMode');
    }


    public function aboutData()
    {
        try {
            $about = DB::table('about_pages')->first();
            return view(theme('pages.about'), compact('about'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function ajaxCounterCity(Request $request)
    {
        try {
            $cities = DB::table('spn_cities')->select('id', 'name')->where('name', 'like', '%' . $request->search . '%')->where('state_id', '=', $request->id)->paginate(10);

            $response = [];
            foreach ($cities as $item) {
                $response[] = [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            }
            if (count($response) == 0) {
                $data['pagination'] = ["more" => false];
            } else {
                $data['pagination'] = ["more" => true];
            }
            $data['results'] = $response;
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json("", 404);
        }
    }

    public function ajaxCounterState(Request $request)
    {
        try {
            $states = DB::table('states')->select('id', 'name')->where('name', 'like', '%' . $request->search . '%')->where('country_id', '=', $request->id)->paginate(10);

            $response = [];
            foreach ($states as $item) {
                $response[] = [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            }
            $data['results'] = $response;
            if (count($response) == 0) {
                $data['pagination'] = ["more" => false];
            } else {
                $data['pagination'] = ["more" => true];
            }
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json("", 404);
        }
    }

    public function searchCertificate(Request $request)
    {

        return view(theme('pages.searchCertificate'));

    }

    public function showCertificate(Request $request)
    {
        try {
            $certificate_record = CertificateRecord::where('certificate_id', $request->certificate_number)->first();
            if ($certificate_record) {
                $course = Course::findOrFail($certificate_record->course_id);

                if ($course->certificate_id != null) {
                    $certificate = Certificate::findOrFail($course->certificate_id);
                } else {
                    if ($course->type == 1) {
                        $certificate = Certificate::where('for_course', 1)->first();
                    } else {
                        $certificate = Certificate::where('for_quiz', 1)->first();
                    }
                }

                if (!$certificate) {
                    Toastr::error(trans('certificate.Certificate Not Found'));
                    return back();
                }


                $title = $certificate_record->certificate_id . ".jpg";

                $downloadFile = new CertificateController();

                $request->certificate_id = $certificate_record->certificate_id;
                $request->course = $course;
                $request->user = User::find($certificate_record->student_id);
                $certificate = $downloadFile->makeCertificate($certificate->id, $request)['image'] ?? '';
                $certificate->encode('jpg');

                $type = 'png';
                $certificate = 'data:image/' . $type . ';base64,' . base64_encode($certificate);

                return view(theme('pages.searchCertificate'), compact('certificate'));
            } else {
                return Redirect::back()->withErrors(['msg', 'Invalid Certificate Number']);
            }


        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function generateUniqueCode()
    {
        do {
            $referal_code = random_int(100000, 999999);
        } while (CertificateRecord::where("certificate_id", "=", $referal_code)->first());

        return $referal_code;
    }

    public function certificateCheck($certificate_number, Request $request)
    {
        try {
            $certificate_record = CertificateRecord::where('certificate_id', $certificate_number)->first();
            $course = Course::findOrFail($certificate_record->course_id);
            if ($course->certificate_id != null) {
                $certificate = Certificate::findOrFail($course->certificate_id);
            } else {
                if ($course->type == 1) {
                    $certificate = Certificate::where('for_course', 1)->first();
                } else {
                    $certificate = Certificate::where('for_quiz', 1)->first();
                }
            }
            if (!$certificate) {
                Toastr::error(trans('certificate.Right Now You Cannot Download The Certificate'));
                return back();
            }


            $title = $certificate_number . ".jpg";

            $downloadFile = new CertificateController();

            $request->certificate_id = $certificate_record->certificate_id;
            $request->course = $course;
            $request->user = User::find($certificate_record->student_id);
            $certificate = $downloadFile->makeCertificate($certificate->id, $request)['image'] ?? '';
            $certificate->encode('png');
            $type = 'png';
            $certificate = 'data:image/' . $type . ';base64,' . base64_encode($certificate);

            if ($certificate == null) {
                Toastr::error('Invalid Certificate Number !', 'Failed');
                return redirect()->back();
            }
            return view(theme('pages.searchCertificate'), compact('certificate'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function certificateDownload($certificate_number, Request $request)
    {
        try {
            $certificate_record = CertificateRecord::where('certificate_id', $certificate_number)->first();
            $course = Course::findOrFail($certificate_record->course_id);

            if ($course->certificate_id != null) {
                $certificate = Certificate::findOrFail($course->certificate_id);
            } else {
                if ($course->type == 1) {
                    $certificate = Certificate::where('for_course', 1)->first();
                } else {
                    $certificate = Certificate::where('for_quiz', 1)->first();
                }
            }

            if (!$certificate) {
                Toastr::error(trans('certificate.Right Now You Cannot Download The Certificate'));
                return back();
            }


            $title = $certificate_number . ".jpg";

            $downloadFile = new CertificateController();

            $request->certificate_id = $certificate_record->certificate_id;
            $request->course = $course;
            $request->user = User::find($certificate_record->student_id);
            $certificate = $downloadFile->makeCertificate($certificate->id, $request)['image'] ?? '';

            $certificate->encode('jpg');
            $headers = [
                'Content-Type' => 'image/jpeg',
                'Content-Disposition' => 'attachment; filename=' . $title,
            ];
            return response()->stream(function () use ($certificate) {
                echo $certificate;
            }, 200, $headers);
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function privacy()
    {
        try {
            $privacy_policy = PrivacyPolicy::getData();
            return view(theme('pages.privacy'), compact('privacy_policy'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }


    public function fullScreenView(Request $request, $course_id, $lesson_id)
    {

        try {
            updateEnrolledCourseLastView($course_id);

            if (isModuleActive('OrgSubscription') && Auth::check()) {
                if (!orgSubscriptionCourseValidity($course_id)) {
                    Toastr::warning('Your Subscription Expire');
                    return redirect()->back();
                }
            }
            if (isModuleActive('OrgSubscription') && Auth::check()) {
                if (!orgSubscriptionCourseSequence($course_id)) {
                    Toastr::warning('You Can Not Continue This . Pls Complete Previous Course');
                    return redirect()->back();
                }
            }
            if (isModuleActive('BundleSubscription')) {
                if (isBundleExpire($course_id)) {
                    Toastr::error('Your bundle validity expired', 'Access Denied');
                    return redirect()->back();
                }
            }

            $result = [];
            $preResult = [];

            $alreadyJoin = 0;
            if (isset($request->quiz_result_id)) {
                $quizTest = QuizTest::findOrFail($request->quiz_result_id);

                if (Auth::check()) {
                    $user = Auth::user();
                    $all = QuizTest::with('details')->where('quiz_id', $quizTest->quiz_id)->where('course_id', $course_id)->where('user_id', $user->id)->get();
                } else {
                    Toastr::error('You must login for continue', 'Failed');
                    return redirect()->back();
                }

                if (count($all) != 0) {
                    $alreadyJoin = 1;
                }

                foreach ($all as $key => $i) {
                    $onlineQuiz = OnlineQuiz::find($i->quiz_id);
                    $date = showDate($i->created_at);
                    $totalQus = totalQuizQus($i->quiz_id);
                    $totalAns = count($i->details);
                    $totalCorrect = 0;
                    $totalScore = totalQuizMarks($i->quiz_id);
                    $score = 0;
                    if ($totalAns != 0) {
                        foreach ($i->details as $test) {
                            if ($test->status == 1) {
                                $score += $test->mark ?? 1;
                                $totalCorrect++;
                            }

                        }
                    }

                    if ($request->quiz_result_id == $i->id) {

                        $result['start_at'] = $i->start_at;
                        $result['end_at'] = $i->end_at;
                        $result['publish'] = $i->publish;
                        $result['duration'] = $i->duration;
                        $result['totalQus'] = $totalQus;
                        $result['totalAns'] = $totalAns;
                        $result['totalCorrect'] = $totalCorrect;
                        $result['totalWrong'] = $totalAns - $totalCorrect;
                        $result['score'] = $score;
                        $result['totalScore'] = $totalScore;
                        $result['passMark'] = $onlineQuiz->percentage ?? 0;
                        $result['mark'] = $score > 0 ? round($score / $totalScore * 100) : 0;;
                        $result['status'] = $result['mark'] >= $result['passMark'] ? "Passed" : "Failed";
                        $result['text_color'] = $result['mark'] >= $result['passMark'] ? "success_text" : "error_text";
                        $i->pass = $result['mark'] >= $result['passMark'] ? "1" : "0";
                        $i->save();
                    } else {
                        $preResult[$key]['quiz_test_id'] = $i->id;
                        $preResult[$key]['totalQus'] = $totalQus;
                        $preResult[$key]['date'] = $date;
                        $preResult[$key]['totalAns'] = $totalAns;
                        $preResult[$key]['totalCorrect'] = $totalCorrect;
                        $preResult[$key]['totalWrong'] = $totalAns - $totalCorrect;
                        $preResult[$key]['score'] = $score;
                        $preResult[$key]['totalScore'] = $totalScore;
                        $preResult[$key]['passMark'] = $onlineQuiz->percentage ?? 0;
                        $preResult[$key]['mark'] = $score > 0 ? round($score / $totalScore * 100) : 0;;
                        $preResult[$key]['status'] = $preResult[$key]['mark'] >= $preResult[$key]['passMark'] ? "Passed" : "Failed";
                        $preResult[$key]['text_color'] = $preResult[$key]['mark'] >= $preResult[$key]['passMark'] ? "success_text" : "error_text";
                        $i->pass = $preResult[$key]['mark'] >= $preResult[$key]['passMark'] ? "1" : "0";
                        $i->save();
                    }

                    $check = Lesson::where('course_id', $i->course_id)->where('quiz_id', $i->quiz_id)->first();
                    if ($check && $i->pass == 1) {
                        $lesson = LessonComplete::where('course_id', $i->course_id)->where('lesson_id', $check->id)->where('user_id', Auth::id())->first();
                        if (!$lesson) {
                            $lesson = new LessonComplete();
                            $lesson->user_id = Auth::id();
                            $lesson->course_id = $i->course_id;
                            $lesson->lesson_id = $check->id;
                        }
                        $lesson->status = 1;
                        $lesson->save();
                    }


                }
            }

            $course = Course::findOrFail($course_id);
            $lesson = Lesson::where('id', $lesson_id)->first();

            if (!$lesson) {
                abort('404');
            }


            //$lesson->is_lock;
            $isEnrolled = false;

            if ($lesson->is_lock == 1) {
                if (!Auth::check()) {
                    Toastr::error('You are not enrolled for this course !', 'Failed');
                    return redirect()->back();
                } else {
                    if (!$course->isLoginUserEnrolled) {
                        Toastr::error('You are not enrolled for this course !', 'Failed');
                        return redirect()->back();
                    } else {
                        $isEnrolled = true;
                    }
                }

            } else {
                $isEnrolled = true;
            }


            if ($course->type == 1)
                $certificate = Certificate::where('for_course', 1)->first();
            else
                $certificate = Certificate::where('for_quiz', 1)->first();

            //drop content  start

            $today = Carbon::now()->toDateString();
            $showDrip = Settings('show_drip') ?? 0;
            $all = Lesson::where('course_id', $course->id)->with('completed')->orderBy('position', 'asc')->get();;

            $lessons = [];
            if ($course->drip == 1) {
                if ($showDrip == 1) {
                    foreach ($all as $key => $data) {
                        $show = false;
                        $unlock_date = $data->unlock_date;
                        $unlock_days = $data->unlock_days;

                        if (!empty($unlock_days) || !empty($unlock_date)) {

                            if (!empty($unlock_date)) {
                                if (strtotime($unlock_date) == strtotime($today)) {
                                    $show = true;
                                }
                            }
                            if (!empty($unlock_days)) {
                                if (Auth::check()) {
                                    $enrolled = DB::table('course_enrolleds')->where('user_id', Auth::user()->id)->where('course_id', $course->id)->where('status', 1)->first();
                                    if (!empty($enrolled)) {
                                        $unlock = Carbon::parse($enrolled->created_at);
                                        $unlock->addDays($data->unlock_days);
                                        $unlock = $unlock->toDateString();

                                        if (strtotime($unlock) <= strtotime($today)) {
                                            $show = true;
                                        }
                                    }

                                }
                            }

                            if ($show) {
                                $lessons[] = $data;
                            }
                        } else {
                            $lessons[] = $data;
                        }


                    }


                } else {
                    $lessons = $all;
                }
            } else {
                $lessons = $all;
            }

            $total = count($lessons);
            // drop content end

            if ($course->drip != 0) {
                $lessonShow = false;
                $unlock_lesson_date = $lesson->unlock_date;
                $unlock_lesson_days = $lesson->unlock_days;
                if (!empty($unlock_lesson_days) || !empty($unlock_lesson_date)) {
                    if (!empty($unlock_lesson_date)) {
                        if (strtotime($unlock_lesson_date) == strtotime($today)) {
                            $lessonShow = true;
                        }

                    }

                    if (!empty($unlock_lesson_days)) {
                        if (!Auth::check()) {
                            $lessonShow = false;
                        } else {
                            $enrolled = DB::table('course_enrolleds')->where('user_id', Auth::user()->id)->where('course_id', $course_id)->where('status', 1)->first();
                            if (!empty($enrolled)) {
                                $unlock_lesson = Carbon::parse($enrolled->created_at);
                                $unlock_lesson->addDays($lesson->unlock_days);
                                $unlock_lesson = $unlock_lesson->toDateString();

                                if (strtotime($unlock_lesson) <= strtotime($today)) {
                                    $lessonShow = true;

                                }
                            }
                        }

                    }
                } else {
                    $lessonShow = true;
                }
                if (Auth::check() && Auth::user()->role_id == 1) {
                    $lessonShow = true;
                }

                if (!$lessonShow) {
                    Toastr::error('Lesson currently unavailable!', 'Failed');
                    return redirect()->back();
                }
            }


            $percentage = round($course->loginUserTotalPercentage);

            $course_reviews = DB::table('course_reveiws')->select('user_id')->where('course_id', $course->id)->get();

            $reviewer_user_ids = [];
            foreach ($course_reviews as $key => $review) {
                $reviewer_user_ids[] = $review->user_id;
            }
            $chapters = Chapter::where('course_id', $course->id)->orderBy('position', 'asc')->get();
            $quizSetup = QuizeSetup::getData();

            if ($lesson->host == "VdoCipher") {
                $otp = $this->getOTPForVdoCipher($lesson->video_url);
                $lesson->otp = $otp['otp'];
                $lesson->playbackInfo = $otp['playbackInfo'];
            }


            $isAdmin = false;
            if (Auth::check()) {
                if (Auth::user()->role_id == 1) {
                    $isAdmin = true;
                }
            }
            $lesson_ids = [];

            foreach ($chapters as $c) {
                foreach ($all as $item) {
                    if ($c->id == $item->chapter_id) {
                        $lesson_ids[] = $item->id;

                    }
                }
            }
            if (!$isAdmin) {
                if ($course->complete_order == 1) {
                    if (!Auth::check()) {
                        Toastr::error('You must login for continue', 'Failed');
                        return redirect()->back();
                    }

                    $index = array_search($lesson_id, $lesson_ids);


                    $previous = $lesson_ids[$index - 1] ?? null;

                    if ($previous) {
                        $isComplete = DB::table('lesson_completes')->where('lesson_id', $previous)->where('user_id', Auth::user()->id)->select('status')->first();

                        if (!$isComplete || $isComplete->status != 1) {
                            Toastr::error(trans('frontend.At First, You need to complete previous lesson'), trans('Failed'));
                            return redirect()->back();
                        }
                    }
                }
            }

            $quizPass = true;
            if (Auth::check()) {
                $hasQuiz = QuizTest::where('course_id', $course->id)->where('user_id', Auth::user()->id)->groupBy('quiz_id')->get();
                $hasPassQuiz = QuizTest::where('course_id', $course->id)->where('user_id', Auth::user()->id)->where('pass', 1)->groupBy('quiz_id')->get();

                if (count($hasQuiz) != count($hasPassQuiz)) {
                    $quizPass = false;
                }
            }
            return view(theme('pages.fullscreen_video'), compact('quizPass', 'alreadyJoin', 'lesson_ids', 'result', 'preResult', 'quizSetup', 'chapters', 'reviewer_user_ids', 'percentage', 'isEnrolled', 'total', 'certificate', 'course', 'lesson', 'lessons'));

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function getOTPForVdoCipher($video_id)
    {
        $data['otp'] = '';
        $data['playbackInfo'] = '';

        try {
            $url = "https://dev.vdocipher.com/api/videos/" . $video_id . "/otp";

            $curl = curl_init();
            $header = array(
                "Accept: application/json",
                "Authorization:Apisecret " . env('VDOCIPHER_API_SECRET'),
                "Content-Type: application/json"
            );

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode([
                    "ttl" => 300,
                ]),
                CURLOPT_HTTPHEADER => $header,
            ));

            $response = json_decode(curl_exec($curl));
            $err = curl_error($curl);

            curl_close($curl);

            if (!$err) {
                $data['otp'] = $response->otp;
                $data['playbackInfo'] = $response->playbackInfo;
            }
        } catch (\Exception $e) {

        }
        return $data;
    }


    public function subscribe(Request $request)
    {

        if (demoCheck()) {
            return redirect()->back();
        }

        $validate_rules = [
            'email' => 'required|email',
        ];


        $request->validate($validate_rules, validationMessage($validate_rules));


        try {
            if (!hasTable('newsletter_settings')) {

                $check = Subscription::where('email', '=', $request->email)->first();
                if (empty($check)) {
                    $subscribe = new Subscription();
                    $subscribe->email = $request->email;
                    $subscribe->save();

                    Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                } else {
                    Toastr::error('Already subscribe!', 'Failed');
                }
            } else {
                $newsletterSetting = NewsletterSetting::getData();
                if ($newsletterSetting->home_service == "Local") {

                    $check = Subscription::where('email', '=', $request->email)->first();

                    if (empty($check)) {
                        $subscribe = new Subscription();
                        $subscribe->email = $request->email;
                        $subscribe->type = 'Homepage';
                        $subscribe->save();

                        Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                    } else {
                        Toastr::error('Already subscribe!', 'Failed');
                    }
                    return Redirect::back();

                } elseif ($newsletterSetting->home_service == "Mailchimp") {
                    if (saasEnv('MailChimp_Status') == "true") {
                        $list = $newsletterSetting->home_list_id;
                        $MailChimp = new MailChimp(saasEnv('MailChimp_API'));
                        $result = $MailChimp->post("lists/$list/members", [
                            'email_address' => $request->email,
                            'status' => 'subscribed',
                        ]);
                        if ($MailChimp->success()) {
                            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                            return Redirect::back();
                        } else {
                            Toastr::error(json_decode($MailChimp->getLastResponse()['body'], TRUE)['title'] ?? 'Something Went Wrong', trans('common.Failed'));
                            return Redirect::back();
                        }
                    }
                } elseif ($newsletterSetting->home_service == "GetResponse") {
                    if (saasEnv('GET_RESPONSE_STATUS') == "true") {
                        $list = $newsletterSetting->home_list_id;
                        $getResponse = new \GetResponse(saasEnv('GET_RESPONSE_API'));

                        $callback = $getResponse->addContact(array(
                            'email' => $request->email,
                            'campaign' => array('campaignId' => $list),

                        ));


                        if (empty($callback)) {
                            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                            return Redirect::back();
                        } else {
                            Toastr::error($callback->message ?? 'Something Went Wrong', trans('common.Failed'));
                            return Redirect::back();
                        }
                    }
                } elseif ($newsletterSetting->home_service == "Acelle") {
                    if (saasEnv('ACELLE_STATUS') == "true") {

                        $list = $newsletterSetting->home_list_id;
                        $email = $request->email;
                        $make_action_url = '/subscribers?list_uid=' . $list . '&EMAIL=' . $email;
                        $acelleController = new AcelleController();
                        $response = $acelleController->curlPostRequest($make_action_url);

                        if ($response) {
                            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                            return Redirect::back();
                        } else {
                            Toastr::error('Something Went Wrong', trans('common.Failed'));
                            return Redirect::back();
                        }
                    }
                }
                Toastr::error('Something went wrong.', trans('common.Failed'));
            }


            return Redirect::back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }


    public function myCart()
    {
        $checkout = request()->checkout;
        if ($checkout) {
            if (Auth::check()) {
                return \redirect(route('CheckOut'));
            } else {
                session(['redirectTo' => route('CheckOut')]);
                return \redirect(route('login'));
            }
        }
        try {
            if (Auth::check()) {
                return view(theme('pages.myCart'));
            } else {
                return view(theme('pages.myCart2'));
            }


        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }

    public function addToCart($id)
    {
        try {

            $user = Auth::user();

            if (Auth::check() && ($user->role_id != 1)) {

                $exist = Cart::where('user_id', $user->id)->where('course_id', $id)->first();
                $oldCart = Cart::where('user_id', $user->id)->first();


                if (isset($exist)) {
                    Toastr::error('Course already added in your cart', 'Failed');
                    return redirect()->back();
                } elseif (Auth::check() && ($user->role_id == 1)) {
                    Toastr::error('You logged in as admin so can not add cart !', 'Failed');
                    return redirect()->back();
                } else {

                    if (isset($oldCart)) {
                        $course = Course::find($id);
                        $cart = new Cart();
                        $cart->user_id = $user->id;
                        $cart->instructor_id = $course->user_id;
                        $cart->course_id = $id;
                        $cart->tracking = $oldCart->tracking;
                        if ($course->discount_price != null) {
                            $cart->price = $course->discount_price;
                        } else {
                            $cart->price = $course->price;
                        }
                        $cart->save();

                    } else {

                        $course = Course::find($id);
                        $cart = new Cart();
                        $cart->user_id = $user->id;
                        $cart->instructor_id = $course->user_id;
                        $cart->course_id = $id;
                        $cart->tracking = getTrx();
                        if ($course->discount_price != null) {
                            $cart->price = $course->discount_price;
                        } else {
                            $cart->price = $course->price;
                        }
                        $cart->save();
                    }


                    Toastr::success('Course Added to your cart', 'Success');
                    return redirect()->back();
                }

            } //If user not logged in then cart added into session

            else {
                $price = 0;
                $course = Course::find($id);
                if (!$course) {
                    Toastr::error('Course not found', 'Failed');
                    return redirect()->back();
                }

                if ($course->discount_price > 0) {
                    $price = $course->discount_price;
                } else {
                    $price = $course->price;
                }


                $cart = session()->get('cart');
                if (!$cart) {
                    $cart = [
                        $id => [
                            "id" => $course->id,
                            "course_id" => $course->id,
                            "instructor_id" => $course->user_id,
                            "instructor_name" => $course->user->name,
                            "title" => $course->title,
                            "image" => $course->image,
                            "slug" => $course->slug,
                            "type" => $course->type,
                            "price" => $price,
                        ]
                    ];
                    session()->put('cart', $cart);
                    Toastr::success('Course Added to your cart1', 'Success');
                    return redirect()->back();
                } elseif (isset($cart[$id])) {
                    Toastr::error('Course already added in your cart', 'Failed');
                    return redirect()->back();
                } else {

                    $cart[$id] = [

                        "id" => $course->id,
                        "course_id" => $course->id,
                        "instructor_id" => $course->user_id,
                        "instructor_name" => $course->user->name,
                        "title" => $course->title,
                        "image" => $course->image,
                        "slug" => $course->slug,
                        "type" => $course->type,
                        "price" => $price,
                    ];

                    session()->put('cart', $cart);

                    Toastr::success('Course Added to your cart', 'Success');
                    return redirect()->back();

                }


            }
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function buyNow($id)
    {
        try {
            $user = Auth::user();
            if (Auth::check() && ($user->role_id != 1)) {

                $exist = Cart::where('user_id', $user->id)->where('course_id', $id)->first();
                $oldCart = Cart::where('user_id', $user->id)->first();
                $course = Course::find($id);

                if (isset($exist)) {
                    Toastr::error('Course already added in your cart', 'Failed');
                    return redirect()->back();
                } elseif (Auth::check() && ($user->role_id == 1)) {
                    Toastr::error('You logged in as admin so can not add cart !', 'Failed');
                    return redirect()->back();
                } else {

                    if (isset($oldCart)) {
                        $course = Course::find($id);
                        $cart = new Cart();
                        $cart->user_id = $user->id;
                        $cart->instructor_id = $course->user_id;
                        $cart->course_id = $id;
                        $cart->tracking = $oldCart->tracking;
                        if ($course->discount_price != null) {
                            $cart->price = $course->discount_price;
                        } else {
                            $cart->price = $course->price;
                        }
                        $cart->save();

                    } else {

                        $course = Course::find($id);
                        $cart = new Cart();
                        $cart->user_id = $user->id;
                        $cart->instructor_id = $course->user_id;
                        $cart->course_id = $id;
                        $cart->tracking = getTrx();
                        if ($course->discount_price != null) {
                            $cart->price = $course->discount_price;
                        } else {
                            $cart->price = $course->price;
                        }
                        $cart->save();
                    }


                    Toastr::success('Course Added to your cart', 'Success');
                    return redirect(route('CheckOut'));
                }

            } //If user not logged in then cart added into session

            else {
                $price = 0;
                $course = Course::find($id);
                if (!$course) {
                    Toastr::error('Course not found', 'Failed');
                    return redirect()->back();
                }

                if ($course->discount_price > 0) {
                    $price = $course->discount_price;
                } else {
                    $price = $course->price;
                }


                $cart = session()->get('cart');
                if (!$cart) {
                    $cart = [
                        $id => [
                            "id" => $course->id,
                            "course_id" => $course->id,
                            "instructor_id" => $course->user_id,
                            "instructor_name" => $course->user->name,
                            "title" => $course->title,
                            "image" => $course->image,
                            "slug" => $course->slug,
                            "type" => $course->type,
                            "price" => $price,
                        ]
                    ];
                    session()->put('cart', $cart);
                    Toastr::success('Course Added to your cart1', 'Success');
                    return redirect()->back();
                } elseif (isset($cart[$id])) {
                    Toastr::error('Course already added in your cart', 'Failed');
                    return redirect()->back();
                } else {

                    $cart[$id] = [

                        "id" => $course->id,
                        "course_id" => $course->id,
                        "instructor_id" => $course->user_id,
                        "instructor_name" => $course->user->name,
                        "title" => $course->title,
                        "image" => $course->image,
                        "slug" => $course->slug,
                        "type" => $course->type,
                        "price" => $price,
                    ];

                    session()->put('cart', $cart);

                    Toastr::success('Course Added to your cart', 'Success');
                    return redirect()->back();

                }


            }
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function removeItem($id)
    {
        try {
            $success = trans('lang.Cart has been Removed Successfully');
            if (Auth::check()) {

                $item = Cart::find($id);
                if ($item) {
                    $item->delete();
                }
                Toastr::success('Course removed from your cart', 'Success');
                return redirect()->back();
            } else {

                $cart = session()->get('cart');

                if (isset($cart[$id])) {
                    if (count($cart) == 1) {
                        unset($cart[$id]);
                        session()->forget('cart');
                    } else {
                        unset($cart[$id]);
                    }


                    session()->put('cart', $cart);
                    Toastr::success('Course removed from your cart', 'Success');
                    return redirect()->back();
                }
            }
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function removeItemAjax($id)
    {
        try {

            if (Auth::check()) {

                $item = Cart::find($id);

                if ($item) {
                    $item->delete();
                }
                return true;
            } else {

                $cart = session()->get('cart');

                if (isset($cart[$id])) {
                    if (count($cart) == 1) {
                        unset($cart[$id]);
                        session()->forget('cart');
                    } else {
                        unset($cart[$id]);
                    }


                    session()->put('cart', $cart);
                    return true;
                }
            }
        } catch (\Exception $e) {
            return false;
        }
    }


    public function categoryCourse(Request $request, $id, $name)
    {
        try {

            return view(theme('pages.search'), compact('request', 'id'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function subCategoryCourse(Request $request, $id, $name)
    {
        $quiz_id = OnlineQuiz::where('sub_category_id', $id)->get()->pluck('id')->toArray();
        $course_id = Course::where('subcategory_id', $id)->get()->pluck('id')->toArray();
        $class_id = VirtualClass::where('sub_category_id', $id)->get()->pluck('id')->toArray();


        $query = Course::with('user', 'category', 'subCategory', 'enrolls', 'comments', 'reviews', 'lessons', 'quiz', 'class')
            ->where('status', 1)
            ->latest();


        $query->where(function ($q) use ($quiz_id, $course_id, $class_id) {
            $q->whereIn('quiz_id', $quiz_id)
                ->orWhereIn('id', $course_id)
                ->orWhereIn('class_id', $class_id);
        });

        $type = $request->type;
        if (empty($type)) {
            $type = '';
        } else {
            $types = explode(',', $type);
            if (count($types) == 1) {
                foreach ($types as $t) {
                    if ($t == 'free') {
                        $query->where('price', 0);
                    } elseif ($t == 'paid') {
                        $query = $query->where('price', '>', 0);
                    }
                }
            }
        }

        $language = $request->language;
        if (empty($language)) {
            $language = '';
        } else {
            $row_languages = explode(',', $language);
            $languages = [];
            $LanguageList = Language::whereIn('code', $row_languages)->first();
            foreach ($row_languages as $l) {
                $lang = $LanguageList->where('code', $l)->first();
                if ($lang) {
                    $languages[] = $lang->id;
                }
            }
            $query->whereIn('lang_id', $languages);
        }


        $level = $request->level;
        if (empty($level)) {
            $level = '';
        } else {
            $levels = explode(',', $level);
            $query->whereIn('level', $levels);
        }

        $mode = $request->mode;
        if (empty($mode)) {
            $mode = '';
        } else {
            $modes = explode(',', $mode);
            $query->whereIn('mode_of_delivery', $modes);
        }

        $order = $request->order;
        if (empty($order)) {
            $order = '';
        } else {
            if ($order == "price") {
                $query->orderBy('price', 'asc');
            } else {
                $query->latest();
            }
        }

        $courses = $query->paginate(9);
        $total = $courses->total();
        $levels = CourseLevel::select('id', 'title')->where('status', 1)->get();

        return view(theme('pages.search'), compact('levels', 'order', 'level', 'order', 'mode', 'language', 'type', 'total', 'courses', 'request', 'id'));

    }


    public function fetch_course(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');
            $data = DB::table('courses')
                ->where('title', 'LIKE', "%{$query}%")
                ->get();
            $output = '<ul>';

            foreach ($data as $row) {

                $output .= '
                        <li>
                            <a style="color:black" href="' . courseDetailsUrl(@$row->id, @$row->type, @$row->slug) . '">' . $row->title . '</a>
                        </li>
                        ';

            }
            $output .= '</ul>';
            echo $output;
        }
    }


    public function submitAns(Request $request)
    {
        $setting = QuizeSetup::getData();

        $qusAns = $request->get('qusAns');

        $array = explode('|', $qusAns);
        $ansId = $array[1];
        $qusId = $array[0];
        $userId = Auth::id() ?? 1;

        $question_review = $setting->question_review;
        $show_result_each_submit = $setting->show_result_each_submit;


        if ($request->get('courseId')) {
            $courseId = $request->get('courseId');


            if (!empty($qusAns)) {
                $totalQusSubmit = QuizTest::where('user_id', $userId)->count();
                $test = QuizTest::where('user_id', $userId)->where('course_id', $courseId)->where('question_id', $qusId)->first();

                if (empty($test)) {
                    $test = new QuizTest();
                    $test->user_id = $userId;
                    $test->course_id = $courseId;
                    $test->quiz_id = $request->get('quizId');
                    $test->question_id = $qusId;
                    $test->ans_id = $ansId;
                    $test->status = $question_review == 1 ? 0 : 1;
                    $test->count = $totalQusSubmit + 1;
                    $test->date = date('m/d/Y');
                    $test->save();
                } else {
                    if ($question_review == 1) {
                        $test->ans_id = $ansId;
                        $test->save();
                    } else {
                        return response()->json(['error' => 'Already Submitted'], 500);
                    }

                }

            }

            if ($show_result_each_submit == 1) {
                $ans = QuestionBankMuOption::find($ansId);

                if ($ans->status == 1) {
                    $result = true;
                } else {
                    $result = false;
                }

                return response()->json(['result' => $result], 200);
            } else {
                return response()->json(['submit' => true], 200);

            }


        } else {
            return response()->json(['error' => 'Something Went Wrong'], 500);
        }
    }


    public function getResult($courseId, $quizId)
    {
        $userId = Auth::id() ?? 1;
        $alreadySubmitTest = QuizTest::where('user_id', $userId)->where('course_id', $courseId)->where('quiz_id', $quizId)->distinct()->get();
        $quiz = OnlineQuiz::find($quizId);
        $totalQus = totalQuizQus($quiz->id);
        $totalAns = count($alreadySubmitTest);
        $totalCorrect = 0;
        $totalScore = totalQuizMarks($quizId);
        $score = 0;
        if ($totalAns != 0) {
            $hasResult = true;
            foreach ($alreadySubmitTest as $test) {
                $test->status = 1;
                $test->save();
                $ans = QuestionBankMuOption::find($test->ans_id);

                if (!empty($ans)) {
                    if ($ans->status == 1) {

                        $score += $ans->question->marks ?? 1;
                        $totalCorrect++;
//                        $totalScore +=$ans->
                    }
                }

            }
        } else {
            $hasResult = false;
        }

        $output = '';

        $output .= ' Total Question ' . $totalQus . '<br>';
        $output .= ' Total Ans ' . $totalAns . '<br>';
        $output .= ' Total Correct ' . $totalCorrect . '<br>';
        $output .= ' Score ' . $score . ' out of ' . $totalScore . ' <br>';
        return ['hasResult' => $hasResult, 'output' => $output];;
    }

    public function contact()
    {
        try {
            $page_content = app('getHomeContent');
            return view(theme('pages.contact'), compact('page_content'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function contactMsgSubmit(Request $request)
    {

        if (saasEnv('NOCAPTCHA_FOR_CONTACT') == 'true') {
            $validate_rules = [
                'name' => 'required',
                'email' => 'required|email',
                'message' => 'required',
                'subject' => 'required',
                'g-recaptcha-response' => 'required|captcha'
            ];
        } else {
            $validate_rules = [
                'name' => 'required',
                'email' => 'required|email',
                'message' => 'required',
                'subject' => 'required',
            ];
        }

        $request->validate($validate_rules, validationMessage($validate_rules));

        if (appMode()) {
            Toastr::error('For demo version you can not send message', trans('common.Failed'));
            return redirect()->back();
        }

        $name = $request->get('name');
        $email = $request->get('email');
        $message = $request->get('message');
        $subject = $request->get('subject');


        $admin = User::where('role_id', 1)->first();


        $send = send_email($admin, 'CONTACT_MESSAGE', [
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'subject' => $subject
        ]);


        if ($send) {
            Toastr::success('Successfully Sent Message', trans('common.Success'));
            return redirect()->back();

        } else {
            Toastr::error('Something went wrong', trans('common.Failed'));
            return redirect()->back();
        }
    }

    public function frontPage($slug)
    {
        try {
            $page = FrontPage::where('slug', $slug)->first();
            if (!$page) {
                Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
                return redirect()->back();
            }

            if ($page->status != 1) {
                Toastr::error('Sorry. Page is not active', trans('common.Failed'));
                return redirect()->back();
            }
            return view(theme('pages.page'), compact('page'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }


    public function search(Request $request)
    {
        try {
            $id = 0;
            return view(theme('pages.search'), compact('request', 'id'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function enrollOrCart($id)
    {


        $course = Course::findOrFail($id);

        $output = [];

        //add to cart
        $output['type'] = 'addToCart';


        try {
            $user = Auth::user();
            if (Auth::check() && ($user->role_id != 1)) {
                if (!$course->isLoginUserEnrolled) {
                    $exist = Cart::where('user_id', $user->id)->where('course_id', $id)->first();
                    $oldCart = Cart::where('user_id', $user->id)->first();


                    if (isset($exist)) {
                        $output['result'] = 'failed';
                        $output['message'] = 'Course already added in your cart';
                    } elseif (Auth::check() && ($user->role_id == 1)) {
                        $output['result'] = 'failed';
                        $output['message'] = 'You logged in as admin so can not add cart';
                    } else {

                        if (isset($oldCart)) {

                            $cart = new Cart();
                            $cart->user_id = $user->id;
                            $cart->instructor_id = $course->user_id;
                            $cart->course_id = $id;
                            $cart->tracking = $oldCart->tracking;
                            if ($course->discount_price != null) {
                                $cart->price = $course->discount_price;
                            } else {
                                $cart->price = $course->price;
                            }
                            $cart->save();

                        } else {
                            $cart = new Cart();
                            $cart->user_id = $user->id;
                            $cart->instructor_id = $course->user_id;
                            $cart->course_id = $id;
                            $cart->tracking = getTrx();
                            if ($course->discount_price != null) {
                                $cart->price = $course->discount_price;
                            } else {
                                $cart->price = $course->price;
                            }
                            $cart->save();
                        }

                        $output['result'] = 'success';
                        $output['total'] = cartItem();
                        $output['message'] = 'Course Added to your cart';
                    }
                } else {
                    $output['result'] = 'failed';
                    $output['message'] = 'Already Enrolled ';
                }

            } //If user not logged in then cart added into session

            else {

                $course = Course::find($id);
                if (!$course) {
                    $output['result'] = 'failed';
                    $output['message'] = 'Course not found';

                }

                if ($course->discount_price > 0) {
                    $price = $course->discount_price;
                } else {
                    $price = $course->price;
                }


                $cart = session()->get('cart');
                if (!$cart) {
                    $cart = [
                        $id => [
                            "id" => $course->id,
                            "course_id" => $course->id,
                            "instructor_id" => $course->user_id,
                            "instructor_name" => $course->user->name,
                            "title" => $course->title,
                            "image" => $course->image,
                            "slug" => $course->slug,
                            "type" => $course->type,
                            "price" => $price,
                        ]
                    ];
                    session()->put('cart', $cart);

                    $output['result'] = 'success';
                    $output['total'] = cartItem();
                    $output['message'] = 'Course Added to your cart';
                } elseif (isset($cart[$id])) {
                    $output['result'] = 'failed';
                    $output['message'] = 'Course already added in your cart';
                } else {

                    $cart[$id] = [

                        "id" => $course->id,
                        "course_id" => $course->id,
                        "instructor_id" => $course->user_id,
                        "instructor_name" => $course->user->name,
                        "title" => $course->title,
                        "image" => $course->image,
                        "slug" => $course->slug,
                        "price" => $price,
                    ];

                    session()->put('cart', $cart);

                    $output['result'] = 'success';
                    $output['total'] = cartItem();
                    $output['message'] = 'Course Added to your cart';

                }

            }
        } catch (\Exception $e) {
            $output['result'] = 'failed';
            $output['message'] = 'Operation Failed !';
        }


        return json_encode($output);
    }

    public function getItemList()
    {
        $carts = [];
        if (Auth::check()) {
            $items = Cart::where('user_id', Auth::id())->with('course', 'course', 'course.user')->get();
            if (!empty($items)) {
                foreach ($items as $key => $cart) {
                    $check = Course::find($cart['course_id']);
                    if ($check) {
                        $carts[$key]['id'] = $cart['id'];
                        $carts[$key]['course_id'] = $cart['course_id'];
                        $carts[$key]['instructor_id'] = $cart['instructor_id'];
                        $carts[$key]['title'] = $cart->course->title;
                        $carts[$key]['instructor_name'] = $cart->course->user->name;
                        $carts[$key]['image'] = getCourseImage($cart->course->thumbnail);
                        if ($cart->course->discount_price != null) {
                            $carts[$key]['price'] = getPriceFormat($cart->course->discount_price);
                        } else {
                            $carts[$key]['price'] = getPriceFormat($cart->course->price);
                        }
                    }

                    if (isModuleActive('BundleSubscription')) {
                        $bundleCheck = BundleCoursePlan::find($cart['bundle_course_id']);
                        if ($bundleCheck) {
                            $carts[$key]['id'] = $cart['id'];
                            $carts[$key]['course_id'] = $cart['course_id'];
                            $carts[$key]['instructor_id'] = $cart['instructor_id'];
                            $carts[$key]['title'] = $bundleCheck->title;
                            $carts[$key]['instructor_name'] = $bundleCheck->user->name;
                            $carts[$key]['image'] = getCourseImage($bundleCheck->icon);
                            $carts[$key]['price'] = getPriceFormat($bundleCheck->price);
                        }
                    }

                }
            }


        } else {
            $items = session()->get('cart');
            if (!empty($items)) {
                foreach ($items as $key => $cart) {
                    $course = Course::find($cart['course_id']);
                    if ($course) {
                        $carts[$key]['id'] = $cart['id'];
                        $carts[$key]['course_id'] = $course->id;
                        $carts[$key]['instructor_id'] = $course->user_id;
                        $carts[$key]['title'] = $course->title;
                        $carts[$key]['type'] = $course->type;
                        $carts[$key]['instructor_name'] = $course->user->name;
                        $carts[$key]['image'] = getCourseImage($course->thumbnail);

                        if ($course->discount_price != null) {
                            $carts[$key]['price'] = getPriceFormat($course->discount_price);
                        } else {
                            $carts[$key]['price'] = getPriceFormat($course->price);
                        }
                    }
                }
            }


        }


        return json_encode($carts);
    }


    public function lessonComplete(Request $request)
    {


        try {
            $lesson = LessonComplete::where('course_id', $request->course_id)->where('lesson_id', $request->lesson_id)->where('user_id', Auth::id())->first();
            $certificateBtn = 0;
            if (!$lesson) {
                $lesson = new LessonComplete();
                $lesson->user_id = Auth::id();
                $lesson->course_id = $request->course_id;
                $lesson->lesson_id = $request->lesson_id;
            }
            $lesson->status = $request->status;
            if ($request->status == 1)
                $success = trans('frontend.Lesson Marked as Complete');
            else
                $success = trans('frontend.Lesson Marked as Incomplete');
            $lesson->save();

            $course = Course::find($request->course_id);
            if ($course) {
                $percentage = round($course->loginUserTotalPercentage);
                if ($percentage >= 100) {
                    $this->getCertificateRecord($course->id);


                    if (UserEmailNotificationSetup('Complete_Course', Auth::user())) {
                        send_email(Auth::user(), 'Complete_Course', [
                            'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                            'course' => $course->title
                        ]);
                    }
                    if (UserBrowserNotificationSetup('Complete_Course', Auth::user())) {

                        send_browser_notification(Auth::user(), $type = 'Complete_Course', $shortcodes = [
                            'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                            'course' => $course->title
                        ],
                            '',//actionText
                            ''//actionUrl
                        );
                    }
                }
            }
            if (count($lesson->course->lessons) == count($lesson->course->completeLessons->where('status', 1)))
                $certificateBtn = 1;


            $previousUrl = app('url')->previous();

            return redirect()->to($previousUrl . '&' . http_build_query(['done' => 1]));


        } catch (\Exception $e) {

            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }

    public function lessonCompleteAjax(Request $request)
    {


        try {
            $lesson = LessonComplete::where('course_id', $request->course_id)->where('lesson_id', $request->lesson_id)->where('user_id', Auth::id())->first();
            $enrolled = CourseEnrolled::where('course_id', $request->course_id)->where('user_id', Auth::id())->first();

            if (!$lesson) {
                $lesson = new LessonComplete();
                $lesson->user_id = Auth::id();
                $lesson->course_id = $request->course_id;
                $lesson->lesson_id = $request->lesson_id;
                $lesson->enroll_id = @$enrolled->id;
            }
            $lesson->status = 1;
            $lesson->save();

            $course = Course::find($request->course_id);
            if ($course) {
                $percentage = round($course->loginUserTotalPercentage);
                if ($percentage >= 100) {

                    if ($enrolled->pathway_id != null) {
                        StudentSkillController::studentCreateSkill(2, $course->id, Auth::user(), $enrolled);
                    } else {
                        StudentSkillController::studentCreateSkill(1, $course->id, Auth::user(), $enrolled);
                    }

                    $this->getCertificateRecord($course->id);


                    if (UserEmailNotificationSetup('Complete_Course', Auth::user())) {
                        send_email(Auth::user(), 'Complete_Course', [
                            'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                            'course' => $course->title
                        ]);
                    }
                    if (UserBrowserNotificationSetup('Complete_Course', Auth::user())) {

                        send_browser_notification(Auth::user(), $type = 'Complete_Course', $shortcodes = [
                            'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                            'course' => $course->title
                        ],
                            '',//actionText
                            ''//actionUrl
                        );
                    }
                }
            }

            return true;


        } catch (\Exception $e) {
            return false;
        }

    }

    public function getCertificateRecord($course_id)
    {
        $websiteController = new WebsiteController();
        try {
            $certificate_record = CertificateRecord::where('student_id', Auth::user()->id)->where('course_id', $course_id)->first();
            if (!$certificate_record) {
                $certificate_record = new CertificateRecord();
                $certificate_record->certificate_id = $websiteController->generateUniqueCode();
                $certificate_record->student_id = Auth::user()->id;
                $certificate_record->course_id = $course_id;
                $certificate_record->created_by = Auth::user()->id;
                $certificate_record->save();
            }


        } catch (\Exception $e) {
            return null;
        }
    }


    public function subscriptionCourses()
    {
        if (!Auth::check()) {
            return redirect('login');
        }
        if (isModuleActive('Subscription')) {
            if (!isSubscribe()) {
                Toastr::error('You must subscribe first', 'Error');
                return redirect()->back();
            }
        }
        try {

            return view(theme('pages.subscription-courses'));

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function orgSubscriptionCourses(Request $request)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        try {

            return view(theme('pages.org-subscription-courses'), compact('request'));

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function orgSubscriptionPlanList($planId, Request $request)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        try {
            return view(theme('pages.org-subscription-plan-list'), compact('request', 'planId'));

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function subscription(Request $request)
    {
        if (isModuleActive('Subscription')) {


            return view(theme('pages.subscription'));

        } else {
            Toastr::error('Module not active', 'Error');
            return redirect()->back();
        }
    }

    public function subscriptionCourseList(Request $request, $plan_id)
    {
        try {
            if (isModuleActive('Subscription')) {
                return view(theme('pages.subscription_course_list'), compact('plan_id'));
            } else {
                Toastr::error('Module not active', 'Error');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function subscriptionCheckout(Request $request)
    {


        if (empty($request->plan)) {
            $s_plan = '';
        } else {
            $s_plan = $request->plan;
        }

        if (empty($request->price)) {
            $price = '';
        } else {
            $price = $request->price;
        }

        if (!empty($s_plan) && !empty($price)) {
            if (Auth::check()) {
                if (Auth::user()->role_id == 3) {
                    $subscription = new CourseSubscriptionController();
                    $addCart = $subscription->addToCart(Auth::user()->id, $s_plan);

                    if (!$addCart) {
                        Toastr::error('Invalid Request', 'Error');
                        return \redirect()->route('courseSubscription');
                    }
                } else {
                    Toastr::error('You must login as a student', 'Error');
                    return \redirect()->route('courseSubscription');
                }

            } else {
                Toastr::error('You must login', 'Error');
                return \redirect()->route('login');
            }
        } else {
            Toastr::error('Invalid Request ', 'Error');
            return \redirect()->route('login');
        }


        return view(theme('pages.subscriptionCheckout'), compact('request', 's_plan', 'price'));

    }


    public function continueCourse($slug)
    {
        try {
            $lesson_id = null;
            if (!Auth::check()) {
                Toastr::error('You must login for continue', 'Failed');
                return redirect()->route('courseDetailsView', $slug);
            }
            $user = Auth::user();
            $course = Course::where('slug', $slug)->first();
            if (!$course) {
                Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
                return redirect()->route('courseDetailsView', $slug);
            }
            $isEnrolled = $course->isLoginUserEnrolled;

            if ($isEnrolled) {
                $lesson = LessonComplete::where('course_id', $course->id)->where('user_id', $user->id)->has('lesson')->orderBy('updated_at', 'desc')->first();
                if (empty($lesson)) {
                    $chapter = Chapter::where('course_id', $course->id)->whereHas('lessons')->orderBy('position', 'asc')->first();
                    if (empty($chapter)) {
                        Toastr::error('No lesson found', 'Failed');
                        return redirect()->route('courseDetailsView', $slug);
                    }
                    $lesson = Lesson::where('course_id', $course->id)->where('chapter_id', $chapter->id)->orderBy('position', 'asc')->first();
                    if (!empty($lesson)) {
                        $lesson_id = $lesson->id;
                    }
                } else {
                    $lesson_id = $lesson->lesson_id;
                }

                if (!empty($lesson_id)) {

                    return \redirect()->to(url('fullscreen-view/' . $course->id . '/' . $lesson_id));
                } else {
                    Toastr::error('There is no lesson for this course!', 'Failed');
                    return redirect()->route('courseDetailsView', $slug);
                }


            } else {
                Toastr::error('You are not enrolled for this course !', 'Failed');
                return redirect()->route('courseDetailsView', $slug);
            }

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function vimeoPlayer($video_id)
    {
        return view('vimeo_player', compact('video_id'));
    }

    public function offline()
    {
        return 'offline';
    }


    public function test()
    {
        return 'okk';
    }

    public function calendarView()
    {
        try {
            $calendars = Calendar::with('course', 'event', 'course.user', 'course.user.role')->get();

            //    return $calendars;
            return view(theme('pages.calendarView'), compact('calendars'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

}
