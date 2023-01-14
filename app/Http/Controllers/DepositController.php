<?php

namespace App\Http\Controllers;

use Adrianbarbos\Mobilpay\Mobilpay;
use App\DepositRecord;
use Brian2694\Toastr\Facades\Toastr;
use Bryceandy\Laravel_Pesapal\Facades\Pesapal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\BankPayment\Http\Controllers\BankPaymentController;
use Modules\Instamojo\Http\Controllers\InstamojoController;
use Modules\MercadoPago\Http\Controllers\MercadoPagoController;
use Modules\Midtrans\Http\Controllers\MidtransController;
use Modules\Mobilpay\Http\Controllers\MobilpayController;
use Modules\Payeer\Http\Controllers\PayeerController;
use Modules\PaymentMethodSetting\Entities\PaymentMethod;
use Modules\Paytm\Http\Controllers\PaytmController;
use Modules\Razorpay\Http\Controllers\RazorpayController;
use Modules\Sslcommerz\Http\Controllers\SslcommerzController;
use Netopia\Payment\Address;
use Netopia\Payment\Invoice;
use Netopia\Payment\Request\Card;
use Omnipay\Omnipay;
use Unicodeveloper\Paystack\Facades\Paystack;

class DepositController extends Controller
{
    public $payPalGateway;

    public function __construct()
    {
        $this->middleware('maintenanceMode');

        $this->payPalGateway = Omnipay::create('PayPal_Rest');
        $this->payPalGateway->setClientId(getPaymentEnv('PAYPAL_CLIENT_ID'));
        $this->payPalGateway->setSecret(getPaymentEnv('PAYPAL_CLIENT_SECRET'));
        $this->payPalGateway->setTestMode(getPaymentEnv('IS_PAYPAL_LOCALHOST')); //set it to 'false' when go live
    }

    public function depositSelectOption(Request $request)
    {
        $data = $request->validate([
            'deposit_amount' => 'required|numeric',
        ]);
        $amount = $request->deposit_amount;
        $records = DepositRecord::where('user_id', Auth::user()->id)->paginate(5);
        $methods = PaymentMethod::where('active_status', 1)->where('method', '!=', 'Wallet')->get(['method', 'logo']);
        return view(theme('depositSelect'), compact('records', 'methods', 'amount'));
    }

