<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class LastActivityEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;



    public function __construct()
    {

    }

}
