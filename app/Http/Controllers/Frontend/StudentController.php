<?php

namespace App\Http\Controllers\Frontend;


use App\User;
use App\UserLogin;
use Carbon\Carbon;
use App\TopicReport;
use App\StudentCustomField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Certificate\Entities\CertificateRecord;
use Modules\Payment\Entities\Cart;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Coupons\Entities\Coupon;
use Illuminate\Support\Facades\Config;
use Modules\Payment\Entities\Checkout;
use Modules\CourseSetting\Entities\Course;
use Modules\Certificate\Entities\Certificate;
use Modules\Assignment\Entities\InfixAssignment;
use Modules\CourseSetting\Entities\CourseReveiw;
use Modules\CourseSetting\Entities\Notification;
use Modules\Assignment\Entities\InfixAssignAssignment;
use Modules\Quiz\Entities\QuizTest;
use Modules\Subscription\Entities\SubscriptionCheckout;
use Modules\Certificate\Http\Controllers\CertificateController;
use Modules\VirtualClass\Entities\ClassComplete;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('maintenanceMode');
    }

    public function myDashboard()
    {
        try {
            return view(theme('pages.myDashboard'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function myCourses(Request $request)
    {

        try {
            return view(theme('pages.myCourses'), compact('request'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function myWishlists()
    {
        try {
            return view(theme('pages.myWishlists'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function myPurchases()
    {
        try {
            return view(theme('pages.myPurchases'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function myBundle()
    {
        try {
            return view(theme('pages.myBundle'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function topicReport($id)
    {

        try {
            $check = TopicReport::where('report_by', Auth::user()->id)->where('report_for', $id)->first();
            if ($check == null) {
                $report = new TopicReport();
                $report->report_by = Auth::user()->id;
                $report->report_for = $id;
                $report->save();
                Toastr::success('Report is under review', 'Success');
                return redirect()->back();
            } else {

                Toastr::error('You have already done report', 'Failed');
                return redirect()->back();
            }

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function myCertificate()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        try {
            return view(theme('pages.myCertificate'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function myAssignment()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        try {
            return view(theme('pages.myAssignment'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function myProfile()
    {
        try {
            $custom_field = StudentCustomField::getData();
            return view(theme('pages.myProfile'), compact('custom_field'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function myAssignmentDetails($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        try {

            $assign_assignment = InfixAssignAssignment::where('student_id', Auth::user()->id)->where('id', $id)->first();
            if ($assign_assignment == null) {
                Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
                return redirect()->back();
            }
            $assignment_info = InfixAssignment::where('id', $assign_assignment->assignment_id)->first();

            return view(theme('pages.assignment_details'), compact('assignment_info', 'assign_assignment'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function ajaxUploadProfilePic(Request $request)
    {

// dd($request) ;
        try {
            $user = Auth::user();
            $fileName = "";
            if ($request->file('file') != "") {
                $file = $request->file('file');
                $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/profile/', $fileName);
                $fileName = 'public/profile/' . $fileName;
                $user->image = $fileName;
            }
            $user->save();
            return $fileName;
        } catch (\Throwable $th) {
            return $th;
        }

    }

    public function myProfileUpdate(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $custom_field = StudentCustomField::getData();

        if (Auth::user()->role_id == 1) {
            $validate_rules = [
                'name' => 'required',
                'email' => 'required|email',

            ];
        } else {
            $validate_rules = [
                'name' => 'required',
                'email' => 'required|email|unique:users,email,' . Auth::id(),
                'username' => 'required|unique:users,username,' . Auth::id(),
                'phone' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:1|unique:users,phone,' . Auth::id(),
                'address' => 'required',
                'city' => 'required',
                'country' => 'required',
                'zip' => 'required',
                'company_id' => $custom_field->required_company ? 'required' : 'nullable',
                'student_type' => $custom_field->required_student_type ? 'required' : 'nullable',
                'identification_number' => $custom_field->required_identification_number ? 'required' : 'nullable',
                'job_title' => $custom_field->required_job_title ? 'required' : 'nullable',
                'gender' => $custom_field->required_gender ? 'required' : 'nullable',
                'dob' => $custom_field->required_dob ? 'required' : 'nullable',
            ];
        }

        $request->validate($validate_rules, validationMessage($validate_rules));


        try {

            if (demoCheck()) {
                return redirect()->back();
            }

            $lang = explode('|', $request->language ?? '');

            $user = Auth::user();

            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->address = $request->address;
            $user->language_id = $lang[0] ?? 19;
            $user->language_code = $lang[1] ?? 'en';
            $user->language_name = $lang[2] ?? 'English';
            $user->language_rtl = $lang[3] ?? '0';
            $user->city = $request->city;
            $user->country = $request->country;
            $user->state = $request->state;
            $user->zip = $request->zip;

            $user->student_type = $request->student_type;
            $user->identification_number = $request->identification_number;
            $user->job_title = $request->job_title;
            $user->dob = $request->dob;
            $user->gender = $request->gender;

            $user->currency_id = Settings('currency_id');
            $user->facebook = $request->facebook;
            $user->twitter = $request->twitter;
            $user->linkedin = $request->linkedin;
            $user->instagram = $request->instagram;
            $user->youtube = $request->youtube;
            $user->headline = $request->headline;
            $user->about = $request->about;
            $fileName = "";
            if ($request->file('image') != "") {
                $file = $request->file('image');
                $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/profile/', $fileName);
                $fileName = 'public/profile/' . $fileName;
                $user->image = $fileName;
            }
            $user->save();

            if ($request->company_name) {
                $user->company->update([
                    'name' => $request->company_name,
                    'sector' => $request->company_sector,
                    'phone' => $request->company_phone,
                    'address' => $request->company_address,
                ]);
            }


            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {

            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }


    public function myAccount()
    {
        try {
            return view(theme('pages.myAccount'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function MyUpdatePassword(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required_with:new_password|same:new_password|min:8'
        ]);
        try {
            if (demoCheck()) {
                return redirect()->back();
            }

            $user = Auth::user();


            if (!Hash::check($request->old_password, $user->password)) {
                Toastr::error('Password Do not match !', 'Failed');
                return redirect()->back();
            }

            $user->update([
                'password' => bcrypt($request->new_password)
            ]);

            $login = UserLogin::where('user_id', Auth::id())->where('status', 1)->latest()->first();
            if ($login) {
                $login->status = 0;
                $login->logout_at = Carbon::now(Settings('active_time_zone'));
                $login->save();
            }


            send_email($user, 'PASS_UPDATE', [
                'time' => Carbon::now()->format('d-M-Y ,s:i A')
            ]);

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
//            Auth::logout();

            return back();


        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }

    public function MyEmailUpdate(Request $request)
    {
        $request->validate([
            'email' => 'required|unique:users,email,' . Auth::id(),
            'password' => 'required',
        ]);
        try {

            $user = Auth::user();

            if (Config::get('app.app_sync')) {
                Toastr::error('For demo version you can not change this !', 'Failed');
                return redirect()->back();
            } else {
                // $success = trans('lang.Password').' '.trans('lang.Saved').' '.trans('lang.Successfully');


                if (!Hash::check($request->password, $user->password)) {
                    Toastr::error('Password Do not match !', 'Failed');
                    return redirect()->back();
                }

                $user->update([
                    'email' => $request->email
                ]);
                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->back();

            }


        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }

    public function deposit(Request $request)
    {
        try {
            return view(theme('pages.deposit'), compact('request'));

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }

    public function loggedInDevices()
    {
        try {
            return view(theme('pages.log_in_devices'));

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }


    }

    public function logOutDevice(Request $request)
    {
        if (!Hash::check($request->password, auth()->user()->password)) {
            Toastr::error(trans('frontend.Your Password Doesnt Match'));
            return back();
        }

        if (demoCheck()) {
            return redirect()->back();
        }

        $login = UserLogin::find($request->id);
        if (!empty($login->api_token)) {
            DB::table('oauth_access_tokens')->where('id', '=', $login->api_token)->delete();
        }
        Auth::logoutOtherDevices($request->password);
        $login->status = 0;
        $login->logout_at = Carbon::now();
        $login->save();

        Toastr::success(trans('frontend.Logged Out SuccessFully'));
        return back();
    }

    public function Invoice($id)
    {

        try {
            return view(theme('pages.myInvoices'), compact('id'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function subInvoice($id)
    {

        try {

            $enroll = SubscriptionCheckout::where('id', $id)
                ->where('user_id', Auth::user()->id)
                ->with('plan', 'user')->first();

            if ($enroll == null) {
                Toastr::error('Invalid Invoice !', 'Failed');
                return redirect()->back();
            }
            return view(theme('pages.mySubInvoices'), compact('enroll'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function StudentApplyCoupon(Request $request)
    {


        $this->validate($request, [
            'code' => 'required',
            'total' => 'required'
        ]);

        try {


            $code = $request->code;
            if (hasTax()) {
                $tax = taxAmount($request->total);
                $errorTotal = applyTax($request->total);
            } else {
                $tax = 0;
                $errorTotal = $request->total;
            }
            $coupon = Coupon::where('code', $code)->whereDate('start_date', '<=', Carbon::now())
                ->whereDate('end_date', '>=', Carbon::now())->where('status', 1)->first();


            $tracking = Cart::where('user_id', Auth::id())->first()->tracking;


            $couponApply = false;


            $checkout = Checkout::where('tracking', $tracking)->first();

            if (empty($checkout)) {
                $checkout = new Checkout();
            }

            if (isset($coupon)) {
                if ($coupon->limit != 0) {
                    if ($coupon->limit <= $coupon->loginUserTotalUsed()) {
                        return response()->json([
                            'error' => "Already used this coupon",
                            'total' => number_format(getPriceAsNumber($errorTotal), 2),
                            'tax' => number_format(getPriceAsNumber($tax), 2),
                        ], 200);
                    }
                }


                $total = $request->total;
                $max_dis = $coupon->max_discount;
                $min_purchase = $coupon->min_purchase;
                $type = $coupon->type;
                $value = $coupon->value;

                $checkTrackingId = Checkout::where('tracking', $tracking)->where('coupon_id', $coupon)->first();

                if ($checkTrackingId) {
                    return response()->json([
                        'error' => "Already used this coupon",
                        'total' => number_format(getPriceAsNumber($errorTotal), 2),
                        'tax' => number_format(getPriceAsNumber($tax), 2),
                    ], 200);
                }

                if ($total >= $min_purchase) {


                    if ($coupon->category == 1) {
                        $couponApply = true;
                    } elseif ($coupon->category == 2) {

                        if (count($checkout->carts) != 1) {
                            return response()->json([
                                'error' => "This coupon apply for single course",
                                'total' => number_format(getPriceAsNumber($errorTotal), 2),
                                'tax' => number_format(getPriceAsNumber($tax), 2),
                            ], 200);
                        }

                        if ($checkout->carts[0]->course_id == $coupon->course_id) {
                            $couponApply = true;
                        } else {
                            return response()->json([
                                'error' => "This coupon is not valid for this course.",
                                'total' => number_format(getPriceAsNumber($errorTotal), 2),
                                'tax' => number_format(getPriceAsNumber($tax), 2),
                            ], 200);
                        }
                    } elseif ($coupon->category == 3) {
//                        dd();
                        if ($coupon->coupon_user_id != $checkout->user_id) {
                            return response()->json([
                                'error' => "This coupon not for you.",
                                'total' => number_format(getPriceAsNumber($errorTotal), 2),
                                'tax' => number_format(getPriceAsNumber($tax), 2),
                            ], 200);
                        } else {
                            $couponApply = true;
                        }
//                        $couponApply=true;
                    }

                    $final = $total;
                    if ($couponApply) {
                        if ($type == 0) {
                            $discount = (($total * $value) / 100);
                            if ($discount >= $max_dis) {

                                $final = ($total - $max_dis);
                                $checkout->discount = $max_dis;
                                $checkout->purchase_price = $final;
                            } else {

                                $final = ($total - $discount);
                                $checkout->discount = $discount;
                                $checkout->purchase_price = $final;

                            }
                        } else {
                            $discount = $value;
                            if ($discount >= $max_dis) {
                                $final = ($total - $max_dis);

                                $checkout->discount = $max_dis;
                                $checkout->purchase_price = $final;
                            } else {
                                $final = ($total - $discount);
                                $checkout->discount = $discount;
                                $checkout->purchase_price = $final;
                            }
                        }
                    }
                    if ($discount > $total) {
                        return response()->json([
                            'error' => "Invalid Request",
                            'total' => number_format(getPriceAsNumber($errorTotal), 2),
                            'tax' => number_format(getPriceAsNumber($tax), 2),
                        ], 200);
                    }
                    if (hasTax()) {
                        $tax = taxAmount($final);
                        $final = applyTax($final);
                        $checkout->tax = $tax;
                        $checkout->purchase_price = $final;

                    } else {
                        $tax = 0;
                    }
                    $checkout->tracking = $tracking;
                    $checkout->purchase_price = getPriceAsNumber($final);
                    $checkout->user_id = Auth::id();
                    $checkout->coupon_id = $coupon->id;
                    $checkout->price = $total;
                    $checkout->status = 0;
                    $checkout->save();
                    return response()->json([
                        'success' => trans("frontend.Coupon Successfully Applied"),
                        'total' => number_format(getPriceAsNumber($final), 2),
                        'tax' => number_format(getPriceAsNumber($tax), 2),
                        'discount' => number_format(getPriceAsNumber($checkout->discount), 2)
                    ], 200);
                } else {
                    return response()->json([
                        'error' => trans('frontend.Coupon Minimum Purchase Does Not Match'),
                        'total' => number_format(getPriceAsNumber($errorTotal), 2),
                        'tax' => number_format(getPriceAsNumber($tax), 2),
                    ], 200);
                }

            } else {
                $checkout->discount = 0;
                $checkout->coupon_id = null;
                $checkout->purchase_price = $request->total;
                $checkout->save();
                return response()->json([
                    'error' => trans('frontend.Invalid Coupon'),
                    'total' => number_format(getPriceAsNumber($errorTotal), 2),
                    'tax' => number_format(getPriceAsNumber($tax), 2),
                ], 200);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => trans('common.Operation Failed')]);
        }
    }

    public function CheckOut(Request $request)
    {

        try {
            $carts = Cart::where('user_id', Auth::id())->count();
            if ($carts == 0) {
                return redirect('/');
            }

            return view(theme('pages.checkout'), compact('request'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function removeProfilePic()
    {
        if (!Auth::check()) {
            return redirect('login');
        }
        try {
            $user = User::find(Auth::user()->id);
            $user->image = '';
            $user->save();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function getCertificate($id, $slug, Request $request)
    {

        $course = Course::findOrFail($id);
        if (!empty($course->certificate_id)) {
            $certificate = Certificate::find($course->certificate_id);
        } else {
            if ($course->type == 1) {
                $certificate = Certificate::where('for_course', 1)->first();
            } elseif ($course->type == 2) {
                $certificate = Certificate::where('for_quiz', 1)->first();
            } elseif ($course->type == 3) {
                $certificate = Certificate::where('for_class', 1)->first();
            } else {
                $certificate = null;
            }
        }

        if (!$certificate) {
            Toastr::error(trans('certificate.Right Now You Cannot Download The Certificate'));
            return back();
        }


        if (!$course->isLoginUserEnrolled) {
            Toastr::error(trans('certificate.You Are Not Already Enrolled This course. Please Enroll It First'));
            return back();
        }
        if ($course->type == 1) {
            $percentage = round($course->loginUserTotalPercentage);
            if ($percentage < 100) {
                Toastr::error(trans('certificate.Please Complete The Course First'));
                return back();
            }
        } elseif ($course->type == 2) {
            $quiz = QuizTest::where('course_id', $course->id)->where('pass', 1)->first();
            if (!$quiz) {
                Toastr::error(trans('certificate.You must pass the quiz'));
                return back();
            }
        } else {
            $certificateCanDownload = false;
            $totalClass = $course->class->total_class;
            $completeClass = ClassComplete::where('course_id', $course->id)->where('class_id', $course->class->id)->count();
            if ($totalClass == $completeClass) {
                $hasCertificate = $course->certificate_id;
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
            if (!$certificateCanDownload) {
                Toastr::error(trans('certificate.You must attend live class'));
                return back();
            }
        }


        $title = "{$course->slug}-certificate-for-" . Auth::user()->name . ".jpg";

        $downloadFile = new CertificateController();
        $websiteController = new WebsiteController();
        try {
            $certificate_record = CertificateRecord::where('student_id', Auth::user()->id)->where('course_id', $course->id)->first();
            if (!$certificate_record) {
                $certificate_record = new CertificateRecord();
                $certificate_record->certificate_id = $websiteController->generateUniqueCode();
                $certificate_record->student_id = Auth::user()->id;
                $certificate_record->course_id = $course->id;
                $certificate_record->created_by = Auth::user()->id;
                $certificate_record->save();
            }

            $request->certificate_id = $certificate_record->certificate_id;
            $request->course = $course;
            $request->user = Auth::user();
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

    public function submitReview(Request $request)
    {

        $this->validate($request, [
            'review' => 'required',
            'rating' => 'required'
        ]);

        try {
            $user_id = Auth::user()->id;

            $review = CourseReveiw::where('user_id', $user_id)->where('course_id', $request->course_id)->first();

            if (is_null($review)) {

                $newReview = new CourseReveiw();
                $newReview->user_id = $user_id;
                $newReview->course_id = $request->course_id;
                $newReview->comment = $request->review;
                $newReview->star = $request->rating;
                $newReview->save();

                $course = Course::find($request->course_id);
                $total = CourseReveiw::where('course_id', $course->id)->sum('star');
                $count = CourseReveiw::where('course_id', $course->id)->where('status', 1)->count();
                $average = $total / $count;
                $course->reveiw = $average;
                $course->total_rating = $average;
                $course->save();


                $course_user = User::findOrFail($course->user_id);
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
                $average = $total / $count;
                $course->reveiw = $average;
                $course->total_rating = $average;
                $course->save();


                if (UserEmailNotificationSetup('Course_Review', $course->user)) {
                    send_email($course->user, 'Course_Review', [
                        'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                        'course' => $course->title,
                        'review' => $newReview->comment,
                        'star' => $newReview->star,
                    ]);
                }
                if (UserBrowserNotificationSetup('Course_Review', $course->user)) {

                    send_browser_notification($course->user, $type = 'Course_Review', $shortcodes = [
                        'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                        'course' => $course->title,
                        'review' => $newReview->comment,
                        'star' => $newReview->star,
                    ],
                        '',//actionText
                        ''//actionUrl
                    );
                }

                Toastr::success('Review Submit Successfully', 'Success');
                return redirect()->back();
            } else {

                Toastr::error('Invalid Action !', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }

}
