<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

use App\Services\SmartView as ServicesSmartView;

class SmartView extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ServicesSmartView::class;
    }
}
