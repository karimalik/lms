<?php

namespace App\Http\Controllers\Auth;

use Browser;
use App\User;
use App\UserLogin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\DB;
use Modules\Payment\Entities\Cart;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use Modules\Coupons\Entities\UserWiseCoupon;
use Modules\CourseSetting\Entities\Course;
use Stevebauman\Location\Facades\Location;
use Illuminate\Validation\ValidationException;
use Modules\FrontendManage\Entities\LoginPage;
use Modules\CourseSetting\Entities\CourseEnrolled;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    protected $providers = [
        'facebook', 'google',
    ];


    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */


    public function redirectToProvider($driver)
    {
        if (!$this->isProviderAllowed($driver)) {
            return $this->sendFailedResponse("{$driver} is not currently supported");
        }

        try {
            return Socialite::driver($driver)->redirect();
        } catch (\Exception $e) {
            return $this->sendFailedResponse($e->getMessage());
        }
    }

    public function handleProviderCallback($driver)
    {
        try {
            $user = Socialite::driver($driver)->user();
        } catch (\Exception $e) {
            return $this->sendFailedResponse($e->getMessage());
        }

        // check for email in returned user
        return empty($user->email)
            ? $this->sendFailedResponse("No email id returned from {$driver} provider.")
            : $this->loginOrCreateAccount($user, $driver);
    }

    protected function sendSuccessResponse()
    {
        if (Auth::user()->role_id == 3) {
            return redirect()->intended('student-dashboard');
        } else {
            return redirect()->intended('home');
        }
    }

        protected function sendFailedResponse($msg = null)
    {
        return redirect()->route('social.login')
            ->withErrors(['msg' => $msg ?: 'Unable to login, try with another provider to login.']);
    }

    protected function loginOrCreateAccount($providerUser, $driver)
    {
        // check for already has account
        $user = User::where('email', $providerUser->getEmail())->first();

        // if user already found
        if ($user) {
            // update the avatar and provider that might have changed
            $user->update([
                'image' => $providerUser->avatar,
                'provider' => $driver,
                'provider_id' => $providerUser->id,
                'access_token' => $providerUser->token,
                'last_activity_at' => now(),
            ]);
        } else {
            // create a new user
            $user = User::create([
                'name' => $providerUser->getName(),
                'email' => $providerUser->getEmail(),
                'image' => $providerUser->getAvatar(),
                'provider' => $driver,
                'email_verified_at' => now(),
                'provider_id' => $providerUser->getId(),
                'access_token' => $providerUser->token,
                'password' => bcrypt(now()),
                'language_id' => Settings('language_id') ?? '19',
                'language_name' => Settings('language_name') ?? 'English',
                'language_code' => Settings('language_code') ?? 'en',
                'language_rtl' => Settings('language_rtl') ?? '0',
                'country' => Settings('country_id'),
                'username' => null,
                'referral' => generateUniqueId(),
                'last_activity_at' => now(),
            ]);

            if (session::get('referral') != null) {
                $invited_by = User::where('referral', session::get('referral'))->first();
                $user_coupon = new UserWiseCoupon();
                $user_coupon->invite_by = $invited_by->id;
                $user_coupon->invite_accept_by = $user->id;
                $user_coupon->invite_code = session::get('referral');
                $user_coupon->save();
            }
        }

        // login the user
        Auth::login($user, true);

        return $this->sendSuccessResponse();
    }

    private function isProviderAllowed($driver)
    {
        return in_array($driver, $this->providers) && config()->has("services.{$driver}");
    }


