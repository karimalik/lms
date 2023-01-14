<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

// class EventNotification implements ShouldQueue
class EventNotification
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $event;
    public $user;
    public function __construct($event,$user)
    {
        $this->event = $event;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
       if (UserEmailNotificationSetup('Event_Invitation',$this->user)) {
            send_email($this->user, $type = 'Event_Invitation', $shortcodes = [
                'name' => $this->user->name,
                'event_title' => $this->event->title,
                'event_host' => $this->event->host,
                'event_url' => $this->event->url,
                'start_date' => showDate($this->event->from_date),
                'end_date' => showDate($this->event->to_date),
                'start_time' => $this->event->start_time,
                'end_time' => $this->event->end_time,
                'description' => $this->event->event_des,
                'event_location' => $this->event->event_location,
            ]);
        }
        if (UserBrowserNotificationSetup('Event_Invitation',$this->user)) {
            send_browser_notification($this->user, $type = 'Event_Invitation', $shortcodes = [
                'name' => $this->user->name,
                'event_title' => $this->event->title,
                'event_host' => $this->event->host,
                'event_url' => $this->event->url,
                'start_date' => showDate($this->event->from_date),
                'end_date' => showDate($this->event->to_date),
                'start_time' => $this->event->start_time,
                'end_time' => $this->event->end_time,
                'description' => $this->event->event_des,
                'event_location' => $this->event->event_location,
            ],
            'View',//actionText
             $this->event->url//actionUrl
            );
        }
    }
}
