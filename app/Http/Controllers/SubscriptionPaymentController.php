<?php

namespace App\Http\Controllers;

use App\BillingDetails;
use App\Events\OneToOneConnection;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use DrewM\MailChimp\MailChimp;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\CourseSetting\Entities\Course;
use Modules\CourseSetting\Entities\CourseEnrolled;
use Modules\CourseSetting\Entities\Notification;
use Modules\MercadoPago\Http\Controllers\MercadoPagoController;
use Modules\Payment\Entities\InstructorPayout;
use Modules\Paytm\Http\Controllers\PaytmController;
use Modules\Razorpay\Http\Controllers\RazorpayController;
use Modules\Subscription\Entities\CourseSubscription;
use Modules\Subscription\Entities\SubscriptionCart;
use Modules\Subscription\Entities\SubscriptionCheckout;
use Modules\Subscription\Entities\SubscriptionCourse;
use Modules\Subscription\Entities\SubscriptionCourseList;
use Modules\Subscription\Entities\SubscriptionSetting;
use Modules\Wallet\Http\Controllers\WalletController;
use Omnipay\Omnipay;
use Unicodeveloper\Paystack\Facades\Paystack;

class SubscriptionPaymentController extends Controller
{
    public $payPalGateway;
    public $allow;


    public function __construct()
    {
        $this->middleware('maintenanceMode');

        $this->allow = true;

        $this->payPalGateway = Omnipay::create('PayPal_Rest');
        $this->payPalGateway->setClientId(getPaymentEnv('PAYPAL_CLIENT_ID'));
        $this->payPalGateway->setSecret(getPaymentEnv('PAYPAL_CLIENT_SECRET'));
        $this->payPalGateway->setTestMode(getPaymentEnv('IS_PAYPAL_LOCALHOST')); //set it to 'false' when go live


    }


