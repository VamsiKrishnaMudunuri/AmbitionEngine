<?php

namespace App\Models\Traits;

use Utility;
use Config;

trait Proration{


    public $invoice_start_date = null;
    public $invoice_end_date = null;


    public function setupInvoice($property, $start_date, $end_date = null){


        if($property->exists) {
            if (!is_null($start_date)) {
                $this->invoice_start_date = $property->subscriptionInvoiceStartDateTime($start_date);
            }

            if (is_null($end_date)) {
                if (!is_null($this->invoice_start_date)) {
                    $this->invoice_end_date = $property->subscriptionInvoiceEndDateTime($this->invoice_start_date->copy()->lastOfMonth());
                }
            } else {
                $this->invoice_end_date = $property->subscriptionInvoiceEndDateTime($end_date);
            }
        }

    }

    public function calculateForInvoice($price, $is_tranditional_rental_formula = false){

        if( !is_null($this->invoice_start_date) && !is_null($this->invoice_end_date) ){


            $reserving_days = $this->invoice_end_date->diffInDays($this->invoice_start_date) + 1;
            $full_month_days = $this->invoice_end_date->daysInMonth;

            if($is_tranditional_rental_formula){
                $full_month_days = $this->invoice_start_date->daysInMonth;
            }

            $price = ($price / $full_month_days) * $reserving_days;

        }


        return Utility::round($price, Config::get('money.precision'));

    }

    public function getInvoiceStartDate(){

        return (!is_null($this->invoice_start_date)) ? $this->invoice_start_date->copy() : null;

    }

    public function getInvoiceEndDate(){

        return (!is_null($this->invoice_end_date)) ? $this->invoice_end_date->copy() : null;

    }


}