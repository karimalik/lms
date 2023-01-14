<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\LmsInstitute;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Modules\Setting\Model\GeneralSetting;

class SubdomainMiddleware
{
    public function handle($request, Closure $next)
    {

        if (config('app.short_url')==request()->getHost()){
            $domain =null;
        }else{
            $domain =str_replace('.'.config('app.short_url'),'',request()->getHost());
        }

        Session::put('domain', $domain);
        $host = $request->getHttpHost();
        $institute = LmsInstitute::findOrFail(1);
        if (isModuleActive('LmsSaas')) {
            if ($domain) {
                $institute = LmsInstitute::where(['domain' => $domain, 'status' => 1])->firstOrFail();
                $request->route()->forgetParameter('subdomain');
            } else if ($host != config('app.short_url') and config('app.allow_custom_domain')) {
                $institute = LmsInstitute::where(['custom_domain' => $host, 'status' => 1])->firstOrFail();
                Session::put('domain', $institute->domain);
            }
                session()->put('institute_id', $institute->id);
        } else{
            if ($host!=config('app.short_url')) {
                session()->put('institute_id', 1);
                return Redirect::to(config('app.url'));
            }
        }

        app()->forgetInstance('institute');
        app()->instance('institute', $institute);

        return $next($request);
    }
}
