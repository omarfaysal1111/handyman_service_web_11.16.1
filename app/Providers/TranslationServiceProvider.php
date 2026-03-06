<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Throwable;
use Schema;
class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            Cache::rememberForever('translations', function () {
                return $this->buildTranslations();
            });
        } catch (Throwable $e) {
            // Fallback for environments where database cache store is enabled but cache table doesn't exist yet.
            $this->app->instance('translations.fallback', $this->buildTranslations());
        }
    }

    private function buildTranslations()
    {
        $translations = collect();
        $language_option = ["ar", "nl", "en", "fr", "de", "hi", "it"];

        try {
            if (Schema::hasTable('settings')) {
                if (\Session::get('setup_data') == '') {
                    $setup_data = sitesetupSession('get');
                    if ($setup_data) {
                        $language_option = $setup_data->language_option;
                    }
                }
            }
        } catch (Throwable $e) {
            // During install/first boot the DB might not be reachable yet.
        }

        foreach ($language_option as $locale) { // supported locales
            $translations[$locale] = [
                'php' => $this->phpTranslations($locale),
                'json' => $this->jsonTranslations($locale),
            ];
        }

        return $translations;
    }

    private function phpTranslations($locale)
    {
        $path = resource_path("lang/$locale");

        return collect(File::allFiles($path))->flatMap(function ($file) use ($locale) {
            $key = ($translation = $file->getBasename('.php'));

            return [$key => trans($translation, [], $locale)];
        });
    }

    private function jsonTranslations($locale)
    {
        $path = resource_path("lang/$locale.json");

        if (is_string($path) && is_readable($path)) {
            return json_decode(file_get_contents($path), true);
        }

        return [];
    }
}
