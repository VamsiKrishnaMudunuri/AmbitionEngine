<?php

namespace App\Services;

use Config;
use Illuminate\Support\Facades\Storage;
use Utility as Util;
use Illuminate\Support\Str;
use Illuminate\Routing\UrlGenerator;

class Url extends UrlGenerator{

    public function skin($path, $secure = null){

        $root = config('app.cdn');
        $minExtensions = array('js', 'css');
        $pathExtension = Str::lower(pathinfo($path, PATHINFO_EXTENSION));

        if (strcasecmp($pathExtension, 'css') == 0) {

            $path = elixir('css' . '/' . $path);

        } else if (strcasecmp($pathExtension, 'js') == 0) {

            $path = elixir('js' . '/' . $path);

        } else {

            $path = '/images' . '/' . $path;

        }

        if(Str::startsWith($root, 'https://')){
            $secure = true;
        }

        return $this->assetFrom($root, $path, $secure);

    }

    public function skinForVendor($path, $secure = null){

        $root = config('app.cdn');

        $minExtensions = array('js', 'css');
        $pathExtension = Str::lower(pathinfo($path, PATHINFO_EXTENSION));

        if (strcasecmp($pathExtension, 'css') == 0) {

            $path = elixir('vendor/css' . '/' . $path);

        } else if (strcasecmp($pathExtension, 'js') == 0) {

            $path = elixir('vendor/js' . '/' . $path);

        } else {

            $path = 'vendor/images' . '/' . $path;

        }

        if(Str::startsWith($root, 'https://')){
            $secure = true;
        }

        return $this->assetFrom($root, $path, $secure);

    }

    public function setLandingIntended($controller){

        $controller = (is_string($controller)) ? $controller : get_class($controller);
        $current = $this->getRequest()->fullUrlWithQuery(Util::parseQueryParams());
        $this->getSession()->put('landing' . $controller . '.url', $current);

    }

    public function getLandingIntended($controller, $default = null){

        $controller = (is_string($controller)) ? $controller : get_class($controller);
        $url = $this->getSession()->get('landing' . $controller . '.url', $default);

        return $url;

    }

    public function pullLandingIntended($controller, $default = null){

        $controller = (is_string($controller)) ? $controller : get_class($controller);
        $url = $this->getSession()->pull('landing' . $controller . '.url', $default);

        return $url;

    }

    public function getLandingIntendedUrl($given_url, $default = null){
        $url = '';

        if(!is_null($given_url)){
            $url = $given_url;
        }else if(!is_null($default)){
            $url = $default;
        }

        return $url;

    }

    public function setAdvancedLandingIntended($route_name, $id = null){

        $arr = array($route_name);

        if(!is_null($id)){

            if(is_array($id)){
                $arr = $arr + $id;
            }else {
                $arr[] = $id;
            }

        }

        $current = $this->getRequest()->fullUrlWithQuery(Util::parseQueryParams());

        $this->getSession()->put('advanced_landing' . implode('.', $arr) . '.url', $current);

    }

    public function getAdvancedLandingIntended($route_name, $id = null, $default = null){

        $arr = array($route_name);

        if(!is_null($id)){
            if(is_array($id)){
                $arr = $arr + $id;
            }else {
                $arr[] = $id;
            }
        }

        $url = $this->getSession()->get('advanced_landing' . implode('.', $arr) . '.url', $default);

        return $url;

    }

    public function pullAdvancedLandingIntended($route_name, $id = null, $default = null){

        $arr = array($route_name);

        if(!is_null($id)){

            if(is_array($id)){
                $arr = $arr + $id;
            }else {
                $arr[] = $id;
            }

        }

        $url = $this->getSession()->pull('advanced_landing' . implode('.', $arr) . '.url', $default);

        return $url;

    }

    public function getAdvancedLandingIntendedUrl($given_url, $default = null){

        $url = '';

        if(!is_null($given_url)){
            $url = $given_url;
        }else if(!is_null($default)){
            $url = $default;
        }

        return $url;

    }

}