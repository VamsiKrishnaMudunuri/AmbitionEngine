<?php

namespace App\Models\MongoDB;

use Exception;
use Utility;
use Translator;
use Config;
use CLDR;
use Carbon\Carbon;
use Illuminate\Support\Arr;

use App\Libraries\Model\MongoDB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Company;

class CompanyActivityStat extends MongoDB
{

    protected $autoPublisher = true;

    public static $rules = array(
        'company_id' => 'required|integer|unique:company_activity_stats',
        'followings' => 'integer',
        'followers' => 'integer',
        'works' => 'integer',
    );

    public static $customMessages = array();

    public function __construct(array $attributes = array()){

        static::$relationsData = array(
            'company' => array(self::BELONGS_TO, Company::class)
        );

        parent::__construct($attributes);

    }

    public function getFollowingsAttribute($value){
        return $value > 0 ? $value : 0;
    }

    public function getFollowingsShortTextAttribute($value){

        $figure = $this->followings;

        return $figure > 1 ?  Translator::transSmart('app.Followings', 'Followings') : Translator::transSmart('app.Following', 'Following');

    }

    public function getFollowingsFullTextAttribute($value){

        $figure = $this->followings;

        return $figure > 1 ?  Translator::transSmart('app.%s Followings', sprintf('%s Followings', $figure, false, ['figure' => $figure ])) : Translator::transSmart('app.%s Following', sprintf('%s Following', $figure, false, ['figure' => $figure ]));

    }

    public function getFollowersAttribute($value){
        return $value > 0 ? $value : 0;
    }

    public function getFollowersShortTextAttribute($value){

        $figure = $this->followers;

        return $figure > 1 ?  Translator::transSmart('app.Followers', 'Followers') : Translator::transSmart('app.Follower', 'Follower');

    }

    public function getFollowersFullTextAttribute($value){

        $figure = $this->followers;

        return $figure > 1 ? Translator::transSmart('app.%s Followers', sprintf('%s Followers', $figure, false, ['figure' => $figure ])) : Translator::transSmart('app.%s Follower', sprintf('%s Follower', $figure, false, ['figure' => $figure ]));

    }

    public function getWorksAttribute($value){
        return $value > 0 ? $value : 0;
    }


    public function getWorksShortTextAttribute($value){

        $figure = $this->works;

        return $figure > 1 ?  Translator::transSmart('app.Employees', 'Employees') : Translator::transSmart('app.Employee', 'Employee');

    }

    public function getWorksFullTextAttribute($value){

        $figure = $this->works;

        return $figure > 1 ? Translator::transSmart('app.%s Employees', sprintf('%s Employees', $figure, false, ['figure' => $figure ])) : Translator::transSmart('app.%s Employees', sprintf('%s Employees', $figure, false, ['figure' => $figure ]));

    }

    public function instance($company_id){


        $this->castToInteger($company_id);

        $instance = $this->where($this->company()->getForeignKey(), '=', $company_id)->first();

        if(is_null($instance)){
            $instance = new static();
            $instance->setAttribute($instance->company()->getForeignKey(), $company_id);
        }

        return $instance;

    }

    public static function getStatsByCompanyID($company_id, $isNeedJson = false){

        $instance = (new static())->instance($company_id);

        if($isNeedJson) {
            $attributes = [
                'followings', 'followings_short_text', 'followings_full_text',
                'followers', 'followers_short_text', 'followers_full_text'
            ];

            foreach ($attributes as $attribute) {
                $instance->setAttribute($attribute, $instance->getAttribute($attribute));
            }
        }

        return $instance;

    }

    public static function incrementFollowing($company_id){

        $instance = (new static())->instance($company_id);
        $instance->followings += 1;
        $instance->save();

    }

    public static function incrementFollower($company_id){

        $instance = (new static())->instance($company_id);
        $instance->followers += 1;
        $instance->save();

    }

    public static function incrementWork($company_id){

        $instance = (new static())->instance($company_id);
        $instance->works += 1;
        $instance->save();

    }

    public static function decrementFollowing($company_id){

        $instance = (new static())->instance($company_id);
        $instance->followings -= 1;
        $instance->save();

    }

    public static function decrementFollower($company_id){

        $instance = (new static())->instance($company_id);
        $instance->followers -= 1;
        $instance->save();

    }

    public static function decrementWork($company_id){

        $instance = (new static())->instance($company_id);
        $instance->works -= 1;
        $instance->save();

    }

}