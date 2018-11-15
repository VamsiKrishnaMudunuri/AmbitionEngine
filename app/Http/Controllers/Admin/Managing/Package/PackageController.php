<?php

namespace App\Http\Controllers\Admin\Managing\Package;

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

use App\Models\Package;
use App\Models\Currency;

class PackageController extends ManagingController
{

    public function __construct()
    {

        parent::__construct();

    }

    public function index(Request $request, $property_id){

        try {

            $package = new Package();
            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);

            $package->setupPrime(${$this->singular()});

            ${$package->plural()} = $package->showAll(${$this->singular()}, [], !Utility::isExportExcel());

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
            $view = SmartView::excel(null, compact($this->singular(), $package->singular(), $package->plural()), Translator::transSmart('app.Packages', 'Packages'));
        }else{
            $view = SmartView::render(null, compact($this->singular(), $package->singular(), $package->plural()));
        }

        return $view;

    }

    public function edit(Request $request, $property_id, $id){

        try {

            $package = new Package();
            $currency = new Currency();

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$package->singular()} = Package::retrieve($id);
            ${$currency->singular()} = ${$currency->singular()}->getByQuote(${$this->singular()}->currency);


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), $package->singular(), $currency->singular()));

    }

    public function postEdit(Request $request, $property_id, $id){

        try {

            Package::edit($id, $request->all());

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

        return $this->responseIntended('admin::managing::package::index', array('property_id' => $property_id))->with(Sess::getKey('success'), Translator::transSmart('app.Package has been updated.', 'Package has been updated.'));

    }

    public function postStatus(Request $request, $property_id, $id){

        try {

            Package::toggleStatus($id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, [Translator::transSmart("app.Package status has been updated.", "Package status has been updated.")]);

    }

}