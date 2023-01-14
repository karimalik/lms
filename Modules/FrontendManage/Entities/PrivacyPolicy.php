<?php

namespace Modules\FrontendManage\Entities;

use App\Traits\Tenantable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class PrivacyPolicy extends Model
{
    use Tenantable;
    protected $fillable = [];

    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            Cache::forget('PrivacyPolicy_'.SaasDomain());
        });
        self::updated(function ($model) {
            Cache::forget('PrivacyPolicy_'.SaasDomain());
        });
        self::deleted(function ($model) {
            Cache::forget('PrivacyPolicy_'.SaasDomain());
        });
    }
    public static function getData()
    {
        return Cache::rememberForever('PrivacyPolicy_'.SaasDomain(), function () {
            return PrivacyPolicy::first();
        });
    }
}
