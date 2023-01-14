<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EdumeThemeSeeder extends Seeder
{

    public function run()
    {
        \Illuminate\Support\Facades\DB::statement("INSERT INTO `themes` (`id`, `user_id`, `name`, `title`, `image`, `version`, `folder_path`, `live_link`, `description`, `is_active`, `status`, `tags`) VALUES
(2, 1, 'edume', 'Edume Theme', 'public/frontend/edume/img/edume.jpg', '1.0.0', 'edume', '#', 'Edume is a new premium theme for infix LMS', 0, 1, 'tags, tags, tags')");
    }
}
