<?php

namespace Modules\FrontendManage\Entities;

use App\Traits\Tenantable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class FrontPage extends Model
{
    use Tenantable;
    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            Cache::forget('FrontPageList_'.SaasDomain());
        });
        self::updated(function ($model) {
            Cache::forget('FrontPageList_'.SaasDomain());
        });
    }
}
