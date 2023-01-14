<?php

namespace Modules\FrontendManage\Entities;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Model;

class HomePageFaq extends Model
{
    use Tenantable;
    protected $fillable = [];
}
