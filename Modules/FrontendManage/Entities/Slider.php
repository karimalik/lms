<?php

namespace Modules\FrontendManage\Entities;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use Tenantable;
    protected $fillable = [];
}
