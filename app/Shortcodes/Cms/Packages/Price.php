<?php

namespace App\Shortcodes\Cms\Packages;

use View;
use Utility;
use App\Models\PackagePrice;

class Price
{

    public static $slug = 'cms-package-price';

    public function register($shortcode, $content, $compiler, $name)
    {


        $type = Utility::htmlDecode($shortcode->type);
        $country = Utility::htmlDecode($shortcode->country);
        $template = Utility::htmlDecode($shortcode->template);


        if(Utility::hasString($type) && Utility::hasString($country)){

            $package_price = (new PackagePrice())->getByTypeAndCountry($type, $country);

        }else{

            $package_price = (new PackagePrice())->getByType($type);
        }



        return View::make('shortcodes.cms.packages.price', compact($package_price->singular(), 'template'));


    }

}