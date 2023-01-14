<?php

namespace Modules\Certificate\Entities;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use Tenantable;
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
