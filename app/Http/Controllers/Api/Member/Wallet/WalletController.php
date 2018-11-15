<?php

namespace App\Http\Controllers\Api\Member\Wallet;


use Exception;
use Auth;
use URL;
use Translator;
use Sess;
use Utility;
use SmartView;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Member;
use App\Models\Wallet;

class WalletController extends Controller
{

    public function __construct()
    {

        parent::__construct();

    }

    public function myWallet(Request $request){

        try {

            $member = Auth::user();
            $wallet = new Wallet();
            ${$wallet->plural()} = $wallet->getMyAndShareBySubscriptionInDetails($member->getKey(), Translator::transSmart('app.My Wallet', 'My Wallet'));


        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact($wallet->plural()));

    }


}