<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Modules\LmsSaas\Entities\SaasCart;
use Modules\LmsSaas\Entities\SaasPlan;

class FrontendSaasController extends Controller
{
    public function index()
    {
        if (isModuleActive('LmsSaas')) {
            return view(theme('pages.saas_plans'));
        } else {
            Toastr::error('Module not active', 'Error');
            return redirect()->back();
        }
    }
    public function saasCheckout(Request $request)
    {

        // return $request;
        // dd(app('getPaymentSetting'));
        if (empty($request->plan)) {
            $s_plan = '';
        } else {
            $s_plan = $request->plan;
        }

        if (empty($request->price)) {
            $price = 0;
        } else {
            $price = $request->price;
        }

        if (!empty($s_plan)) {
            if (isModuleActive('LmsSaas') && Auth::check()) {
                if (Auth::user()->role_id == 1) {
                   $addCart=$this->addToCart(Auth::user()->id, $s_plan);
                    if (!$addCart) {
                        Toastr::error('Invalid Request', 'Error');
                        return \redirect()->route('saasPackages');
                    }
                } else {
                    Toastr::error('You must login as a lms admin', 'Error');
                    return \redirect()->route('lms_register');
                }

            } else {
                Toastr::error('You must login', 'Error');
                return \redirect()->route('login');
            }
        } else {
            Toastr::error('Invalid Request ', 'Error');
            return \redirect()->route('login');
        }


        return view(theme('pages.saasCheckout'), compact('request', 's_plan', 'price'));

    }

public function addToCart($user_id, $plan_id)
    {
        if (demoCheck()) {
            return false;
        }
        if (!Auth::check()) {
            Toastr::error('You must login', 'Failed');
            return false;
        }
        if (Auth::user()->role_id != 1) {
            Toastr::error('You must login as a lms admin', 'Failed');
            return false;


        }
        $cart = SaasCart::where('user_id', $user_id)->first();
        $plan = SaasPlan::find($plan_id);
        if (empty($plan)) {
            Toastr::error('Invalid Plan', 'Error');
            return false;
        }
        if (!$cart) {
            $cart = new SaasCart();
        }
        $cart->user_id = $user_id;
        $cart->plan_id = $plan_id;
        $cart->tracking = getTrx(20);;
        $cart->price = $plan->price;
        $cart->days = $plan->days;
        $cart->save();
        return true;
    }
}
