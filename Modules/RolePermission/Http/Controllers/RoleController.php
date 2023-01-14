<?php

namespace Modules\RolePermission\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Routing\Controller;
use Modules\RolePermission\Entities\Permission;
use Modules\RolePermission\Entities\Role;
use Modules\RolePermission\Http\Requests\RoleFormRequest;
use Modules\RolePermission\Repositories\RoleRepository;

class RoleController extends Controller
{

    public $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->middleware(['auth']);
        $this->roleRepository = $roleRepository;
    }

    public function index()
    {
        $data['RoleList'] = $this->roleRepository->all();
        return view('rolepermission::index', $data);
    }

    public function studentIndex()
    {

        $PermissionList = Permission::where('status', 1)->where('backend', 0)->get();
        $role = Role::with('permissions')->find(3);
        $data['role'] = $role;
        $data['MainMenuList'] = $PermissionList->where('type', 1);
        $data['SubMenuList'] = $PermissionList->where('type', 2);
        $data['ActionList'] = $PermissionList->where('type', 3);
        $data['PermissionList'] = $PermissionList;
        return view('rolepermission::permission', $data);

    }

    public function staffIndex()
    {

        $PermissionList = Permission::where('status', 1)->where('backend', 1)->get();
        $role = Role::with('permissions')->find(4);
        $data['role'] = $role;
        $data['MainMenuList'] = $PermissionList->where('type', 1);
        $data['SubMenuList'] = $PermissionList->where('type', 2);
        $data['ActionList'] = $PermissionList->where('type', 3);
        $data['PermissionList'] = $PermissionList;
        return view('rolepermission::permission', $data);

    }

    public function create()
    {
        return view('rolepermission::create');
    }

    public function store(RoleFormRequest $request)
    {
        try {
            $this->roleRepository->create($request->except("_token"));
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->route('permission.roles.index');
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function show($id)
    {
        $PermissionList = Permission::where('status', 1)->where('backend', 1)->get();
        $role = Role::with('permissions')->find($id);
        $data['role'] = $role;
        $data['MainMenuList'] = $PermissionList->where('type', 1);
        $data['SubMenuList'] = $PermissionList->where('type', 2);
        $data['ActionList'] = $PermissionList->where('type', 3);
        $data['PermissionList'] = $PermissionList;
        return view('rolepermission::permission', $data);
    }


    public function edit(Role $role)
    {
        try {
            if (isModuleActive('HumanResource')) {
                $data['RoleList'] = $this->roleRepository->all();
                $data['role'] = $role;
                return view('rolepermission::index', $data);
            }
            $RoleList = $this->roleRepository->all();
            return view('rolepermission::role', compact('RoleList', 'role'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function update(RoleFormRequest $request, $id)
    {
        try {
            $this->roleRepository->update($request->except("_token"), $id);
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->route('permission.roles.index');
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function destroy($id)
    {
        try {
            $this->roleRepository->delete($id);
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }
}
