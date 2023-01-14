<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
use App\Notifications\VerifyEmail;
use App\User;
use App\UserLogin;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * @group  User management
 *
 * APIs for managing user
 */
class AuthController extends Controller
{
    use VerifiesEmails {
        VerifiesEmails::verify as parentVerify;
    }
    /**
     * Create user
     *
     * @bodyParam  name string required The name of the User.
     * @bodyParam email string required The email address of the User.
     * @bodyParam phone string required The phone number of the User.
     * @bodyParam password string required Set password.
     * @bodyParam password_confirmation string required Set confirm password.
     * @return [string] message
     */

    /*{
    "name": "Ashif",
    "phone": "01722334455",
    "email": "ashif@gmail.com",
    "password": "123456",
    "password_confirmation": "123456"
}*/
    public function signup(Request $request)
    {

        try {
            $rules = [
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:255',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|confirmed|min:8'
            ];

            //   $this->validate($request,$rules);

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $response = $validator->messages();
            } else {
                $user =  User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone ?? null,
                    'username' => $request->email,
                    'password' => bcrypt($request->password)
                ]);

                if (Settings('email_verification') != 1) {
                    $user->email_verified_at = date('Y-m-d H:m:s');
                    $user->save();
                } else {
                    $user->sendEmailVerificationNotification();
                    //  $user->notify(new VerifyEmail());
                }

