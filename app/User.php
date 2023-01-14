<?php

namespace App;


use App\Models\LmsInstitute;
use App\Notifications\PasswordResetNotification;
use App\Notifications\VerifyEmail;
use App\Traits\UserChatMethods;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Laravel\Passport\HasApiTokens;
use Modules\Affiliate\Entities\AffiliateReferralPayment;
use Modules\Affiliate\Entities\AffiliateUserWallet;
use Modules\Affiliate\Entities\AffiliateWithdraw;
use Modules\Affiliate\Entities\ReferralUser;
use Modules\Certificate\Entities\CertificateRecord;
use Modules\CourseSetting\Entities\Course;
use Modules\CourseSetting\Entities\CourseEnrolled;
use Modules\CourseSetting\Entities\CourseReveiw;
use Modules\Forum\Entities\Forum;
use Modules\Forum\Entities\ForumReply;
use Modules\HumanResource\Entities\ApplyLeave;
use Modules\HumanResource\Entities\Attendance;
use Modules\HumanResource\Entities\LeaveDefine;
use Modules\Localization\Entities\Language;
use Modules\OfflinePayment\Entities\OfflinePayment;
use Modules\Org\Entities\OrgBranch;
use Modules\Org\Entities\OrgPosition;
use Modules\OrgInstructorPolicy\Entities\OrgPolicy;
use Modules\Payment\Entities\InstructorPayout;
use Modules\Payment\Entities\Subscriber;
use Modules\Payment\Entities\Withdraw;
use Modules\Quiz\Entities\StudentTakeOnlineQuiz;
use Modules\RolePermission\Entities\Role;
use Modules\Survey\Entities\SurveyAssign;
use Modules\SystemSetting\Entities\Currency;
use Modules\SystemSetting\Entities\Message;
use Modules\SystemSetting\Entities\Staff;

