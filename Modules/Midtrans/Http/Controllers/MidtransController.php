<?php

namespace Modules\Midtrans\Http\Controllers;

use App\Http\Controllers\DepositController;
use App\Http\Controllers\PaymentController;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Midtrans\Entities\MidtransOrder;
use Modules\Payeer\Entities\PayeerOrder;

class MidtransController extends Controller
{


    public function __construct()
    {
        \Midtrans\Config::$serverKey = getPaymentEnv('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = getPaymentEnv('MIDTRANS_ENV');
        \Midtrans\Config::$isSanitized = getPaymentEnv('MIDTRANS_SANITIZE');
        \Midtrans\Config::$is3ds = getPaymentEnv('MIDTRANS_3DS');
    }

    public function makePayment($request)
    {

        $order_id = uniqid();
        if ($request->type == "Test") {
            $payAmount = $amount = $request->test_amount;
        } elseif ($request->type == "Deposit") {
            $payAmount = $request->deposit_amount;
            $amount = convertCurrency(Settings('currency_code') ?? 'BDT', 'IND', $request->deposit_amount);
        } else {
            $payAmount = $request->amount;
            $amount = convertCurrency(Settings('currency_code') ?? 'BDT', 'IND', $request->amount);
        }
        $order = new MidtransOrder();
        $order->type = $request->type;
        $order->order_id = $order_id;
        $order->user_id = Auth::user()->id;
        $order->amount = $payAmount;
        $order->save();

        $params = array(
            'transaction_details' => array(
                'order_id' => $order_id,
                'gross_amount' => $amount,
            )
        );

        $paymentUrl = \Midtrans\Snap::createTransaction($params)->redirect_url;

        return \redirect()->to($paymentUrl);

    }


    public function paymentSuccess(Request $request)
    {

        $order = MidtransOrder::where('order_id', $request->order_id)->where('user_id', Auth::user()->id)->first();

        if ($order) {
            if ($request->transaction_status == 'settlement') {
                if ($order->type == "Test") {
                    Toastr::success('Payment done successfully', 'Success');
                    return redirect()->route('paymentmethodsetting.test');
                } elseif ($order->type == "Deposit") {
                    $deposit = new DepositController();
                    $payWithMidtrans = $deposit->depositWithGateWay($order->amount, $request, "Midtrans");

                } else {
                    $payment = new PaymentController();
                    $payWithMidtrans = $payment->payWithGateWay($request, "Midtrans");
                }

                $order->delete();

                if ($payWithMidtrans) {
                    Toastr::success('Payment done successfully', 'Success');
                    return redirect(route('studentDashboard'));
                } else {
                    Toastr::error('Something Went Wrong', 'Error');
                    return redirect(route('studentDashboard'));
                }
            }
        } else {
            Toastr::error('Something Went Wrong', 'Error');
            return redirect(route('studentDashboard'));
        }

        if (empty($response)) {
            Toastr::error('Something Went Wrong', 'Error');
            return redirect(route('studentDashboard'));
        }

    }


    public function paymentPending()
    {
        Toastr::error('Payment is pending .', 'Pending');
        return redirect()->back();
    }

    public function paymentFailed()
    {
        $order = PayeerOrder::where('user_id', Auth::user()->id)->latest()->first();
        if ($order) {
            $order->delete();
        }
        Toastr::error('Payment Failed .', 'Failed');
        return redirect()->back();
    }
}
