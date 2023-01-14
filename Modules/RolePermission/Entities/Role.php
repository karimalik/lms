<?php

namespace Modules\RolePermission\Entities;

use App\Traits\Tenantable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Modules\RolePermission\Entities\SaasPermission;

class Role extends Model
{

    protected $guarded = [''];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class,'role_permission','role_id','permission_id')
        ->where('role_permission.lms_id',Auth::user()->lms_id);
    }


    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            Cache::forget('PermissionList_'.SaasDomain());
            Cache::forget('RoleList_'.SaasDomain());
            Cache::forget('PolicyPermissionList_'.SaasDomain());
            Cache::forget('PolicyRoleList_'.SaasDomain());
        });
        self::updated(function ($model) {
            Cache::forget('PermissionList_'.SaasDomain());
            Cache::forget('RoleList_'.SaasDomain());
            Cache::forget('PolicyPermissionList_'.SaasDomain());
            Cache::forget('PolicyRoleList_'.SaasDomain());
        });
    }
}
