<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Shortcode;

use App\Shortcodes\Cms\Packages\Price;
use App\Shortcodes\Cms\Property\Map;
use App\Shortcodes\Cms\Property\Package;
use App\Shortcodes\Cms\Property\CommunityManager;
use App\Shortcodes\Cms\Property\Address;
use App\Shortcodes\Cms\Property\Phone;


class ShortcodesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        Shortcode::register(Price::$slug, Price::class);
        Shortcode::register(Map::$slug, Map::class);
        Shortcode::register(Package::$slug, Package::class);
        Shortcode::register(CommunityManager::$slug, CommunityManager::class);
        Shortcode::register(Address::$slug, Address::class);
        Shortcode::register(Phone::$slug, Phone::class);

    }

}