<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class FooterNewsLetter extends Component
{


    public function render()
    {
        $newsletterSetting = Cache::rememberForever('newsletterSetting_'.SaasDomain(), function () {
            return DB::table('newsletter_settings')
                ->select('home_status', 'home_service', 'home_list_id', 'student_status', 'student_service', 'student_list_id', 'instructor_status',
                    'instructor_status', 'instructor_service', 'instructor_list_id')
                ->first();
        });
        return view(theme('components.footer-news-letter'), compact('newsletterSetting'));
    }
}
