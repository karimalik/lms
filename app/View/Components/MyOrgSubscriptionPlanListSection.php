<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Modules\OrgSubscription\Entities\OrgCourseSubscription;

class MyOrgSubscriptionPlanListSection extends Component
{
    public $request, $courses, $plan;

    public function __construct($plan, $request)
    {
        $search = $request->search;
        $this->plan = $plan = OrgCourseSubscription::find($plan);
        $this->courses = $plan->assign;
        $this->request = $request;
    }

    public function render()
    {
        return view(theme('components.my-org-subscription-plan-list-section'));
    }
}
