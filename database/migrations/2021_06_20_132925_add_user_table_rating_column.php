<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddUserTableRatingColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            if (!Schema::hasColumn('users', 'total_rating')) {
                $table->integer('total_rating')->default(0);;
            }

            if (!Schema::hasColumn('users', 'language_rtl')) {
                $table->boolean('language_rtl')->default(false);;
            }
        });

        $users = \App\User::all();
        foreach ($users as $user) {
            $courses = \Modules\CourseSetting\Entities\Course::where('user_id', $user->id)->get();
            $rating = 0;
            foreach ($courses as $course) {
                $rating = $rating + $course->total_rating;
            }

            if (count($courses) != 0) {
                $avg = ($rating / count($courses));
            } else {
                $avg = 0;
            }
            $user->language_rtl = $user->userLanguage->rtl;
            $user->total_rating = $avg;

            $user->save();
        }


        DB::statement('ALTER TABLE `users` CHANGE `username` `username` VARCHAR(100) NULL');
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
