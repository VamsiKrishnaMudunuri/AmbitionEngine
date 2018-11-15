<?php

namespace App\Models;

use Exception;
use Utility;
use Translator;
use Hash;
use Config;
use CLDR;
use URL;
use Auth;
use Session;
use Html;
use Domain;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;

use App\Mail\Admin\SignupInvitation as SignupInvitationMail;

use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

class SignupInvitation extends Model
{

    protected $autoPublisher = false;

    public static $rules = array(
        'email' => 'required|email',
        'name' => 'string|max:255',
        'token' => 'string|max:255',
    );

    public static $customMessages = array();

    public $supportImportFileExtension = array('xls', 'xlsx', 'csv');

    public function __construct(array $attributes = array())
    {

        parent::__construct($attributes);

    }

    public function beforeValidate()
    {

        return true;
    }

    public function createNewToken()
    {
        return hash_hmac('sha256', Str::random(40), config('app.key'));

    }


    public function send($people)
    {

        try {

            foreach($people as $person) {


                $email = trim( $person['email'] );
                $name = trim( Utility::hasString(trim($person['name'])) ? $person['name'] : '');

                if(!Utility::hasString($email)){
                    continue;
                }

                $instance = new static();

                $instance->getConnection()->transaction(function () use ($instance, $email, $name) {

                    try{

                        $found = $instance->where('email', '=', $email)->first();

                        if(!is_null($found) && $found->exists){
                            $found->delete();
                        }

                        $instance->setAttribute('email', $email);
                        $instance->setAttribute('name',  $name);
                        $instance->setAttribute('token', $instance->createNewToken());

                        $instance->save();


                        Mail::queue(new SignupInvitationMail($instance));

                    }catch(ModelValidationException $e){



                    }catch(Exception $e){


                    }

                });

            }

        }catch (Exception $e) {

            throw $e;

        }

    }

    public function deleteByToken($token){

        return $this->where('token', '=', $token)->delete();

    }

    public function deleteByEmail($email){

        return $this->where('email', '=', $email)->delete();

    }

    public function isValid($token){

        return $this->where('token', '=', $token)->count();
    }

    public function getInvalidMessage(){

        $company = (new Temp())->getCompanyDefault();
        $email = $company->support_email;
        return Translator::transSmart('app.It seems like your signup invitation link is invalid. Kindly contact the Community Team at <a href="mailto:%s">%s</a>, should you have any inquiries or concerns.', sprintf('It seems like your signup invitation link is invalid. Kindly contact the Community Team at <a href="mailto:%s">%s</a>, should you have any inquiries or concerns.', $email, $email), true, ['email1' => $email, 'email2' => $email] );


    }
}