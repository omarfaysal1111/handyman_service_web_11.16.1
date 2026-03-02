<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Throwable;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // Fail-safe for environments configured with database cache
        // but missing the `cache` table (common on fresh/shared hosting deploys).
        try {
            if (config('cache.default') === 'database' && !Schema::hasTable('cache')) {
                config(['cache.default' => 'file']);
            }
        } catch (Throwable $e) {
            config(['cache.default' => 'file']);
        }
    }
}
