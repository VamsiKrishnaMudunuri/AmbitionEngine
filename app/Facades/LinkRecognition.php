<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

use App\Services\LinkRecognition as ServicesLinkRecognition;

class LinkRecognition extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ServicesLinkRecognition::class;
    }
}
