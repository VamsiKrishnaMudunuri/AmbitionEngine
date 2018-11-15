<?php

namespace App\Providers;

use Illuminate\Routing\RoutingServiceProvider;
use Illuminate\Support\ServiceProvider;

use App\Services\Url;

class UrlServiceProvider extends RoutingServiceProvider
{

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //

        $this->registerUrlGenerator();

    }

    /**
     * Create custom Url class by overridden RoutingServiceProvider::registerUrlGenerator.
     *
     * @return void
     * @see RoutingServiceProvider::registerUrlGenerator
     */
    protected function registerUrlGenerator()
    {
        $this->app['url'] = $this->app->share(function ($app) {
            $routes = $app['router']->getRoutes();

            // The URL generator needs the route collection that exists on the router.
            // Keep in mind this is an object, so we're passing by references here
            // and all the registered routes will be available to the generator.
            $app->instance('routes', $routes);

            $url = new Url(
                $routes, $app->rebinding(
                'request', $this->requestRebinder()
            )
            );

            $url->setSessionResolver(function () {
                return $this->app['session'];
            });

            // If the route collection is "rebound", for example, when the routes stay
            // cached for the application, we will need to rebind the routes on the
            // URL generator instance so it has the latest version of the routes.
            $app->rebinding('routes', function ($app, $routes) {
                $app['url']->setRoutes($routes);
            });

            return $url;
        });
    }
}
