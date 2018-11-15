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

use App\Libraries\Model\Model;
use App\Models\Traits\Proration;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

class SubscriptionComplimentary extends Model
{


    protected $autoPublisher = true;

    public static $rules = array(
        'subscription_id' => 'required|integer',
        'category' => 'required|integer',
        'credit' => 'required|price',
        'debit' => 'required|price'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array())
    {
        static::$relationsData = array(
            'subscription' => array(self::BELONGS_TO, Subscription::class),
        );

        static::$customMessages = array(

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

    public function scopeCategoryQuery($query, $category){

        return $query->where('category', '=', $category);

    }

    public function getCategoryNameAttribute($value){

        $value = '';

        if($this->exists){
            $value = Utility::constant(sprintf('facility_category.%s.name', $this->category));
        }

        return $value;

    }

    public function remaining(){

        $amount = $this->credit - $this->debit;
        return Utility::round($amount, Config::get('money.precision'));
    }

    public function used(){
        return Utility::round($this->debit, Config::get('money.precision'));
    }

    public function hasBalance(){

        $flag = false;

        if($this->remaining() > 0){
            $flag = true;
        }

        return $flag;
    }

    public function transactions($id){

        return $this
            ->where($this->subscription()->getForeignKey(), '=', $id)
            ->get();

    }


    public function transactionsByPropertyAndUser($property_id, $user_id){

        $property = new Property();
        $subscription = new Subscription();
        $subscription_user = new SubscriptionUser();

        $complimentaries = $this
            ->selectRaw(sprintf('%s.category, SUM(%s.credit) AS credit, SUM(%s.debit) AS debit', $this->getTable(), $this->getTable(), $this->getTable()))
            ->join($subscription->getTable(), function($query) use ($property_id, $property, $subscription){
                $query
                    ->on(sprintf('%s.%s', $this->getTable(), $this->subscription()->getForeignKey()), '=', sprintf('%s.%s', $subscription->getTable(), $subscription->getKeyName()))
                    ->where(sprintf('%s.%s', $subscription->getTable(), $subscription->property()->getForeignKey()), '=', $property_id)
                    ->whereIn(sprintf('%s.status', $subscription->getTable()), $subscription->confirmStatus);
            })
            ->join($subscription_user->getTable(), function($query) use($user_id, $subscription, $subscription_user){
                $query
                    ->on(sprintf('%s.%s', $subscription->getTable(), $subscription->getKeyName()), '=', sprintf('%s.%s', $subscription_user->getTable(), $subscription_user->subscription()->getForeignKey()))
                    ->where(sprintf('%s.%s', $subscription_user->getTable(), $subscription_user->user()->getForeignKey()), '=', $user_id);
            })
            ->where(sprintf('%s.%s', $subscription->getTable(), $subscription->property()->getForeignKey()), '=', $property_id)
            ->where(sprintf('%s.%s', $subscription_user->getTable(), $subscription_user->user()->getForeignKey()), '=', $user_id)
            ->groupBy([sprintf('%s.%s', $subscription->getTable(), $subscription->property()->getForeignKey()), sprintf('%s.category', $this->getTable())])
            ->get();


        $complimentaries->map(function($complimentary){
            $complimentary['category_name'] = $complimentary->category_name;
        });

        return $complimentaries;

    }

    public function transactionsByPropertyAndCategoryAndUser($property_id, $facility_category, $user_id, $inOnlyForDefaultUser = null){

        $property = new Property();
        $subscription = new Subscription();
        $subscription_user = new SubscriptionUser();

        $complimentaries = $this
            ->selectRaw(sprintf('%s.category, SUM(%s.credit) AS credit, SUM(%s.debit) AS debit, SUM(%s.credit) - SUM(%s.debit) AS balance', $this->getTable(), $this->getTable(), $this->getTable(), $this->getTable(), $this->getTable()))
            ->join($subscription->getTable(), function($query) use ($property_id, $property, $subscription){
                $query
                    ->on(sprintf('%s.%s', $this->getTable(), $this->subscription()->getForeignKey()), '=', sprintf('%s.%s', $subscription->getTable(), $subscription->getKeyName()))
                    ->where(sprintf('%s.%s', $subscription->getTable(), $subscription->property()->getForeignKey()), '=', $property_id)
                    ->whereIn(sprintf('%s.status', $subscription->getTable()), $subscription->confirmStatus);
            })
            ->join($subscription_user->getTable(), function($query) use($user_id, $subscription, $subscription_user, $inOnlyForDefaultUser){
                $builder = $query
                    ->on(sprintf('%s.%s', $subscription->getTable(), $subscription->getKeyName()), '=', sprintf('%s.%s', $subscription_user->getTable(), $subscription_user->subscription()->getForeignKey()))
                    ->where(sprintf('%s.%s', $subscription_user->getTable(), $subscription_user->user()->getForeignKey()), '=', $user_id);

                if(!is_null($inOnlyForDefaultUser) && is_bool($inOnlyForDefaultUser)){
                    if($inOnlyForDefaultUser){
                        $builder = $builder
                            ->where(sprintf('%s.is_default', $subscription_user->getTable()), '=', Utility::constant('status.1.slug'));
                    }else{
                        $builder = $builder
                            ->where(sprintf('%s.is_default', $subscription_user->getTable()), '!=', Utility::constant('status.1.slug'));
                    }
                }
            })
            ->where(sprintf('%s.%s', $subscription->getTable(), $subscription->property()->getForeignKey()), '=', $property_id)
            ->where(sprintf('%s.%s', $subscription_user->getTable(), $subscription_user->user()->getForeignKey()), '=', $user_id)
            ->where(sprintf('%s.category', $this->getTable()), '=', $facility_category)
            ->groupBy([sprintf('%s.%s', $subscription->getTable(), $subscription->property()->getForeignKey()), sprintf('%s.category', $this->getTable())])
            ->get();

        $complimentaries->map(function($complimentary){
            $complimentary['category_name'] = $complimentary->category_name;
        });

        return $complimentaries;

    }

    public function transactionsWithOnlyHasBalanceByPropertyAndCategoryAndUser($property_id, $facility_category, $user_id, $inOnlyForDefaultUser = null, $isUseForUpdate = false){

        $property = new Property();
        $subscription = new Subscription();
        $subscription_user = new SubscriptionUser();

        $builder = $this;
        $builder = $this;

        if($isUseForUpdate){
            $builder = $builder->lockForUpdate();
        }

        return $builder
            ->selectRaw(sprintf('%s.category, SUM(%s.credit) AS credit, SUM(%s.debit) AS debit, SUM(%s.credit) - SUM(%s.debit) AS balance', $this->getTable(), $this->getTable(), $this->getTable(), $this->getTable(), $this->getTable()))

            ->join($subscription->getTable(), function($query) use ($property_id, $property, $subscription){
                $query
                    ->on(sprintf('%s.%s', $this->getTable(), $this->subscription()->getForeignKey()), '=', sprintf('%s.%s', $subscription->getTable(), $subscription->getKeyName()))
                    ->where(sprintf('%s.%s', $subscription->getTable(), $subscription->property()->getForeignKey()), '=', $property_id)
                    ->whereIn(sprintf('%s.status', $subscription->getTable()), $subscription->confirmStatus);
            })
            ->join($subscription_user->getTable(), function($query) use($user_id, $subscription, $subscription_user, $inOnlyForDefaultUser){
                $builder = $query
                    ->on(sprintf('%s.%s', $subscription->getTable(), $subscription->getKeyName()), '=', sprintf('%s.%s', $subscription_user->getTable(), $subscription_user->subscription()->getForeignKey()))
                    ->where(sprintf('%s.%s', $subscription_user->getTable(), $subscription_user->user()->getForeignKey()), '=', $user_id);

                if(!is_null($inOnlyForDefaultUser) && is_bool($inOnlyForDefaultUser)){
                    if($inOnlyForDefaultUser){
                        $builder = $builder
                            ->where(sprintf('%s.is_default', $subscription_user->getTable()), '=', Utility::constant('status.1.slug'));
                    }else{
                        $builder = $builder
                            ->where(sprintf('%s.is_default', $subscription_user->getTable()), '!=', Utility::constant('status.1.slug'));
                    }
                }
            })
            ->where(sprintf('%s.%s', $subscription->getTable(), $subscription->property()->getForeignKey()), '=', $property_id)
            ->where(sprintf('%s.%s', $subscription_user->getTable(), $subscription_user->user()->getForeignKey()), '=', $user_id)
            ->where(sprintf('%s.category', $this->getTable()), '=', $facility_category)
            ->groupBy([sprintf('%s.%s', $subscription->getTable(), $subscription->property()->getForeignKey()), sprintf('%s.category', $this->getTable())])
            ->having('balance', '>', 0)
            ->get();


    }

    public function transactionsWithOnlyHasBalanceByUser($property_id, $facility_category, $user_id, $inOnlyForDefaultUser = null, $isUseForUpdate = false){

        $property = new Property();
        $subscription = new Subscription();
        $subscription_user = new SubscriptionUser();

        $builder = $this;

        if($isUseForUpdate){
            $builder = $builder->lockForUpdate();
        }

        return $builder
            ->selectRaw(sprintf('%s.*, SUM(%s.credit) AS credit, SUM(%s.debit) AS debit, SUM(%s.credit) - SUM(%s.debit) AS balance', $this->getTable(), $this->getTable(), $this->getTable(), $this->getTable(), $this->getTable()))

            ->join($subscription->getTable(), function($query) use ($property_id, $property, $subscription){
                $query
                    ->on(sprintf('%s.%s', $this->getTable(), $this->subscription()->getForeignKey()), '=', sprintf('%s.%s', $subscription->getTable(), $subscription->getKeyName()))
                    ->where(sprintf('%s.%s', $subscription->getTable(), $subscription->property()->getForeignKey()), '=', $property_id)
                    ->whereIn(sprintf('%s.status', $subscription->getTable()), $subscription->confirmStatus);
            })
            ->join($subscription_user->getTable(), function($query) use($user_id, $subscription, $subscription_user, $inOnlyForDefaultUser){
                $builder = $query
                    ->on(sprintf('%s.%s', $subscription->getTable(), $subscription->getKeyName()), '=', sprintf('%s.%s', $subscription_user->getTable(), $subscription_user->subscription()->getForeignKey()))
                    ->where(sprintf('%s.%s', $subscription_user->getTable(), $subscription_user->user()->getForeignKey()), '=', $user_id);

                if(!is_null($inOnlyForDefaultUser) && is_bool($inOnlyForDefaultUser)){
                    if($inOnlyForDefaultUser){
                        $builder = $builder
                            ->where(sprintf('%s.is_default', $subscription_user->getTable()), '=', Utility::constant('status.1.slug'));
                    }else{
                        $builder = $builder
                            ->where(sprintf('%s.is_default', $subscription_user->getTable()), '!=', Utility::constant('status.1.slug'));
                    }
                }
            })
            ->where(sprintf('%s.%s', $subscription->getTable(), $subscription->property()->getForeignKey()), '=', $property_id)
            ->where(sprintf('%s.%s', $subscription_user->getTable(), $subscription_user->user()->getForeignKey()), '=', $user_id)
            ->where(sprintf('%s.category', $this->getTable()), '=', $facility_category)
            ->groupBy([sprintf('%s.%s', $this->getTable(), $this->subscription()->getForeignKey()), sprintf('%s.category', $this->getTable())])
            ->having('balance', '>', 0)
            ->get();


    }

    public function transactionsBySubscriptionAndUser($subscription_id, $user_id){

        $subscription = new Subscription();
        $subscription_user = new SubscriptionUser();

        return $this
            ->select(sprintf('%s.*', $this->getTable()))
            ->join($subscription->getTable(), function($query) use ($subscription_id, $subscription){
                $query
                    ->on(sprintf('%s.%s', $this->getTable(), $this->subscription()->getForeignKey()), '=', sprintf('%s.%s', $subscription->getTable(), $subscription->getKeyName()))
                    ->where(sprintf('%s.%s', $subscription->getTable(), $subscription->getKeyName()), '=', $subscription_id)
                    ->whereIn(sprintf('%s.status', $subscription->getTable()), $subscription->confirmStatus);
            })
            ->join($subscription_user->getTable(), function($query) use($user_id, $subscription, $subscription_user){
                $query
                    ->on(sprintf('%s.%s', $subscription->getTable(), $subscription->getKeyName()), '=', sprintf('%s.%s', $subscription_user->getTable(), $subscription_user->subscription()->getForeignKey()))
                    ->where(sprintf('%s.%s', $subscription_user->getTable(), $subscription_user->user()->getForeignKey()), '=', $user_id);
            })
            ->where(sprintf('%s.%s', $subscription->getTable(), $subscription->getKeyName()), '=', $subscription_id)
            ->where(sprintf('%s.%s', $subscription_user->getTable(), $subscription_user->user()->getForeignKey()), '=', $user_id)
            ->get();


    }

    public function autoReset($limit = 20, $test = false){

        $today = Carbon::now();

        $subscription = new Subscription();
        $builder = $subscription
            ->whereIn('status', $subscription->confirmStatus);

        if(!$test) {
            $builder = $builder->where('next_reset_complimentaries_date', '<=', $today);
        }

        $subscriptions = $builder->take($limit)->get();

        foreach($subscriptions as $subscription){

            try{

                $subscription->getConnection()->transaction(function () use($subscription, $today){

                    $subs = $subscription->with(['property'])->lockForUpdate()->find($subscription->getKey());

                    $property = $subs->property;

                    $next_reset_complimentaries_date = $property->subscriptionNextResetComplimentariesForNextMonth($subs->next_reset_complimentaries_date);

                    $this->reset($subs->getKey(), $subs->complimentaries);

                    $subs->fillable($subs->getRules(['next_reset_complimentaries_date'], false, true));
                    $subs->setAttribute('next_reset_complimentaries_date', $next_reset_complimentaries_date);
                    $subs->save();

                });

            }catch(ModelNotFoundException $e){



            }catch(ModelValidationException $e){



            }catch(IntegrityException $e){



            }catch(Exception $e){




            }

        }

    }

    public function add($subscription_id, $complimentaries = array()){

        foreach($complimentaries as $category => $value){

            $instance = new static();

            $attributes = [
                'subscription_id' => $subscription_id,
                'category' => $category,
                'credit' => $value,
                'debit' => 0.00

            ];

            $instance->fill($attributes);

            $instance->save();

        }

    }

    public function reset($subscription_id, $complimentaries = array()){

        if(!Utility::hasArray($complimentaries)){

            $this
                ->lockForUpdate()
                ->where($this->subscription()->getForeignKey(), '=', $subscription_id)
                ->delete();

        }else{

            $this
                ->lockForUpdate()
                ->where($this->subscription()->getForeignKey(), '=', $subscription_id)
                ->whereNotIn('category', array_keys($complimentaries))
                ->delete();

            foreach ($complimentaries as $category => $value){

                $instance = (new static())
                    ->lockForUpdate()
                    ->where($this->subscription()->getForeignKey(), '=', $subscription_id)
                    ->where('category', '=', $category)
                    ->first();

                if(is_null($instance)){
                    $instance = new static();
                }

                $attributes = [
                    'subscription_id' => $subscription_id,
                    'category' => $category,
                    'credit' => $value,
                    'debit' => 0.00

                ];

                $instance->fill($attributes);

                $instance->save();



            }

        }


    }


}