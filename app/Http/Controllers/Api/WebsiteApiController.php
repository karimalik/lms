<?php

namespace App\Http\Controllers\Api;

use App\BillingDetails;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentController;
use App\LessonComplete;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Coupons\Entities\Coupon;
use Modules\Coupons\Entities\UserWiseCoupon;
use Modules\Coupons\Entities\UserWiseCouponSetting;
use Modules\CourseSetting\Entities\Course;
use Modules\CourseSetting\Entities\CourseComment;
use Modules\CourseSetting\Entities\CourseCommentReply;
use Modules\CourseSetting\Entities\CourseEnrolled;
use Modules\CourseSetting\Entities\CourseReveiw;
use Modules\CourseSetting\Entities\Notification;
use Modules\Payment\Entities\Cart;
use Modules\Payment\Entities\Checkout;
use Modules\Payment\Entities\InstructorPayout;
use Modules\PaymentMethodSetting\Entities\PaymentMethod;
use Modules\Quiz\Entities\OnlineExamQuestionAssign;
use Modules\Quiz\Entities\OnlineQuiz;
use Modules\Quiz\Entities\QuestionBankMuOption;
use Modules\Quiz\Entities\QuizMarking;
use Modules\Quiz\Entities\QuizTest;
use Modules\Quiz\Entities\QuizTestDetails;
use Modules\Quiz\Entities\QuizTestDetailsAnswer;
use Modules\Setting\Model\GeneralSetting;
use Modules\VirtualClass\Entities\VirtualClass;
use paytm\paytmchecksum\PaytmChecksum;
use JoisarJignesh\Bigbluebutton\Facades\Bigbluebutton;
use Modules\BBB\Entities\BbbMeeting;

/**
 * @group  Frontend Api
 *
 * APIs for managing frontend api
 */
class WebsiteApiController extends Controller
{
    /**
     * Cart List
     * @response {
     * "success": true,
     * "data": [
     * {
     * "id": 1,
     * "course_id": 1,
     * "user_id": 6,
     * "instructor_id": 2,
     * "tracking": "MQKR46KB7JJP",
     * "price": 10,
     * "created_at": "2020-11-17T06:29:05.000000Z",
     * "updated_at": "2020-11-17T06:29:05.000000Z",
     * "course": {
     * "id": 1,
     * "category_id": 1,
     * "subcategory_id": 1,
     * "quiz_id": null,
     * "user_id": 2,
     * "lang_id": 1,
     * "title": "Managerial Accounting Advance Course",
     * "slug": "managerial-accounting",
     * "duration": "5H",
     * "image": "public/demo/course/image/1.png",
     * "thumbnail": "public/demo/course/thumb/1.png",
     * "price": 20,
     * "discount_price": 10,
     * "publish": 1,
     * "status": 1,
     * "level": 2,
     * "trailer_link": "https://www.youtube.com/watch?v=mlqWUqVZrHA",
     * "host": "Youtube",
     * "meta_keywords": null,
     * "meta_description": null,
     * "about": "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text\r\n            ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book",
     * "special_commission": null,
     * "total_enrolled": 1,
     * "reveune": 50,
     * "reveiw": 0,
     * "type": 1,
     * "created_at": null,
     * "updated_at": null,
     * "dateFormat": "17th November 2020",
     * "publishedDate": "17th November 2020 12:28 pm",
     * "sumRev": 2,
     * "purchasePrice": 21,
     * "enrollCount": 1,
     * "user": {
     * "id": 2,
     * "role_id": 2,
     * "name": "Teacher",
     * "photo": "public/infixlms/img/admin.png",
     * "image": "public/infixlms/img/admin.png",
     * "avatar": "public/infixlms/img/admin.png",
     * "mobile_verified_at": null,
     * "email_verified_at": "2020-09-09T10:52:36.000000Z",
     * "notification_preference": "mail",
     * "is_active": 1,
     * "username": "teacher@infixedu.com",
     * "email": "teacher@infixedu.com",
     * "email_verify": "0",
     * "phone": null,
     * "address": null,
     * "city": "1374",
     * "country": "19",
     * "zip": null,
     * "dob": null,
     * "about": null,
     * "facebook": null,
     * "twitter": null,
     * "linkedin": null,
     * "instagram": null,
     * "subscribe": 0,
     * "provider": null,
     * "provider_id": null,
     * "status": 1,
     * "balance": 0,
     * "currency_id": 112,
     * "special_commission": 1,
     * "payout": "Paypal",
     * "payout_icon": "/uploads/payout/pay_1.png",
     * "payout_email": "demo@paypal.com",
     * "referral": "4MLV6zZjd9",
     * "added_by": 0,
     * "created_at": "2020-11-16T04:39:07.000000Z",
     * "updated_at": "2020-11-16T04:39:07.000000Z"
     * }
     * }
     * }
     * ],
     * "message": "Getting Cart info"
     * }
     *
     */

