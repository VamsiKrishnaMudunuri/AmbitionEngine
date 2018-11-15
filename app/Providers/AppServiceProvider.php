<?php

namespace App\Providers;

use DB;
use Validator;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Inacho\CreditCard;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //


        if(
            strcasecmp(config('app.protocol'), 'https') == 0 ||
            strcasecmp($this->app['request']->getScheme(), 'https') == 0
        ) {
            $this->app['request']->server->set('HTTPS', true);
        }


        if (config('app.debug')){
            DB::connection('mongodb')->enableQueryLog();
        }

        Relation::morphMap([
            'users' => 'App\Models\User',
            'companies' => 'App\Models\Company',
            'followings' =>  'App\Models\MongoDB\Following',
            'followers' =>  'App\Models\MongoDB\Follower',
            'places' => 'App\Models\MongoDB\Place',
            'activities' => 'App\Models\MongoDB\Activity',
            'posts' => 'App\Models\MongoDB\Post',
            'comments' => 'App\Models\MongoDB\Comment',
            'groups' => 'App\Models\MongoDB\Group',
            'jobs' => 'App\Models\MongoDB\Job',
            'business_opportunities' => 'App\Models\MongoDB\BusinessOpportunity',
            'likes' => 'App\Models\MongoDB\Like',
            'joins' => 'App\Models\MongoDB\Join',
            'invites' => 'App\Models\MongoDB\Invite',
            'goings' => 'App\Models\MongoDB\Going'
        ]);

        Validator::extend('ccdt', function($attribute, $value, $parameters, $validator) {
            try {
                $value = array_map('trim', explode('/', $value));
                return CreditCard::validDate(strlen($value[1]) == 2 ? (2000+$value[1]) : $value[1], $value[0]);
            } catch(\Exception $e) {
                return false;
            }
        });

        Validator::extend('length', function($attribute, $value, $parameters, $validator) {
            $maxLength = Arr::first($parameters);
            return (is_null($value) || strlen($value) <= $maxLength) ? true : false;
        });

        Validator::extend('flexible_url', function($attribute, $value, $parameters, $validator) {

            return  preg_match('/^(?:(ftp|http|https)?:\/\/)?(?:[\w-]+\.)+([a-z]|[A-Z]|[0-9]){2,6}$/i', $value);

        });

        Validator::extend('username', function($attribute, $value, $parameters, $validator) {
            return preg_match('/^[^\W_]{1}[a-z0-9\-]*$/i', $value);
        });

        Validator::extend('slug', function($attribute, $value, $parameters, $validator) {
            return preg_match('/^[^\W_]{1}[a-z0-9\-\_\/]*$/i', $value);
        });

        Validator::extend('price', function($attribute, $value, $parameters, $validator) {
            $first = Arr::first($parameters, null, 12);
            $last = Arr::last($parameters, null, 2);
            $regex = sprintf('/^\d{1,%s}(\.\d{0,%s})?$/', $first, $last);
            return preg_match($regex, $value);
        });

        Validator::extend('signed_price', function($attribute, $value, $parameters, $validator) {
            $first = Arr::first($parameters, null, 12);
            $last = Arr::last($parameters, null, 2);
            $regex = sprintf('/^[+-]{0,1}\d{1,%s}(\.\d{0,%s})?$/', $first, $last);
            return preg_match($regex, $value);
        });

        Validator::extend('coordinate', function($attribute, $value, $parameters, $validator) {
            $first = Arr::first($parameters, null, 12);
            $last = Arr::last($parameters, null, 2);
            $regex = sprintf('/^\d{1,%s}(\.\d{0,%s})?$/', $first, $last);
            return preg_match($regex, $value);
        });


        Validator::extend('greater_than', function($attribute, $value, $parameters, $validator){

            $attr = $value;
            $other = Arr::first($parameters);

            if(!is_numeric($other)){
                $other =  Arr::get($validator->getData(), $other);
            }

            if($attr > $other){
                return true;
            }else{
                return false;
            }

        });

        Validator::extend('greater_than_equal', function($attribute, $value, $parameters, $validator){

            $attr = $value;
            $other = Arr::first($parameters);

            if(!is_numeric($other)){
                $other =  Arr::get($validator->getData(), $other);
            }

            if($attr >= $other){
                return true;
            }else{
                return false;
            }

        });

        Validator::extend('greater_than_time', function($attribute, $value,  $parameters, $validator){

            $attr  =  strtotime($value);
            $other = strtotime(Arr::get($validator->getData(), Arr::first($parameters)));

            if($attr > $other){
                return true;
            }else{
                return false;
            }

        });

        Validator::extend('greater_than_equal_time', function($attribute, $value,  $parameters, $validator){

            $attr  =  strtotime($value);
            $other = strtotime(Arr::get($validator->getData(), Arr::first($parameters)));

            if($attr >= $other){
                return true;
            }else{
                return false;
            }

        });

        Validator::extend('greater_than_datetime', function($attribute, $value,  $parameters, $validator){

            $attr  =  strtotime($value);
            $other = strtotime(Arr::get($validator->getData(), Arr::first( $parameters )));

            if($attr > $other){
                return true;
            }else{
                return false;
            }

        });

        Validator::extend('greater_than_datetime_equal', function($attribute, $value,  $parameters, $validator){

            $attr  =  strtotime($value);

            $other = strtotime(Arr::get($validator->getData(), Arr::first($parameters)));

            if($attr >= $other){
                return true;
            }else{
                return false;
            }

        });

        Validator::extend('less_than', function($attribute, $value, $parameters, $validator){

            $attr = $value;
            $other = Arr::first($parameters);

            if(!is_numeric($other)){
                $other =  Arr::get($validator->getData(), $other);
            }

            if($attr < $other){
                return true;
            }else{
                return false;
            }

        });

        Validator::extend('less_than_equal', function($attribute, $value, $parameters, $validator){

            $attr = $value;
            $other = Arr::first($parameters);

            if(!is_numeric($other)){
                $other =  Arr::get($validator->getData(), $other);
            }

            if($attr <= $other){
                return true;
            }else{
                return false;
            }

        });

        Validator::extend('less_than_datetime', function($attribute, $value,  $parameters, $validator){

            $attr  =  strtotime($value);
            $other = strtotime(Arr::get($validator->getData(), Arr::first( $parameters )));

            if($attr < $other){
                return true;
            }else{
                return false;
            }

        });

        Validator::extend('less_than_datetime_equal', function($attribute, $value,  $parameters, $validator){

            $attr  =  strtotime($value);

            $other = strtotime(Arr::get($validator->getData(), Arr::first($parameters)));

            if($attr <= $other){
                return true;
            }else{
                return false;
            }

        });

        /**
         * Validate min field if the 'opponent(e.g: max field)' is less than this field
         * (Mainly for commission structure only otherwise just use "less_than" rules)
         * for generic usage.
         *
         * If the max field is exist && do have value && do have paremeter exist - do checking(this for commission only).
         *
         * If the max field is exist && do have value - do checking using normal behaviour.
         */
        Validator::extend('min_if', function($attribute, $value, $parameters, $validator) {

            $allAttributes = $validator->getData();
            $other = Arr::get($allAttributes, Arr::first($parameters));

            if (count($parameters) > 1) {
                // For commission structure onyl
                if ($parameters[1] === 'exist') {
                    if ($other) {
                        return $value > $other ? false : true;
                    } else {
                        return true;
                    }
                }
            // cover for other field
            } else {
                return $value > $other ? false : true;
            }
        });

        /**
         * Validate max field if the 'opponent(e.g: max field)' is less than this field
         * (Mainly for commission structure only otherwise just use "less_than" rules)
         * for generic usage.
         *
         * If the min field is exist && do have value && do have paremeter exist - do checking(this for commission only).
         *
         * If the min field is exist && do have value - do checking using normal behaviour.
         */
        Validator::extend('max_if', function($attribute, $value, $parameters, $validator) {

            $allAttributes = $validator->getData();
            $other = Arr::get($allAttributes, Arr::first($parameters));

            if (count($parameters) > 1) {
                // For commission structure onyl
                if ($parameters[1] === 'exist') {
                    if ($value) {
                        return $value < $other ? false : true;
                    } else {
                        return true;
                    }
                }
                // cover for other field
            } else {
                return $value < $other ? false : true;
            }
        });

        Validator::replacer('min_if', function($message, $attribute, $rule, $parameters) {
            return str_replace([':other'],  str_replace('_', ' ', Arr::first($parameters)), $message);
        });

        Validator::replacer('max_if', function($message, $attribute, $rule, $parameters) {
            return str_replace([':other'],  str_replace('_', ' ', Arr::first($parameters)), $message);
        });

        Validator::replacer('length', function($message, $attribute, $rule, $parameters) {
            return str_replace([':other'],  str_replace('_', ' ', Arr::first($parameters)), $message);
        });

        Validator::replacer('price', function($message, $attribute, $rule, $parameters) {
            $first = Arr::first($parameters, null, 12);
            $last = Arr::last($parameters, null, 2);
            $symbol = sprintf('%s.%s', str_repeat('#', $first), str_repeat('#', $last));
            $digit = $first + $last;
            return str_replace([':symbol', ':digit'], [$symbol, $digit] , $message);
        });

        Validator::replacer('signed_price', function($message, $attribute, $rule, $parameters) {
            $first = Arr::first($parameters, null, 12);
            $last = Arr::last($parameters, null, 2);
            $symbol = sprintf('%s.%s', str_repeat('#', $first), str_repeat('#', $last));
            $digit = $first + $last;
            return str_replace([':symbol', ':digit'], [$symbol, $digit] , $message);
        });

        Validator::replacer('coordinate', function($message, $attribute, $rule, $parameters) {
            $first = Arr::first($parameters, null, 12);
            $last = Arr::last($parameters, null, 2);
            $symbol = sprintf('%s.%s', str_repeat('#', $first), str_repeat('#', $last));
            $digit = $first + $last;
            return str_replace([':symbol', ':digit'], [$symbol, $digit] , $message);
        });

        Validator::replacer('greater_than', function($message, $attribute, $rule, $parameters){
            return str_replace([':other'],  str_replace('_', ' ', Arr::first($parameters)), $message);

        });

        Validator::replacer('greater_than_equal', function($message, $attribute, $rule, $parameters){

            return str_replace([':other'], str_replace('_', ' ', Arr::first($parameters)), $message);

        });

        Validator::replacer('greater_than_time', function($message, $attribute, $rule, $parameters){

            return str_replace([':other'], str_replace('_', ' ', Arr::first($parameters)), $message);

        });

        Validator::replacer('greater_than_equal_time', function($message, $attribute, $rule, $parameters){

            return str_replace([':other'], str_replace('_', ' ', Arr::first($parameters)), $message);

        });

        Validator::replacer('greater_than_datetime', function($message, $attribute, $rule, $parameters){

            return str_replace([':other'], str_replace('_', ' ', Arr::first($parameters)), $message);

        });

        Validator::replacer('greater_than_datetime_equal', function($message, $attribute, $rule, $parameters){

            return str_replace([':other'], str_replace('_', ' ', Arr::first($parameters)), $message);

        });

        Validator::replacer('less_than', function($message, $attribute, $rule, $parameters){
            return str_replace([':other'],  str_replace('_', ' ', Arr::first($parameters)), $message);

        });

        Validator::replacer('less_than_equal', function($message, $attribute, $rule, $parameters){

            return str_replace([':other'], str_replace('_', ' ', Arr::first($parameters)), $message);

        });

        Validator::replacer('less_than_datetime', function($message, $attribute, $rule, $parameters){

            return str_replace([':other'], str_replace('_', ' ', Arr::first($parameters)), $message);

        });

        Validator::replacer('less_than_datetime_equal', function($message, $attribute, $rule, $parameters){

            return str_replace([':other'], str_replace('_', ' ', Arr::first($parameters)), $message);

        });

        Validator::replacer('dimensions', function($message, $attribute, $rule, $parameters){

            return str_replace([':width', ':height'], [Arr::last(explode('=', Arr::first($parameters))), Arr::last(explode('=', Arr::last($parameters)))], $message);
        
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
