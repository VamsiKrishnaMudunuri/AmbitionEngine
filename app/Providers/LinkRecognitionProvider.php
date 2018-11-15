<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\LinkRecognition;

class LinkRecognitionProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //

        $this->app->singleton(LinkRecognition::class, function(){
            return new LinkRecognition();
        });

    }
}