                $result = $user->save();
                if ($result) {
                    $response = [
                        'success' => true,
                        'message' => 'Successfully created user!',
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Something went wrong',
                    ];
                }
            }


            return response()->json($response, 200);
        } catch (\Exception $exception) {

            return $response = [
                'success' => false,
//            'data'    => $result,
                'message' => $exception->getMessage(),
            ];
        }
    }

    /**
     * Login user and create token
     *
     * @bodyParam email string required The email address of the User.
     * @bodyParam password string required The password of the User.
     * @response {
     * "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiY2UwN2I1ZjY0YzdiZGRlMWYwMjJlZWVhOWJmYzViZDQwZDk0MDM4ZmNhNzhhN2VkN2RkODYyZWU0Y2JhODk4MTZkZmVjMzBkNGYwNTA5MDUiLCJpYXQiOjE2MDU1Mjk1NzEsIm5iZiI6MTYwNTUyOTU3MSwiZXhwIjoxNjM3MDY1NTcxLCJzdWIiOiI2Iiwic2NvcGVzIjpbXX0.kxYeLAD_LMkSOs1KigyMnarEI5F8LrhEL1ogNLBdSqmQryfEdkaW1xgDNr1RaDzxLck0_6eQoifJb9n5qP3DD8chqVRS3UXtvGHh6SqB0_YExHkp8o1GHGBG1PgxOFm85QRUJYw8rvxsMo8wcumez4WgsqPIDOJkC9epp7KhCQV0psmsp0-ZCbptZNUabrvtrwaz_dhmFVluLvNrbG5_0pAI6CCOK9cWwv9T6zE5dQDxS-0_CA_LfXfhE9mvg-7rnWVmMqpbPpmpYdbv1tN7w652GbWiTSaYp3Psi_-UuljJ5kmBP_ia5sMYLMJc8VVPQPoNX4sTvQ6HeibpFCsXu3dEs8JrbQl_WI_za9th-G9jOPcAA6kXFrLe6Su84YSMafCjl1-Cc0UczC_S-ziaIn3PMGM04qfBrLmMoM22njBpIZ5U6R34hXe4LEPD1KGkiP7RhO0wi1budJmXoHDQjXWgpaMKH9I053f1hz_REoM0huNvZ0ADrA7Xo_8gWcx5oUM-HvLK80MIm3-f1xrPQW4hwh3r__NrMaLJkpY7yM5de8x_0-6i5vGCiksTl7zbrfCGarS9fo4qmyEN2n6Fns5Pq89iK3uKeBQe2c6QHNIxtDoxladZF9s5UHdp2OjTC4hDahNJxUuwSvo8Cd_MS2TWJhqXeYLGkgh_GpPqCGA",
     * "token_type": "Bearer",
     * "expires_at": "2021-11-16 18:26:11"
     * }
     */

    /*{
         "email": "ashif@gmail.com",
         "password": "123456"
        }*/
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        $credentials = request(['email', 'password']);

        try {
            if (!Auth::attempt($credentials))
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);

            $user = $request->user();

            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            if ($request->remember_me)
                $token->expires_at = Carbon::now()->addWeeks(1);
            $result = $token->save();

            $data = [
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'is_verify' => $user->email_verified_at,
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ];

            if ($result) {

                $request->merge([
                    'api_token' => $token->id,
                ]);

                $check = $this->attemptUserCheck($request);

                if (!$check['type']) {
                    $response = [
                        'success' => false,
                        'message' => $check['message'],
                    ];
                } else {
                    $response = [
                        'success' => true,
                        'data' => $data,
                        'message' => 'Successfully login!',
                    ];
                }


            } else {
                $response = [
                    'success' => false,
                    'message' => 'Something went wrong',
                ];
            }

            return response()->json($response, 200);
        } catch (\Exception $exception) {
            $response = [
                'success' => false,
                'message' => $exception->getMessage()
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->token()->revoke();
            $response = [
                'success' => true,
                'message' => 'Successfully logged out',
            ];
            return response()->json($response, 200);
        } catch (\Exception $exception) {
            $response = [
                'success' => false,
                'message' => $exception->getMessage()
            ];
            return response()->json($response, 500);
        }

    }

    /**
     * Get the authenticated User
     *
     * @response
     *  {
     * "success": true,
     * "data": [
     * {
     * "id": 6,
     * "role_id": 3,
     * "name": "Ashif",
     * "photo": "public/infixlms/img/admin.png",
     * "image": "public/infixlms/img/admin.png",
     * "avatar": "public/infixlms/img/admin.png",
     * "mobile_verified_at": null,
     * "email_verified_at": null,
     * "notification_preference": "mail",
     * "is_active": 1,
     * "username": "ashif@gmail.com",
     * "email": "ashif@gmail.com",
     * "email_verify": "0",
     * "phone": "01722334455",
     * "address": null,
     * "city": "1374",
     * "country": "19",
     * "zip": null,
     * "dob": null,
     * "about": null,
     * "facebook": null,
     * "twitter": null,
     * "linkedin": null,
     * "instagram": null,
     * "subscribe": 0,
     * "provider": null,
     * "provider_id": null,
     * "status": 1,
     * "balance": 0,
     * "currency_id": 112,
     * "special_commission": 1,
     * "payout": "Paypal",
     * "payout_icon": "/uploads/payout/pay_1.png",
     * "payout_email": "demo@paypal.com",
     * "referral": null,
     * "added_by": 0,
     * "created_at": "2020-11-16T12:09:40.000000Z",
     * "updated_at": "2020-11-16T12:09:40.000000Z"
     * } ,
     * "message": "Getting user info"
     * }
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        try {
            $data = $request->user();
            $response = [
                'success' => true,
                'data' => $data,
                'message' => 'Getting user info',
            ];

            return response()->json($response, 200);
        } catch (\Exception $exception) {
            $response = [
                'success' => false,
                'message' => $exception->getMessage()
            ];
            return response()->json($response, 500);
        }
    }


    /**
     * Change Password
     *
     * @bodyParam old_password string required The current password of the User.
     * @bodyParam new_password string required The new password of the User.
     * @bodyParam confirm_password string required The confirm password of the User.
     * @response {
     * "success": true,
     * "message": "Password updated successfully."
     * }
     */
    public function changePassword(Request $request)
    {
        $input = $request->all();
        $userid = $data = $request->user()->id;
        $rules = array(
            'old_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $arr = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());
        } else {
            try {
                if ((Hash::check(request('old_password'), Auth::user()->password)) == false) {
                    $arr = array("status" => 400, "message" => "Check your old password.", "data" => array());
                } else if ((Hash::check(request('new_password'), Auth::user()->password)) == true) {
                    $arr = array("status" => 400, "message" => "Please enter a password which is not similar then current password.", "data" => array());
                } else {
                    User::where('id', $userid)->update(['password' => Hash::make($input['new_password'])]);
                    $arr = array("status" => 200, "message" => "Password updated successfully.", "data" => array());
                }
            } catch (\Exception $ex) {
                if (isset($ex->errorInfo[2])) {
                    $msg = $ex->errorInfo[2];
                } else {
                    $msg = $ex->getMessage();
                }
                $arr = array("status" => 400, "message" => $msg, "data" => array());
            }
        }
        if ($arr['status'] == 200) {
            $status = true;
        } else {
            $status = false;
        }
        $response = [
            'success' => $status,
            'message' => $arr['message'],
        ];
        return response()->json($response, $arr['status']);

    }

    public function setFcmToken(Request $request)
    {

        try {
            $user = User::find($request->id);
            $user->device_token = $request->token;
            $user->save();

            $response = [
                'success' => true,
                'message' => 'Successfully set fcm token',
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
            return response()->json($response, 500);
        }
    }


    public function attemptUserCheck($request)
    {
        $result['type'] = true;
        $result['message'] = '';

        if (Auth::user()->status == 0) {
            Auth::logout();

            $result['type'] = false;
            $result['message'] = 'Your account has been disabled !';
            return $result;
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

            $loginController = new LoginController();
            if (!$loginController->multipleLogin($request)) {
                $result['type'] = false;
                $result['message'] = 'Your Account is already logged in, into ' . Settings('device_limit') . ' devices';
                return $result;
            }
        }

        session(['role_id' => Auth::user()->role_id]);
        if (isModuleActive('Chat')) {
            userStatusChange(auth()->id(), 1);
        }


        return $result;

    }
}
