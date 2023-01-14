<?php

namespace Modules\RolePermission\Entities;

use App\Traits\Tenantable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RolePermission extends Pivot
{
    // use Tenantable;

    protected $fillable = [];
    protected $table = 'role_permission';
    
  

}
