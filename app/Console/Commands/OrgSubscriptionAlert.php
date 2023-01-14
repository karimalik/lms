<?php

namespace App\Console\Commands;


use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\OrgSubscription\Entities\OrgSubscriptionCheckout;
use Modules\OrgSubscription\Entities\OrgSubscriptionSetting;

class OrgSubscriptionAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alert:orgSubscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Alert before expire org subscription plan';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::alert('alert run');

        if (isModuleActive('OrgSubscription')) {
            $setting = OrgSubscriptionSetting::first();
            if ($setting) {
                $alert = $setting->alert;
                $checkouts = OrgSubscriptionCheckout::whereDate('end_date', '>', Carbon::now())->get();

                $now = Carbon::now();

                foreach ($checkouts as $checkout) {
                    $date = $checkout->end_date;
                    Log::alert($now);
                    Log::alert($date);
                    $datework = Carbon::createFromDate($date);
                    $days = $datework->diffInDays($now);
                    if ($days == $alert) {
                        Log::info('Send mail');
                        send_email($checkout->user, 'Org_Subscription_Alert', [
                            'student' => $checkout->user->name,
                            'plan' => $checkout->plan->title,
                            'expire_date' => $checkout->end_date
                        ]);
                    } else {
                        Log::info('Check expire');
                    }


                }
                return true;

            }
        } else {
            return true;
        }


    }
}
