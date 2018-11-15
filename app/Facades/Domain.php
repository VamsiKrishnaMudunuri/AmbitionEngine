<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

use App\Opts\Domain as OptsDomain;

class Domain extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return OptsDomain::class;
    }
}
