<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Portals\Cms;

class CmsServiceProvider extends ServiceProvider
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

        $this->app->singleton(Cms::class, function(){
            return new Cms();
        });

    }
}
