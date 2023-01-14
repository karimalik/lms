<?php

namespace Modules\Backup\Http\Controllers;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class BackupController extends Controller
{


    public function index()
    {
        try {
            $dir = is_dir(public_path("/database-backup"));
            $getDirData = [];
            if ($dir) {
                $getDirData = scandir(public_path("/database-backup"));
            }
            $allBackup = [];

            foreach ($getDirData as $key => $value) {
                if ($value != '.' && $value != '..')
                    array_push($allBackup, $value);
            }

            $data = [
                'allBackup' => $allBackup
            ];

            return view('backup::backup.index', $data);

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }


    public function checkValidDate($date, $format = "d-m-Y")
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            Artisan::call('backup:database');
            Toastr::success('New database backup has been created', 'Backup Done!!');
            return redirect()->back();
        } catch (\Exception $e) {

//            Toastr::error('Directory not empty!', 'Error!!');
            return redirect()->back();
        }
    }

    public function delete($dir)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            $dir = public_path("/database-backup/" . $dir);
            if (is_dir($dir)) {
                array_map("unlink", glob("$dir/*.*"));
                rmdir($dir);
                Toastr::success('Database backup has been deleted', 'Delete Done!!');
                return redirect()->back();
            }

            Toastr::error('Something Wrong', 'Error!!');
            return redirect()->back();
        } catch (\Exception $e) {

            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }

    public function import(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }


        $rules = [
            'db_file' => 'required',
        ];

        $this->validate($request, $rules, validationMessage($rules));

        try {


            if (pathinfo($request->db_file->getClientOriginalName(), PATHINFO_EXTENSION) !== 'sql') {
                Toastr::error('Invalid File, file should be sql', 'Invalid File!!');
                return redirect()->back();
            }

            set_time_limit(2700);

            DB::statement("SET foreign_key_checks=0");
            // $tableNames = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();

            $tableNames = DB::select('SHOW TABLES');

            foreach ($tableNames as $name) {

                //if you don't want to truncate migrations
                if (head($name) == 'migrations') {
                    continue;
                }
                DB::table(head($name))->truncate();
            }

            DB::statement("SET foreign_key_checks=1");
            $file = $request->file('db_file');
            $filename = $file->getClientOriginalName();
            $file->move(public_path() . "/tmpfile/", $filename);
            $sql = public_path() . "/tmpfile/" . $filename;
            DB::unprepared(file_get_contents($sql));

            if (file_exists($sql)) {
                unlink($sql);
            }

            Toastr::success('Database import successfully', 'import Done!!');
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }
}
