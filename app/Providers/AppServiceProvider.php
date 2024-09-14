<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Models\SiteConfig;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        
    }
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::component('layouts.blank', 'blank-layout');

        view()->composer('*', function ($view) {
            $siteConfig = SiteConfig::first();
            $view->with('siteConfig', $siteConfig);
        });
    }
}
