<?php

namespace App\Models;

use Exception;
use Closure;
use Utility;
use Translator;
use Hash;
use Config;
use CLDR;
use Carbon\Carbon;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;


class LeadActivity extends Model
{

    protected $autoPublisher = true;

    protected $dates = [];

    public static $rules = array(
        'lead_id' => 'required|integer',
	    'status' => 'required|max:20',
	    'remark' => 'max:1000'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();
    
    public function __construct(array $attributes = array())
    {
        static::$relationsData = array(
            'lead' => array(self::BELONGS_TO, Lead::class)
        );
	
	    static::$customMessages = array();
        
        parent::__construct($attributes);

    }

    public function beforeValidate(){
    	
    	
        return true;

    }

    public function beforeSave(){
    	
    	
        return true;

    }

    public function setExtraRules(){

        return array();
    }
	
	public function showAll($lead, $order = [], $paging = true){
		
		try {
			
			$and = [];
			$or = [];
			
			$inputs = Utility::parseSearchQuery(function($key, $value, $callback){
				
				switch($key){
					
					default:
						$value = sprintf('%%%s%%', $value);
						break;
				}
				
				
				$callback($value, $key);
				
			});
			
			
			if(!Utility::hasArray($order)){
				$order[$this->getCreatedAtColumn()] = "DESC";
			}
			
			$instance = $this
				->where($this->lead()->getForeignKey(), '=', $lead->getKey())
				->show($and, $or, $order, $paging);
			
		}catch(InvalidArgumentException $e){
			
			throw $e;
			
		}catch(Exception $e){
			
			throw $e;
			
		}
		
		return $instance;
		
	}
    
    public function log($lead, $attributes){
    	
    	$remark = Arr::get($attributes, '_remark', '');
    	$remark = (Utility::hasString($remark)) ? $remark : '';
    	
    	$this->setAttribute($this->lead()->getForeignKey(), $lead->getKey());
    	$this->setAttribute('status', $lead->status);
    	$this->setAttribute('remark', $remark);
    	$this->save();
    	
    }

}