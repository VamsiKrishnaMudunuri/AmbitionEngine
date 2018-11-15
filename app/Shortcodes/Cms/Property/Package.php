<?php

namespace App\Shortcodes\Cms\Property;

use View;
use Utility;
use Illuminate\Database\Eloquent\Collection;

use App\Models\Property;

class Package
{

    public static $slug = 'cms-office-package';

    public function register($shortcode, $content, $compiler, $name)
    {

        $id = Utility::htmlDecode($shortcode->id);

        $property = (new Property())->getFacilitySubscriptionPriceByGroupingFacilityCategory($id);

        if(is_null($property)){
            $property = new Property();
        }

        $desks = new Collection();

        $privateOffices = new Collection();

        if(!is_null($property->facilities)){

            $desks = $property->facilities->whereIn('category', [
                Utility::constant('facility_category.0.slug'),
                Utility::constant('facility_category.1.slug')
            ]);

            $privateOffices =
                $property->facilities->whereIn('category', [
                Utility::constant('facility_category.2.slug')
            ]);

        }


        return View::make('shortcodes.cms.property.package', compact($property->singular(), 'desks', 'privateOffices'));


    }

}