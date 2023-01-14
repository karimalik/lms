<?php

namespace App\View\Components;

use App\BillingDetails;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;
use Modules\Payment\Entities\Cart;
use Modules\Payment\Entities\Checkout;
use Modules\PaymentMethodSetting\Entities\PaymentMethod;

class PaymentPageSection extends Component
{

    public function __construct()
    {
        //
    }


    public function render()
    {
        $profile = Auth::user();
        $bills = BillingDetails::with('country')->where('user_id', Auth::id())->get();

        $countries = DB::table('countries')->select('id', 'name')->get();
        $states = DB::table('states')->where('country_id', $profile->country)->where('id', $profile->state)->select('id', 'name')->get();
        $cities = DB::table('spn_cities')->where('state_id', $profile->state)->where('id', $profile->city)->select('id', 'name')->get();


        $tracking = Cart::where('user_id', Auth::id())->first()->tracking;
        $total = Cart::where('user_id', Auth::user()->id)->sum('price');
        $checkout = Checkout::where('tracking', $tracking)->where('user_id', Auth::id())->latest()->first();
        if (empty($checkout->billing_detail_id)) {
            Toastr::error('Billing Details ', 'Failed');
            return redirect()->route('CheckOut');
        }


        $methods = PaymentMethod::where('active_status', 1)->where('module_status', 1)->where('method', '!=', 'Offline Payment')->get(['method', 'logo']);

        $carts = Cart::where('user_id', Auth::id())->with('course', 'course.user')->get();

        return view(theme('components.payment-page-section'), compact('methods', 'bills', 'checkout', 'profile', 'countries', 'cities', 'carts','states'));
    }
}
