<?php

namespace Modules\CourseSetting\Entities;

use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Package extends Model
{


    protected $fillable = [];

    protected $casts = [
        'courses' => 'object',

    ];
}
