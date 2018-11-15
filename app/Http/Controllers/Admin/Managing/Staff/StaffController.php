<?php

namespace App\Http\Controllers\Admin\Managing\Staff;

use Exception;
use URL;
use Translator;
use Sess;
use Utility;
use SmartView;
use Config;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Controllers\Admin\Managing\ManagingController;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

use App\Models\Temp;
use App\Models\Member;
use App\Models\Sandbox;
use App\Models\Currency;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Transaction;
use App\Models\PropertyUser;

class StaffController extends ManagingController
{

    public function __construct()
    {

        parent::__construct();

    }

    public function index(Request $request, $property_id){

        try {

            $member = new Member();
            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$member->plural()} = $member->showAllForStaffOnlyByProperty((new Temp())->getCompanyDefault()->getKey(), ${$this->singular()}->getKey(), [], !Utility::isExportExcel());

            URL::setAdvancedLandingIntended(Utility::routeName(), [$property_id]);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $view = null;

        if(Utility::isExportExcel()){
            $view = SmartView::excel(null, compact($this->singular(), $member->singular(), $member->plural()), Translator::transSmart('app.Staff', 'Staff'));
        }else{
            $view = SmartView::render(null, compact($this->singular(), $member->singular(), $member->plural()));
        }

        return $view;

    }

