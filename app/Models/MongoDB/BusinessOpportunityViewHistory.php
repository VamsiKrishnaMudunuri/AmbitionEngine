<?php

namespace App\Models\MongoDB;

use DB;
use Exception;
use Utility;
use Translator;
use Config;
use CLDR;
use Carbon\Carbon;
use Illuminate\Support\Arr;

use Illuminate\Database\Eloquent\Collection;

use App\Libraries\Model\MongoDB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\User;
use App\Models\Company;

class BusinessOpportunityViewHistory extends MongoDB
{

    protected $autoPublisher = true;

    public static $rules = array(
        'business_opportunity_id' => 'required|max:32',
        'user_id' => 'required|integer',
        'member_id' => 'nullable|integer',
        'company_id' => 'nullable|integer'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public function __construct(array $attributes = array()){

        static::$relationsData = array(
            'businessOpportunity' => array(self::BELONGS_TO, BusinessOpportunity::class),
            'user' => array(self::BELONGS_TO, User::class),
            'member' => array(self::BELONGS_TO, User::class),
            'company' => array(self::BELONGS_TO, Company::class)
        );

        parent::__construct($attributes);

    }

    public function afterSave(){


        return true;

    }


    public function instance($user_id){

        $this->castToInteger($user_id);

        $instance = $this->where($this->user()->getForeignKey(), '=', $user_id)->first();

        if(is_null($instance)){
            $instance = new static();
        }

        return $instance;

    }

    public static function upsertMember($business_opportunity_id, $user_id, $member_ids){

        try {


            $instance = new static();
            $existing_member_ids = $instance
                ->where($instance->businessOpportunity()->getForeignKey(), '=', $instance->objectID($business_opportunity_id))
                ->where($instance->user()->getForeignKey(), '=', $user_id)
                ->whereIn($instance->member()->getForeignKey(), $member_ids)
                ->get()
                ->pluck($instance->member()->getForeignKey())
                ->toArray();


            $member_ids = array_diff($member_ids, $existing_member_ids);


            foreach($member_ids as $member_id){
                $attributes = [
                    $instance->businessOpportunity()->getForeignKey() => $instance->objectID($business_opportunity_id),
                    $instance->user()->getForeignKey() => $user_id,
                    $instance->member()->getForeignKey() => $member_id
                ];
                (new static())->fill($attributes)->save();
            }


        }catch(ModelNotFoundException $e){

            throw $e;

        }catch (ModelVersionException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        return $instance;

    }

    public static function upsertCompany($business_opportunity_id, $user_id, $company_ids){

        try {


            $instance = new static();
            $existing_company_ids = $instance
                ->where($instance->businessOpportunity()->getForeignKey(), '=', $instance->objectID($business_opportunity_id))
                ->where($instance->user()->getForeignKey(), '=', $user_id)
                ->whereIn($instance->company()->getForeignKey(), $company_ids)
                ->get()
                ->pluck($instance->company()->getForeignKey())
                ->toArray();


            $company_ids = array_diff($company_ids, $existing_company_ids);


            foreach($company_ids as $company_id){

                $attributes = [
                    $instance->businessOpportunity()->getForeignKey() => $instance->objectID($business_opportunity_id),
                    $instance->user()->getForeignKey() => $user_id,
                    $instance->company()->getForeignKey() => $company_id,
                ];

                (new static())->fill($attributes)->save();

            }


        }catch(ModelNotFoundException $e){

            throw $e;

        }catch (ModelVersionException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        return $instance;

    }



}