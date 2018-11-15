<?php

namespace App\Services;

use Exception;
use DateTime;
use DateTimeZone;
use App;
use Auth;
use Config;
use Utility as Util;
use Translator as Translate;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Carbon\Carbon;
use ArrayObject;
use Punic\Data;
use Punic\Calendar;
use Punic\Territory;
use Punic\Currency;
use Punic\Language;
use Punic\Phone;
use Punic\Number;
use Punic\Misc;
use Punic\Unit;

class Cldr{

    private function setLocale(){
        Data::setDefaultLocale(App::getLocale());
    }

    private function constant($key = null, $isListFormat = false){

        $namespace = 'cldr.';

        $key = strtolower($key);

        $list = Translate::transSmart($namespace . $key);

        if($isListFormat){

            if(Util::hasArray($list)){
                $arr = [];

                foreach($list as $key => $value){
                    $arr[$value['slug']] = $value['name'];
                }

                $list = $arr;

            }

        }

        return $list;

    }

    function getCountries()
    {
        $this->setLocale();
        static $countries = null;

        if($countries === null){

            $countries = Territory::getCountries();
        }


        return $countries;

    }

    function getLanguages()
    {
        $this->setLocale();
        static $languages = null;

        if($languages === null){
            $languages = Language::getAll();
        }

        return $languages;

    }

    function getSupportLanguages()
    {
        $languages = $this->getLanguages();

        $arr = [];

        $support = Config::get('app.locale_support');

        if(in_array('*', $support)){

            $arr = $languages;

        }else {

            foreach ($support as $key => $locale) {

                if (isset($languages[$locale])) {
                    $arr[$locale] = $languages[$locale];
                }
            }

        }

        return $arr;

    }

    function getNationalities()
    {
        $this->setLocale();
        static $nationalities = null;

        if($nationalities === null){
            $nationalities = $this->constant('nationalities', true);
        }

        return $nationalities;

    }

    function getPhoneCountryCodes($showCountryName = true){

        $this->setLocale();
        static $codes = null;

        $mapping = ['61' => 'AU'];

        if($codes === null){

            $countries = $this->getCountries();

            foreach($countries as $key => $value){

                $code = Phone::getPrefixesForTerritory($key);


                $match = array_intersect(array_keys($mapping), array_values($code));

                if($match){

                    $redefined_country_phone_code = Arr::first($match);
                    $code = array($redefined_country_phone_code);
                    $value = $countries[$mapping[$redefined_country_phone_code]];

                }

                foreach($code as $ck => $cv){

                    $codes[$cv] = $showCountryName ? $value . ' ( +' . $cv . ')' : '+'. $cv;
                }

            }

            asort($codes);

        }

        return $codes;

    }

    function getCurrencies($isShortForm = false, $isSortByAlphabetAsc = false)
    {
        $this->setLocale();
        $currencies = null;

        if($currencies === null){

            $currencies = [
                'short' => [],
                'full' => [],
            ];

            foreach(Currency::getAllCurrencies() as $key => $currency){
                $currencies['short'][$key] = $key;
                $currencies['full'][$key] = sprintf('%s - %s', $key, $currency);
            }


        }

        if($isSortByAlphabetAsc){
            asort($currencies['short']);
            asort($currencies['full']);
        }

        return ($isShortForm) ? $currencies['short'] : $currencies['full'];

    }

    public function getCurrencyByCountryCode($countryCode)
    {
        return Currency::getCurrencyForTerritory($countryCode);
    }

    function getSupportCurrencies($isShortForm = false, $isSortByAlphabetAsc = false)
    {
        $currencies = $this->getCurrencies($isShortForm, $isSortByAlphabetAsc);

        $arr = [];
        $support = Config::get('currency.support');

        if(in_array('*', $support)){

            $arr = $currencies;

        }else{

            foreach($support as $key => $code){

                if(isset($currencies[$code])){
                    $arr[$code] = $currencies[$code];
                }

            }

        }



        return $arr;

    }

