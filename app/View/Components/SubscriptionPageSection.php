<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Modules\Subscription\Entities\CourseSubscription;
use Modules\Subscription\Entities\Faq;
use Modules\Subscription\Entities\PlanFeature;
use Modules\Subscription\Entities\SubscriptionSetting;

class SubscriptionPageSection extends Component
{

    public function render()
    {
        $faqs = Faq::where('status', 1)->orderBy('order', 'asc')->get();
        $plans = CourseSubscription::where('status', 1)->orderBy('order', 'asc')->get();
        $plan_features = PlanFeature::where('status', 1)->orderBy('order', 'asc')->get();
        $setting = SubscriptionSetting::getData();
        return view(theme('components.subscription-page-section'), compact('faqs', 'plans', 'plan_features', 'setting'));
    }
}
