<?php

namespace Modules\Instamojo\Http\Controllers;

use App\Http\Controllers\DepositController;
use App\Http\Controllers\PaymentController;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class InstamojoController extends Controller
{
    public $url, $key, $token;

    public function __construct()
    {
        $this->url = getPaymentEnv('Instamojo_URL');
        $this->key = getPaymentEnv('');
        $this->token = getPaymentEnv('Instamojo_API_AUTH_TOKEN');
    }


    public function testProcess(Request $request)
    {

        $amount = convertCurrency(Settings('currency_code') ?? 'BDT', 'INR', $request->test_amount);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . 'payment-requests/');
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array("X-Api-Key:" . $this->key,
                "X-Auth-Token:" . $this->token));
        $payload = array(
            'purpose' => 'Test',
            'amount' => $amount,
            'buyer_name' => Auth::user()->name,
            'redirect_url' => route('instamojoTestSuccess'),
            'send_email' => true,
            'email' => Auth::user()->email,
            'allow_repeated_payments' => false
        );

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response);
        return $response->payment_request->longurl;
    }

    public function testSuccess(Request $request)
    {


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->url . 'payments/' . $request->get('payment_id'));
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array("X-Api-Key:" . $this->key,
                "X-Auth-Token:" . $this->token));

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) {
            Toastr::error('Failed, Try Again!!', 'Error');
            return redirect()->route('paymentmethodsetting.test');
        } else {
            $data = json_decode($response);

        }


        if ($data->success == true) {
            if ($data->payment->status == 'Credit') {
                Toastr::success('Payment done successfully', 'Success');
                return redirect()->route('paymentmethodsetting.test');
            }
        }
    }


    public function depositProcess(Request $request)
    {

        $amount = convertCurrency(Settings('currency_code') ?? 'BDT', 'INR', $request->deposit_amount);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . 'payment-requests/');
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array("X-Api-Key:" . $this->key,
                "X-Auth-Token:" . $this->token));
        $payload = array(
            'purpose' => 'Deposit',
            'amount' => $amount,
            'buyer_name' => Auth::user()->name,
            'redirect_url' => route('instamojoDepositSuccess'),
            'send_email' => true,
            'email' => Auth::user()->email,
            'allow_repeated_payments' => false
        );


        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response);
        return $response->payment_request->longurl;
    }

    public function depositSuccess(Request $request)
    {


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->url . 'payments/' . $request->get('payment_id'));
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array("X-Api-Key:" . $this->key,
                "X-Auth-Token:" . $this->token));

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) {
            Toastr::error('Deposit Failed, Try Again!!', 'Error');
            return redirect()->route('deposit');
        } else {
            $data = json_decode($response);

        }


        if ($data->success == true) {
            if ($data->payment->status == 'Credit') {
                $deposit = new DepositController();
                $amount = round(convertCurrency($data->payment->currency, strtoupper(Settings('currency_code') ?? 'BDT'), $data->payment->amount));
                $payWithInstamojo = $deposit->depositWithGateWay($amount, $response, "Instamojo");

                if ($payWithInstamojo) {
                    Toastr::success('Payment done successfully', 'Success');
                    return redirect(route('studentDashboard'));
                } else {
                    Toastr::error('Something Went Wrong', 'Error');
                    return redirect(route('studentDashboard'));
                }
            }
        }
    }

    public function paymentProcess($amount)
    {

        try {
            $amount = convertCurrency(Settings('currency_code') ?? 'BDT', 'INR', $amount);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url . 'payment-requests/');
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER,
                array("X-Api-Key:" . $this->key,
                    "X-Auth-Token:" . $this->token));
            $payload = array(
                'purpose' => 'Payment',
                'amount' => $amount,
                'buyer_name' => Auth::user()->name,
                'redirect_url' => route('instamojoPaymentSuccess'),
                'send_email' => true,
                'email' => Auth::user()->email,
                'allow_repeated_payments' => false
            );

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
            $response = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($response);
            if ($response->success) {
                return $response->payment_request->longurl;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public function paymentSuccess(Request $request)
    {


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->url . 'payments/' . $request->get('payment_id'));
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array("X-Api-Key:" . $this->key,
                "X-Auth-Token:" . $this->token));

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) {
            Toastr::error('Payment Failed, Try Again!!', 'Error');
            return redirect()->route('orderPayment');
        } else {
            $data = json_decode($response);

        }


        if ($data->success == true) {
            if ($data->payment->status == 'Credit') {
                $payment = new PaymentController();
                $payWithInstamojo = $payment->payWithGateWay($response, "Instamojo");

                if ($payWithInstamojo) {
                    Toastr::success('Payment done successfully', 'Success');
                    return redirect(route('studentDashboard'));
                } else {
                    Toastr::error('Something Went Wrong', 'Error');
                    return redirect(route('studentDashboard'));
                }
            }
        }
    }
}
