<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

use App\Portals\Cms as CmsCldr;

class Cms extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return CmsCldr::class;
    }
}
