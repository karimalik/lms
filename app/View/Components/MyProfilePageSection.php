<?php

namespace App\View\Components;

use App\StudentCustomField;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MyProfilePageSection extends Component
{

    public function render()
    {
        $profile = Auth::user();
        $countries = DB::table('countries')->select('id', 'name')->get();
        $states = DB::table('states')->where('country_id', $profile->country)->where('id', $profile->state)->select('id', 'name')->get();
        $cities = DB::table('spn_cities')->where('state_id', $profile->state)->where('id', $profile->city)->select('id', 'name')->get();
        $custom_field = StudentCustomField::getData();

        $langs = DB::table('languages')
            ->select('id', 'native', 'code', 'rtl')
            ->where('status', '=', 1)
            ->get();

        return view(theme('components.my-profile-page-section'), compact('profile', 'countries', 'cities', 'langs', 'custom_field', 'states'));
    }
}
