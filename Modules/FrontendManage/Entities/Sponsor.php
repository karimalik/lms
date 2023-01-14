<?php

namespace Modules\FrontendManage\Entities;

use App\Traits\Tenantable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    use Tenantable;
    protected $fillable = [];

    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            Cache::forget('SponsorList_'.SaasDomain());
        });
        self::updated(function ($model) {
            Cache::forget('SponsorList_'.SaasDomain());
        });
    }
}
