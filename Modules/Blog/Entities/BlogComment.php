<?php

namespace Modules\Blog\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;

class BlogComment extends Model
{
    protected $fillable = [];

    public function replies()
    {
        return $this->hasMany(BlogComment::class, 'comment_id')->orderByDesc('id');
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }
}
