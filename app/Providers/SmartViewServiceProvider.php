<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\SmartView;

class SmartViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //

        $this->app->singleton(SmartView::class, function(){
            return new SmartView();
        });

    }
}
