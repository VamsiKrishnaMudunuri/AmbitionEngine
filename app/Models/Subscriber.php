<?php

namespace App\Models;


use Translator;
use Exception;
use Utility;
use Hash;
use Config;
use CLDR;
use Carbon\Carbon;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

class Subscriber extends Model
{

    protected $autoPublisher = true;

    public static $rules = array(
        'mailchimp_id' => 'required|integer',
        'user_id' => 'nullable|integer',
        'is_subscribe_from_mailchimp' => 'required|boolean',
        'email' => 'required|max:100|email',
        'language' => 'required|max:5',
        'full_name' => 'nullable|max:100',
        'first_name' => 'nullable|max:50',
        'last_name' => 'nullable|max:50',
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public function __construct(array $attributes = array())
    {

        static::$relationsData = array(
            'mailchimp' => array(self::BELONGS_TO, Mailchimp::class),
        );

        static::$customMessages = array(
            'email.required' => Translator::transSmart('app.Email is required.', 'Email is required.'),
            'language.required' => Translator::transSmart('app.Language is required.')
        );

        parent::__construct($attributes);

    }

    public function beforeValidate(){

        return true;

    }

    public static function del($id){

        try {

            $instance = (new static())->with([])->findOrFail($id);

            $instance->getConnection()->transaction(function () use ($instance){

                $instance->discard();

            });

        } catch(ModelNotFoundException $e){

            throw $e;

        } catch (ModelVersionException $e){

            throw $e;

        } catch(IntegrityException $e) {

            throw $e;

        } catch (Exception $e){

            throw $e;

        }

    }
    
}