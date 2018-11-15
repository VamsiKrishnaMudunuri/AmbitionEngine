<?php

namespace App\Models;

use Exception;
use Utility;
use Translator;
use Hash;
use Config;
use CLDR;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Collection;
use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

class Currency extends Model
{

    protected $autoPublisher = false;

    private $precision;

    public static $rules = array(
        'base' => 'required|max:3',
        'quote' => 'required|max:3',
        'base_amount' => 'required',
        'quote_amount' => 'required',
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();


    public function __construct(array $attributes = array())
    {

        $this->precision = Config::get('currency.precision');

        parent::__construct($attributes);

    }


    public function beforeSave(){

        return true;

    }

    public function setExtraRules(){

        return array();
    }

    public function getBaseAmountAttribute($value){

        if(fmod($value, 1) == 0){
            $value = Utility::round($value, 0);
        }

        return $value;

    }

    public function getQuoteAmountAttribute($value){

        if(fmod($value, 1) == 0){
            $value = Utility::round($value, 0);
        }

        return $value;
    }

    public function formatBaseAmountWithCredit(){

        $val = sprintf('%s %s', CLDR::number($this->base_amount, 0), trans_choice('plural.credit', intval($this->base_amount)));

        return $val;
    }

    public function formatQuoteAmountWithCredit(){

        $val = sprintf('%s %s', CLDR::number($this->quote_amount, 0), trans_choice('plural.credit', intval($this->quote_amount)));

        return $val;
    }

    public function formatBaseAmountWithCode(){

        $val = CLDR::showPrice($this->base_amount, $this->base, $this->precision);
        return $val;

    }

    public function formatQuoteAmountWithCode(){

        $val = CLDR::showPrice($this->quote_amount, $this->quote, $this->precision);

        return $val;
    }

    public function getPrecision(){
        return $this->precision;
    }

    public function setPrecision($precision){

        $this->precision = $precision;
        return $this;

    }

    public function getByQuote($quote){

        $currency = $this
                    ->where('quote', '=', $quote)
                    ->first();

        return is_null($currency) ? new static() : $currency;

    }

    public function getByQuoteOrFail($quote){

        $currency = $this
            ->where('quote', '=', $quote)
            ->firstOrFail();

        return $currency;

    }

    public function swap(){

        try {

            $clone = clone $this;

            $this->base = $clone->quote;
            $this->quote = $clone->base;
            $this->base_amount = $clone->base_amount;
            $this->quote_amount = Utility::round($clone->base_amount / $clone->quote_amount, $this->precision);

        }catch (Exception $ex){

        }

    }

    public function convertFromBaseToQuote($amount){

        $figure = $amount * $this->quote_amount;

        return Utility::round($figure, $this->precision);

    }

    public function convertFromQuoteToBase($amount){

        $figure = $amount / $this->quote_amount;

        return Utility::round($figure, $this->precision);

    }

    public function convert($quote, $amount){

        $figure = $amount / $this->quote_amount * $quote->quote_amount;

        return Utility::round($figure, $this->precision);

    }

    public function exchange($base, $quote, $amount){

        $base = $this
            ->where('quote', '=', $base)
            ->first();

        $quote = $this
            ->where('quote', '=', $quote)
            ->first();

        $figure = 0;

        if(!is_null($base) && !is_null($quote)){
            $figure = $base->setPrecision($this->precision)->convert($quote, $amount);
        }

        return ['base' => $base, 'quote' => $quote, 'figure' => Utility::round($figure, $this->precision)];

    }

    public function exchangeOrFail($base, $quote, $amount){

        try {

            $base = $this
                ->where('quote', '=', $base)
                ->first();

            $quote = $this
                ->where('quote', '=', $quote)
                ->first();

            if (is_null($base) || is_null($quote)) {

                throw new IntegrityException($this, Translator::transSmart("app.There was an error for currency conversion.', 'There was an error for currency conversion."));

            }

        }catch (IntegrityException $e){

            throw $e;

        }catch(Exception $e){


            throw $e;

        }

        $figure = $base->setPrecision($this->precision)->convert($quote, $amount);


        return ['base' => $base, 'quote' => $quote, 'figure' => Utility::round($figure, $this->precision)];

    }



    public static function retrieve($id){

        try {

            $result = (new static())->with([])->checkInOrFail($id);

        }catch(ModelNotFoundException $e){


            throw $e;

        }


        return $result;

    }

}