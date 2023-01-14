<?php

namespace Modules\RolePermission\Entities;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Permission extends Model
{

    protected $fillable = [];

    public function roles()
    {
        return $this->belongsToMany(Role::class,'role_permission','permission_id','role_id');
    }



    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            Cache::forget('PermissionList_'.SaasDomain());
            Cache::forget('RoleList_'.SaasDomain());
        });
        self::updated(function ($model) {
            Cache::forget('PermissionList_'.SaasDomain());
            Cache::forget('RoleList_'.SaasDomain());
            Cache::forget('PolicyPermissionList_'.SaasDomain());
            Cache::forget('PolicyRoleList_'.SaasDomain());
        });
    }

}
