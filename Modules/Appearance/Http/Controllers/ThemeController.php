<?php

namespace Modules\Appearance\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ZipArchive;
use App\Traits\UploadTheme;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Modules\Appearance\Entities\Theme;
use Modules\Appearance\Services\ThemeService;

class ThemeController extends Controller
{
    use UploadTheme;

    protected $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    public function index()
    {
        try {
            $activeTheme = $this->themeService->activeOne();
            $ThemeList = $this->themeService->getAllActive();
            return view('appearance::theme.index', compact('ThemeList', 'activeTheme'));
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    public function create()
    {
        try {
            return view('appearance::theme.components.create');
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function active(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            $this->themeService->isActive($request->only('id'), $request->id);

            UpdateGeneralSetting('frontend_active_theme', $this->themeService->showById($request->id)->name);

            $notification = array(
                'messege' => 'Theme Change Successfully.',
                'alert-type' => 'success'
            );
            Cache::forget('frontend_active_theme_'.SaasDomain());
            Cache::forget('getAllTheme_'.SaasDomain());
            Cache::forget('color_theme_'.SaasDomain());
            GenerateGeneralSetting(SaasDomain());
            GenerateHomeContent(SaasDomain());

            return redirect(route('appearance.themes.index'))->with($notification);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function store(Request $request)
    {
        $message = trans('common.Theme Update Successfully');

        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            if ($request->hasFile('themeZip')) {

                $dir = 'theme';
                if (!is_dir($dir))
                    mkdir($dir, 0777, true);

                $path = $request->themeZip->store('theme');

                $request->themeZip->getClientOriginalName();

                $zip = new ZipArchive;
                $res = $zip->open(storage_path('app/' . $path));

                $random_dir = Str::random(10);


                $dir = explode('/', $zip->getNameIndex(0))[0] ?? $zip->getNameIndex(0);

                if ($res === true) {
                    $zip->extractTo(storage_path('app/temp/' . $random_dir . '/theme'));
                    $zip->close();
                } else {
                    dd('could not open');
                }

                $str = @file_get_contents(storage_path('app/temp/') . $random_dir . '/theme/' . $dir . '/config.json', true);

                $json = json_decode($str, true);
                if (!empty($json['files'])) {
                    foreach ($json['files'] as $key => $directory) {
                        if ($key == 'asset_path') {
                            if (!is_dir($directory)) {
                                mkdir(base_path($directory), 0777, true);
                            }
                        }
                        if ($key == 'view_path') {
                            if (!is_dir($directory)) {
                                mkdir(base_path($directory), 0777, true);
                            }
                        }
                    }
                }

                // Create/Replace new files.
                if (!empty($json['files'])) {
                    foreach ($json['files'] as $key => $file) {

                        if ($key == 'asset_path') {
                            $src = base_path('storage/app/temp/' . $random_dir . '/theme' . '/' . $json['folder_path'] . '/asset');
                            $dst = base_path($file);
                            $this->recurse_copy($src, $dst);

                        }
                        if ($key == 'view_path') {
                            $src = base_path('storage/app/temp/' . $random_dir . '/theme' . '/' . $json['folder_path'] . '/view');
                            $dst = base_path($file);
                            $this->recurse_copy($src, $dst);
                        }
                    }
                }
                $alreadyHas = Theme::where('name', $json['name'])->first();

                if (!$alreadyHas) {
                    Theme::create([
                        'user_id' => Auth::user()->id,
                        'name' => $json['name'],
                        'title' => $json['title'],
                        'image' => $json['image'],
                        'version' => $json['version'],
                        'folder_path' => $json['folder_path'],
                        'live_link' => $json['live_link'],
                        'description' => $json['description'],
                        'is_active' => $json['is_active'],
                        'status' => $json['status'],
                        'item_code' => $json['item_id'],
                        'tags' => $json['tags']
                    ]);
                    $message = trans('common.New Theme Upload Successfully');
                }
            }
            if (is_dir('theme') || is_dir('temp')) {

                $this->delete_directory(storage_path('app/theme'));
                $this->delete_directory(storage_path('app/temp'));
            }


            Toastr::success($message, trans('common.Success'));

            return redirect(route('appearance.themes.index'));
        } catch (Exception $e) {
            if (is_dir('theme') || is_dir('temp')) {

                $this->delete_directory(storage_path('app/theme'));
                $this->delete_directory(storage_path('app/temp'));
            }

            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function show($id)
    {
        try {
            $theme = $this->themeService->showById($id);
            return view('appearance::theme.components.show', compact('theme'));
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    public function destroy(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            $this->themeService->delete($request->id);

            $notification = array(
                'messege' => 'Theme Deleted Successfully.',
                'alert-type' => 'success'
            );
            return redirect(route('appearance.themes.index'))->with($notification);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function demo()
    {
        try {
            return view('appearance::theme.demo');
        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function demoSubmit(Request $request)
    {
        $request->validate([
            'demo' => ['required', 'mimes:zip'],
        ]);
        try {


            if ($request->hasFile('demo')) {
                $path = $request->demo->store('updateFile');
                $request->demo->getClientOriginalName();
                $zip = new ZipArchive;
                $res = $zip->open(storage_path('app/' . $path));
                if ($res === true) {
                    $zip->extractTo(storage_path('app/tempUpdate'));
                    $zip->close();
                } else {
                    abort(500, 'Error! Could not open File');
                }

                $str = @file_get_contents(storage_path('app/tempUpdate/config.json'), true);
                if ($str === false) {
                    abort(500, 'The update file is corrupt.');

                }

                $json = json_decode($str, true);
                if (empty($json) || empty($json['version'])) {
                    Toastr::error('Config File Missing', trans('common.Failed'));
                    return redirect()->back();
                }

                $src = storage_path('app/tempUpdate');
                $dst = base_path('/');
                $this->recurse_copy($src, $dst);

                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                if (isset($json['tables']) & !empty($json['tables'])) {
                    foreach ($json['tables'] as $migration) {
                        DB::table($migration)->truncate();
                    }
                }
                $sql = base_path('/demo.sql');
                if (File::exists($sql)) {
                    DB::unprepared(file_get_contents($sql));
                }
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');


            }


            if (storage_path('app/updateFile')) {
                $this->delete_directory(storage_path('app/updateFile'));
            }
            if (storage_path('app/tempUpdate')) {
                $this->delete_directory(storage_path('app/tempUpdate'));
            }
            Artisan::call('cache:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');
            File::delete(File::glob('bootstrap/cache/*.php'));
            GenerateGeneralSetting(SaasDomain());
            GenerateHomeContent(SaasDomain());
            Toastr::success("Demo Import Successfully", 'Success');
            return redirect()->back();
        } catch (Exception $e) {
            if (storage_path('app/updateFile')) {
                $this->delete_directory(storage_path('app/updateFile'));
            }
            if (storage_path('app/tempUpdate')) {
                $this->delete_directory(storage_path('app/tempUpdate'));
            }
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }
}
