<?php

namespace App\Http\Controllers\Admin\Managing\Facility\Item;

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

use App\Models\Sandbox;
use App\Models\Facility;

class ItemController extends ManagingController
{

    public function __construct()
    {

        parent::__construct();

    }

    public function index(Request $request, $property_id){

        try {

            $facility = new Facility();
            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$facility->plural()} = $facility->showAll(${$this->singular()}, [], !Utility::isExportExcel());

            URL::setAdvancedLandingIntended(Utility::routeName(), [${$this->singular()}->getKey()]);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $view = null;

        if(Utility::isExportExcel()){
            $view = SmartView::excel(null, compact($this->singular(), $facility->singular(), $facility->plural()), Translator::transSmart('app.%s - Facilities', sprintf('%s - Facilities', ${$this->singular()}->name, false, ['name' => ${$this->singular()}->name ])));
        }else{
            $view = SmartView::render(null, compact($this->singular(), $facility->singular(),  $facility->plural()));
        }

        return $view;


    }

    public function add(Request $request, $property_id, $category){

        try {


            $facility = new Facility();
            $sandbox = new Sandbox();

            $facility->isNotSupportedCategoryAndFail($category);
            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);

            $facility->seat = 1;
            $facility->status = Utility::constant('status.1.slug');
            $facility->tax_name = Utility::constantDefault('tax', 'name');
            $facility->tax_value = Utility::constantDefault('tax', 'value');

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), $facility->singular(), $sandbox->singular(), 'category'));

    }

    public function postAdd(Request $request, $property_id, $category){


        try {

            $facility = new Facility();
            $facility->isNotSupportedCategoryAndFail($category);
            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            Facility::add(${$this->singular()}, $category, $request->all());

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

        } catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return redirect()->route('admin::managing::facility::item::index', array('property_id' => $property_id))->with(Sess::getKey('success'),  Translator::transSmart("app.Facility has been added.", "Facility has been added."));

    }

    public function edit(Request $request, $property_id, $id){

        try {

            $sandbox = new Sandbox();

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $facility = Facility::retrieve($id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), $facility->singular(), $sandbox->singular()));

    }

    public function postEdit(Request $request, $property_id, $id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            Facility::edit($id, $request->all());

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

        return $this->responseIntended('admin::managing::facility::item::index', array('property_id' => $property_id))->with(Sess::getKey('success'),  Translator::transSmart("app.Facility has been updated.", "Facility has been updated."));
    }

    public function postStatus(Request $request, $property_id, $id){

        try {

            Facility::toggleStatus($id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, [Translator::transSmart("app.Facility status has been updated.", "Facility status has been updated.")]);

    }

    public function postDelete(Request $request, $property_id, $id){

        try {

            Facility::del($id);

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

        } catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }finally{
	
	        $request->flush();
	
        }

        return $this->responseIntended('admin::managing::facility::item::index', array('property_id' => $property_id))->with(Sess::getKey('success'), Translator::transSmart("app.Facility has been deleted.", "Facility has been deleted."));

    }

}