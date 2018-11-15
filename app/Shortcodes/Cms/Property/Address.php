<?php

namespace App\Shortcodes\Cms\Property;

use View;
use Utility;

use App\Models\Property;

class Address
{

    public static $slug = 'cms-office-address';

    public function register($shortcode, $content, $compiler, $name)
    {


        $id = Utility::htmlDecode($shortcode->id);

        $property = (new Property())->find($id);

        if(is_null($property)){
            $property = new Property();
        }

        return View::make('shortcodes.cms.property.address', compact($property->singular()));


    }

}