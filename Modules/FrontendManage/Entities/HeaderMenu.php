<?php

namespace Modules\FrontendManage\Entities;

use App\Traits\Tenantable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class HeaderMenu extends Model
{
    use Tenantable;
    protected $guarded = ['id'];


    public function childs(){
        return $this->hasMany(HeaderMenu::class,'parent_id','id')->orderBy('position');
    }


    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            Cache::forget('menus_'.SaasDomain());
        });
        self::updated(function ($model) {
            Cache::forget('menus_'.SaasDomain());
        });
        self::deleted(function ($model) {
            Cache::forget('menus_'.SaasDomain());
        });
        static::addGlobalScope(new \App\Scopes\LmsScope);
    }
}
