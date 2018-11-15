<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Hashids\Hashids;

class HashidsServiceProvider extends ServiceProvider
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

        $this->app->singleton(Hashids::class, function($app){
            return new Hashids(config('app.hashids.salt'), config('app.hashids.length'));
        });

    }
}
