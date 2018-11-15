<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

use Hashids\Hashids as ServicesHashids;

class Hashids extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ServicesHashids::class;
    }
}
