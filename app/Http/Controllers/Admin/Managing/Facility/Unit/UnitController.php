<?php

namespace App\Http\Controllers\Admin\Managing\Facility\Unit;

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
use App\Models\FacilityUnit;

class UnitController extends ManagingController
{

    public function __construct()
    {

        parent::__construct();

    }

    public function index(Request $request, $property_id, $facility_id){

        try {

            $facility = new Facility();
            $facility_unit = new FacilityUnit();
            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$facility->singular()} = $facility->getOneOrFail($facility_id);
            ${$facility_unit->plural()} =  $facility_unit->showAll(${$facility->singular()}, [], !Utility::isExportExcel());


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

            $view = SmartView::excel(null, compact($this->singular(), $facility->singular(), $facility_unit->singular(), $facility_unit->plural()), Translator::transSmart('app.%s - %s - Quantities',
                sprintf('%s - %s - Quantities', ${$this->singular()}->name, ${$facility->singular()}->name,
                    false,
                    ['office' => ${$this->singular()}->name, 'facility' =>  ${$facility->singular()}->name])));

        }else{

            $view = SmartView::render(null, compact($this->singular(),  $facility->singular(), $facility_unit->singular(), $facility_unit->plural()));

        }

        return $view;

    }

    public function add(Request $request, $property_id, $facility_id){

        try {

            $facility = new Facility();
            $facility_unit = new FacilityUnit();

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$facility->singular()} = $facility->getOneOrFail($facility_id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), $facility->singular(), $facility_unit->singular()));

    }

    public function postAdd(Request $request, $property_id, $facility_id){


        try {

            $facility = new Facility();
            $facility_unit = new FacilityUnit();

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$facility->singular()} = $facility->getOneOrFail($facility_id);

            $countForNewEntry = FacilityUnit::add(${$facility->singular()}, $request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return redirect()->route('admin::managing::facility::unit::index', array('property_id' => $property_id, 'facility_id' => $facility_id))->with(Sess::getKey('success'), Translator::transSmart("app.%s units have been added.", sprintf("%s units have been added.", $countForNewEntry), false, ['unit' => $countForNewEntry]));


    }

    public function edit(Request $request, $property_id, $facility_id, $id){

        try {

            $facility = new Facility();

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$facility->singular()} = $facility->getOneOrFail($facility_id);
            $facility_unit = FacilityUnit::retrieve($id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), $facility->singular(), $facility_unit->singular()));

    }

    public function postEdit(Request $request, $property_id, $facility_id, $id){

        try {

            $facility = new Facility();
            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$facility->singular()} = $facility->getOneOrFail($facility_id);
            FacilityUnit::edit($id, $request->all());

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

        return $this->responseIntended('admin::managing::facility::unit::index', array('property_id' => $property_id, 'facility_id' => $facility_id))->with(Sess::getKey('success'), Translator::transSmart("app.Unit has been updated.", "Unit has been updated."));

    }

    public function postStatus(Request $request, $property_id, $facility_id, $id){

        try {

            FacilityUnit::toggleStatus($id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, [Translator::transSmart("app.Unit's status has been updated.", "Unit's status has been updated.")]);

    }

    public function postDelete(Request $request, $property_id, $facility_id, $id){

        try {

            $facility = new Facility();
            ${$facility->singular()} = $facility->getOneOrFail($facility_id);
            FacilityUnit::del($facility, $id);

        } catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        }catch(IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }finally{
	
	        $request->flush();
	
        }

        return $this->responseIntended('admin::managing::facility::unit::index', array('property_id' => $property_id, 'facility_id' => $facility_id))->with(Sess::getKey('success'), Translator::transSmart("app.Unit has been deleted.", "Unit has been deleted."));

    }

}