<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AddUserManageRolePsermission extends Migration
{

    public function up()
    {
        $sql = [
            ['name' => 'User manager', 'route' => 'user.manager', 'parent_route' => null, 'type' => 1],
            ['name' => 'Staff', 'route' => 'staffs.index', 'parent_route' => 'user.manager', 'type' => 2],
            ['name' => 'Create', 'route' => 'staffs.store', 'parent_route' => 'staffs.index', 'type' => 3],
            ['name' => 'Update', 'route' => 'staffs.update', 'parent_route' => 'staffs.index', 'type' => 3],
            ['name' => 'Delete', 'route' => 'staffs.destroy', 'parent_route' => 'staffs.index', 'type' => 3],
            ['name' => 'View', 'route' => 'staffs.view', 'parent_route' => 'staffs.index', 'type' => 3],
            ['name' => 'Active', 'route' => 'staffs.active', 'parent_route' => 'staffs.index', 'type' => 3],
            ['name' => 'Inactive', 'route' => 'staffs.inactive', 'parent_route' => 'staffs.index', 'type' => 3],
            ['name' => 'Resume', 'route' => 'staffs.resume', 'parent_route' => 'staffs.index', 'type' => 3],

            ['name' => 'Department', 'route' => 'hr.department.index', 'parent_route' => 'user.manager', 'type' => 2],
            ['name' => 'Store', 'route' => 'hr.department.index', 'parent_route' => 'hr.department.index', 'type' => 3]
        ];

        DB::table('permissions')->insert($sql);
        Cache::forget('PermissionList');
        Cache::forget('RoleList');
        Cache::forget('PolicyPermissionList');
        Cache::forget('PolicyRoleList');
    }

    public function down()
    {
        //
    }
}
