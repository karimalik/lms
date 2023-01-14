<?php

namespace Modules\Setting\Http\Controllers;

use App\GoogleToken;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Socialite\Facades\Socialite;

class GoogleDriveController extends Controller
{

    public function index()
    {
        return view('setting::gdrive-setting');
    }

    public function update(Request $request)
    {
        if ($request->gdrive_project_id) {
            SaasEnvSetting(SaasDomain(), 'GOOGLE_DRIVE_PROJECT_ID', $request->gdrive_project_id);
        }
        if ($request->gdrive_client_id) {
            SaasEnvSetting(SaasDomain(), 'GOOGLE_DRIVE_CLIENT_ID', $request->gdrive_client_id);
        }
        if ($request->gdrive_client_secret) {
            SaasEnvSetting(SaasDomain(), 'GOOGLE_DRIVE_CLIENT_SECRET', $request->gdrive_client_secret);
        }

        SaasEnvSetting(SaasDomain(), 'GOOGLE_DRIVE_REDIRECT', url('/') . '/setting/login/google/callback');

        GenerateGeneralSetting(SaasDomain());

        Toastr::success(trans('setting.Api Settings Saved Successfully'));
        return back();
    }

    public function redirectToGoogleProvider()
    {
        try {
            $parameters = [
                'access_type' => 'offline',
                'approval_prompt' => 'force'
            ];
            return Socialite::driver('google-drive')->scopes(["https://www.googleapis.com/auth/drive"])->with($parameters)->redirect();
        } catch (\Exception $e) {
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }
    }

    public function googleLogout()
    {
        GoogleToken::where('user_id', \auth()->id())->delete();
        return back();
    }

    public function handleProviderGoogleCallback()
    {
        $auth_user = Socialite::driver('google-drive')->user();
        $data = [
            'token' => $auth_user->token,
            'expires_in' => $auth_user->expiresIn,
            'google_email' => $auth_user->email,
        ];

        if ($auth_user->refreshToken) {
            $data['refresh_token'] = $auth_user->refreshToken;
        }

        $data['user_id'] = auth()->id();
        GoogleToken::create($data);

        return redirect()->route('gdrive.setting');
    }
}
