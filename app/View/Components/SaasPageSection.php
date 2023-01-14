<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Modules\LmsSaas\Entities\SaasPlan;
use Modules\Subscription\Entities\SubscriptionSetting;

class SaasPageSection extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $plans = SaasPlan::where('status', 1)->orderBy('id', 'asc')->get();
        $setting = [];
        return view(theme('components.saas-page-section'), compact('plans','setting'));
    }
}
