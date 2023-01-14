<?php

namespace Modules\Blog\Entities;

use App\User;
use App\Traits\Tenantable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{

    use Tenantable;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function parent()
    {
        return $this->belongsTo(BlogCategory::class, 'parent_id')->with('parent')->withDefault();
    }

    public function childs()
    {
        return $this->hasMany(BlogCategory::class, 'parent_id')->with('childs');
    }

    public function getAllChildIds($child, $pathCode = [])
    {
        if (isset($child->childs)) {
            if (count($child->childs) != 0) {
                foreach ($child->childs as $child) {
                    $pathCode[] = $child->id;
                    $pathCode = $this->getAllChildIds($child, $pathCode);
                }
                return $pathCode;
            }
        }
        return $pathCode;
    }
}
