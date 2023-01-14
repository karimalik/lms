<?php

namespace Modules\Blog\Entities;

use App\User;
use App\Traits\Tenantable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{

    use Tenantable;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function category()
    {
        return $this->belongsTo(BlogCategory::class)->withDefault();
    }


    public function comments()
    {
        return $this->hasMany(BlogComment::class)->where('type', 1)->orderByDesc('id');
    }

    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            saasPlanManagement('blog_post', 'create');
            Cache::forget('BlogPosList_' . SaasDomain());
        });
        self::updated(function ($model) {
            Cache::forget('BlogPosList_' . SaasDomain());
        });
        self::deleted(function ($model) {
            saasPlanManagement('blog_post', 'delete');
            Cache::forget('BlogPosList_' . SaasDomain());
        });
    }
}