    public function edit(Request $request, $property_id, $id){

        try {

            $member = new Member();
            $sandbox = new Sandbox();

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$member->singular()} = Member::retrieve($id);
	
	
	        $activeMenus = (new Temp())->getPropertyMenuAll();
	
	        $properties = [];
	
	        foreach($activeMenus as $countries){
		        foreach($countries as $office){
			        $properties[] = $office;
		        }
	        }
	        
        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), $member->singular(), $sandbox->singular(), 'properties', 'id'));

    }

    public function postEdit(Request $request, $property_id, $id){

        try {

            Member::edit($id, $request->all());

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

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return $this->responseIntended('admin::managing::staff::index', array('property_id' => $property_id))->with(Sess::getKey('success'), Translator::transSmart('app.Staff has been updated.', 'Staff has been updated.'));

    }

    public function editNetwork(Request $request, $property_id, $id){

        try {

            $member = new Member();

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$member->singular()} = Member::retrieve($id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), $member->singular()));

    }

    public function postEditNetwork(Request $request, $property_id,  $id){

        try {

            Member::updateNetwork($id, $request->all());

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

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return $this->responseIntended('admin::managing::staff::index', array('property_id' => $property_id))->with(Sess::getKey('success'), Translator::transSmart('app.Network configuration has been updated.', 'Network configuration has been updated.'));
    }

    public function editPrinter(Request $request, $property_id, $id){

        try {

            $member = new Member();

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$member->singular()} = Member::retrieve($id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), $member->singular()));

    }

    public function postEditPrinter(Request $request, $property_id, $id){

        try {

            Member::updatePrinter($id, $request->all());

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

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return $this->responseIntended('admin::managing::staff::index', array('property_id' => $property_id))->with(Sess::getKey('success'), Translator::transSmart('app.Printer configuration has been updated.', 'Printer configuration has been updated.'));

    }

    public function postStatus(Request $request, $property_id, $id)
    {

        try {

            Member::toggleStatus($id);

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (Exception $e) {


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, ['message' => Translator::transSmart("app.Member's status has been updated.", "Member's status has been updated.")]);

    }

    public function postAssignManager(Request $request, $property_id, $id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            (new PropertyUser())->assignPersonInCharge($property_id, $id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        }catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, []);

    }

    public function profile(Request $request, $property_id, $id){

        try {

            $member = new Member();
            $sandbox = new Sandbox();

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$member->singular()} = $member->getWithWalletByIDOrFail($id);


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), $member->singular(), $sandbox->singular(), 'id'));

    }

    public function wallet(Request $request, $property_id, $id){

        try {

            $member = new Member();
            $wallet = new Wallet();
            $wallet_transaction = new WalletTransaction();
            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$member->singular()} = $member->findOrFail($id);
            ${$member->singular()}->upsertWallet();

            ${$wallet->singular()} = $wallet->getByUserOrFail(${$member->singular()}->getKey());

            ${$wallet_transaction->plural()} = $wallet_transaction->showAll(${$wallet->singular()});

            URL::setAdvancedLandingIntended(Utility::routeName(), [$property_id, $id]);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $view = null;

        if(Utility::isExportExcel()){
            $view = SmartView::excel(null, compact($this->singular(), $member->singular(), $wallet->singular(), $wallet_transaction->plural()), Translator::transSmart('app.Wallet Transactions', 'Wallet Transactions'));
        }else{
            $view = SmartView::render(null, compact($this->singular(), $member->singular(), $wallet->singular(), $wallet_transaction->plural()));
        }

        return $view;

    }

    public function topUpWallet(Request $request, $property_id, $id){

        try {

            $member = new Member();
            $wallet = new Wallet();
            $wallet_transaction = new WalletTransaction();
            $base_currency = new Currency();
            $quote_currency = new Currency();
            $transaction = new Transaction();


            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$member->singular()} = $member->getWithVaultOrFail($id);
            ${$wallet->singular()} = $wallet->getByUserOrFail(${$member->singular()}->getKey());
            $base_currency = $base_currency->getByQuoteOrFail($wallet->currency);
            $quote_currency = $quote_currency->getByQuoteOrFail(${$this->singular()}->currency);

            if(${$member->singular()}->hasVault()){
                $transaction->enableUseOfExistingTokenForm();
                $transaction->setCardNumber(${$member->singular()}->vault->payment->card_number);
            }

            $transaction->initializeClientTokenOrFail(${$wallet->singular()}->merchant_id);


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(PaymentGatewayException $e){

            Sess::setErrors($e->getMessage());

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact($this->singular(), $member->singular(), $wallet->singular(), $wallet_transaction->singular(), 'base_currency', 'quote_currency', $transaction->singular()));

    }

    public function postTopUpWallet(Request $request, $property_id, $id){

        try {

            $member = new Member();
            $wallet = new Wallet();

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$member->singular()} = $member->findOrFail($id);
            ${$wallet->singular()} = $wallet->getByUserOrFail(${$member->singular()}->getKey());
            ${$wallet->singular()}->topUp(${$wallet->singular()}->getKey(), ${$this->singular()}->currency, $request->all());

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

        return redirect()
            ->to(URL::getAdvancedLandingIntended('admin::managing::staff::wallet', [$property_id, $id],  URL::route('admin::managing::staff::wallet', array('property_id' => $property_id, 'id' => $id))))
            ->with(Sess::getKey('success'), Translator::transSmart("app.You have successfully topped up the wallet.", "You have successfully topped up the wallet."));

    }

    public function editWalletTransaction(Request $request, $property_id, $user_id, $id){

        try {

            $member = new Member();
            $wallet = new Wallet();
            $wallet_transaction = new WalletTransaction();
            $base_currency = new Currency();
            $quote_currency = new Currency();
            $transaction = new Transaction();

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$member->singular()} = $member->getWithVaultOrFail($user_id);
            ${$wallet->singular()} = $wallet->getByUserOrFail(${$member->singular()}->getKey());
            ${$wallet_transaction->singular()} = WalletTransaction::retrieve($id);
            $base_currency = $base_currency->getByQuoteOrFail(${$wallet_transaction->singular()}->base_currency);
            $quote_currency = $quote_currency->getByQuoteOrFail(${$wallet_transaction->singular()}->quote_currency);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), $member->singular(), $wallet->singular(),  $wallet_transaction->singular(), 'base_currency', 'quote_currency'));

    }

    public function postEditWalletTransaction(Request $request, $property_id, $user_id,  $id){

        try {

            $member = new Member();
            $wallet = new Wallet();

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$member->singular()} = $member->findOrFail($user_id);
            ${$wallet->singular()} = $wallet->getByUserOrFail(${$member->singular()}->getKey());

            WalletTransaction::edit($id, $request->all());

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

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return redirect()
            ->to(URL::getAdvancedLandingIntended('admin::managing::staff::wallet', [$property_id, $user_id],  URL::route('admin::managing::member::wallet', array('property_id' => $property_id, 'id' => $user_id))))
            ->with(Sess::getKey('success'), Translator::transSmart("app.Transaction record has been updated.", "Transaction record has been updated."));

    }

}