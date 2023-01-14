<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use Modules\OrgSubscription\Entities\OrgSubscriptionCheckout;

class MyOrgSubscriptionPlanSection extends Component
{
    public $request;

    public function __construct($request)
    {
        $this->request = $request;
    }


    public function render()
    {
        $query = OrgSubscriptionCheckout::where('user_id', Auth::user()->id)->with('plan', 'plan.assign');
        $search = $this->request->search;
        if (!empty($search)) {
            $query->whereHas('plan', function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%");
            });
        }

        $plans = $query->get();
        return view(theme('components.my-org-subscription-plan-section'), compact('plans'));
    }
}
