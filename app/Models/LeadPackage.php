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


class LeadPackage extends Model
{

    protected $autoPublisher = true;

    protected $dates = [];

    public static $rules = array(
        'lead_id' => 'required|integer',
	    'category' => 'required|integer',
	    'quantity' => 'required|integer'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();
    
    public function __construct(array $attributes = array())
    {
        static::$relationsData = array(
            'lead' => array(self::BELONGS_TO, Lead::class)
        );
	
	    static::$customMessages = array(
		    'category.required' => Translator::transSmart('app.Package is required.', 'Package is required.'),
		    'category.integer' => Translator::transSmart('app.Package Must be an integer.', 'Package Must be an integer.'),
		    'quantity.required' => Translator::transSmart('app.Seat is required.', 'Seat is required.'),
		    'quantity.integer' => Translator::transSmart('app.Seat Must be an integer.', 'Seat Must be an integer.'),
	    );
        
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
    
    public function getRulesForSaveMany(){
    	
    	return array(
		    sprintf('%s.*.category', $this->getTable()) => 'required|integer',
		    sprintf('%s.*.quantity', $this->getTable())  => 'required|integer'
		 
	    );
    }
	
	
	public function getRuleMessageForSaveMany(){
		
		return array(
			sprintf('%s.*.category.required', $this->getTable()) => Translator::transSmart('app.Membership type is required.', 'Membership type is required.'),
			sprintf('%s.*.category.integer', $this->getTable()) => Translator::transSmart('app.Membership type must be integer.', 'Membership type must be integer.'),
			sprintf('%s.*.quantity.required', $this->getTable()) => Translator::transSmart('app.Number of seat(s) is required.', 'Number of seat(s) is required.'),
			sprintf('%s.*.quantity.integer', $this->getTable()) => Translator::transSmart('app.Number of seat(s) must be integer.', 'Number of seat(s) must be integer.'),
		
		);
		
	}
}