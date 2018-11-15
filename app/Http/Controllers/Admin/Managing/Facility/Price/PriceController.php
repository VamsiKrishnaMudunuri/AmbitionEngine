<?php

namespace App\Http\Controllers\Admin\Managing\Facility\Price;

use Exception;
use URL;
use Translator;
use Sess;
use Utility;
use SmartView;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Managing\ManagingController;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Facility;
use App\Models\FacilityPrice;
use App\Models\Currency;
use App\Models\Wallet;

class PriceController extends ManagingController
{

    public function __construct()
    {

        parent::__construct();

    }

    public function index(Request $request, $property_id, $facility_id){

        try {

            $facility = new Facility();
            $facility_price = new FacilityPrice();
            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$facility->singular()} = $facility->getOneOrFail($facility_id);
            ${$facility_price->plural()} =  $facility_price->showAll(${$facility->singular()}, [], !Utility::isExportExcel());
            $rules = $facility_price->getAllRules(${$facility->singular()});

            URL::setAdvancedLandingIntended(Utility::routeName(), [${$this->singular()}->getKey(),  ${$facility->singular()}->getKey()]);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $view = null;

        if(Utility::isExportExcel()){
            $view = SmartView::excel(null, compact($this->singular(), $facility->singular(), $facility_price->singular(), $facility_price->plural(), 'rules'), Translator::transSmart('app.%s - %s - Prices',
                sprintf('%s - %s - Prices', ${$this->singular()}->name, ${$facility->singular()}->name,
                    false,
                    ['office' => ${$this->singular()}->name, 'facility' =>  ${$facility->singular()}->name])));
        }else{
            $view = SmartView::render(null, compact($this->singular(),  $facility->singular(), $facility_price->singular(), $facility_price->plural(), 'rules'));
        }

        return $view;


    }

    public function add(Request $request, $property_id, $facility_id, $rule){

        try {

            $facility = new Facility();
            $facility_price = new FacilityPrice();
            $currency = new Currency();
            $wallet = new Wallet();

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$facility->singular()} = $facility->getOneOrFail($facility_id);

            ${$facility_price->singular()}->isNotSupportedRuleAndFail($rule, ${$facility->singular()}->category);
            ${$facility_price->singular()}->status = Utility::constant('status.1.slug');
            ${$facility_price->singular()}->is_collect_deposit_offline = Utility::constant('status.0.slug');
            ${$facility_price->singular()}->rule = $rule;
            ${$currency->singular()} = ${$currency->singular()}->getByQuote(${$this->singular()}->currency);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), $facility->singular(), $facility_price->singular(), $wallet->singular(), $currency->singular()));

    }

    public function postAdd(Request $request, $property_id, $facility_id, $rule){


        try {

            $facility = new Facility();
            $facility_price = new FacilityPrice();

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$facility->singular()} = $facility->getOneOrFail($facility_id);

            ${$facility_price->singular()}->isNotSupportedRuleAndFail($rule, ${$facility->singular()}->category);

            FacilityPrice::add(${$facility->singular()}, $rule, $request->all());

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

        return redirect()->route('admin::managing::facility::price::index', array('property_id' => $property_id, 'facility_id' => $facility_id))->with(Sess::getKey('success'), Translator::transSmart("app.Price has been added.", "Price has been added."));


    }

    public function edit(Request $request, $property_id, $facility_id, $id){

        try {

            $facility = new Facility();
            $currency = new Currency();
            $wallet = new Wallet();

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$facility->singular()} = $facility->getOneOrFail($facility_id);
            $facility_price = FacilityPrice::retrieve($id);
            ${$currency->singular()} = ${$currency->singular()}->getByQuote(${$this->singular()}->currency);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), $facility->singular(), $facility_price->singular(), $wallet->singular(), $currency->singular()));

    }

    public function postEdit(Request $request, $property_id, $facility_id, $id){

        try {

            $facility = new Facility();
            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$facility->singular()} = $facility->getOneOrFail($facility_id);
            FacilityPrice::edit($id, $request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return $this->responseIntended('admin::managing::facility::price::index', array('property_id' => $property_id, 'facility_id' => $facility_id))->with(Sess::getKey('success'), Translator::transSmart("app.Price has been updated.", "Price has been updated."));

    }

    public function postStatus(Request $request, $property_id, $facility_id, $id){

        try {

            FacilityPrice::toggleStatus($id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, [Translator::transSmart("app.Price status has been updated.", "Price status has been updated.")]);

    }

    public function postDelete(Request $request, $property_id, $facility_id, $id){

        try {

            FacilityPrice::del($id);

        } catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        } catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }finally{
	
	        $request->flush();
	
        }

        return $this->responseIntended('admin::managing::facility::price::index', array('property_id' => $property_id, 'facility_id' => $facility_id))->with(Sess::getKey('success'), Translator::transSmart("app.Price has been deleted.", "Price has been deleted."));

    }

}