    public function cartList()
    {

        try {

            $carts = Cart::where('user_id', Auth::id())->with('course', 'course.user')->get();

            if (count($carts) != 0) {
                $response = [
                    'success' => true,
                    'data' => $carts,
                    'message' => 'Getting Cart info',
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Cart is empty ',
                ];
            }

            return response()->json($response, 200);
        } catch (\Exception $exception) {
            $response = [
                'success' => false,
                'message' => $exception->getMessage()
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Add to cart
     *
     * @queryParam id required The id of Cart Example:1.
     * @response  {
     * "success": false,
     * "message": "Course already added in your cart"
     * }
     */

    public function addToCart($id)
    {
        try {
            $user = Auth::user();
            if (Auth::check() && ($user->role_id != 1)) {

                $exist = Cart::where('user_id', $user->id)->where('course_id', $id)->first();
                $oldCart = Cart::where('user_id', $user->id)->first();

                if (isset($exist)) {
                    $message = 'Course already added in your cart';
                    $success = false;
                } elseif (Auth::check() && ($user->role_id == 1)) {
                    $message = 'You logged in as admin so can not add cart !';
                    $success = false;
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

                    $message = 'Course Added to your cart';
                    $success = true;
                }

            } //If user not logged in then cart added into session

            else {
                $message = 'Only student can add to cart';
                $success = true;
            }
            $response = [
                'success' => $success,
                'message' => $message,
            ];

            return response()->json($response, 200);
        } catch (\Exception $exception) {
            $response = [
                'success' => false,
                'message' => $exception->getMessage()
            ];
            return response()->json($response, 500);
        }

    }

    /**
     * Remove cart
     * @queryParam id required The id of course/quiz Example:1.
     * @response  {
     * "success": false,
     * "message": "Course removed from your cart"
     * }
     */

    public function removeCart($id)
    {

        try {

            if (Auth::check()) {
                $item = Cart::find($id);
                if ($item) {
                    $item->delete();
                    $success = true;
                    $message = 'Course removed from your cart';
                } else {
                    $success = false;
                    $message = 'Something went wrong';
                }

            } else {
                $success = false;
                $message = 'Something went wrong';
            }

            $response = [
                'success' => $success,
                'message' => $message,
            ];

            return response()->json($response, 200);
        } catch (\Exception $exception) {
            $response = [
                'success' => false,
                'message' => $exception->getMessage()
            ];
            return response()->json($response, 500);
        }
    }


    /**
     * Apply Coupon
     * @bodyParam code string required The code of coupon Example:newyear2020
     * @bodyParam total number required The total of Amount Example:5000
     * @response  {
     * "success": true,
     * "total":30,
     * "message": "Coupon Successful Applied"
     * }
     */
    public function applyCoupon(Request $request)
    {

        try {
            $code = $request->code;

            $coupon = Coupon::where('code', $code)->whereDate('start_date', '<=', Carbon::now())
                ->whereDate('end_date', '>=', Carbon::now())->where('status', 1)->first();
            if (isset($coupon)) {

                $tracking = Cart::where('user_id', Auth::id())->first()->tracking;
                $total = $request->total;
                $max_dis = $coupon->max_discount;
                $min_purchase = $coupon->min_purchase;
                $type = $coupon->type;
                $value = $coupon->value;

                $couponApply = false;


                $checkout = Checkout::where('tracking', $tracking)->first();
                if (empty($checkout)) {
                    $checkout = new Checkout();
                }

                $checkTrackingId = Checkout::where('tracking', $tracking)->where('coupon_id', $coupon)->first();

                if ($checkTrackingId) {
                    $response = [
                        'success' => false,
                        'message' => "Already used this coupon",
                    ];
                    return response()->json($response, 200);

                }

                if ($total >= $min_purchase) {


                    if ($coupon->category == 1) {
                        $couponApply = true;
                    } elseif ($coupon->category == 2) {

                        if (count($checkout->carts) != 1) {
                            return response()->json([
                                'error' => "This coupon apply for single course",
                                'total' => $total,
                            ], 200);
                        }

                        if ($checkout->carts[0]->course_id == $coupon->course_id) {
                            $couponApply = true;
                        } else {
                            return response()->json([
                                'error' => "This coupon is not valid for this course.",
                                'total' => $total,
                            ], 200);
                        }
                    } elseif ($coupon->category == 3) {
//                        dd();
                        if ($coupon->coupon_user_id != $checkout->user_id) {
                            return response()->json([
                                'error' => "This coupon not for you.",
                                'total' => $total,
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
                            'success' => false,
                            "message" => "Invalid Request"
                        ], 200);
                    }

                    $checkout->tracking = $tracking;
                    $checkout->user_id = Auth::id();
                    $checkout->coupon_id = $coupon->id;
                    $checkout->price = $final;
                    $checkout->status = 0;
                    $checkout->save();
                    $response = [
                        'success' => true,
                        'total' => number_format($final, 2),
                        'message' => "Coupon Successful Applied",
                    ];
                    return response()->json($response, 200);

                } else {

                    $response = [
                        'success' => false,
                        'message' => "Coupon Minimum Purchase Does Not Match",
                    ];
                    return response()->json($response, 200);

                }

            } else {
                $response = [
                    'success' => false,
                    'message' => "Invalid Coupon",
                ];
                return response()->json($response, 200);

            }

        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => "Operation Failed",
            ];
            return response()->json($response, 500);
        }


    }


    /**
     * My Courses
     * @response
     * {
     * "success": true,
     * "data": [
     * {
     * "id": 1,
     * "category_id": 1,
     * "subcategory_id": 1,
     * "quiz_id": null,
     * "user_id": 2,
     * "lang_id": 1,
     * "title": "Managerial Accounting Advance Course",
     * "slug": "managerial-accounting",
     * "duration": "5H",
     * "image": "public/demo/course/image/1.png",
     * "thumbnail": "public/demo/course/thumb/1.png",
     * "price": 20,
     * "discount_price": 10,
     * "publish": 1,
     * "status": 1,
     * "level": 2,
     * "trailer_link": "https://www.youtube.com/watch?v=mlqWUqVZrHA",
     * "host": "Youtube",
     * "meta_keywords": null,
     * "meta_description": null,
     * "about": "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text\r\n            ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book",
     * "special_commission": null,
     * "total_enrolled": 1,
     * "reveune": 50,
     * "reveiw": 0,
     * "type": 1,
     * "created_at": null,
     * "updated_at": null,
     * "dateFormat": "17th November 2020",
     * "publishedDate": "17th November 2020 10:40 am",
     * "sumRev": 2,
     * "purchasePrice": 21,
     * "enrollCount": 1
     * }
     * ],
     * "total": 11,
     * "message": "Getting Courses Data"
     * }
     */
    public function myCourses()
    {
        try {
            $courses = CourseEnrolled::where('course_enrolleds.user_id', Auth::user()->id)
                ->leftjoin('courses', 'courses.id', 'course_enrolleds.course_id')
                ->where('courses.type', 1)
                ->select('courses.*')
                ->with('user')
                ->get();

            foreach ($courses as $course) {
                $user = User::where('id', $course->user_id)->first();
                $complete = Course::where('id', $course->id)->with('completeLessons')->first();
                $course->totalCompletePercentage = $complete->LoginUserTotalPercentage;
                $course->user = $user;
            }
            $response = [
                'success' => true,
                'data' => $courses,
                'message' => "Getting my courses",
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => "Something went wrong",
            ];
            return response()->json($response, 500);
        }
    }


    public function myQuizzes()
    {
        try {
            $courses = CourseEnrolled::where('course_enrolleds.user_id', Auth::user()->id)
                ->leftjoin('courses', 'courses.id', 'course_enrolleds.course_id')
                ->where('courses.type', 2)
                ->select('courses.*')
                ->with('user')
                ->get();
            foreach ($courses as $course) {
                $user = User::where('id', $course->user_id)->first();
                $tests = QuizTest::where('user_id', Auth::user()->id)->get();
                $course->user = $user;
                $course->tests = $tests;
            }

            $response = [
                'success' => true,
                'data' => $courses,
                'message' => "Getting my Quiz",
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => "Something went wrong",
            ];
            return response()->json($response, 500);
        }
    }

    public function myClasses()
    {
        try {
            $courses = CourseEnrolled::where('course_enrolleds.user_id', Auth::user()->id)
                ->leftjoin('courses', 'courses.id', 'course_enrolleds.course_id')
                ->where('courses.type', 3)
                ->select('courses.*')
                ->with('user')
                ->get();
            foreach ($courses as $course) {
                $class = VirtualClass::where('id', $course->class_id)->first();
                $course->class = $class;

                $user = User::where('id', $course->user_id)->first();
                $course->user = $user;
            }
            $response = [
                'success' => true,
                'data' => $courses,
                'message' => "Getting my Classes",
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => "Something went wrong",
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Update Profile
     * @bodyParam name string required The name of User Example:Student
     * @bodyParam email string required The email of User Example:user@email.com
     * @bodyParam phone string required The phone number of User Example:01711223344
     * @bodyParam address string required The address of User Example:Dhaka,Bangladesh
     * @bodyParam city string required The city of User Example:Dhaka
     * @bodyParam country string required The country of User Example:Bangladesh
     * @bodyParam zip string required The zip of User Example:1200
     * @bodyParam about string required The about of User Example:something.....
     * @bodyParam image file  The profile image of User Example:image.png
     * @response  {
     * "success": true,
     * "message": "Password has been changed"
     * }
     */

    public function updateProfile(Request $request)
    {
        /*   if (Auth::user()->role_id == 1) {
               $request->validate([
                   'name' => 'required',
                   'email' => 'required|email',

               ]);
           } else {
               $request->validate([
                   'name' => 'required',
                   'email' => 'required|email',
                   'phone' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:1|unique:users',
                   'address' => 'required',
                   'city' => 'required',
                   'country' => 'required',
                   'zip' => 'required',
               ]);
           }*/


        try {

            $user = Auth::user();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->address = $request->address;
            $user->language_id = $request->language;
            $user->city = $request->city;
            $user->country = $request->country;
            $user->zip = $request->zip;
            $user->currency_id = 112;
            $user->facebook = $request->facebook;
            $user->twitter = $request->twitter;
            $user->linkedin = $request->linkedin;
            $user->instagram = $request->instagram;
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
            $response = [
                'success' => true,
                'message' => "Profile has been updated",
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => "Something went wrong",
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Review Course
     *
     * @bodyParam course_id  integer required The course_id of course/quiz Example:1
     * @bodyParam review string required The review  of course/quiz Example:Something
     * @bodyParam rating integer required The rating  of course/quiz Example:5
     * @response  {
     * "success": true,
     * "message": "Review Submit Successful"
     * }
     */
    public function submitReview(Request $request)
    {
        $this->validate($request, [
            'review' => 'required',
            'course_id' => 'required',
            'rating' => 'required'
        ]);

        try {
            $user_id = Auth::user()->id;
            $review = CourseReveiw::where('user_id', $user_id)->where('course_id', $request->course_id)->first();
// return $review;
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
                $course->save();

                // $notification = new Notification();
                // $notification->author_id = Auth::user()->id;
                // $notification->user_id = $user_id;
                // $notification->course_id = $request->course_id;
                // $notification->course_review_id = $newReview->id;
                // $notification->save();

                if (UserEmailNotificationSetup('Course_Review',$course->user)) {
                    send_email($course->user, 'Course_Review', [
                        'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                        'course' => $course->title,
                        'review' => $newReview->comment,
                        'star' => $newReview->star,
                    ]);
                }
                if (UserBrowserNotificationSetup('Course_Review',$course->user)) {

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
                $success = true;
                $message = 'Review Submit Successful';
            } else {
                $success = false;
                $message = 'Invalid Action!';
            }

            $response = [
                'success' => $success,
                'message' => $message
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => "Something went wrong",
            ];
            return response()->json($response, 500);
        }

    }

    /**
     * Comment Course
     *
     * @bodyParam course_id integer required The course_id of course/quiz Example:1
     * @bodyParam comment string required The comment  of course/quiz Example:Something
     * @response  {
     * "success": true,
     * "message": "Operation Successful"
     * }
     */
    public function comment(Request $request)
    {
        $this->validate($request, [
            'comment' => 'required',
            'course_id' => 'required',
        ]);

        try {
            $course = Course::where('id', $request->course_id)->where('status', 1)->first();

            if (isset($course)) {

                $comment = new CourseComment();
                $comment->user_id = Auth::user()->id;
                $comment->course_id = $request->course_id;
                $comment->instructor_id = $course->user_id;
                $comment->comment = $request->comment;
                $comment->status = 1;
                $comment->save();

                // $notification = new Notification();
                // $notification->author_id = Auth::user()->id;
                // $notification->user_id = $course->user_id;
                // $notification->course_id = $course->id;
                // $notification->course_comment_id = $comment->id;
                // $notification->save();


                if (UserEmailNotificationSetup('Course_comment',$course->user)) {
                    send_email($course->user, 'Course_comment', [
                        'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                        'course' => $course->title,
                        'comment' => $comment->comment,
                    ]);
                }
                if (UserBrowserNotificationSetup('Course_comment',$course->user)) {

                    send_browser_notification($course->user, $type = 'Course_comment', $shortcodes = [
                        'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                        'course' => $course->title,
                        'comment' => $comment->comment,
                    ],
                    '',//actionText
                    ''//actionUrl
                    );
                }

                $success = true;
                $message = 'Operation successful';
            } else {
                $success = false;
                $message = 'Invalid Action !';
            }
            $response = [
                'success' => $success,
                'message' => $message
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => "Something went wrong",
            ];
            return response()->json($response, 500);
        }

    }

    /**
     * Comment Reply Course
     *
     * @bodyParam comment_id integer required The comment id of Comment Example:1
     * @bodyParam reply string required The reply  of Comment Example:Something
     * @response  {
     * "success": true,
     * "message": "Operation Successful"
     * }
     */
    public function commentReply(Request $request)
    {
        $this->validate($request, [
            'comment_id' => 'required',
            'reply' => 'required',
        ]);

        try {
            $comment = CourseComment::find($request->comment_id);
            $course = $comment->course;


            if (isset($course)) {

                $comment = new CourseCommentReply();
                $comment->user_id = Auth::user()->id;
                $comment->course_id = $course->id;
                $comment->comment_id = $request->comment_id;
                $comment->reply = $request->reply;
                $comment->status = 1;
                $comment->save();


                if (UserEmailNotificationSetup('Course_comment_Reply',$course->user)) {
                    send_email($course->user, 'Course_comment_Reply', [
                        'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                        'course' => $course->title,
                        'comment' => $comment->comment,
                        'reply' => $comment->reply,
                    ]);
                }
                if (UserBrowserNotificationSetup('Course_comment_Reply',$course->user)) {

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


                $success = true;
                $message = 'Operation successful';
            } else {
                $success = false;
                $message = 'Invalid Action !';
            }
            $response = [
                'success' => $success,
                'message' => $message
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => "Something went wrong",
            ];
            return response()->json($response, 500);
        }

    }


    /**
     * Checkout
     *
     * @bodyParam billing_address  string required  Select "new" || "previous"
     * @bodyParam old_billing  integer required  If select Previous billing Address
     * @bodyParam first_name  string required  If select New billing Address
     * @bodyParam last_name  string required  If select New billing Address
     * @bodyParam country  string required  If select New billing Address
     * @bodyParam address1  string required  If select New billing Address
     * @bodyParam city  string required  If select New billing Address
     * @bodyParam phone  string required  If select New billing Address
     * @bodyParam email  string required  If select New billing Address
     * @response  {
     * "success": true,
     * "message": "Operation Successful"
     * }
     */
    public function makeOrder(Request $request)
    {
        $response = array('response' => '', 'success' => false);
        /* $validator = Validator::make($request->all(), [
             'billing_address' => 'required',
             'old_billing' => 'required_if:billing_address,previous',
             'first_name' => 'required_if:billing_address,new',
             'last_name' => 'required_if:billing_address,new',
             'country' => 'required_if:billing_address,new',
             'address1' => 'required_if:billing_address,new',
             'city' => 'required_if:billing_address,new',
             'phone' => 'required_if:billing_address,new',
             'email' => 'required_if:billing_address,new',
         ]);
         if ($validator->fails()) {
             return $response['response'] = $validator->messages();
         }*/

        try {
            $profile = Auth::user();
            $tracking = Cart::where('user_id', Auth::id())->first()->tracking;
            if ($profile->role_id == 3) {
                /* if (isSubscribe()) {
                     $total = 0;
                 } else {
                     $total = Cart::where('user_id', Auth::user()->id)->sum('price');
                 }*/
                $total = Cart::where('user_id', Auth::user()->id)->sum('price');
            }

            $checkout = Checkout::where('tracking', $tracking)->where('user_id', Auth::id())->latest()->first();
            if (!$checkout) {
                $checkout = new Checkout();
                $checkout->discount = 0.00;
                $checkout->purchase_price = $total;
                $checkout->tracking = $tracking;
                $checkout->user_id = Auth::id();
                $checkout->price = $total;
                $checkout->status = 0;
                $checkout->save();
            }


            if ($request->billing_address == 'new') {
                $bill = BillingDetails::where('tracking_id', $tracking)->first();

                if (empty($bill)) {
                    $bill = new BillingDetails();
                }

                $bill->user_id = Auth::id();
                $bill->tracking_id = $tracking;
                $bill->first_name = $request->first_name;
                $bill->last_name = $request->last_name;
                $bill->company_name = $request->company_name;
                $bill->country = $request->country;
                $bill->address1 = $request->address1;
                $bill->address2 = $request->address2;
                $bill->city = $request->city;
                $bill->state = $request->state;
                $bill->zip_code = $request->zip_code;
                $bill->phone = $request->phone;
                $bill->email = $request->email;
                $bill->details = $request->details;
                $bill->payment_method = null;
                $bill->save();
            } else {
                $bill = BillingDetails::where('id', $request->old_billing)->first();
            }

            $checkout_info = $checkout;
            if ($checkout_info) {
                $checkout_info->billing_detail_id = $bill->id;
                $checkout_info->save();

                if ($checkout_info->purchase_price == 0) {
                    $checkout_info->payment_method = 'None';
                    $bill->payment_method = 'None';
                    $checkout_info->save();
                    $carts = Cart::where('tracking', $checkout_info->tracking)->get();

                    foreach ($carts as $cart) {

                        $payment = new PaymentController();
                        $payment->directEnroll($cart->course_id, $checkout_info->tracking);
                        $cart->delete();

                    }


                    $response = [
                        'success' => true,
                        'type' => 'Free',
                        'message' => 'Operation successful'
                    ];
                    return response()->json($response, 200);
                } else {
                    $response = [
                        'success' => true,
                        'type' => 'Paid',
                        'message' => 'Operation successful. Go to Payment page'
                    ];
                    return response()->json($response, 200);

                }
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Operation Failed.'
                ];
                return response()->json($response, 500);
            }
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
            return response()->json($response, 500);

        }
    }

    /**
     * Payment
     *
     * @queryParam response  required array Response Form Gateway.
     * @queryParam gateWayName  required string Gateway Name.
     * @response  {
     * "success": true,
     * "message": "Successfully Done"
     * }
     */
    public static function payWithGateWay(Request $request, $gateWayName)
    {
        try {
            if (isset($request->response)) {
                $response = $request->response;
            } else {
                $response = null;
            }


            if (Auth::check()) {
                $user = Auth::user();
                $track = Cart::where('user_id', $user->id)->first()->tracking;
                $total = Cart::where('user_id', Auth::user()->id)->sum('price');
                $checkout_info = Checkout::where('tracking', $track)->where('user_id', $user->id)->latest()->first();

                if ($gateWayName == "Wallet") {
                    if ($user->balance < $checkout_info->purchase_price) {

                        $response = [
                            'success' => false,
                            'message' => 'Insufficient balance'
                        ];
                        return response()->json($response, 200);
                    } else {
                        $newBal = ($user->balance - $checkout_info->purchase_price);
                        $user->balance = $newBal;
                        $user->save();

                    }
                }


                if (isset($checkout_info)) {

                    $discount = $checkout_info->discount;

                    $carts = Cart::where('tracking', $track)->get();

                    foreach ($carts as $cart) {


                        $course = Course::find($cart->course_id);
                        $enrolled = $course->total_enrolled;
                        $course->total_enrolled = ($enrolled + 1);

                        //==========================Start Referral========================
                        $purchase_history = CourseEnrolled::where('user_id', Auth::user()->id)->first();
                        $referral_check = UserWiseCoupon::where('invite_accept_by', Auth::user()->id)->where('category_id', null)->where('course_id', null)->first();
                        $referral_settings = UserWiseCouponSetting::where('role_id', Auth::user()->role_id)->first();

                        if ($purchase_history == null && $referral_check != null) {
                            $referral_check->category_id = $course->category_id;
                            $referral_check->subcategory_id = $course->subcategory_id;
                            $referral_check->course_id = $course->id;
                            $referral_check->save();
                            $percentage_cal = ($referral_settings->amount / 100) * $checkout_info->price;

                            if ($referral_settings->type == 1) {
                                if ($checkout_info->price > $referral_settings->max_limit) {
                                    $bonus_amount = $referral_settings->max_limit;
                                } else {
                                    $bonus_amount = $referral_settings->amount;
                                }
                            } else {
                                if ($percentage_cal > $referral_settings->max_limit) {
                                    $bonus_amount = $referral_settings->max_limit;
                                } else {
                                    $bonus_amount = $percentage_cal;
                                }
                            }

                            $referral_check->bonus_amount = $bonus_amount;
                            $referral_check->save();

                            $invite_by = User::find($referral_check->invite_by);
                            $invite_by->balance += $bonus_amount;
                            $invite_by->save();

                            $invite_accept_by = User::find($referral_check->invite_accept_by);
                            $invite_accept_by->balance += $bonus_amount;
                            $invite_accept_by->save();
                        }
                        //==========================End Referral========================
                        if ($discount != 0 || !empty($discount)) {
                            $itemPrice = $cart->price - ($discount / count($carts));
                            $discount_amount = $cart->price - $itemPrice;
                        } else {
                            $itemPrice = $cart->price;
                            $discount_amount = 0.00;
                        }
                        $enroll = new CourseEnrolled();
                        $instractor = User::find($cart->instructor_id);
                        $enroll->user_id = $user->id;
                        $enroll->tracking = $track;
                        $enroll->course_id = $course->id;
                        $enroll->purchase_price = $itemPrice;
                        $enroll->coupon = null;
                        $enroll->discount_amount = $discount_amount;
                        $enroll->status = 1;


                        if (!is_null($course->special_commission)) {
                            $commission = $course->special_commission;
                            $reveune = ($cart->price * $commission) / 100;
                            $enroll->reveune = $reveune;
                        } elseif (!is_null($instractor->special_commission)) {
                            $commission = $instractor->special_commission;
                            $reveune = ($cart->price * $commission) / 100;
                            $enroll->reveune = $reveune;
                        } else {

                            $commission = Settings('commission');
                            $reveune = ($cart->price * $commission) / 100;
                            $enroll->reveune = $reveune;
                        }

                        $payout = new InstructorPayout();
                        $payout->instructor_id = $course->user_id;
                        $payout->reveune = $reveune;

                        $payout->status = 0;
                        $payout->save();



                         if (UserEmailNotificationSetup('Course_Enroll_Payment',$checkout_info->user)) {
                             send_email($checkout_info->user, 'Course_Enroll_Payment', [
                               'time' => \Illuminate\Support\Carbon::now()->format('d-M-Y ,s:i A'),
                                'course' => $course->title,
                                'currency' => $checkout_info->user->currency->symbol ?? '$',
                                'price' => ($checkout_info->user->currency->conversion_rate * $cart->price),
                                'instructor' => $course->user->name,
                                'gateway' => 'Sslcommerz',
                            ]);
                        }
                        if (UserBrowserNotificationSetup('Course_Enroll_Payment',$checkout_info->user)) {

                            send_browser_notification($checkout_info->user, $type = 'Course_Enroll_Payment', $shortcodes = [
                               'time' => \Illuminate\Support\Carbon::now()->format('d-M-Y ,s:i A'),
                                'course' => $course->title,
                                'currency' => $checkout_info->user->currency->symbol ?? '$',
                                'price' => ($checkout_info->user->currency->conversion_rate * $cart->price),
                                'instructor' => $course->user->name,
                                'gateway' => 'Sslcommerz',
                            ],
                            '',//actionText
                            ''//actionUrl
                            );
                        }
                         if (UserEmailNotificationSetup('Enroll_notify_Instructor',$instractor)) {
                             send_email($instractor, 'Enroll_notify_Instructor', [
                                'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                                'course' => $course->title,
                                'currency' => $instractor->currency->symbol ?? '$',
                                'price' => ($instractor->currency->conversion_rate * $cart->price),
                                'rev' => @$reveune,
                            ]);
                        }
                        if (UserBrowserNotificationSetup('Enroll_notify_Instructor',$instractor)) {

                            send_browser_notification($instractor, $type = 'Enroll_notify_Instructor', $shortcodes = [
                                'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                                'course' => $course->title,
                                'currency' => $instractor->currency->symbol ?? '$',
                                'price' => ($instractor->currency->conversion_rate * $cart->price),
                                'rev' => @$reveune,
                            ],
                            '',//actionText
                            ''//actionUrl
                            );
                        }


                        $enroll->save();

                        $course->reveune = (($course->reveune) + ($enroll->reveune));

                        $course->save();

                        // $notification = new Notification();
                        // $notification->author_id = $course->user_id;
                        // $notification->user_id = $checkout_info->user->id;
                        // $notification->course_id = $course->id;
                        // $notification->course_enrolled_id = $enroll->id;
                        // $notification->status = 0;

                        // $notification->save();

                    }

                    $checkout_info->payment_method = $gateWayName;
                    $checkout_info->status = 1;
                    $checkout_info->response = json_encode($response);
                    $checkout_info->save();

                    //            $user->save();


                    if ($checkout_info->user->status == 1) {

                        foreach ($carts as $old) {
                            $old->delete();
                        }
                    }
                    $response = [
                        'success' => true,
                        'message' => 'Operation successful.'
                    ];
                    return response()->json($response, 200);

                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Operation Failed.'
                    ];
                    return response()->json($response, 500);
                }

            } else {
                $response = [
                    'success' => false,
                    'message' => 'Operation Failed.'
                ];
                return response()->json($response, 500);
            }


        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
            return response()->json($response, 500);

        }
    }

    /**
     * Available Payment Methods
     * @response {
     * "success": true,
     * "data": [
     * {
     * "method": "PayPal",
     * "logo": "public/demo/gateway/paypal.png"
     * },
     * {
     * "method": "Stripe",
     * "logo": "public/demo/gateway/stripe.png"
     * },
     * {
     * "method": "PayStack",
     * "logo": "public/demo/gateway/paystack.png"
     * },
     * {
     * "method": "RazorPay",
     * "logo": "public/demo/gateway/razorpay.png"
     * },
     * {
     * "method": "PayTM",
     * "logo": "public/demo/gateway/paytm.png"
     * },
     * {
     * "method": "Bank Payment",
     * "logo": ""
     * },
     * {
     * "method": "Offline Payment",
     * "logo": ""
     * },
     * {
     * "method": "Wallet",
     * "logo": ""
     * }
     * ],
     * "message": "Operation successful"
     * }
     */
    public function paymentMethods()
    {
        try {
            $methods = PaymentMethod::where('active_status', 1)->get(['method', 'logo']);
            $response = [
                'success' => true,
                'data' => $methods,
                'message' => "Operation successful"
            ];
            return response()->json($response, 200);

        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => "Operation Failed!"
            ];
            return response()->json($response, 200);

        }
    }


    /**
     * Billing Address
     * @response {
     * "success": true,
     * "data": [
     * {
     * "id": 1,
     * "tracking_id": "K3USKPJBC5U8",
     * "user_id": 3,
     * "first_name": "Student",
     * "last_name": "",
     * "company_name": "Spondon IT",
     * "country": {
     * "id": 19,
     * "name": "Bangladesh",
     * "iso3": "BGD",
     * "iso2": "BD",
     * "phonecode": "880",
     * "currency": "BDT",
     * "capital": "Dhaka",
     * "active_status": 1,
     * "created_at": "2018-07-20T08:41:03.000000Z",
     * "updated_at": "2018-07-20T08:41:03.000000Z"
     * },
     * "address1": "Dhaka",
     * "address2": "",
     * "city": "Dhaka",
     * "zip_code": "1200",
     * "phone": "01723442233",
     * "email": "student@infixedu.com",
     * "details": "add here additional info.",
     * "payment_method": null,
     * "created_at": "2021-03-03T11:21:01.000000Z",
     * "updated_at": "2021-03-03T11:21:01.000000Z"
     * },
     * {
     * "id": 2,
     * "tracking_id": "765A3UJ7B11M",
     * "user_id": 3,
     * "first_name": "Student",
     * "last_name": "",
     * "company_name": "Spondon IT",
     * "country": {
     * "id": 19,
     * "name": "Bangladesh",
     * "iso3": "BGD",
     * "iso2": "BD",
     * "phonecode": "880",
     * "currency": "BDT",
     * "capital": "Dhaka",
     * "active_status": 1,
     * "created_at": "2018-07-20T08:41:03.000000Z",
     * "updated_at": "2018-07-20T08:41:03.000000Z"
     * },
     * "address1": "Dhaka",
     * "address2": "",
     * "city": "Dhaka",
     * "zip_code": "1200",
     * "phone": "01723442233",
     * "email": "student@infixedu.com",
     * "details": "add here additional info.",
     * "payment_method": null,
     * "created_at": "2021-03-03T11:21:01.000000Z",
     * "updated_at": "2021-03-03T11:21:01.000000Z"
     * }
     * ],
     * "message": "Operation successful"
     * }
     */
    public function billingAddress(Request $request)
    {
        try {
            $bills = BillingDetails::with('country')->where('user_id', $request->user()->id)->get();

            $response = [
                'success' => true,
                'data' => $bills,
                'message' => "Operation successful"
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => "Operation Failed!"
            ];
            return response()->json($response, 200);

        }
    }


    /**
     * Countries
     * @response {
     * "success": true,
     * "data": [
     * {
     * "id": 1,
     * "name": "Afghanistan"
     * }
     * ],
     * "message": "Operation successful"
     * }
     */
    public function countries()
    {
        try {
            $countries = DB::table('countries')->select('id', 'name')->get();
            $response = [
                'success' => true,
                'data' => $countries,
                'message' => "Operation successful"
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => "Operation Failed!"
            ];
            return response()->json($response, 200);

        }
    }


    /**
     * Cities
     * @queryParam country_id required The id of course/quiz Example:1.
     * @response {
     * "success": true,
     * "data": [
     * {
     * "id": 1,
     * "name": "Dhaka"
     * }
     * ],
     * "message": "Operation successful"
     * }
     */
    public function cities($country_id)
    {
        try {
            $cities = DB::table('spn_cities')->where('country_id', $country_id)->select('id', 'name')->get();
            $response = [
                'success' => true,
                'data' => $cities,
                'message' => "Operation successful"
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => "Operation Failed!"
            ];
            return response()->json($response, 200);

        }
    }


    /**
     * Payment Gateways
     * @response {
     * "success": true,
     * "data": [
     * {
     * "id": 1,
     * "method": "method-name",
     * "logo": "image.png"
     * }
     * ],
     * "message": "Operation successful"
     * }
     */
    public function paymentGateways()
    {
        try {
            $methods = PaymentMethod::where('active_status', 1)->get(['method', 'logo']);

            $response = [
                'success' => true,
                'data' => $methods,
                'message' => "Operation successful"
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => "Operation Failed!"
            ];
            return response()->json($response, 200);

        }
    }


    /**
     * My Billing Address
     * @response {
     * "success": true,
     * "data": [
     * {
     * "success": true,
     * "data": [
     * {
     * "id": 1,
     * "tracking_id": "K3USKPJBC5U8",
     * "user_id": 3,
     * "first_name": "Student",
     * "last_name": "",
     * "company_name": "Spondon IT",
     * "country": {
     * "id": 19,
     * "name": "Bangladesh",
     * "iso3": "BGD",
     * "iso2": "BD",
     * "phonecode": "880",
     * "currency": "BDT",
     * "capital": "Dhaka",
     * "active_status": 1,
     * "created_at": "2018-07-20T08:41:03.000000Z",
     * "updated_at": "2018-07-20T08:41:03.000000Z"
     * }
     * ],
     * "message": "Operation successful"
     * }
     * ],
     * "message": "Operation successful"
     * }
     */

    public function myBilling()
    {
        try {
            $bills = BillingDetails::with('country')->where('user_id', Auth::id())->latest()->get();

            $response = [
                'success' => true,
                'data' => $bills,
                'message' => "Operation successful"
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => "Operation Failed!"
            ];
            return response()->json($response, 200);

        }
    }

    public function paytmOrderGenerate(Request $request)
    {

        try {
            $paytmParams = array();
            $user = Auth::user();
            $track = Cart::where('user_id', $user->id)->first()->tracking ?? '';
            $paytmParams["MID"] = getPaymentEnv('PAYTM_MERCHANT_ID');
            $paytmParams["ORDERID"] = $track;


            $body['mid'] = getPaymentEnv('PAYTM_MERCHANT_ID');
            $body['key_secret'] = getPaymentEnv('PAYTM_MERCHANT_KEY');
            $body['website'] = getPaymentEnv('PAYTM_MERCHANT_WEBSITE');
            $body['orderId'] = $track;
            $body['amount'] = $request->amount;
            $body['callbackUrl'] = $request->callbackUrl;
            $body['mode'] = $request->mode;
            $body['testing'] = $request->testing;

            $paytmChecksum = PaytmChecksum::generateSignature(json_encode($body), getPaymentEnv('PAYTM_MERCHANT_KEY'));

            $response = [
                'success' => true,
                'tracking' => $track,
                'paytmChecksum' => $paytmChecksum,
                'message' => "Operation successful"
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => "Operation Failed!"
            ];
            return response()->json($response, 200);
        }

    }


    public function paytmOrderVerify(Request $request)
    {

        try {

            $paytmParams = array();

            $user = Auth::user();
            $track = Cart::where('user_id', $user->id)->first()->tracking;

            $paytmParams["MID"] = getPaymentEnv('PAYTM_MERCHANT_ID');
            $paytmParams["ORDERID"] = $track;

            $isVerifySignature = PaytmChecksum::verifySignature($paytmParams, getPaymentEnv('PAYTM_MERCHANT_KEY'), $request->paytmChecksum);
            if ($isVerifySignature) {
                $result = "Checksum Matched";
            } else {
                $result = "Checksum Mismatched";
            }

            $response = [
                'success' => true,
                'result' => $result,
                'message' => "Operation successful"
            ];
            return response()->json($response, 200);

        } catch (\Exception $e) {


            return response()->json($e, 200);
        }

    }

    public function getBbbMeetingUrl($meeting_id, $user_name)
    {
        try {
            Bigbluebutton::getMeetingInfo([
                'meetingID' => $meeting_id,
            ]);
            $localBbbMeeting = BbbMeeting::where('meeting_id', $meeting_id)->first();

            if (!$localBbbMeeting->isRunning()) {
                $status = 'Not running';
            } else {
                $status = 'running';

            }
            $url = Bigbluebutton::start([
                'meetingID' => $meeting_id,
                'password' => $localBbbMeeting->attendee_password,
                'userName' => $user_name,
            ]);
            $data['url'] = $url;
            $data['status'] = $status;
            return $data;
        } catch (\Exception $e) {
            return null;
        }


    }

    public function quizStart($courseId, $quizId)
    {
        try {
            $userId = Auth::id();
            $quiz = new QuizTest();
            $quiz->user_id = $userId;
            $quiz->course_id = $courseId;
            $quiz->quiz_id = $quizId;
            $quiz->quiz_type = 2;
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

    public function singleQusSubmit(Request $request)
    {

        try {
//            parameters
//            type -> String example:M
//            assign_id -> integer
//            quiz_test_id -> integer
//            ans ->array
            $answer = $request->ans;
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

    public function finalQusSubmit(Request $request)
    {
//        quiz_test_id
        //type
        $userId = Auth::id();
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

        return true;
    }

    public function quizResults()
    {
        $quiz = QuizTest::with('quiz')->where('user_id', Auth::user()->id)->get();

        $response = [
            'success' => true,
            'data' => $quiz,
            'message' => "Operation successful"
        ];
        return $response;
    }

    public function quizResult($course_id, $quiz_id)
    {
        $quizzes = QuizTest::with('quiz', 'details')->where('course_id', $course_id)->where('quiz_id', $quiz_id)->get();


        foreach ($quizzes as $key => $i) {
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
            $preResult[$key]['publish'] = $i->publish;
            $preResult[$key]['status'] = $preResult[$key]['mark'] >= $preResult[$key]['passMark'] ? "Passed" : "Failed";
            $preResult[$key]['text_color'] = $preResult[$key]['mark'] >= $preResult[$key]['passMark'] ? "success_text" : "error_text";
            $i->pass = $preResult[$key]['mark'] >= $preResult[$key]['passMark'] ? "1" : "0";
            $i->save();

            $i->result = $preResult;

        }

        $response = [
            'success' => true,
            'data' => $quizzes,
            'message' => "Operation successful"
        ];
        return $response;
    }

    public function quizResultPreview($quiz_id)
    {
        $quizTest = QuizTest::with('quiz', 'quiz.assign', 'quiz.assign.questionBank', 'quiz.assign.questionBank.questionMu')->findOrFail($quiz_id);

        $questions = [];

        foreach ($quizTest->quiz->assign as $key => $assign) {
            $questions[$key]['qus'] = $assign->questionBank->question;
            $questions[$key]['type'] = $assign->questionBank->type;

            $test = QuizTestDetails::where('quiz_test_id', $quizTest->id)->where('qus_id', $assign->questionBank->id)->first();
            $questions[$key]['isSubmit'] = false;
            $questions[$key]['isWrong'] = false;

            if ($assign->questionBank->type != "M") {

                if ($test) {
                    $questions[$key]['isSubmit'] = true;
                    if ($test->status == 0) {
                        $questions[$key]['isWrong'] = true;
                    }
                    $questions[$key]['answer'] = $test->answer;
                }
            } else {
                foreach (@$assign->questionBank->questionMu as $key2 => $option) {
                    $questions[$key]['option'][$key2]['title'] = $option->title;
                    $questions[$key]['option'][$key2]['right'] = $option->status == 1 ? true : false;
                }

                if ($test) {
                    $questions[$key]['isSubmit'] = true;
                    if ($test->status == 0) {
                        $questions[$key]['option'][$key2]['wrong'] = $test->status == 0 ? true : false;
                        $questions[$key]['isWrong'] = true;
                    }
                }
            }
            if (!$questions[$key]['isSubmit']) {
                $questions[$key]['isWrong'] = true;
            }
        }


        $quizTest->questions = $questions;
        $response = [
            'success' => true,
            'data' => $quizTest,
            'message' => "Operation successful"
        ];
        return $response;
    }


    /**
     * Lesson Complete
     * @bodyParam course_id string required The id of Course Example:1
     * @bodyParam lesson_id string required The id of Lesson Example:1
     * @bodyParam status string required The status of Lesson Example:1
     * @response  {
     * "success": true,
     * "message": "Lesson complete successfully"
     * }
     */
    public function lessonComplete(Request $request)
    {


        try {
            $lesson = LessonComplete::where('course_id', $request->course_id)->where('lesson_id', $request->lesson_id)->where('user_id', Auth::id())->first();

            if (!$lesson) {
                $lesson = new LessonComplete();
                $lesson->user_id = Auth::id();
                $lesson->course_id = $request->course_id;
                $lesson->lesson_id = $request->lesson_id;
            }
            $lesson->status = $request->status;
            $lesson->save();
            $course = Course::find($request->course_id);
            if ($course) {
                $percentage = round($course->loginUserTotalPercentage);
                if ($percentage >= 100) {
                    $this->getCertificateRecord($course->id);


                if (UserEmailNotificationSetup('Complete_Course',Auth::user())) {
                    send_email(Auth::user(), 'Complete_Course', [
                        'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                        'course' => $course->title
                    ]);
                }
                if (UserBrowserNotificationSetup('Complete_Course',Auth::user())) {

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

            $response = [
                'success' => true,
                'message' => "Lesson complete successfully"
            ];
            return $response;

        } catch (\Exception $e) {
            return false;
        }

    }
}



