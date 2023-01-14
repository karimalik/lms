<?php

namespace Modules\FrontendManage\Entities;

use App\Traits\Tenantable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class LoginPage extends Model
{
    use Tenantable;

    protected $fillable = [];

    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            Cache::forget('login_page_'.SaasDomain());
        });
        self::updated(function ($model) {
            Cache::forget('login_page_'.SaasDomain());
        });
        self::deleted(function ($model) {
            Cache::forget('login_page_'.SaasDomain());
        });
    }

    public static function getData()
    {
        return Cache::rememberForever('login_page_' . SaasDomain(), function () {
            return LoginPage::firstOrCreate();
        });
    }
}
