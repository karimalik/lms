<?php

namespace Modules\Quiz\Entities;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Model;

class QuizTestDetails extends Model
{
    use Tenantable;
    protected $guarded = ['id'];
}
