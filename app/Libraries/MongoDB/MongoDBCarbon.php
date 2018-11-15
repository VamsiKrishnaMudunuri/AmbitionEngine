<?php

namespace App\Libraries\MongoDB;

use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;

class MongoDBCarbon extends Carbon {


    function fullTimestamp()
    {
       return $this->timestamp * 1000;

    }

    function toUTCDateTime(){

        return new UTCDateTime($this->fullTimestamp());

    }
}