//    --------------------end social lite


    public function redirectPath()
    {

        if (Auth::user()->role_id == 3) {
            $path = route('studentDashboard');
        } else {
            $path = route('dashboard');

        }
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : $path;
    }


    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function hasTooManyLoginAttempts(Request $request)
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request),
            $this->maxAttempts()
        );
    }

    /**
     * Increment the login attempts for the user.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    protected function incrementLoginAttempts(Request $request)
    {
        $this->limiter()->hit(
            $this->throttleKey($request),
            $this->decayMinutes()
        );
    }

    /**
     * Redirect the user after determining they are locked out.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        throw ValidationException::withMessages([
            $this->username() => [Lang::get('auth.throttle', ['seconds' => $seconds])],
        ])->status(429);
    }

    /**
     * Clear the login locks for the given user credentials.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    protected function clearLoginAttempts(Request $request)
    {
        $this->limiter()->clear($this->throttleKey($request));
    }

    /**
     * Fire an event when a lockout occurs.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    protected function fireLockoutEvent(Request $request)
    {
        event(new Lockout($request));
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    protected function throttleKey(Request $request)
    {
        return Str::lower($request->input($this->username())) . '|' . $request->ip();
    }

    /**
     * Get the rate limiter instance.
     *
     * @return \Illuminate\Cache\RateLimiter
     */
    protected function limiter()
    {
        return app(RateLimiter::class);
    }

    /**
     * Get the maximum number of attempts to allow.
     *
     * @return int
     */
    public function maxAttempts()
    {
        return property_exists($this, 'maxAttempts') ? $this->maxAttempts : 5;
    }

    /**
     * Get the number of minutes to throttle for.
     *
     * @return int
     */
    public function decayMinutes()
    {
        return property_exists($this, 'decayMinutes') ? $this->decayMinutes : 1;
    }


    public function showLoginForm()
    {
        $token = request('token');
        if ($token && Storage::exists($token)) {
            $content = Storage::get($token);
            $data = explode('|', $content);
            if ($data && count($data) == 2) {
                $email = $data[0];
                $password = $data[1];
                $user = User::where('email', $email)->where('lms_id', app('institute')->id)->first();
                if ($user && Hash::check($password, $user->password)) {
                    Auth::login($user);
                    Storage::delete($token);
                    return redirect()->route('home');
                }
            }
        }
        $page = LoginPage::getData();
        return view(theme('auth.login'), compact('page'));

    }

    /**
     * Handle a login request to the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {

        $this->validateLogin($request);


        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if (Auth::user()->status == 0) {
                Auth::logout();

                Toastr::error('Your account has been disabled !', 'Failed');
                return back();
            }

            if (isModuleActive('LmsSaas') && Auth::user()->institute->domain != SaasDomain()) {
                $user = Auth::user();
                Auth::logout();
                if ($user->lms_id != 1) {
                    $domain = $user->institute->domain . '.';
                } else {
                    $domain = '';
                }
                $token = md5(uniqid());
                Storage::put($token, $request->email . '|' . $request->password);
                $url = 'http://' . $domain . config('app.short_url') . '/login?token=' . $token;
                return redirect()->to($url);

            }

            if (Auth::user()->role_id == 3) {

                //device  limit
                $user = Auth::user();
                $time = Settings('device_limit_time');
                $last_activity = $user->last_activity_at;
                if ($time != 0) {
                    if (!empty($last_activity)) {
                        $valid_activity = Carbon::parse($last_activity)->addMinutes($time);
                        $current_time = Carbon::now();
                        if ($current_time->lt($valid_activity)) {
                        } else {
                            $login = UserLogin::where('user_id', Auth::id())->where('status', 1)->latest()->first();
                            if ($login) {
                                $login->status = 0;
                                $login->logout_at = Carbon::now(Settings('active_time_zone'));
                                $login->save();
                            }
                        }
                    }
                }
                $user->last_activity_at = now();
                $user->save();


                if (!$this->multipleLogin($request)) {
                    Toastr::error('Your Account is already logged in, into ' . Settings('device_limit') . ' devices', 'Error!');
                    return back();
                }

                if (session()->get('cart') != null && count(session()->get('cart')) > 0) {

                    foreach (session()->get('cart') as $key => $session_cart) {
                        $checkHasCourse = Course::find($session_cart['course_id']);
                        if ($checkHasCourse) {
                            $enolledCousrs = CourseEnrolled::where('user_id', Auth::user()->id)->where('course_id', $session_cart['course_id'])->first();
                            if (!$enolledCousrs) {
                                $hasInCart = Cart::where('course_id', $session_cart['course_id'])->where('user_id', Auth::user()->id)->first();
                                if (!$hasInCart) {
                                    if ($checkHasCourse->discount_price != null) {
                                        $price = $checkHasCourse->discount_price;
                                    } else {
                                        $price = $checkHasCourse->price;
                                    }
                                    $cart = new Cart();
                                    $cart->user_id = Auth::user()->id;
                                    $cart->instructor_id = $session_cart['instructor_id'];
                                    $cart->course_id = $session_cart['course_id'];
                                    $cart->tracking = getTrx();
                                    $cart->price = $price;
                                    $cart->save();
                                }

                            }

                        }

                    }
                }
            }


            session(['role_id' => Auth::user()->role_id]);
            if (isModuleActive('Chat')) {
                userStatusChange(auth()->id(), 1);
            }


            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function multipleLogin($request)
    {
        $device_limit = Settings('device_limit');
        $logins = DB::table('user_logins')
            ->where('status', '=', 1)
            ->where('user_id', '=', Auth::id())
            ->latest()
            ->get();
        if ($device_limit != 0) {
            if (count($logins) == $device_limit) {
                Auth::logout();
                return false;
            }
        }

        $login = UserLogin::create([
            'user_id' => Auth::id(),
            'ip' => $request->ip(),
            'browser' => !empty(Browser::browserName()) ? Browser::browserName() : $request->browser,
            'os' => !empty(Browser::platformName()) ? Browser::platformName() : $request->os,
            'token' => \session()->getId(),
            'api_token' => !empty($request->api_token) ? $request->api_token : null,
            'login_at' => Carbon::now(Settings('active_time_zone')),
            'location' => Location::get($request->ip())
        ]);
        \session()->put('login_token', $login->token);
        return true;
    }

    /**
     * Validate the user login request.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        if (saasEnv('NOCAPTCHA_FOR_LOGIN') == 'true') {
            $rules = [
                $this->username() => 'required|string',
                'password' => 'required|string',
                'g-recaptcha-response' => 'required|captcha'
            ];
        } else {
            $rules = [
                $this->username() => 'required|string',
                'password' => 'required|string',
            ];
        }
        $this->validate($request, $rules, validationMessage($rules));

//        $request->validate($rules);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request),
            $request->filled('remember')
        );
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $goto = \session('redirectTo') ?? redirect()->intended($this->redirectPath())->getTargetUrl();
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->to($goto);
    }

    /**
     * The user has been authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @param mixed $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        //update activity
        if (Auth::check()) {
            $user = Auth::user();
            $user->last_activity_at = now();
            $user->save();
        }
    }

    /**
     * Get the failed login response instance.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';

    }

    /**
     * The user has logged out of the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        //
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }


    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->redirectTo = url()->previous();
    }

    //user logout method
    public function logout(Request $request)
    {
        if (Auth::check()) {
            if (Auth::user()->role_id == 3) {
                $login = UserLogin::where('user_id', Auth::id())->where('status', 1)->latest()->first();
                if ($login) {
                    $login->status = 0;
                    $login->logout_at = Carbon::now(Settings('active_time_zone'));
                    $login->save();
                }
            }
            if (isModuleActive('Chat')) {
                userStatusChange(auth()->id(), 0);
            }
            Auth::logout();
            session(['role_id' => '']);
            Session::flush();
        }

        return redirect('/');
    }


    public function autologin($key)
    {
        if (appMode()) {
            if ($key == 'admin') {
                $user = User::where('role_id', 1)->first();
                $url = route('dashboard');
            } elseif ($key == 'teacher') {
                $user = User::where('role_id', 2)->first();
                $url = route('dashboard');

            } else {
                $user = User::where('role_id', 3)->first();
                $url = route('studentDashboard');
            }
            Auth::loginUsingId($user->id);
            return redirect()->to($url);
        } else {
            return redirect()->back();
        }
    }
}
