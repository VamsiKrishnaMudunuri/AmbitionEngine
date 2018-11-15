<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

use App\Services\Sess as ServicesSess;

class Sess extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ServicesSess::class;
    }
}
