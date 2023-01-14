<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Modules\NotificationSetup\Entities\RoleEmailTemplate;
use Modules\NotificationSetup\Entities\UserNotificationSetup;

class NotificationController extends Controller
{
    public function ajaxNotificationMakeRead(Request $request){
        Auth::user()->unreadNotifications->where('id', $request->id)->markAsRead();
    }
    public function NotificationMakeAllRead(Request $request)
    {
        
        if (!Auth::check()) {
            return redirect('login');
        }
        try {

            Auth::user()->unreadNotifications->markAsRead();
            Toastr::success('All Notification Marked As Read !', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }
    public function myNotificationSetup()
    {
         return view(theme('pages.myNotificationsSetup'));
    }
   public function myNotification(Request $request)
    {
        if (!Auth::check()) {
            return redirect('login');
        }
        try {
            return view(theme('pages.myNotifications'), compact('request'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }
}
