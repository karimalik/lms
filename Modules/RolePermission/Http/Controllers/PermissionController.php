<?php

namespace Modules\RolePermission\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Modules\RolePermission\Entities\Role;
use Modules\RolePermission\Entities\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        // $this->middleware(['auth:admin', 'permission']);
    }


    public function index(Request $request)
    {
        $role_id = $request['id'];
        if ($role_id == null || $role_id == 1) {
            return redirect(route('permission.roles.index'));
        }
        if ($role_id == 3) {
            $backend = 0;
        } else {
            $backend = 1;
        }
        $PermissionList = Permission::where('status', 1)->where('backend', $backend)->get();
        $role = Role::with('permissions')->find($role_id);
        $data['role'] = $role;
        $data['MainMenuList'] = $PermissionList->where('type', 1);
        $data['SubMenuList'] = $PermissionList->where('type', 2);
        $data['ActionList'] = $PermissionList->where('type', 3);
        $data['PermissionList'] = $PermissionList;
        return view('rolepermission::permission', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => "required",
            'module_id' => "required|array"
        ]);

        if ($validator->fails()) {
            Toastr::error('Please Select Minimum one Permission', 'Failed');
            return redirect()->back();
        }

        try {
            $array = array_unique($request->module_id);
            $module_array = [];
            foreach ($array as $key => $value) {
                $module_array[$key]['permission_id'] = $value;
                $module_array[$key]['lms_id'] = Auth::user()->lms_id;
            }
            DB::beginTransaction();
            $role = Role::findOrFail($request->role_id);
            $role->permissions()->wherePivot('lms_id', Auth::user()->lms_id)->detach();

            $role->permissions()->attach($module_array);
            DB::commit();
            Cache::forget('PermissionList_' . SaasDomain());
            Cache::forget('RoleList_' . SaasDomain());
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }
}
