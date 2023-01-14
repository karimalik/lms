<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

// class SendInvitation implements ShouldQueue
class SendInvitation
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $course;
    public $user;
    public function __construct($course,$user)
    {
        $this->course = $course;
        $this->user = $user;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (UserEmailNotificationSetup('Course_Invitation',$this->user)) {
                send_email($this->user, $type = 'Course_Invitation', $shortcodes = [
                'name' => $this->user->name,
                'course_name' => $this->course->title,
                'instructor_name' => $this->course->user->name,
                'course_url' => route('courseDetailsView',$this->course->slug),
                // 'price' => getPriceFormat($this->course->price),
                'price' => $this->course->price,
                'about' => $this->course->about,
            ]);
        }
        if (UserBrowserNotificationSetup('Course_Invitation',$this->user)) {

            send_browser_notification($this->user, $type = 'Course_Invitation', $shortcodes = [
                'name' => $this->user->name,
                'course_name' => $this->course->title,
                'instructor_name' => $this->course->user->name,
                'course_url' => route('courseDetailsView',$this->course->slug),
                'price' => $this->course->price,
                'about' => $this->course->about,
            ],
            'Visit',//actionText
            route('courseDetailsView',$this->course->slug)//actionUrl
            );
        }
        // Log::info($details);
    }
}
