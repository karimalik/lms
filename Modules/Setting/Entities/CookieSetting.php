<?php

namespace Modules\Setting\Entities;

use App\Traits\Tenantable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class CookieSetting extends Model
{
    use Tenantable;
    protected $fillable = [];


    public static function boot()
    {
        parent::boot();


        self::created(function ($model) {
            Cache::forget('cookie_'.SaasDomain());
            Cache::forget('CookieSetting_'.SaasDomain());
        });


        self::updated(function ($model) {
            Cache::forget('cookie_'.SaasDomain());
            Cache::forget('CookieSetting_'.SaasDomain());
        });

        self::deleted(function ($model) {
            Cache::forget('cookie_'.SaasDomain());
            Cache::forget('CookieSetting_'.SaasDomain());
        });
        static::addGlobalScope(new \App\Scopes\LmsScope);
    }


    public static function getData()
    {
        return Cache::rememberForever('CookieSetting_'.SaasDomain(), function () {
            return CookieSetting::select('image', 'details', 'btn_text', 'text_color', 'bg_color', 'allow')->first();
        });
    }
}
