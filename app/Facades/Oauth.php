<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

use App\Opts\Oauth as OptsOauth;

class Oauth extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return OptsOauth::class;
    }
}
