<?php

namespace App\Http\Controllers\Api;

use App\AboutPage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TableApiController extends Controller
{

    public function tableData($table_name)
    {
        try {
            $data = DB::table($table_name)->get();
        } catch (\Exception $e) {
            $data = null;
        }
        return response()->json($data, 200);
    }
}