    public function depositSubmit(Request $request)
    {
        $data = $request->validate([
            'deposit_amount' => 'required|numeric',
            'method' => 'required',
        ]);


        if ($data['method'] == "Sslcommerz") {
            $ssl = new SslcommerzController();
            $ssl->deposit($data['deposit_amount']);

        } elseif ($data['method'] == "PayPal") {

            $response = $this->payPalGateway->purchase(array(
                'amount' => convertCurrency(Settings('currency_code') ?? 'BDT', Settings('currency_code'), $data['deposit_amount']),
                'currency' => Settings('currency_code'),
                'returnUrl' => route('paypalDepositSuccess'),
                'cancelUrl' => route('paypalDepositFailed'),

            ))->send();

            if ($response->isRedirect()) {
                $response->redirect(); // this will automatically forward the customer
            } else {
                Toastr::error($response->getMessage(), trans('common.Failed'));
                return \redirect()->back();
            }
        } elseif ($data['method'] == "Midtrans") {

            try {
                $midtrans = new MidtransController();
                $request->merge(['type' => 'Deposit']);
                $response = $midtrans->makePayment($request);

                if ($response) {
                    return $response;
                } else {
                    Toastr::error('Something went wrong', 'Failed');
                    return \redirect()->back();
                }
            } catch (\Exception $e) {
                GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

            }


        } elseif ($data['method'] == "Payeer") {

            try {
                $payeer = new PayeerController();
                $request->merge(['type' => 'Deposit']);
                $response = $payeer->makePayment($request);
                if ($response) {
                    return \redirect()->to($response);
                } else {
                    Toastr::error('Something went wrong', 'Failed');
                    return \redirect()->back();
                }
            } catch (\Exception $e) {
                GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

            }


        } elseif ($data['method'] == "MercadoPago") {
            $mercadoPagoController = new MercadoPagoController();
            $response = $mercadoPagoController->payment($request->all());
            return response()->json(['target_url' => $response]);
        } elseif ($data['method'] == "Instamojo") {
            $instamojo = new InstamojoController();
            $response = $instamojo->depositProcess($request);
            if ($response) {
                return \redirect()->to($response);
            } else {
                Toastr::error('Something went wrong', 'Failed');
                return \redirect()->back();
            }

        } elseif ($data['method'] == "Stripe") {

            if (empty($request->get('stripeToken'))) {
                Toastr::error('Something went wrong', 'Failed');
                return redirect(route('studentDashboard'));
            }

            $token = $request->stripeToken;
            $gatewayStripe = Omnipay::create('Stripe');
            $gatewayStripe->setApiKey(getPaymentEnv('STRIPE_SECRET'));


            $response = $gatewayStripe->purchase(array(
                'amount' => convertCurrency(Settings('currency_code') ?? 'BDT', Settings('currency_code'), $data['deposit_amount']),
                'currency' => Settings('currency_code'),
                'token' => $token,
            ))->send();

            if ($response->isRedirect()) {
                // redirect to offsite payment gateway
                $response->redirect();
            } elseif ($response->isSuccessful()) {
                // payment was successful: update database

                $amount = number_format(convertCurrency(strtoupper($response->getData()['currency'],), strtoupper(Settings('currency_code') ?? 'BDT'), $response->getData()['amount'] / 100), 2);

                $payWithStripe = $this->depositWithGateWay($amount, $response, "Stripe");
                if ($payWithStripe) {
                    Toastr::success('Payment done successfully', 'Success');
                    return redirect(route('studentDashboard'));
                } else {
                    Toastr::error('Something Went Wrong', 'Error');
                    return redirect(route('studentDashboard'));
                }

            } else {

                if ($response->getCode() == "amount_too_small") {
                    $amount = number_format(convertCurrency(Settings('currency_code'), strtoupper(Settings('currency_code') ?? 'BDT'), 0.5), 2);
                    $message = "Amount must be at least " . Settings('currency_symbol') . ' ' . $amount;
                    Toastr::error($message, 'Error');
                } else {
                    Toastr::error($response->getMessage(), 'Error');
                }
                return redirect()->back();

            }
        } elseif ($data['method'] == "RazorPay") {

            if (empty($request->razorpay_payment_id)) {
                Toastr::error('Something Went Wrong', 'Error');
                return \redirect()->back();
            }

            $payment = new RazorpayController();
            $response = $payment->payment($request->razorpay_payment_id);
            if ($response['type'] == "error") {
                Toastr::error($response['message'], 'Error');
                return \redirect()->back();
            }


            $amount = number_format(convertCurrency(strtoupper($response['response']['currency'],), strtoupper(Settings('currency_code') ?? 'BDT'), $response['response']['amount'] / 100), 2);

            $payWithRazorPay = $this->depositWithGateWay($amount, $response['response'], "RazorPay");

            if ($payWithRazorPay) {
                Toastr::success('Payment done successfully', 'Success');
                return redirect(route('studentDashboard'));
            } else {
                Toastr::error('Something Went Wrong', 'Error');
                return redirect(route('studentDashboard'));
            }
        } elseif ($data['method'] == "PayTM") {

            $phone = Auth::user()->phone;
            $email = Auth::user()->email;
            if (empty($phone)) {
                Toastr::error('Phone number is required. Please update your profile ', 'Error');
                return redirect()->back();
            }

            $payment = new PaytmController();
            $userData = [
                'user' => Auth::user()->id,
                'mobile' => $phone,
                'email' => $email,
                'amount' => convertCurrency(Settings('currency_code') ?? 'BDT', 'INR', $data['deposit_amount']),
                'order' => Auth::user()->phone . "_" . rand(1, 1000),
            ];
            return $payment->deposit($userData);
        } elseif ($data['method'] == "PayStack") {
            try {
                return Paystack::getAuthorizationUrl()->redirectNow();
            } catch (\Exception $e) {
                GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

            }
        } elseif ($data['method'] == "Bank Payment") {


            $rules = [
                'bank_name' => 'required',
                'branch_name' => 'required',
                'type' => 'required',
                'account_number' => 'required',
                'account_holder' => 'required',
                'image' => 'mimes:jpeg,jpg,png,gif|required',
            ];
            $this->validate($request, $rules, validationMessage($rules));


            try {

                $payment = new BankPaymentController();
                $result = $payment->store($request);

                if ($result) {
                    return redirect(route('studentDashboard'));
                } else {
                    return redirect()->back();
                }

            } catch (\Exception $e) {
                GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

            }
        } elseif ($data['method'] == "Pesapal") {
            try {
                $paymentData = [
                    'amount' => $request->deposit_amount,
                    'currency' => Settings('currency_code'),
                    'description' => 'Deposit',
                    'type' => 'MERCHANT',
                    'reference' => 'Deposit|' . $request->deposit_amount,
                    'first_name' => Auth::user()->first_name,
                    'last_name' => Auth::user()->last_name,
                    'email' => Auth::user()->email,
                ];

                $iframe_src = Pesapal::getIframeSource($paymentData);

                return view('laravel_pesapal::iframe', compact('iframe_src'));

            } catch (\Exception $e) {
                GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

            }
        } elseif ($data['method'] == "Mobilpay") {
            $mobilpay = new MobilpayController();
            return $mobilpay->depositProcess($request);

        }

    }


    public static function depositWithGateWay($amount, $response, $gateWayName, $user = null)
    {
        try {
            if (Auth::check()) {
                $user = Auth::user();
            }

            if ($user) {

                DB::beginTransaction();
                $user->balance += $amount;
                $user->save();
                Log::info($user);
                $depositRecord = new DepositRecord();
                $depositRecord->user_id = $user->id;
                $depositRecord->method = $gateWayName;
                $depositRecord->amount = $amount;
                $depositRecord->response = json_encode($response);
                $depositRecord->save();
                Toastr::success('Deposit done successfully', 'Success');
                DB::commit();
                return true;

            } else {

                Log::info('Something Went Wrong');
                Toastr::error('Something Went Wrong', 'Error');
                return false;
            }


        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent(), true);
        }
    }

    public function paypalDepositSuccess(Request $request)
    {

        // Once the transaction has been approved, we need to complete it.
        if ($request->input('paymentId') && $request->input('PayerID')) {
            $transaction = $this->payPalGateway->completePurchase(array(
                'payer_id' => $request->input('PayerID'),
                'transactionReference' => $request->input('paymentId'),
            ));
            $response = $transaction->send();

            if ($response->isSuccessful()) {
                // The customer has successfully paid.
                $arr_body = $response->getData();
                $paymentAmount = $arr_body['transactions'][0]['amount'];

                $amount = number_format(convertCurrency($paymentAmount['currency'], Settings('currency_code') ?? 'BDT', $paymentAmount['total']), 2);


                $payWithPapal = $this->depositWithGateWay($amount, $arr_body, "PayPal");
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

    public function paypalDepositFailed()
    {
        Toastr::error('User is canceled the payment.', 'Failed');
        return redirect()->back();
    }
}
