<?php

namespace App\Providers;

use Collective\Html\HtmlServiceProvider;
use Illuminate\Support\ServiceProvider;

use App\Services\HtmlBuilder;

class HtmlBuilderServiceProvider extends HtmlServiceProvider
{

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->registerHtmlBuilder();

        $this->registerFormBuilder();

        $this->app->alias('html', 'App\Services\HtmlBuilder');
        $this->app->alias('form', 'Collective\Html\FormBuilder');

    }

    protected function registerHtmlBuilder()
    {
        $this->app->singleton('html', function ($app) {
            return new HtmlBuilder($app['url'], $app['view']);
        });
    }

    public function provides()
    {
        return ['html', 'form', 'App\Services\HtmlBuilder', 'Collective\Html\FormBuilder'];
    }
}
