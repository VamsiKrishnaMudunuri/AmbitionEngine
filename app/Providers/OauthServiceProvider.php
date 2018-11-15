<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Opts\Oauth;

class OauthServiceProvider extends ServiceProvider
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

        $this->app->singleton(Oauth::class, function($app){
            return new Oauth($app);
        });

    }
}
