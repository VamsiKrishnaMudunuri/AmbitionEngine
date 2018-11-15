<?php

namespace App\Shortcodes\Cms\Property;

use View;
use Utility;

use App\Models\Property;

class CommunityManager
{

    public static $slug = 'cms-community-manager';

    public function register($shortcode, $content, $compiler, $name)
    {


        $url = Utility::htmlDecode($shortcode->url);

        return View::make('shortcodes.cms.property.community_manager', compact('url'));


    }

}