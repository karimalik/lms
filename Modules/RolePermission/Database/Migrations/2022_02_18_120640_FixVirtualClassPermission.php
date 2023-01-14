<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class FixVirtualClassPermission extends Migration
{

    public function up()
    {
        DB::table('permissions')->where('name', 'List')
            ->where('route', 'virtual-class.index')->update(
                [
                    'route' => 'virtual-class.list'
                ]
            );

        DB::table('permissions')->where('name', 'Virtual Class List')
            ->where('route', 'virtual-class.index')->update(
                [
                    'parent_route' => 'virtual-class'
                ]
            );

        DB::table('permissions')->insert([
            [
                'name' => 'Google Drive Config',
                'route' => 'gdrive.setting',
                'parent_route' => 'settings',
                'type' => 2,
            ], [
                'name' => 'Update',
                'route' => 'gdrive.setting.update',
                'parent_route' => 'gdrive.setting',
                'type' => 3,
            ],
        ]);

    }


    public function down()
    {
        //
    }
}
