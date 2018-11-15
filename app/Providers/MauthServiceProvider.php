<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Opts\Mauth;

class MauthServiceProvider extends ServiceProvider
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

        $this->app->singleton(Mauth::class, function($app){
            return new Mauth($app);
        });

    }
}
