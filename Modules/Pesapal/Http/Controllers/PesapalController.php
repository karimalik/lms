<?php

namespace Modules\Pesapal\Http\Controllers;

use App\Http\Controllers\DepositController;
use App\Http\Controllers\PaymentController;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redirect;

class PesapalController extends Controller
{


    public function success(Request $request)
    {
        $ref = $request->pesapal_merchant_reference;
        $ref = explode('|', $ref);
        $type = $ref[0] ?? '';
        $amount = $ref[1] ?? '';


        if ($type == "Deposit") {
            $payment = new DepositController();
            $amount = round(convertCurrency($amount, strtoupper(Settings('currency_code')  ?? 'BDT'), $amount));
            $payWithPesapal = $payment->depositWithGateWay($amount, $request->all(), "Pesapal");

        } else if ($type == "Payment") {
            $payment = new PaymentController();
            $payWithPesapal = $payment->payWithGateWay($request->all(), "Pesapal");
        }

        if ($payWithPesapal) {
            Toastr::success('Payment done successfully', 'Success');
            return redirect(route('studentDashboard'));
        } else {
            Toastr::error('Something Went Wrong', 'Error');
            return Redirect::back();
        }

    }
}
