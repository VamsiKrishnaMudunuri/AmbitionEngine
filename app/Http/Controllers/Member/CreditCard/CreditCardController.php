<?php

namespace App\Http\Controllers\Member\CreditCard;

use Exception;
use Translator;
use CLDR;
use Sess;
use Auth;
use Utility;
use SmartView;
use Domain;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

use App\Models\Member;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\Vault;

class CreditCardController extends Controller
{

    public function __construct()
    {
        parent::__construct(new Member());
    }

    public function index(Request $request){

        try {


            $user = Auth::user();
            ${$this->singular()} = $this->getModel()->getWithVaultOrFail($user->getKey());


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular()));


    }

    public function edit(Request $request){

        try {


            $wallet = new Wallet();
            $transaction = new Transaction();

            $transaction->initializeClientTokenOrFail($wallet->merchant_id);


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(PaymentGatewayException $e){

            Sess::setErrors($e->getMessage());

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact($transaction->singular()));

    }

    public function postEdit(Request $request){

        try {

            $user = Auth::user();
            (new Vault())->updatePaymentMethodByUser($user->getKey(), $request->all());

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(PaymentGatewayException $e){

            $this->throwPaymentGatewayException($request, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return $this->responseIntended(Domain::route('member::creditcard::index'), array())->with(Sess::getKey('success'), Translator::transSmart('app.Your credit card has been updated.', 'Your credit card has been updated.'));


    }

}
