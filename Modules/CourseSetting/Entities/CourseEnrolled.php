<?php

namespace Modules\CourseSetting\Entities;

use App\User;
use Carbon\Carbon;
use App\BillingDetails;
use App\Traits\Tenantable;
use Illuminate\Support\Facades\Auth;
use Modules\Payment\Entities\Checkout;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Modules\SkillAndPathway\Entities\Pathway;

class CourseEnrolled extends Model
{

    use Tenantable;

    protected $fillable = ['user_id', 'course_id', 'purchase_price'];

    protected $appends = ['enrolledDate'];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id')->withDefault();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault();
    }

    public function getenrolledDateAttribute()
    {
        return Carbon::parse($this->created_at)->isoformat('Do MMMM Y H:ss a');
    }

    public function scopeEnrollStudent($query)
    {
        return $query->whereHas('course', function ($query) {
            $query->where('user_id', Auth::id());
        });
    }

    public function checkout()
    {
        return $this->belongsTo(Checkout::class, 'tracking', 'tracking')->withDefault();

    }

    public function bill()
    {
        return $this->belongsTo(BillingDetails::class, 'tracking', 'tracking_id')->withDefault();

    }

    public function enrolledBy()
    {
        return $this->belongsTo(User::class, 'enrolled_by', 'id')->withDefault();
    }

    public function pathway()
    {
        return $this->belongsTo(Pathway::class, 'pathway_id', 'id')->withDefault();
    }
}
