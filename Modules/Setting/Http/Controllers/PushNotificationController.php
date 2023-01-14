<?php

namespace Modules\Setting\Http\Controllers;

use App\Models\User;
use App\Notifications\FCMNotification;
use Benwilkins\FCM\FcmMessage;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;


class PushNotificationController extends Controller
{
    public function pushNotification()
    {
        return view('setting::push-notification');
    }


    public function pushNotificationSubmit(Request $request)
    {
        $users = User::where('role_id', 3)->where('status', 1)->get();
        foreach ($users as $user) {
            if (!empty($user->device_token)) {

                Http::withToken(config('services.fcm.key'))
                    ->post('https://fcm.googleapis.com/fcm/send', [
                        "to" => $user->device_token,
                        "notification" => [
                            "priority" => "high",
                            "title" => $request->title,
                            "body" => $request->details,
                            "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                            "image" => getCourseImage(Settings('logo'))
                        ],
                        "data" => [
                            "priority" => "high",
                            "title" => $request->title,
                            "body" => $request->details,
                            "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                            "image" => getCourseImage(Settings('logo'))
                        ],
                    ]);
            }

        }

        Toastr::success(trans('common.Operation successful'), trans('common.Success'));
        return redirect()->back();
    }
}
