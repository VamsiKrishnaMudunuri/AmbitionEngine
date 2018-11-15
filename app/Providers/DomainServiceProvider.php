<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Opts\Domain;

class DomainServiceProvider extends ServiceProvider
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

        $this->app->singleton(Domain::class, function(){
            return new Domain();
        });

    }
}
