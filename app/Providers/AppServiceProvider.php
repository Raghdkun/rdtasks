<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

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
        //
        \Illuminate\Pagination\Paginator::useBootstrap();
        
        // Set PHP default timezone
        date_default_timezone_set('America/Indiana/Indianapolis');
        
        // Set Carbon default timezone using the correct method
        Carbon::setTestNow();
        Carbon::setLocale(config('app.locale'));
    }
}
