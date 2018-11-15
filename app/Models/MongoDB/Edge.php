<?php

namespace App\Models\MongoDB;

use Exception;
use Utility;
use Translator;
use Config;
use CLDR;
use Carbon\Carbon;
use Purifier;
use URL;
use Domain;
use Illuminate\Support\Arr;
use App\Libraries\Model\MongoDB;
use MongoDB\BSON\ObjectID;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\User;
use App\Models\Sandbox;

class Edge extends MongoDB
{

    public function number($vertex){

        $count = 0;

        if($vertex->exists){
            $singular = static::singular();
            $plural = static::plural();
            $attributes = $vertex->getAttributes();
            $stats = (isset($attributes['stats'])) ? $attributes['stats']  : array();
            if(Utility::hasArray($stats) && isset($stats[$plural])){
                $count =  $stats[$plural];
            }
        }

        return $count;
    }

    public function numberInText($vertex){

        $count = 0;

        if($vertex->exists){
            $singular = static::singular();
            $plural = static::plural();
            $attributes = $vertex->getAttributes();
            $stats = (isset($attributes['stats'])) ? $attributes['stats']  : array();
            if(Utility::hasArray($stats) && isset($stats[$plural])){
                $count =  $stats[$plural];
            }
        }

        return $count;
    }

    public function text($vertex){

        $arr = array(
            'simple' => '',
            'simple_row' => array('count' => 0, 'word' => trans_choice('plural.member', intval(0))),
            'short' => '',
            'long' => '',
        );

        if($vertex->exists){
            $singular = static::singular();
            $plural = static::plural();
            $attributes = $vertex->getAttributes();
            $stats = (isset($attributes['stats'])) ? $attributes['stats']  : array();
            if(Utility::hasArray($stats) && isset($stats[$plural])){
                $count = $this->number($vertex);
                $countText = $this->numberInText($vertex);
                $arr['simple'] = sprintf('%s %s', $count, trans_choice('plural.member', intval($count)));
                $arr['simple_row']['count'] = $count;
                $arr['simple_row']['word'] = trans_choice('plural.member', intval($count));
                $arr['short'] =  sprintf('%s %s', $count, trans_choice(sprintf('plural.%s', $singular), intval($count)));
                $arr['long'] = sprintf(Utility::constant(sprintf('%s_text.0.name', $singular)),  $count, trans_choice(sprintf('plural.%s', $singular), intval( $count)));

                if(isset($vertex->getRelations()[$plural])){
                    if(!$vertex->$plural->isEmpty()){
                        if( $count <= 1) {
                            $arr['long'] = Utility::constant(sprintf('%s_text.1.name', $singular));
                        }else{
                            $arr['long'] = sprintf(Utility::constant(sprintf('%s_text.2.name', $singular)),   $count - 1);
                        }
                    }
                }
            }


        }

        return $arr;

    }

}