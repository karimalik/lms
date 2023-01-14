<?php

namespace Modules\PopupContent\Entities;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PopupContent extends Model
{
    use Tenantable;

    protected $fillable = [];

    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            Cache::forget('popup_contents_' . SaasDomain());
        });
        self::updated(function ($model) {
            Cache::forget('popup_contents_' . SaasDomain());
        });
        self::deleted(function ($model) {
            Cache::forget('popup_contents_' . SaasDomain());
        });
    }

    public static function getData()
    {
        if (function_exists('SaasDomain')) {
            $domain = SaasDomain();
        } else {
            $domain = 'main';
        }
        return Cache::rememberForever('popup_contents_' . $domain, function () {
            return PopupContent::first();
        });
    }
}
