<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\CourseSetting\Entities\CourseLevel;

class CreateCourseLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_levels', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        $level1 = new CourseLevel();
        $level1->id = 1;
        $level1->title = 'Beginner';
        $level1->status = 1;
        $level1->save();

        $level2 = new CourseLevel();
        $level2->id = 2;
        $level2->title = 'Intermediate';
        $level2->status = 1;
        $level2->save();

        $level3 = new CourseLevel();
        $level3->id = 3;
        $level3->title = 'Advance';
        $level3->status = 1;
        $level3->save();

        $level4 = new CourseLevel();
        $level3->id = 4;
        $level4->title = 'Pro';
        $level4->status = 1;
        $level4->save();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_levels');
    }
}
