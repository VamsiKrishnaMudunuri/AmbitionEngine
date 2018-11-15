<?php

namespace App\Models;

use Exception;
use Translator;
use Utility;
use Hash;
use Config;
use CLDR;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;

use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

class Agent extends User
{
    
}