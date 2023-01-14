<?php

namespace Modules\FooterSetting\Entities;

use App\Traits\Tenantable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Modules\FrontendManage\Entities\FrontPage;

class FooterWidget extends Model
{
    use Tenantable;

    protected $guarded = ['id'];

    public function frontpage()
    {
        return $this->belongsTo(FrontPage::class, 'page_id')->withDefault();
    }

    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            Cache::forget('sectionWidgets_' . SaasDomain());
        });
        self::updated(function ($model) {
            Cache::forget('sectionWidgets_' . SaasDomain());
        });
        self::deleted(function ($model) {
            Cache::forget('sectionWidgets_' . SaasDomain());
        });
    }
}