//class User extends Authenticatable
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable, UserChatMethods;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */


    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['first_name', 'last_name', 'blocked_by_me'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class)->withDefault();
    }

    public function offlinePayments()
    {
        return $this->hasMany(OfflinePayment::class, 'user_id');
    }


    public function courses()
    {
        return $this->hasMany(Course::class, 'user_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscriber::class, 'user_id', 'id')->whereDate('valid', '>=', Carbon::now());
    }


    public function enrolls()
    {
        return $this->hasManyThrough(CourseEnrolled::class, Course::class);
    }

    public function withdraws()
    {
        return $this->hasMany(Withdraw::class, 'instructor_id');
    }


    public function earnings()
    {
        return $this->hasMany(InstructorPayout::class, 'instructor_id');
    }

    public function forumReply()
    {
        return $this->hasMany(ForumReply::class, 'user_id');
    }

    public function forums()
    {
        return $this->hasMany(Forum::class, 'created_by');
    }

    public function gettotalEarnAttribute()
    {

        return round($this->earnings()->sum('reveune'), 2);
    }

    public function lastMessage()
    {
        $message = Message::where('sender_id', $this->id)->orWhere('reciever_id', $this->id)->orderBy('id', 'desc')->first();
        if ($message) {
            return $message;
        } else {
            return null;
        }
    }

    public function reciever()
    {
        return $this->hasOne(Message::class, 'reciever_id', 'id')->latest();
    }


    public function sender()
    {
        return $this->hasOne(Message::class, 'sender_id')->latest();
    }

    public function getmessageFormatAttribute()
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    public function enrollCourse()
    {
        return $this->belongsToMany(Course::class, 'course_enrolleds', 'user_id', 'course_id');
    }

    public function enCoursesInstance()
    {
        return $this->hasMany(CourseEnrolled::class)->groupBy(['course_id']);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }


    public function recievers()
    {
        return $this->hasMany(Message::class, 'reciever_id')->latest();
    }

    public function senders()
    {
        return $this->hasMany(Message::class, 'sender_id')->latest();
    }

    public function userLanguage()
    {
        return $this->belongsTo(Language::class, 'language_id')->withDefault();
    }

    public function enrollStudents()
    {
        return $this->hasMany(CourseEnrolled::class)->EnrollStudent();
    }

    public function apiKey()
    {
        return $this->zoom_api_key_of_user;
    }

    public function apiSecret()
    {
        return $this->zoom_api_serect_of_user;
    }

    public function submittedExam()
    {
        return $this->hasOne(StudentTakeOnlineQuiz::class, 'student_id')->latest();
    }

    public function userCountry()
    {
        return $this->belongsTo(Country::class, 'country')->withDefault();
    }

    public function totalCourses()
    {
        $totalCourses = Course::where('user_id', '=', $this->id)->count();
        return $totalCourses;
    }

    public function totalEnrolled()
    {
        $totalEnrolled = Course::where('user_id', '=', $this->id)->sum('total_enrolled');
        return $totalEnrolled;
    }


    public function totalRating()
    {

        $totalRatings['rating'] = 0;
        $ReviewList = DB::table('courses')
            ->join('course_reveiws', 'course_reveiws.course_id', 'courses.id')
            ->select('courses.id', 'course_reveiws.id as review_id', 'course_reveiws.star as review_star')
            ->where('courses.user_id', $this->id)
            ->get();
        $totalRatings['total'] = count($ReviewList);

        foreach ($ReviewList as $Review) {
            $totalRatings['rating'] += $Review->review_star;
        }

        if ($totalRatings['total'] != 0) {
            $avg = ($totalRatings['rating'] / $totalRatings['total']);
        } else {
            $avg = 0;
        }

        if ($avg != 0) {
            if ($avg - floor($avg) > 0) {
                $rate = number_format($avg, 1);
            } else {
                $rate = number_format($avg, 0);
            }
            $totalRatings['rating'] = $rate;
        }
        return $totalRatings;
    }

    public function sendEmailVerificationNotification()
    {
        if (!Session::has('reg_email')) {
            try {
                Session::put('reg_email', $this->email);
                $this->notify(new VerifyEmail());

            } catch (\Exception $e) {
                Log::error($e);
            }
        }
    }

    public function sendPasswordResetNotification($token)
    {
        try {
            $this->notify(new PasswordResetNotification($token));
        } catch (\Exception $e) {
            Log::error($e);
        }
    }

    public function stateDetails()
    {
        return $this->belongsTo(State::class, 'state')->withDefault();
    }

    public function cityDetails()
    {
        return $this->belongsTo(City::class, 'city')->withDefault();
    }

    public function stateName()
    {
        return $this->stateDetails->name;
    }

    public function cityName()
    {
        return $this->cityDetails->name;
    }

    public function totalSellCourse()
    {
        return $this->hasManyThrough(CourseEnrolled::class, Course::class, 'user_id', 'course_id', 'id');
    }

    public function totalReview()
    {
        return $this->hasManyThrough(CourseReveiw::class, Course::class, 'user_id', 'course_id', 'id');
    }

    public function routeNotificationForFcm($notification)
    {
        return $this->device_token;
    }

    public function position()
    {
        return $this->belongsTo(OrgPosition::class, 'org_position_code', 'code')->withDefault();
    }

    public function branch()
    {
        return $this->belongsTo(OrgBranch::class, 'org_chart_code', 'code')->withDefault();
    }

    public function policy()
    {
        return $this->belongsTo(OrgPolicy::class, 'policy_id')->withDefault();
    }

    public function totalCertificate()
    {
        return $this->hasMany(CertificateRecord::class, 'student_id')->count();
    }


    public function totalStudentCourses()
    {
        $enrolls = $this->hasMany(CourseEnrolled::class, 'user_id')->with('course')->get();

        $result['complete'] = 0;
        $result['process'] = 0;
        foreach ($enrolls as $enroll) {
            if ($enroll->course->loginUserTotalPercentage >= 100) {
                $result['complete']++;
            } else {
                $result['process']++;
            }

        }
        return $result;
    }

    public function category()
    {
        $courses = $this->courses;

        $category_ids = [];
        $category = [];
        foreach ($courses as $key => $course) {
            if (!array_search($course->category_id, $category_ids)) {
                $category_ids[] = $course->category_id;
                $category[] = $course->category->name;
            }
        }
        if (count($category) != 0) {
            $result = $category[0];
        } else {
            $result = 'N/A';
        }
        return $result;
    }

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function leaves()
    {
        return $this->hasMany(ApplyLeave::class)->CarryForward();
    }

    public function leaveDefines()
    {
        return $this->hasMany(LeaveDefine::class, 'role_id', 'role_id');

    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendSurvey($survey)
    {
        $assign = SurveyAssign::where('survey_id', $survey->id)->where('user_id', $this->id)->first();
        if ($assign && $assign->is_attended == 1) {
            return true;
        }
        return false;
    }

    public function institute()
    {
        return $this->belongsTo(LmsInstitute::class, 'lms_id', 'id');
    }


    protected static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            if ($model->role_id == 2) {
                saasPlanManagement('instructor', 'create');
            }
            if ($model->role_id == 3) {
                saasPlanManagement('student', 'create');
            }
        });
        self::deleted(function ($model) {
            if ($model->role_id == 2) {
                saasPlanManagement('instructor', 'delete');
            }
            if ($model->role_id == 3) {
                saasPlanManagement('student', 'delete');
            }

        });

    }


//    public function dropboxTokens(): \Illuminate\Database\Eloquent\Relations\HasMany
//    {
//        return $this->hasMany(DropBoxToken::class);
//    }
//
//    public function dropboxToken(): \Illuminate\Database\Eloquent\Relations\HasOne
//    {
//        return $this->hasOne(DropBoxToken::class)->latest();
//    }

    public function googleTokens(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(GoogleToken::class);
    }

    public function googleToken(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(GoogleToken::class)->orderBy('id', 'desc');
    }

    public function isReferralUser()
    {
        return $this->hasOne(ReferralUser::class, 'user_id');
    }

    public function affiliateWallet()
    {
        return $this->hasOne(AffiliateUserWallet::class, 'user_id');
    }

    public function affiliateTransaction()
    {
        return $this->hasMany(AffiliateWithdraw::class, 'user_id');
    }

    public function affiliateCommissions()
    {
        return $this->hasMany(AffiliateReferralPayment::class, 'payment_to');
    }

}