    function getTimezones($isShortForm = false, $isSortByAlphabetAsc = false) {

        $this->setLocale();

        $timezones = null;

        if ($timezones === null) {

            $timezones = [
                'short' => [],
                'full' => [],
            ];

            $offsets = [];
            $now = new DateTime();

            foreach (DateTimeZone::listIdentifiers() as $timezone) {

                $now->setTimezone(new DateTimeZone($timezone));
                $offsets[] = $offset = $now->getOffset();

                $hours = intval($offset / 3600);
                $minutes = abs(intval($offset % 3600 / 60));
                $gmt = ' (GMT' . ($offset ? ' ' . sprintf('%+03d:%02d', $hours, $minutes) : '') . ')';

                $name = Calendar::getTimezoneExemplarCity($timezone, false);
                if(empty($name)){
                    $timezoneArr = explode('/', $timezone);
                    if($timezoneArr){
                        $name = isset($timezoneArr[1]) ? $timezoneArr[1] : $timezoneArr[0];
                        $name = str_replace('_', ' ', $name);
                        $name = str_replace('St ', 'St. ', $name);

                    }
                }

                $timezones['short'][$timezone] = $gmt;
                $timezones['full'][$timezone] = $name . $gmt;

            }

            array_multisort((new ArrayObject($offsets))->getArrayCopy(), $timezones['short']);
            array_multisort((new ArrayObject($offsets))->getArrayCopy(), $timezones['full']);

        }

        if($isSortByAlphabetAsc){
            asort($timezones['short']);
            asort($timezones['full']);
        }

        return ($isShortForm) ? $timezones['short'] : $timezones['full'];

    }

