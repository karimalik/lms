<?php

namespace Modules\CourseSetting\Entities;

use App\Traits\Tenantable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class CourseLevel extends Model
{
    use Tenantable;
    protected $fillable = [];


    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            Cache::forget('CourseLevel_'.SaasDomain());
        });
        self::updated(function ($model) {
            Cache::forget('CourseLevel_'.SaasDomain());
        });
        self::deleted(function ($model) {
            Cache::forget('CourseLevel_'.SaasDomain());
        });
    }

    public static function getAllActiveData()
    {
        return Cache::rememberForever('CourseLevel_'.SaasDomain(), function () {
            return CourseLevel::select('id', 'title')->where('status', 1)->get();
        });
    }
}
