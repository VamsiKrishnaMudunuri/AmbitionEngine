<?php

namespace App\Http\Controllers\Member\Wallet;

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
use Illuminate\Http\JsonResponse;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

use App\Models\Member;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Transaction;
use App\Models\Currency;

class WalletController extends Controller
{

    public function __construct()
    {
        parent::__construct(new Member());
    }

    public function index(Request $request){

        try {


            $user = Auth::user();
            ${$this->singular()} = (new Member())->getWithWalletOrFail($user->getKey());


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular()));


    }

    public function topUp(Request $request){

        try {

            $user = Auth::user();

            $wallet = new Wallet();
            $wallet_transaction = new WalletTransaction();
            $base_currency = new Currency();
            $quote_currency = new Currency();
            $transaction = new Transaction();

            ${$this->singular()} = $this->getModel()->getWithVaultOrFail($user->getKey());
            ${$wallet->singular()} = $wallet->getByUser(${$this->singular()}->getKey());

            $base_currency = $base_currency->getByQuoteOrFail($wallet->currency);
            $quote_currency = $quote_currency->getByQuoteOrFail(${$this->singular()}->currency);

            if(!${$wallet->singular()}->exists){
                ${$this->singular()}->upsertWallet();
            }

            if(${$this->singular()}->hasVault()){
                $transaction->enableUseOfExistingTokenForm();
                $transaction->setCardNumber(${$this->singular()}->vault->payment->card_number);
            }

            $transaction->initializeClientTokenOrFail(${$wallet->singular()}->merchant_id);


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(PaymentGatewayException $e){

            Sess::setErrors($e->getMessage());

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact($this->singular(), $wallet->singular(), $wallet_transaction->singular(), 'base_currency', 'quote_currency', $transaction->singular()));

    }

    public function postTopUp(Request $request){

        try {

            $user = Auth::user();
            $wallet = new Wallet();

            ${$this->singular()} = $this->getModel()->findOrFail($user->getKey());
            ${$wallet->singular()} = $wallet->getByUserOrFail($user->getKey());
            ${$wallet->singular()}->topUp(${$wallet->singular()}->getKey(), ${$this->singular()}->currency,  $request->all(), Utility::constant('payment_method.2.slug'));

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        } catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        }catch(PaymentGatewayException $e){

            $this->throwPaymentGatewayException($request, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $message = Translator::transSmart("You have successfully topped up your wallet.", "You have successfully topped up your wallet.");
        $response = $this->responseIntended(Domain::route('member::wallet::index'), array())->with(Sess::getKey('success'), $message);

        if(Utility::isJsonRequest()){
            $response = new JsonResponse($message);
        }

        return $response;


    }

}
