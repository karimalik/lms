<?php

namespace App\Models;

use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Modules\LmsSaas\Entities\SaasCheckout;
use Modules\LmsSaas\Entities\SaasInstitutePlanManagement;
use Modules\Setting\Model\GeneralSetting;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LmsInstitute extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];


    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function settings()
    {
        $settings = DB::table('general_settings')->where('lms_id', $this->id)->pluck('value', 'key');
        $settings['country_name'] = DB::table('countries')->where('id', $settings['country_id'])->first()->name ?? '';
        return $settings;
    }

    public function plan_management()
    {
        return $this->belongsTo(SaasInstitutePlanManagement::class, 'id','lms_id')->withDefault();
    }

    public function saas_checkout()
    {
        return $this->hasMany(SaasCheckout::class, 'lms_id')->orderBy('id', 'desc');
    }
}
