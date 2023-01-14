<?php

namespace App;

use App\Traits\Tenantable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class AboutPage extends Model
{
use Tenantable;
    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            Cache::forget('AboutPage_'.SaasDomain());
        });
        self::updated(function ($model) {
            Cache::forget('AboutPage_'.SaasDomain());
        });
        self::deleted(function ($model) {
            Cache::forget('AboutPage_'.SaasDomain());
        });
    }

    public static function getData()
    {
        return Cache::rememberForever('AboutPage_'.SaasDomain(), function () {
            return AboutPage::first();
        });
    }

}
