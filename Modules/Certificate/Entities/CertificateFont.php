<?php

namespace Modules\Certificate\Entities;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Model;

class CertificateFont extends Model
{
    use Tenantable;
    protected $fillable = [];
}
