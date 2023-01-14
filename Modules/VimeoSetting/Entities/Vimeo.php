<?php

namespace Modules\VimeoSetting\Entities;

use App\Traits\Tenantable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Vimeo extends Model
{

    use Tenantable;
    protected $fillable = [];


    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            Cache::forget('vimeoSetting_'.SaasDomain());
        });
        self::updated(function ($model) {
            Cache::forget('vimeoSetting_'.SaasDomain());
        });
    }
}
