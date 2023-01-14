<?php

namespace Modules\PaymentMethodSetting\Entities;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class PaymentMethod extends Model
{
    use Tenantable;

    protected $fillable = [];
}
