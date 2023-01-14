<?php

namespace Modules\Appearance\Entities;

use App\Traits\Tenantable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class ThemeCustomize extends Model
{
    use Tenantable;
    protected $guarded = ['id'];

    public function theme()
    {
        return $this->belongsTo(Theme::class, 'theme_id')->withDefault();
    }


    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            Cache::forget('theme_customizes_'.SaasDomain());
            Cache::forget('color_theme_'.SaasDomain());
        });
        self::updated(function ($model) {
            Cache::forget('theme_customizes_'.SaasDomain());
            Cache::forget('color_theme_'.SaasDomain());
        });
        self::deleted(function ($model) {
            Cache::forget('theme_customizes_'.SaasDomain());
            Cache::forget('color_theme_'.SaasDomain());
        });
    }

    public static function getData()
    {
        return Cache::rememberForever('theme_customizes_'.SaasDomain(), function () {
            return ThemeCustomize::select('*')->first();
        });
    }
}
