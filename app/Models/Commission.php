<?php

namespace App\Models;

use Str;
use Cms;
use CLDR;
use Utility;
use Exception;

use Illuminate\Support\Arr;
use App\Libraries\Model\Model;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Commission extends Model
{
    protected $autoPublisher = true;

    public static $rules = array(
        'currency' => 'required|max:3',
        'role' => 'required|max:100',
        'country' => 'required|max:3',
        'status' => 'required|boolean'
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public static $sandbox = array();

    public function __construct(array $attributes = array())
    {
        static::$relationsData = [
            'commissionItems' => array(self::HAS_MANY, CommissionItem::class),
        ];

        parent::__construct($attributes);
    }

    public function beforeValidate()
    {
        return true;
    }

    public function beforeSave()
    {
        return true;
    }

    public function setExtraRules()
    {
        return array();
    }

    public function getCountryNameAttribute($value)
    {
        return CLDR::getCountries()[$this->country];
    }

    public function getByCountryCode($countryCode)
    {
        $this->with(['commissionItems']);

        $instance = $this->where('country', '=', $countryCode)->get();

        return $instance;
    }

    /**
     * Limited the data by Role.
     *
     * @param $query
     * @param null $role
     * @return mixed
     */
    public function scopeByRole($query, $role = null)
    {
        if (Utility::hasArray($role)) {
            $query->whereIn('role', $role);
        } else {
            $query->where('role', '=', $role);
        }

        return $query;
    }

    /**
     * Base query to fetch commission data.
     *
     * @param null $country
     *
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws IntegrityException
     * @throws ModelValidationException
     */
    public function getCommissionQuery($country = null)
    {
        try {
            $builder = (new static())->newQuery();

            $builder->with(['commissionItems']);
            $builder->where('country', '=', $country);

            return $builder;

        } catch(ModelNotFoundException $e) {
            throw $e;

        } catch(ModelValidationException $e) {
            throw $e;

        } catch(IntegrityException $e) {
            throw $e;

        } catch(Exception $e) {
            throw $e;

        }
    }

    /**
     * Get commission by Roles.
     *
     * @param string|array $role E.g: agent, salesperson, user or ['agent', 'user']
     * @param null $country E.g: MY, AF. Will fallback to MY if no parameter passed to country.
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws IntegrityException
     * @throws ModelValidationException
     */
    public function getCommissionByRole($role = null, $country = null)
    {
        try {

            $builder = $this->getCommissionQuery($country)->byRole($role);

            return $builder->get();

        } catch(ModelNotFoundException $e) {
            throw $e;

        } catch(ModelValidationException $e) {
            throw $e;

        } catch(IntegrityException $e) {
            throw $e;

        } catch(Exception $e) {
            throw $e;

        }
    }

    /**
     * Get agent commission only.
     *
     * @param null $country
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws IntegrityException
     * @throws ModelValidationException
     */
    public function getAgentCommission($country = null)
    {
        try {
            return $this->getCommissionByRole(Utility::constant('commission_schema.agent.slug'), $country)->first();

        } catch(ModelNotFoundException $e) {
            throw $e;

        } catch(ModelValidationException $e) {
            throw $e;

        } catch(IntegrityException $e) {
            throw $e;

        } catch(Exception $e) {
            throw $e;

        }
    }

    /**
     * Get salesperson commission.
     *
     * @param null $country
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws IntegrityException
     * @throws ModelValidationException
     */
    public function getSalespersonCommission($country = null)
    {
        try {
            return $this->getCommissionByRole(Utility::constant('commission_schema.salesperson.slug'), $country)->first();

        } catch(ModelNotFoundException $e) {
            throw $e;

        } catch(ModelValidationException $e) {
            throw $e;

        } catch(IntegrityException $e) {
            throw $e;

        } catch(Exception $e) {
            throw $e;

        }
    }

    /**
     * Get member commission.
     *
     * @param null $country
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws IntegrityException
     * @throws ModelValidationException
     */
    public function getMemberCommission($country = null)
    {
        try {
            return $this->getCommissionByRole(Utility::constant('commission_schema.user.slug'), $country)->first();

        } catch(ModelNotFoundException $e) {
            throw $e;

        } catch(ModelValidationException $e) {
            throw $e;

        } catch(IntegrityException $e) {
            throw $e;

        } catch(Exception $e) {
            throw $e;

        }
    }

    /**
     * Get member commission's item by Tier
     *
     * @param int $tier E.g: 1, 2, 3, 4
     * @param null $country
     * @return mixed
     * @throws IntegrityException
     * @throws ModelValidationException
     */
    public function getMemberCommissionByTier($tier = 1, $country = null)
    {
        try {

            $collection = $this->getMemberCommission($country)->commissionItems->filter(function ($value, $key) use ($tier) {
                return $value->type_number == $tier;
            })->first();

            $collection->load(['commission']);

            return $collection;

        } catch(ModelNotFoundException $e) {
            throw $e;

        } catch(ModelValidationException $e) {
            throw $e;

        } catch(IntegrityException $e) {
            throw $e;

        } catch(Exception $e) {
            throw $e;

        }
    }

    /**
     * Get member commission by amount where fall in which range of amount under which tier.
     *
     * @param $amount
     * @param null $country
     *
     * @return mixed
     * @throws IntegrityException
     * @throws ModelValidationException
     */
    public function getMemberCommissionInRange($amount, $country = null)
    {
        try {

            if (is_null($amount)) {
                throw new \InvalidArgumentException(Translator::transSmart("app.Amount parameter is required.", "Amount parameter is required."));
            }

            $collection = $this->getMemberCommission($country)->commissionItems->filter(function ($value, $key) use ($amount) {
                if ($value->min && $value->max == 0) {
                    return $amount >= $value->min;
                }

                return $amount >= $value->min && $amount <= $value->max;
            });

            if ($collection->isNotEmpty()) {
                return $collection->load(['commission'])->first();
            }

            return $collection;

        } catch(ModelNotFoundException $e) {
            throw $e;

        } catch(ModelValidationException $e) {
            throw $e;

        } catch(IntegrityException $e) {
            throw $e;

        } catch(Exception $e) {
            throw $e;

        }
    }

    /**
     * Get salesperson's commission by month of contract.
     *
     * @param $month
     * @param null $country
     *
     * @return mixed
     * @throws IntegrityException
     * @throws ModelValidationException
     */
    public function getSalespersonCommissionInRange($month, $country = null)
    {
        try {

            if (is_null($month)) {
                throw new \InvalidArgumentException(Translator::transSmart("app.Month parameter is required.", "Month parameter is required."));
            }

            $collection = $this->getSalespersonCommission($country)->commissionItems->filter(function ($value, $key) use ($month) {
                if ($value->min && $value->max == 0) {
                    return $month >= $value->min;
                }

                return $month >= $value->min && $month < $value->max;
            });

            if ($collection->isNotEmpty()) {
                return $collection->load(['commission'])->first();
            }

            return $collection;

        } catch(ModelNotFoundException $e) {
            throw $e;

        } catch(ModelValidationException $e) {
            throw $e;

        } catch(IntegrityException $e) {
            throw $e;

        } catch(Exception $e) {
            throw $e;

        }
    }

    public function showAll($order = [], $paging = true)
    {
        try {
            $and = [];
            $or = [];

            $instance = (new static());

            $inputs = Utility::parseSearchQuery(function($key, $value, $callback) {

                switch($key){

                    default:
                        $value = sprintf('%%%s%%', $value);
                        break;
                }

                $callback($value, $key);
            });

            $and[] = ['operator' => 'like', 'fields' => $inputs];

            $instance->with(['commissionItems']);

            $instance = $this->show($and, $or, $order, $paging);

        } catch(InvalidArgumentException $e) {
            throw $e;

        } catch(Exception $e) {
            throw $e;

        }

        return $instance;
    }
	
	public function showAllForActive($order = [], $paging = true)
	{
		try {
			$and = [];
			$or = [];
			
			$instance = (new static());
			
			$inputs = Utility::parseSearchQuery(function($key, $value, $callback) {
				
				switch($key){
					
					default:
						$value = sprintf('%%%s%%', $value);
						break;
				}
				
				$callback($value, $key);
			});
			
			$and[] = ['operator' => 'like', 'fields' => $inputs];
			
			if(!Utility::hasArray($order)){
				$order['country'] = "ASC";
			}
			
			$instance->with(['commissionItems']);
			
			$instance = $this
				->where('status', '=', Utility::constant('status.1.slug'))
				->show($and, $or, $order, $paging);
			
		} catch(InvalidArgumentException $e) {
			throw $e;
			
		} catch(Exception $e) {
			throw $e;
			
		}
		
		return $instance;
	}

    public function setup($countryCode = null)
    {
        try {

            $this->getConnection()->transaction(function () use ($countryCode) {

                $commissions = Utility::constant('commission_structure');

                foreach($commissions as $key => $commission) {

                    $role = $key;
                    $builder = (new static())->newQuery();

                    $builder
                        ->where('role', '=', $role)
                        ->where('country', '=', $countryCode);

                    $instance = $builder->first();

                    if (is_null($instance)) {

                        $instance = (new static());

                        $attributes = array(
                            'currency' => is_null($countryCode) ? config('currency.default') : CLDR::getCurrencyByCountryCode($countryCode),
                            'country' => $countryCode,
                            'role' => $role,
                            'status' => Utility::constant('status.1.slug')
                        );

                        $instance->fill($attributes);

                        $instance->save();

                        foreach ($commission as $item) {

                            $commisionItem = new CommissionItem();
                            $commisionItem->percentage = $item['percentage'];
                            $commisionItem->type = $item['type'];
                            $commisionItem->type_number = $item['type_number'];
                            $commisionItem->min = $item['min'];
                            $commisionItem->max = $item['max'];
                            $instance->commissionItems()->save($commisionItem);

                        }
                    }
                }
            });

        } catch(ModelNotFoundException $e) {
            throw $e;

        } catch(ModelValidationException $e) {
            throw $e;

        } catch(IntegrityException $e) {
            throw $e;

        } catch(Exception $e) {
            throw $e;

        }
    }

    public static function edit($id, $attributes)
    {
        try {

            $instance = new static();

            $instance->with([])->checkOutOrFail($id,  function ($model, $cb) use ($instance, $attributes) {
                $model->fill($attributes);

            }, function($model, $status){

            }, function($model)  use (&$instance){

                $instance = $model;

            });

        } catch (ModelNotFoundException $e) {
            throw $e;

        } catch (ModelVersionException $e) {

            throw $e;

        } catch (ModelValidationException $e) {
            throw $e;

        } catch (Exception $e) {
            throw $e;

        }

        return $instance;
    }
}
