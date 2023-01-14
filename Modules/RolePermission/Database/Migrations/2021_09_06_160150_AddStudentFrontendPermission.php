<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStudentFrontendPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = [
            ['id' => 359, 'module_id' => 359, 'parent_id' => null, 'name' => 'Student Dashboard', 'route' => 'studentDashboard', 'type' => 1, 'backend' => 0],
            ['id' => 360, 'module_id' => 360, 'parent_id' => null, 'name' => 'My Courses', 'route' => 'myCourses', 'type' => 1, 'backend' => 0],
            ['id' => 361, 'module_id' => 361, 'parent_id' => null, 'name' => 'My Quizzes', 'route' => 'myQuizzes', 'type' => 1, 'backend' => 0],
            ['id' => 362, 'module_id' => 362, 'parent_id' => null, 'name' => 'Live Classes', 'route' => 'myClasses', 'type' => 1, 'backend' => 0],
//            ['id' => 363, 'module_id' => 363, 'parent_id' => null, 'name' => 'Live Classes', 'route' => 'myClasses', 'type' => 1, 'backend' => 0],
            ['id' => 364, 'module_id' => 364, 'parent_id' => null, 'name' => 'Purchase History', 'route' => 'myPurchases', 'type' => 1, 'backend' => 0],
            ['id' => 365, 'module_id' => 365, 'parent_id' => null, 'name' => 'My Profile', 'route' => 'myProfile', 'type' => 1, 'backend' => 0],
            ['id' => 366, 'module_id' => 366, 'parent_id' => null, 'name' => 'Account Settings', 'route' => 'myAccount', 'type' => 1, 'backend' => 0],
            ['id' => 367, 'module_id' => 367, 'parent_id' => null, 'name' => 'Deposit', 'route' => 'deposit', 'type' => 1, 'backend' => 0],
            ['id' => 368, 'module_id' => 368, 'parent_id' => null, 'name' => 'Logged In Devices', 'route' => 'logged.in.devices', 'type' => 1, 'backend' => 0],
            ['id' => 369, 'module_id' => 369, 'parent_id' => null, 'name' => 'Referral', 'route' => 'referral', 'type' => 1, 'backend' => 0],
            ['id' => 370, 'module_id' => 370, 'parent_id' => null, 'name' => 'My Certificate', 'route' => 'myCertificate', 'type' => 1, 'backend' => 0],

        ];

        DB::table('permissions')->insert($sql);

        $sql2 = "INSERT INTO `role_permission` (`permission_id`, `role_id`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
                        (359, 3, 1, 1, 1, now(), now()),
                        (360, 3, 1, 1, 1, now(), now()),
                        (361, 3, 1, 1, 1, now(), now()),
                        (362, 3, 1, 1, 1, now(), now()),
                        (363, 3, 1, 1, 1, now(), now()),
                        (364, 3, 1, 1, 1, now(), now()),
                        (365, 3, 1, 1, 1, now(), now()),
                        (366, 3, 1, 1, 1, now(), now()),
                        (367, 3, 1, 1, 1, now(), now()),
                        (368, 3, 1, 1, 1, now(), now()),
                        (369, 3, 1, 1, 1, now(), now()),
                        (370, 3, 1, 1, 1, now(), now())
                        ";

        DB::statement($sql2);
        Cache::forget('PermissionList');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
