<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Language;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\Setting\Model\GeneralSetting;
use Modules\RolePermission\Entities\RolePermission;


class FrontendHomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('maintenanceMode');
    }

    public function index()
    {
        try {
            //SaasDomain();
            $blocks = json_decode(HomeContents('homepage_block_positions'));
            $homeContent =  app('getHomeContent');
            return view(theme('pages.index'), compact('blocks', 'homeContent'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
    public function test()
    {
        $domain=session()->get('domain') ?? 'main';
        $path = Storage::path('settings.json');
        $settings = GeneralSetting::get(['key', 'value'])->pluck('value', 'key')->toArray();
        $strJsonFileContents = file_get_contents($path);
        $file_data = json_decode($strJsonFileContents, true);
        $setting_array[$domain]=$settings;
        $merged_array= array_merge($file_data,$setting_array);
        $merged_array= json_encode($merged_array,JSON_PRETTY_PRINT);
        file_put_contents($path, $merged_array);

        return file_get_contents($path);
    }
}