    public function payment(Request $request)
    {

        try {

            $plan = CourseSubscription::where('id', $request->plan_id)->first();
            if (!$plan) {
                Toastr::error('Invalid Request', 'Error');
                return \redirect()->route('courseSubscription');
            }

            $cart = SubscriptionCart::where('user_id', Auth::user()->id)->with('plan')->first();

            if (!$cart) {
                Toastr::error('Invalid Request', 'Error');
                return \redirect()->route('courseSubscription');
            }

            $rules = [
                'old_billing' => 'required_if:billing_address,previous',
                'first_name' => 'required_if:billing_address,new',
                'last_name' => 'required_if:billing_address,new',
                'country' => 'required_if:billing_address,new',
                'address1' => 'required_if:billing_address,new',
                'city' => 'required_if:billing_address,new',
                'phone' => 'required_if:billing_address,new',
                'email' => 'required_if:billing_address,new',
            ];
            $this->validate($request, $rules, validationMessage($rules));


            if ($request->billing_address == 'new') {
                $bill = BillingDetails::where('tracking_id', $request->tracking_id)->first();

                if (empty($bill)) {
                    $bill = new BillingDetails();
                    $bill->tracking_id = $cart->tracking;
                    $bill->user_id = Auth::id();
                }


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

            return view(theme('pages.subscriptionPayment'), compact('plan', 'cart', 'bill'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function subscriptionSubmit(Request $request)
    {

        if (!Auth::check()) {
            Toastr::error('You must login', 'Failed');
            $this->allow = false;
            return redirect()->back();
        }

        if (demoCheck()) {
            return redirect()->back();
        }
        if (!$this->allow) {
            return redirect()->back();
        }
        $cart = SubscriptionCart::where('user_id', Auth::user()->id)->first();
        if (!$cart) {
            Toastr::error('Something went wrong.', 'Failed');
            return redirect()->route('courseSubscription');
        }
        try {
            if ($request->payment_method == "Sslcommerz") {


            } elseif ($request->payment_method == "PayPal") {

                try {
                    $response = $this->payPalGateway->purchase(array(
                        'amount' => convertCurrency(Settings('currency_code') ?? 'BDT', Settings('currency_code'), $cart->price),
                        'currency' => Settings('currency_code'),
                        'returnUrl' => route('paypalSubscriptionSuccess'),
                        'cancelUrl' => route('paypalSubscriptionFailed'),

                    ))->send();

                    if ($response->isRedirect()) {
                        $response->redirect(); // this will automatically forward the customer
                    } else {
                        Toastr::error($response->getMessage(), trans('common.Failed'));
                        return redirect()->route('courseSubscriptionCheckout');
                    }
                } catch (\Exception $e) {
                    Toastr::error("Something Went Wrong", 'Failed');
                    return redirect()->route('courseSubscriptionCheckout');
                }

            } //paypel payment getaway
            elseif ($request->payment_method == "Stripe") {

                $request->validate([
                    'stripeToken' => 'required'
                ]);
                $token = $request->stripeToken ?? '';
                $gatewayStripe = Omnipay::create('Stripe');
                $gatewayStripe->setApiKey(getPaymentEnv('STRIPE_SECRET'));

                $response = $gatewayStripe->purchase(array(
                    'amount' => convertCurrency(Settings('currency_code') ?? 'BDT', Settings('currency_code'), $cart->price),
                    'currency' => Settings('currency_code'),
                    'token' => $token,
                ))->send();

                if ($response->isRedirect()) {
                    // redirect to offsite payment gateway
                    $response->redirect();
                } elseif ($response->isSuccessful()) {
                    // payment was successful: update database

                    $payWithStripe = $this->payWithGateWay($response->getData(), "Stripe");
                    if ($payWithStripe) {
                        Toastr::success('Payment done successfully', 'Success');
                        return redirect(route('studentDashboard'));
                    } else {
                        Toastr::error('Something Went Wrong', 'Error');
                        return redirect()->route('courseSubscriptionCheckout');
                    }
                } else {

                    if ($response->getCode() == "amount_too_small") {
                        $amount = round(convertCurrency(Settings('currency_code'), strtoupper(Settings('currency_code') ?? 'BDT'), 0.5));
                        $message = "Amount must be at least " . Settings('currency_symbol') . ' ' . $amount;
                        Toastr::error($message, 'Error');
                    } else {
                        Toastr::error($response->getMessage(), 'Error');
                    }
                    return redirect()->back();
                }


            } //payment getway
            elseif ($request->payment_method == "RazorPay") {

                if (empty($request->razorpay_payment_id)) {
                    Toastr::error('Something Went Wrong', 'Error');
                    return redirect()->route('courseSubscriptionCheckout');
                }

                $payment = new RazorpayController();
                $response = $payment->payment($request->razorpay_payment_id);

                if ($response['type'] == "error") {
                    Toastr::error($response['message'], 'Error');
                    return redirect()->route('courseSubscriptionCheckout');
                }

                $payWithRazorPay = $this->payWithGateWay($response['response'], "RazorPay");

                if ($payWithRazorPay) {
                    Toastr::success('Payment done successfully', 'Success');
                    return redirect(route('studentDashboard'));
                } else {
                    Toastr::error('Something Went Wrong', 'Error');
                    return redirect()->route('courseSubscriptionCheckout');
                }


            } //payment getway
            elseif ($request->payment_method == "PayTM") {


                $userData = [
                    'user' => Auth::user()->name,
                    'mobile' => Auth::user()->phone,
                    'email' => Auth::user()->email,
                    'amount' => convertCurrency(Settings('currency_code') ?? 'BDT', 'INR', $cart->price),
                    'order' => uniqid() . "_" . rand(1, 1000),
                ];

                $payment = new PaytmController();
                return $payment->subscription($userData);


            } elseif ($request->payment_method == "MercadoPago") {
                $mercadoPagoController = new MercadoPagoController();
                $response = $mercadoPagoController->payment($request->all());
                return response()->json(['target_url' => $response]);
            } elseif ($request->payment_method == "PayStack") {

                try {
                    return Paystack::getAuthorizationUrl()->redirectNow();

                } catch (\Exception $e) {

                    Toastr::error($e->getMessage(), trans('common.Failed'));
                    return redirect()->route('courseSubscriptionCheckout');
                }


            } //payment getway

            elseif ($request->payment_method == "Wallet") {


                $payment = new WalletController();
                $response = $payment->subscription($request);

                if ($response['type'] == "error") {
                    Toastr::error($response['message'], 'Error');
                    return redirect()->route('courseSubscriptionCheckout');
                }

                $payWithWallet = $this->payWithGateWay($response['response'], "Wallet");

                if ($payWithWallet) {
                    Toastr::success('Payment done successfully', 'Success');
                    return redirect(route('studentDashboard'));
                } else {
                    Toastr::error('Something Went Wrong', 'Error');
                    return redirect()->route('courseSubscriptionCheckout');
                }

            }
        } catch (\Exception $e) {

            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }


    }

    public function paypalSubscriptionSuccess(Request $request)
    {


        if ($request->input('paymentId') && $request->input('PayerID')) {
            $transaction = $this->payPalGateway->completePurchase(array(
                'payer_id' => $request->input('PayerID'),
                'transactionReference' => $request->input('paymentId'),
            ));
            $response = $transaction->send();

            if ($response->isSuccessful()) {
                // The customer has successfully paid.
                $arr_body = $response->getData();
                $payWithPapal = $this->payWithGateWay($arr_body, "PayPal");
                if ($payWithPapal) {
                    Toastr::success('Payment done successfully', 'Success');
                    return redirect(route('studentDashboard'));
                } else {
                    Toastr::error('Something Went Wrong', 'Error');
                    return redirect(route('studentDashboard'));
                }

            } else {
                $msg = str_replace("'", " ", $response->getMessage());
                Toastr::error($msg, 'Failed');
                return redirect()->back();
            }
        } else {
            Toastr::error('Transaction is declined');
            return redirect()->back();
        }


    }

    public function paypalSubscriptionFailed()
    {
        Toastr::error('User is canceled the payment.', 'Failed');
        return redirect()->back();
    }

    public static function payWithGateWay($response, $gateWayName)
    {
        try {

            if (Auth::check()) {
                $user = Auth::user();
                $cart = SubscriptionCart::where('user_id', $user->id)->first();


                if ($cart) {
                    $plan = SubscriptionCart::where('plan_id', $cart->plan_id)->first();
                    $bill = BillingDetails::where('id', $cart->billing_detail_id)->first();

                    if (!$bill) {
                        Toastr::error('Billing address not found', 'Error');
                        return false;
                    }
                    if ($plan) {
                        $start_date = Carbon::now();
                        $end_date = $start_date->addDays($plan->days);
                        $subCheckout = SubscriptionCheckout::where('tracking', $cart->tracking)->first();

                        if (!$subCheckout) {
                            $subCheckout = new SubscriptionCheckout();
                        }
                        $subCheckout->user_id = $user->id;
                        $subCheckout->plan_id = $plan->plan_id;
                        $subCheckout->price = $plan->price;
                        $subCheckout->billing_detail_id = $bill->id;
                        $subCheckout->tracking = $bill->tracking_id;
                        $subCheckout->start_date = Carbon::now();
                        $subCheckout->end_date = $end_date;
                        $subCheckout->days = $plan->days;
                        $subCheckout->payment_method = $gateWayName;
                        $subCheckout->status = 1;
                        $subCheckout->response = json_encode($response);
                        $subCheckout->save();

                        $subscripitonPayment = new SubscriptionPaymentController();
                        $subscripitonPayment->coursesEnrollBySubscription($cart->plan_id);

                        Toastr::success('Checkout Successfully Done', 'Success');

                        $user->subscription_validity_date = $end_date;
                        $user->save();
                        $plan->delete();


                        DB::table('course_enrolleds')
                            ->where('subscription', '=', 1)
                            ->where('user_id', $user->id)
                            ->update(['subscription_validity_date' => $end_date]);


                        return true;

                    } else {
                        Toastr::error('Something Went Wrong', 'Error');
                        return false;
                    }
                } else {
                    Toastr::error('Something Went Wrong', 'Error');
                    return false;
                }


            } else {
                Toastr::error('You must login', 'Error');
                return false;
            }


        } catch (\Exception $e) {

            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());


        }
    }

    public function coursesEnrollBySubscription($plan_id)
    {
        try {
            $setting = SubscriptionSetting::getData();
            $plan = CourseSubscription::find($plan_id);

            $ids = [];
            if ($setting->type == 2) {

                $courses_ids = SubscriptionCourseList::Where('plan_id', $plan_id)->select('course_id')->get();
                foreach ($courses_ids as $courses_id) {
                    $ids[] = $courses_id['course_id'];
                }
            } else {
                $courses_ids = Course::where('status', 1)->select('id')->get();
                foreach ($courses_ids as $courses_id) {
                    $ids[] = $courses_id['id'];
                }
            }
            if ($ids) {

                //enroll start
                $user = Auth::user();
                foreach ($ids as $course_id) {

                    $course = Course::findOrFail($course_id);
                    $instractor = User::findOrFail($course->user_id);

                    $check = CourseEnrolled::where('user_id', $user->id)->where('course_id', $course_id)->first();
                    if ($check) {
                        //

                    } else {
                        $enrolled = $course->total_enrolled;
                        $course->total_enrolled = ($enrolled + 1);
                        $enrolled = new CourseEnrolled();
                        $enrolled->user_id = $user->id;
                        $enrolled->course_id = $course_id;

                        if (isModuleActive('Subscription')) {
                            if (isSubscribe()) {
                                $enrolled->subscription = 1;
                                $enrolled->subscription_validity_date = $user->subscription_validity_date;
                            }
                        }

                        $enrolled->save();

                        $plan_price = $plan->price;

                        $itemPrice = $plan_price / count($ids);
                        $enrolled->purchase_price = $itemPrice;

                        if (!is_null($course->special_commission) && $course->special_commission != 0) {
                            $commission = $course->special_commission;
                            $reveune = ($itemPrice * $commission) / 100;
                            $enrolled->reveune = $reveune;
                        } elseif (!is_null($instractor->special_commission) && $instractor->special_commission != 0) {
                            $commission = $instractor->special_commission;
                            $reveune = ($itemPrice * $commission) / 100;
                            $enrolled->reveune = $reveune;
                        } else {
                            $commission = 100 - Settings('commission');
                            $reveune = ($itemPrice * $commission) / 100;
                            $enrolled->reveune = $reveune;
                        }
                        $payout = new InstructorPayout();
                        $payout->instructor_id = $course->user_id;
                        $payout->reveune = $reveune;
                        $payout->status = 0;
                        $payout->save();


                        $enrolled->save();

                        $course->reveune = (($course->reveune) + ($enrolled->reveune));

                        $course->save();

                        // $notification = new Notification();
                        // $notification->author_id = $course->user_id;
                        // $notification->user_id = $user->id;
                        // $notification->course_id = $course->id;
                        // $notification->course_enrolled_id = $enrolled->id;
                        // $notification->status = 0;

                        // $notification->save();

                        if (isModuleActive('Chat')) {
                            event(new OneToOneConnection($instractor, $user, $course));
                        }

                        //start email subscription
                        if ($instractor->subscription_api_status == 1) {
                            try {
                                if ($instractor->subscription_method == "Mailchimp") {
                                    $list = $course->subscription_list;
                                    $MailChimp = new MailChimp($instractor->subscription_api_key);
                                    $MailChimp->post("lists/$list/members", [
                                        'email_address' => Auth::user()->email,
                                        'status' => 'subscribed',
                                    ]);

                                } elseif ($instractor->subscription_method == "GetResponse") {

                                    $list = $course->subscription_list;
                                    $getResponse = new \GetResponse($instractor->subscription_api_key);
                                    $getResponse->addContact(array(
                                        'email' => Auth::user()->email,
                                        'campaign' => array('campaignId' => $list),

                                    ));
                                }
                            } catch (\Exception $exception) {
                                GettingError($exception->getMessage(), url()->current(), request()->ip(), request()->userAgent(), true);

                            }
                        }

                    }

                }
                // enroll end


            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


}
