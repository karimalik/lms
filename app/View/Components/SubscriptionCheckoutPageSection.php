<?php

namespace App\View\Components;

use App\BillingDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;
use Modules\PaymentMethodSetting\Entities\PaymentMethod;
use Modules\Subscription\Entities\SubscriptionCart;

class SubscriptionCheckoutPageSection extends Component
{
    public $request, $s_plan, $price;

    public function __construct($request, $plan, $price)
    {
        $this->request = $request;
        $this->s_plan = $plan;
        $this->price = $price;
    }

    public function render()
    {
        $type = $this->request->type;
        if (!empty($type)) {
            $current = BillingDetails::where('user_id', Auth::id())->latest()->first();
        } else {
            $current = '';
        }
        $profile = Auth::user();
        $bills = BillingDetails::with('country')->where('user_id', Auth::id())->latest()->get();
        $countries = DB::table('countries')->select('id', 'name')->get();
        $states = DB::table('states')->where('country_id', $profile->country)->where('id', $profile->state)->select('id', 'name')->get();
        $cities = DB::table('spn_cities')->where('state_id', $profile->state)->where('id', $profile->city)->select('id', 'name')->get();
        $cart = SubscriptionCart::where('user_id', Auth::id())->first();
        $methods = PaymentMethod::where('active_status', 1)->where('module_status', 1)->where('method', '!=', 'Bank Payment')->where('method', '!=', 'Offline Payment')->get(['method', 'logo']);

        return view(theme('components.subscription-checkout-page-section'), compact('cart', 'profile', 'current', 'bills', 'countries', 'cities', 'methods','states'));
    }
}
