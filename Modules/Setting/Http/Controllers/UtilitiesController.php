<?php

namespace Modules\Setting\Http\Controllers;

use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class UtilitiesController extends Controller
{
    public function __construct()
    {
        if (!defined('STDIN')) {
            define('STDIN', fopen('php://stdin', 'r'));
        }
    }

    public function index(Request $request)
    {
        if (isset($request->utilities)) {
            if (demoCheck()) {
                return redirect()->back();
            }

            $utility = $request->utilities;
            if ($utility == "optimize_clear") {
                Artisan::call('optimize:clear');

                File::delete(File::glob('bootstrap/cache/*.php'));

            } elseif ($utility == "clear_log") {
                array_map('unlink', array_filter((array)glob(storage_path('logs/*.log'))));
                array_map('unlink', array_filter((array)glob(storage_path('debugbar/*.json'))));

            } elseif ($utility == "change_debug") {
                envu([
                    'APP_DEBUG' => env('APP_DEBUG') ? "false" : "true"
                ]);
            } elseif ($utility == "force_https") {
                putEnvConfigration('FORCE_HTTPS', env('FORCE_HTTPS') ? "false" : "true");

            } else {
                Toastr::error(trans('common.Invalid Command'), trans('common.Failed'));
                return redirect()->back();
            }

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();

        }
        return view('setting::utilities');
    }

    public function resetDatabase(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }


        try {
            if ($request->password == "") {
                Toastr::error(__('common.enter_your_password'));
            } elseif (Hash::check($request->password, auth()->user()->password)) {
                $this->freshDatabase();
                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            } else {
                Toastr::error(__('common.Password did not match with your account password'));
            }
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error(trans('common.Operation Failed'), trans('common.Error'));
            return redirect()->back();
        }

    }

    public function importDemoDatabase(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }

        try {
            if ($request->password == "") {
                Toastr::error(__('common.enter_your_password'));
            } elseif (Hash::check($request->password, auth()->user()->password)) {
                $this->freshDatabase();
                Artisan::call('db:seed', array('--force' => true));

                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            } else {
                Toastr::error(__('common.Password did not match with your account password'));
            }
            return back();
        } catch (\Exception $e) {
            Toastr::error(trans('common.Operation Failed'), trans('common.Error'));
            return redirect()->back();
        }

    }

    public function freshDatabase()
    {
        $user = DB::table('users')->where('id', 1)->first();
        Artisan::call('db:wipe', array('--force' => true));
        Artisan::call('migrate', array('--force' => true));
        User::where('id', 1)->update(collect($user)->toArray());
        UpdateGeneralSetting('system_domain', env('APP_URL'));
        GenerateHomeContent(SaasDomain());
    }

}
