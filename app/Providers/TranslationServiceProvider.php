<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        try {
            Cache::forget('translations');
            if (DB::connection()->getDatabaseName() != '') {
                if (Schema::hasTable('languages')) {
                    Cache::put('translations', $this->getTranslations());
                }
            }
        } catch (\Exception $exception) {

        }


    }

    private function getTranslations()
    {
        $translations = collect();

        try {
            $ln = DB::table('languages')->where('status', 1)->pluck('code')->toArray();
            foreach ($ln as $locale) {
                $translations[$locale] = [
                    'php' => $this->phpTranslations($locale),
                    'json' => $this->jsonTranslations($locale),
                ];
            }
        } catch (\Exception $exception) {

        }

        return $translations;
    }

    private function phpTranslations($locale)
    {
        try {
            $path = resource_path("lang/$locale");

            return collect(File::allFiles($path))->flatMap(function ($file) use ($locale) {
                $key = ($translation = $file->getBasename('.php'));
                return [$key => trans($translation, [], $locale)];

            });
        } catch (\Exception $exception) {
            $path = resource_path("lang/en");
            return collect(File::allFiles($path))->flatMap(function ($file) use ($locale) {
                $key = ($translation = $file->getBasename('.php'));
                return [$key => trans($translation, [], $locale)];
            });

        }
    }

    private function jsonTranslations($locale)
    {

        $files = glob(resource_path('lang/' . $locale . '/*.php'));

        $lang = [];

        foreach ($files as $file) {
            if (file_exists($file) && is_array(include($file))) {
                $lang = array_merge($lang, include($file));
            }
        }

        if (!json_encode($lang, true)) {
            return json_encode($lang, JSON_INVALID_UTF8_IGNORE);
        }
        return json_encode($lang, true);
    }
}
