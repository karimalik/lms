<?php

namespace Modules\CourseSetting\Entities;

use App\Traits\Tenantable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Modules\Quiz\Entities\OnlineQuiz;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use Tenantable;

    protected $fillable = ['name', 'status', 'show_home', 'position_order', 'image', 'thumbnail'];

    protected $appends = ['courseCount'];


    public function subcategories()
    {

        return $this->hasMany(Category::class, 'parent_id', 'id')->select('id', 'parent_id', 'name')->orderBy('position_order');
    }

    public function activeSubcategories()
    {

        return $this->hasMany(Category::class, 'parent_id', 'id')->select('id', 'parent_id', 'name')->where('status', 1)->orderBy('position_order');
    }

    public function courses()
    {

        return $this->hasMany(Course::class, 'category_id', 'id')->where('status', 1);
    }

    public function getcourseCountAttribute()
    {
        return $this->courses()->count();
    }

    public function totalCourses()
    {
        return $this->courses()->count();
    }

    public function getSlugAttribute()
    {
        return Str::slug($this->name) == "" ? str_replace(' ', '-', $this->name) : Str::slug($this->name);
    }


    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            Cache::forget('categories_'.SaasDomain());
        });
        self::updated(function ($model) {
            Cache::forget('categories_'.SaasDomain());
        });
        self::deleted(function ($model) {
            Cache::forget('categories_'.SaasDomain());
        });
    }


    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id')->with('parent')->withDefault();
    }

    public function childs()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('childs');
    }

    public function quizzesCategoryCount()
    {
        return $this->hasMany(OnlineQuiz::class, 'category_id', 'id');
    }

    public function quizzesSubCategoryCount()
    {

        return $this->hasMany(OnlineQuiz::class, 'sub_category_id', 'id');
    }


    public function getQuizzesCountAttribute()
    {
        if (!$this->relationLoaded('quizzesCategoryCount')) {
            $this->load('quizzesCategoryCount');
        }
        if (!$this->relationLoaded('quizzesSubCategoryCount')) {
            $this->load('quizzesSubCategoryCount');
        }
        return $this->quizzesCategoryCount->count() + $this->quizzesSubCategoryCount->count();
    }

    public function totalEnrolled()
    {
        return $this->hasManyThrough('Modules\CourseSetting\Entities\Course', 'Modules\CourseSetting\Entities\CourseEnrolled', 'course_id', 'id');

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


    public function getFullPathAttribute()
    {
        $codes = $this->getAllParent($this);
        $sort = array_reverse($codes, true);
        $sort[] = $this->id;
        return implode("/", $sort);
    }

    public function getAllParent($child, $pathCode = [])
    {
        if (!empty($child->parent->id)) {
            $pathCode[] = $child->parent->id;
            return $this->getAllParent($child->parent, $pathCode);
        }
        return $pathCode;
    }
}
