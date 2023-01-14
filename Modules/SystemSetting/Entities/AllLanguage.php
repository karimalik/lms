<?php

namespace Modules\SystemSetting\Entities;

use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class AllLanguage extends Model
{


    protected $fillable = [];
    protected $table = "all_languages";
}
