<?php

namespace Modules\CourseSetting\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\CourseSetting\Entities\Category;
use Faker\Generator as Faker;

class CategorySeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Category::insert([
            'name' => 'Business',
            'status' => '1',
            'show_home' => '1',
            'title' => 'Voluptas eos placeat',
            'description' => 'Laboris Nam laborum voluptatibus dolor aspernatur laboriosam commodo in voluptatem Temporibus eum',
            'position_order' => 2,
            'image' => 'public/demo/category/image/' . '1.' . 'png',
            'thumbnail' => 'public/demo/category/thumb/' . '1.' . 'png',
            'url' => 'https://youtu.be/bG9eMa_025c',
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);

        Category::insert([
            'name' => '3D Modeling',
            'status' => '1',
            'show_home' => '1',
            'title' => 'Voluptas eos placeat',
            'description' => 'Laboris Nam laborum voluptatibus dolor aspernatur laboriosam commodo in voluptatem Temporibus eum',
            'position_order' => 2,
            'image' => 'public/demo/category/image/' . '2.' . 'png',
            'thumbnail' => 'public/demo/category/thumb/' . '2.' . 'png',
            'url' => 'https://youtu.be/bG9eMa_025c',
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);

        Category::insert([
            'name' => 'UI UX Design',
            'status' => '1',
            'show_home' => '1',
            'title' => 'Voluptas eos placeat',
            'description' => 'Laboris Nam laborum voluptatibus dolor aspernatur laboriosam commodo in voluptatem Temporibus eum',
            'position_order' => 3,
            'image' => 'public/demo/category/image/' . '3.' . 'png',
            'thumbnail' => 'public/demo/category/thumb/' . '3.' . 'png',
            'url' => 'https://youtu.be/bG9eMa_025c',
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);

        Category::insert([
            'name' => 'Mobile Development',
            'status' => '1',
            'show_home' => '1',
            'title' => 'Voluptas eos placeat',
            'description' => 'Laboris Nam laborum voluptatibus dolor aspernatur laboriosam commodo in voluptatem Temporibus eum',
            'position_order' => 4,
            'image' => 'public/demo/category/image/' . '4.' . 'png',
            'thumbnail' => 'public/demo/category/thumb/' . '4.' . 'png',
            'url' => 'https://youtu.be/bG9eMa_025c',
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);

        Category::insert([
            'name' => 'Software Development',
            'status' => '1',
            'show_home' => '1',
            'title' => 'Voluptas eos placeat',
            'description' => 'Laboris Nam laborum voluptatibus dolor aspernatur laboriosam commodo in voluptatem Temporibus eum',
            'position_order' => 5,
            'image' => 'public/demo/category/image/' . '5.' . 'png',
            'thumbnail' => 'public/demo/category/thumb/' . '5.' . 'png',
            'url' => 'https://youtu.be/bG9eMa_025c',
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);



        Category::insert([
            'name' => 'Accounting',
            'status' => '1',
            'show_home' => '1',
            'title' => 'Accounting',
            'description' => 'Accounting',
            'position_order' => 1,
            'image' => 'public/demo/category/image/' . '5.' . 'png',
            'thumbnail' => 'public/demo/category/thumb/' . '5.' . 'png',
            'url' => '',
            'parent_id'=>'1',
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);


        Category::insert([
            'name' => 'MBA',
            'status' => '1',
            'show_home' => '1',
            'title' => 'MBA',
            'description' => 'MBA',
            'position_order' => 1,
            'image' => 'public/demo/category/image/' . '5.' . 'png',
            'thumbnail' => 'public/demo/category/thumb/' . '5.' . 'png',
            'url' => '',
            'parent_id'=>'1',
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);

        Category::insert([
            'name' => 'Blender Creator ',
            'status' => '1',
            'show_home' => '1',
            'title' => 'Blender Creator',
            'description' => 'Blender Creator',
            'position_order' => 3,
            'image' => 'public/demo/category/image/' . '5.' . 'png',
            'thumbnail' => 'public/demo/category/thumb/' . '5.' . 'png',
            'url' => '',
            'parent_id'=>'2',
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);

        Category::insert([
            'name' => '3D environments ',
            'status' => '1',
            'show_home' => '1',
            'title' => '3D environments',
            'description' => '3D environments',
            'position_order' => 3,
            'image' => 'public/demo/category/image/' . '5.' . 'png',
            'thumbnail' => 'public/demo/category/thumb/' . '5.' . 'png',
            'url' => '',
            'parent_id'=>'2',
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);


        Category::insert([
            'name' => 'Adobe XD ',
            'status' => '1',
            'show_home' => '1',
            'title' => 'Adobe XD',
            'description' => 'Adobe XD',
            'position_order' => 5,
            'image' => 'public/demo/category/image/' . '5.' . 'png',
            'thumbnail' => 'public/demo/category/thumb/' . '5.' . 'png',
            'url' => '',
            'parent_id'=>'3',
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);



        Category::insert([
            'name' => 'UI Design',
            'status' => '1',
            'show_home' => '1',
            'title' => 'UI Design',
            'description' => 'UI Design',
            'position_order' => 6,
            'image' => 'public/demo/category/image/' . '5.' . 'png',
            'thumbnail' => 'public/demo/category/thumb/' . '5.' . 'png',
            'url' => '',
            'parent_id'=>'3',
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);


        Category::insert([
            'name' => 'App Development',
            'status' => '1',
            'show_home' => '1',
            'title' => 'App Development',
            'description' => 'App Development',
            'position_order' => 7,
            'image' => 'public/demo/category/image/' . '5.' . 'png',
            'thumbnail' => 'public/demo/category/thumb/' . '5.' . 'png',
            'url' => '',
            'parent_id'=>'4',
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);



        Category::insert([
            'name' => 'Python',
            'status' => '1',
            'show_home' => '1',
            'title' => 'Python',
            'description' => 'Python',
            'position_order' => 8,
            'image' => 'public/demo/category/image/' . '5.' . 'png',
            'thumbnail' => 'public/demo/category/thumb/' . '5.' . 'png',
            'url' => '',
            'parent_id'=>'4',
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);

        Category::insert([
            'name' => 'iOS Development',
            'status' => '1',
            'show_home' => '1',
            'title' => 'iOS Development',
            'description' => 'iOS Development',
            'position_order' => 8,
            'image' => 'public/demo/category/image/' . '5.' . 'png',
            'thumbnail' => 'public/demo/category/thumb/' . '5.' . 'png',
            'url' => '',
            'parent_id'=>'5',
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);



        Category::insert([
            'name' => 'Laravel',
            'status' => '1',
            'show_home' => '1',
            'title' => 'Laravel',
            'description' => 'Laravel',
            'position_order' => 8,
            'image' => 'public/demo/category/image/' . '5.' . 'png',
            'thumbnail' => 'public/demo/category/thumb/' . '5.' . 'png',
            'url' => '',
            'parent_id'=>'5',
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);




    }
}
