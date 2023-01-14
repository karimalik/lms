<?php

namespace Modules\SystemSetting\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\SystemSetting\Entities\Department;

class DepartmentController extends Controller
{
    public function index()
    {
        try {
            $departments = Department::all();
            return view('systemsetting::department.index', compact('departments'));

        } catch (\Exception $e) {
            Toastr::error('Operation failed');
            return back();
        }
    }

    public function store(Request $request)
    {
        $validate_rules = [
            'name' => ['required', 'string','max:255'],
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));

        try {
            Department::create($request->all());

            return response()->json([
                'success' => trans('common.Operation successful'),
                'TableData' => $this->loadTableData(),
            ]);

        } catch (\Exception $e) {
            return  response()->json([
                'error' => trans('common.Something Went Wrong'),
            ]);
        }
    }

    public function update(Request $request)
    {
        $validate_rules = [
            'name' => ['required', 'string','max:255'],
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));

        try {
            $department = Department::find($request->id);
            $department->update($request->all());

            return response()->json([
                'success' => trans('common.Operation successful'),
                'TableData' => $this->loadTableData(),
            ]);

        } catch (\Exception $e) {
            return  response()->json([
                'error' => trans('common.Something Went Wrong'),
            ]);
        }
    }

    public function delete(Request $request)
    {
        $validate_rules = [
            'id' => 'required',
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));

        try {

            Department::find($request['id'])->delete();
            return response()->json([
                'success' => trans('common.Operation successful'),
                'TableData' => $this->loadTableData(),
            ]);

        } catch (\Exception $e) {
            return  response()->json([
                'error' => trans('common.Something Went Wrong'),
            ]);
        }
    }

    private function loadTableData()
    {
        try {
            $departments = Department::all();
            return  (string)view('systemsetting::department.components.list', compact('departments'));

        } catch (\Exception $e) {
            // LogActivity::errorLog($e->getMessage());
            return  response()->json([
                'error' => trans('common.Something Went Wrong'),
            ]);
        }
    }
}
