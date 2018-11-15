<?php

namespace App\Businesses\Admin\Report\Reservation;


use Exception;
use InvalidArgumentException;
use URL;
use Auth;
use Translator;
use Sess;
use Session;
use Carbon\Carbon;
use Utility;
use SmartView;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\Collection;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Property;
use App\Models\Facility;
use App\Models\Reservation;

class RoomController
{

    public function __construct()
    {



    }

    public function listingByProperty($property_id, $from_date = null, $to_date = null){

        try {

            $property = (new Property())->getOneOrFail($property_id);
            $facility = new Facility();
            $reservation = new Reservation();

            $reservations = new Collection();
            $initial_from_date =  $property->today()->startOfMonth();
            $initial_to_date = $initial_from_date->copy()->endOfMonth();

            if(Utility::hasString($from_date)){
                $initial_from_date = $property->localDate( $from_date )->startOfDay();
            }

            if(Utility::hasString($to_date)){
                $initial_to_date = $property->localDate( $to_date )->endOfDay();
            }

            $property->setAttribute('from_date', $initial_from_date);
            $property->setAttribute('to_date', $initial_to_date);
            $property->validateModels([['model' => $property, 'rules' => ['from_date' => 'required|date', 'to_date' => 'required|date|greater_than_datetime_equal:from_date'], 'customMessages' => []]]);


            $app_from_date = $property->localToAppDate($initial_from_date->copy());
            $app_to_date = $property->localToAppDate($initial_to_date->copy());


            $reservations = $reservation
                ->selectRaw(sprintf('%s.*', $reservation->getTable()))
                ->with(['user', 'property', 'facility', 'facilityUnit', 'complimentaries', 'walletTransactionsCancelledWithQuery'])
                ->join($facility->getTable(), sprintf('%s.%s', $reservation->getTable(), $reservation->facility()->getForeignKey()), '=', sprintf('%s.%s', $facility->getTable(), $facility->getKeyName()))
                ->where(sprintf('%s.category', $facility->getTable()), '=',  Utility::constant('facility_category.3.slug'))
                ->where(sprintf('%s.start_date', $reservation->getTable()), '>=', $app_from_date)
                ->where(sprintf('%s.end_date', $reservation->getTable()), '<=', $app_to_date)
                ->orderBy(sprintf('%s.start_date', $reservation->getTable()), 'ASC')
                ->get();


            $from_date = $initial_from_date->toDateTimeString();
            $to_date = $initial_to_date->toDateTimeString();


        }catch(ModelNotFoundException $e){

            throw $e;

        }catch(ModelValidationException $e){


            throw $e;

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return compact($property->singular(), 'reservations', 'from_date', 'to_date');

    }



}