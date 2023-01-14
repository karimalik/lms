<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddCoulumnInAboutPage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('about_pages', function ($table) {
            if (!Schema::hasColumn('about_pages', 'show_testimonial')) {
                $table->boolean('show_testimonial')->default(1);
            }

            if (!Schema::hasColumn('about_pages', 'show_brand')) {
                $table->boolean('show_brand')->default(1);
            }

            if (!Schema::hasColumn('about_pages', 'show_become_instructor')) {
                $table->boolean('show_become_instructor')->default(1);
            }


            if (!Schema::hasColumn('about_pages', 'total_teacher')) {
                $table->string('total_teacher')->default('100+');
            }



            if (!Schema::hasColumn('about_pages', 'total_student')) {
                $table->string('total_student')->default('200+');
            }

            if (!Schema::hasColumn('about_pages', 'total_courses')) {
                $table->string('total_courses')->default('150+');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
