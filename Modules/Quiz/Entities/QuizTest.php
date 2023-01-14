<?php

namespace Modules\Quiz\Entities;

use App\User;
use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class QuizTest extends Model
{
use Tenantable;

    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(QuizTestDetails::class, 'quiz_test_id');
    }

    public function quiz()
    {
        return $this->belongsTo(OnlineQuiz::class, 'quiz_id')->withDefault();
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

}
