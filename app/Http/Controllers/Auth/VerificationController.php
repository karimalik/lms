<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails {
        VerifiesEmails::verify as parentVerify;
    }

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }


    public function resend_mail()
    {
        $user = Auth::user();

        if (Settings('email_verification') != 1) {
            $user->email_verified_at = date('Y-m-d H:m:s');
            $user->save();
        } else {
            $user->sendEmailVerificationNotification();
        }
        return back();
    }

    public function verify(Request $request)
    {
        if ($request->user() && $request->user() != $request->route('id')) {
            Auth::logout();
        }
        if (!$request->user()) {
            $userId = $request->route('id');
            Auth::loginUsingId($userId, true);
        }

        if (!UserDomainCheck()) {
            Auth::logout();
            Toastr::error('Please Login to your domain !', 'Failed');
            return back();
        }

        return $this->parentVerify($request);
    }

    public function show(Request $request)
    {
        if (Storage::exists(md5(Auth::user()->email))) {
            $email = Storage::get(md5(Auth::user()->email));
            $user = User::where('email', trim($email))->first();
            $user->sendEmailVerificationNotification();
            Storage::delete(md5(Auth::user()->email));
        }

        if (Session::has('reg_email')) {
            Session::forget('reg_email');
        }
        if ($request->user()->hasVerifiedEmail()) {
            if (SaasDomain() == 'main' && Auth::user()->lms_id != 1) {
                Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
                Auth::logout();
                return redirect()->route('login');
            }
            return redirect('/');
        }

        return view(theme('auth.verify'));
    }

    /**
     * The user has been verified.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    protected function verified(Request $request)
    {
        //after verified
        if (Auth::check()) {
            send_email(Auth::user(), 'New_Student_Reg', [
                'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                'name' => Auth::user()->name
            ]);
        }

    }
}
