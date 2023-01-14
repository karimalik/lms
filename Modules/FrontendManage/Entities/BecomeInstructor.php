<?php

namespace Modules\FrontendManage\Entities;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Model;

class BecomeInstructor extends Model
{
use Tenantable;

    protected $fillable = [];
}