    function getTimeZonesByCountryCode($country_code){

        $this->setLocale();

        $country_code = Str::upper($country_code);

        return DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, $country_code);

    }


    function getTimeZoneWithCapitalsByCountryCode($country_code){

        $this->setLocale();

        return Arr::first($this->getTimeZonesByCountryCode($country_code), null, config('app.timezone', 'UTC'));

    }


    function getCountryCodeByPhoneCode($code){

        $arr = Phone::getTerritoriesForPrefix($code);

        return Arr::first($arr);
    }

    function getCountryByCode($code){

        $name = '';

        $countries = $this->getCountries();

        $code = strtoupper($code);

        if(isset($countries[$code])){
            $name = $countries[$code];
        }

        return $name;

    }

    function getCountryCodeByName($name){

        $code = '';

        $countries = $this->getCountries();

        $search = array_search(strtolower($name), array_map('strtolower', $countries));

        if($search){
            $code = $search;
        }

        return $code;

    }

    function getLanguageByCode($code){

        $name = '';
        $languages = $this->getLanguages();

        if(isset($languages[$code])){
            $name = $languages[$code];
        }

        return $name;

    }

    function getTimezoneByCode($code, $isShortForm = false){

        $name = '';
        $timezones = $this->getTimezones($isShortForm);

        if(isset($timezones[$code])){
            $name = $timezones[$code];
        }

        return $name;

    }

    public function getTimezoneName($code, $format = 'long'){

        $this->setLocale();
        return Calendar::getTimezoneNameNoLocationSpecific($code, $format);

    }

    function getCurrencyByCode($code, $isShortForm = false){

        $name = '';
        $currencies = $this->getCurrencies($isShortForm);

        if(isset($currencies[$code])){

            $name = $currencies[$code];

        }

        return $name;
    }

    function getCurrencySymbolByCode($code){

        return Currency::getSymbol($code, 'narrow');

    }

    function getMonthName($dt, $wide = 'wide'){
        $this->setLocale();
        return Calendar::getMonthName($dt, $wide);
    }

    function showDate($value, $format = 'long'){

        $this->setLocale();

        $str = '';
        $datetime = null;


        try {
            if(!is_null($value)) {
                $datetime = Calendar::toDateTime(new DateTime($value, ($value instanceof Carbon) ? $value->getTimezone() : null));
            }
        }catch(Exception $e){

        }

        if(!is_null($datetime)){

            $str = Calendar::formatDate($datetime,  $format);
        }


        return $str;

    }

    function showDateTime($value, $format = '', $toTimezone = '', $fromTimezone = '', $isNeedTimeOnly = false){

        $this->setLocale();

        $str = '';
        $datetime = null;
        $format = ($format) ? $format : 'long|medium';
        $fromTimezone = (empty($fromTimezone)) ? config('app.timezone', 'UTC') : $fromTimezone;

        if(empty($toTimezone)){

            if(Auth::check()){
                if(Auth::user()->timezone){
                    $toTimezone = Auth::user()->timezone;
                }
            }

            if(empty($toTimezone)){
                $toTimezone = config('app.timezone', 'UTC');
            }
        }


        try {

            if(!is_null($value)) {
                if (strcasecmp($fromTimezone, $toTimezone) == 0) {

                    $datetime = Calendar::toDateTime(new DateTime($value));

                } else {

                    $datetime = Calendar::toDateTime(new DateTime($value), $toTimezone, $fromTimezone);

                }
            }

        }catch(Exception $e){

        }

        if(!is_null($datetime)){
            if($isNeedTimeOnly){
                $str = Calendar::formatTime($datetime, $format);
            }else{
                $str = Calendar::formatDatetime($datetime, $format);
            }

        }


        return $str;

    }

    function showTime($value, $format = '', $toTimezone = '', $fromTimezone = ''){

        $format = ($format) ? $format : 'long';
        return  (!is_null($value)) ? $this->showDateTime($value, $format, $toTimezone, $fromTimezone, true) : '';

    }

    function showRelativeDateTime($value, $format = '', $toTimezone = '', $fromTimezone = '', $interval = 30){

        $this->setLocale();
        $str = '';

        try {

            $datetime = null;
            $format = ($format) ? $format : 'long^|short';
            $fromTimezone = (empty($fromTimezone)) ? config('app.timezone', 'UTC') : $fromTimezone;

            if (empty($toTimezone)) {

                if (Auth::check()) {
                    if (Auth::user()->timezone) {
                        $toTimezone = Auth::user()->timezone;
                    }
                }

                if (empty($toTimezone)) {
                    $toTimezone = config('app.timezone', 'UTC');
                }
            }

            $str = '';
            $now = Carbon::now($toTimezone);
            $value = (new Carbon($value))->timezone($toTimezone);

            if ($now->diffInDays($value) > $interval) {

                $str = call_user_func_array(array($this, 'showDateTime'), func_get_args());

            } else {

                $unit = '';
                $deltaForUnit = null;
                $delta = null;

                if ($now->diffInWeeks($value) > 0) {
                    $unit = 'week';
                    $deltaForUnit = (Carbon::SECONDS_PER_MINUTE * Carbon::MINUTES_PER_HOUR * Carbon::HOURS_PER_DAY) * Carbon::DAYS_PER_WEEK;
                } else if ($now->diffInDays($value) > 0) {
                    $unit = 'day';
                    $deltaForUnit = Carbon::SECONDS_PER_MINUTE * Carbon::MINUTES_PER_HOUR * Carbon::HOURS_PER_DAY;
                } else if ($now->diffInHours($value) > 0) {
                    $unit = 'hour';
                    $deltaForUnit = Carbon::SECONDS_PER_MINUTE * Carbon::MINUTES_PER_HOUR;
                } else if ($now->diffInMinutes($value) > 0) {
                    $unit = 'minute';
                    $deltaForUnit = Carbon::SECONDS_PER_MINUTE;
                } else if ($now->diffInSeconds($value) > 0) {
                    $unit = 'second';
                    $deltaForUnit = Carbon::SECONDS_PER_MINUTE;
                }

                $delta = intval(round((( ($now->greaterThan($value)) ? $value->getTimestamp() - $now->getTimestamp() : $now->getTimestamp() - $value->getTimestamp()) / $deltaForUnit)));

                $data = Data::get('dateFields');

                if (isset($data[$unit])) {
                    $data = $data[$unit];
                    $key = "relative-type-$delta";
                    if (isset($data[$key])) {

                        $str = $data[$key];

                    }else{

                        $when = '';

                        if($value->greaterThan($now)){
                            $when = 'future';
                        }else{
                            $when = 'past';
                        }
                        $key = "relativeTime-type-$when";

                        if(isset($data[$key])) {

                            $arr = $data[$key];
                            $plural = 'other';

                            if(isset($arr['relativeTimePattern-count-one'])) {
                                if (abs($delta) <= 1) {
                                    $plural = 'one';
                                }
                            }

                            $key = "relativeTimePattern-count-$plural";

                            if(isset($arr[$key])){
                                $str = $arr[$key];
                            }
                        }
                    }
                }


                if(Util::hasString($str)){

                    $str = Misc::fixCase(str_replace('{0}', abs($delta), $str), 'titlecase-firstword');

                }
            }

        }catch(Exception $e){

        }




        return $str;

    }

    function showRelativeDateTimeUnit($value, $now = null, $format = '', $toTimezone = '', $fromTimezone = ''){

        $this->setLocale();
        $str = '';

        try {

            $datetime = null;
            $format = ($format) ? $format : 'long,0';
            $fromTimezone = (empty($fromTimezone)) ? config('app.timezone', 'UTC') : $fromTimezone;

            if (empty($toTimezone)) {

                if (Auth::check()) {
                    if (Auth::user()->timezone) {
                        $toTimezone = Auth::user()->timezone;
                    }
                }

                if (empty($toTimezone)) {
                    $toTimezone = config('app.timezone', 'UTC');
                }
            }

            $str = '';
            $now = (Util::hasString($now)) ? (new Carbon($now))->timezone($toTimezone) : Carbon::now($toTimezone);
            $value = (new Carbon($value))->timezone($toTimezone);

            $unit = '';
            $delta = null;

            if (($delta = $now->diffInYears($value)) > 0) {
                $unit = 'year';
            }
            else if (($delta = $now->diffInMonths($value)) > 0) {
                $unit = 'month';
            }
            else if (($delta = $now->diffInWeeks($value)) > 0) {
                $unit = 'week';

            } else if (($delta = $now->diffInDays($value)) > 0) {
                $unit = 'day';
            } else if (($delta = $now->diffInHours($value)) > 0) {
                $unit = 'hour';
            } else if (($delta = $now->diffInMinutes($value)) > 0) {
                $unit = 'minute';
            } else if (($delta = $now->diffInSeconds($value)) > 0) {
                $unit = 'second';
            }


            $str = Unit::format($delta, $unit, $format);


        }catch(Exception $e){

        }


        return $str;

    }

    function toDateTime($value, $toTimezone = '', $fromTimezone = ''){

        $this->setLocale();

        $str = '';
        $datetime = null;
        $fromTimezone = (empty($fromTimezone)) ? config('app.timezone', 'UTC') : $fromTimezone;

        if(empty($toTimezone)){

            if(Auth::check()){
                if(Auth::user()->timezone){
                    $toTimezone = Auth::user()->timezone;
                }
            }

            if(empty($toTimezone)){
                $toTimezone = config('app.timezone', 'UTC');
            }
        }

        try {

            if(!is_null($value)) {

                if($value instanceof Carbon){
                    $value = $value->toDateTimeString();
                }

                if (strcasecmp($fromTimezone, $toTimezone) == 0) {

                    $datetime = Calendar::toDateTime(new DateTime($value));

                } else {

                    $datetime = Calendar::toDateTime(new DateTime($value), $toTimezone, $fromTimezone);

                }
            }

        }catch(Exception $e){

        }




        return is_null($datetime) ? new Carbon() : Carbon::instance($datetime);

    }

    function now($timezone = ''){

        $this->setLocale();


        if(empty($timezone)){

            if(Auth::check()){
                if(Auth::user()->timezone){
                    $timezone = Auth::user()->timezone;
                }
            }

            if(empty($timezone)){
                $timezone = config('app.timezone', 'UTC');
            }
        }


        return Carbon::now($timezone);

    }

    function today($timezone = ''){

        $this->setLocale();


        if(empty($timezone)){

            if(Auth::check()){
                if(Auth::user()->timezone){
                    $timezone = Auth::user()->timezone;
                }
            }

            if(empty($timezone)){
                $timezone = config('app.timezone', 'UTC');
            }
        }



        return Carbon::today($timezone);

    }

    function number($value, $precision = 2){

        $this->setLocale();

        return Number::format($value, $precision);

    }

    function showNil(){
        return $this->constant('nil');
    }

    function showTax($value = null, $stripPercentageSymbol = false){
        return (!is_null($value) && $value > 0) ? ( ($stripPercentageSymbol) ? $value : sprintf('%s%s', $value, '&#37;')) : $this->showNil();
    }

    function showDiscount($value = null, $stripPercentageSymbol = false){
        return (!is_null($value) && $value > 0) ? ( ($stripPercentageSymbol) ? $value : sprintf('%s%s', $value, '&#37;')) : $this->showNil();
    }


    function showPrice($value, $currency_code = null, $precision = 2, $isNeedSymbol = false){

        $price = '';

        if(!is_null($currency_code)){
            $price = sprintf('%s %s', ((!$isNeedSymbol) ? $this->getCurrencyByCode($currency_code, true) : $this->getCurrencySymbolByCode($currency_code)), $this->number($value, $precision));
        }else{
            $price = $this->number($value, $precision);
        }

        return $price;

    }

    function showCredit($value, $precision = 0, $isOnlyNeedFigure = false){

        $credit = '';


        if($isOnlyNeedFigure){
            $credit = $this->number($value, $precision);
        }else{
            $credit = sprintf('%s %s', $this->number($value, $precision), trans_choice('plural.credit', intval($value)));
        }

        return $credit;

    }

}