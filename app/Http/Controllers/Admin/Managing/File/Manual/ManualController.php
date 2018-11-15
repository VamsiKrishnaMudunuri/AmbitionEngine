<?php

namespace App\Http\Controllers\Admin\Managing\File\Manual;

use App\Models\Property;
use Exception;
use Translator;
use Sess;
use URL;
use Utility;
use SmartView;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Managing\ManagingController;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Sandbox;

class ManualController extends ManagingController
{

    public function __construct()
    {

        parent::__construct();


    }

    public function index(Request $request, $property_id){

        try {

            $sandbox = new Sandbox();
            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$sandbox->plural()} = ${$this->singular()}->showManuals();

            URL::setAdvancedLandingIntended(Utility::routeName(), [$property_id]);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact($this->singular(), $sandbox->plural()));

    }

    public function add(Request $request, $property_id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $sandbox = new Sandbox();

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(true, compact($this->singular(), $sandbox->singular()));

    }

    public function postAdd(Request $request, $property_id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $sandbox = ${$this->singular()}->addManual($request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return redirect()->route('admin::managing::file::manual::index', array('property_id' => $property_id))->with(Sess::getKey('success'), Translator::transSmart('app.Manual has been added.', 'Manual has been added.'));

    }

    public function edit(Request $request, $property_id, $id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $sandbox = ${$this->singular()}->getManualOrFail($id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), $sandbox->singular()));

    }

    public function postEdit(Request $request, $property_id, $id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $sandbox = ${$this->singular()}->editManual($id, $request->all());

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

        return $this->responseIntended('admin::managing::file::manual::index', array('property_id' => $property_id))->with(Sess::getKey('success'), Translator::transSmart('app.Manual has been updated.', 'Manual has been updated.'));


    }

    public function postDelete(Request $request, $property_id, $id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$this->singular()}->delManual($id);

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

        }

        return $this->responseIntended('admin::managing::file::manual::index', array('property_id' => $property_id))->with(Sess::getKey('success'), Translator::transSmart('app.Manual has been deleted.', 'Manual has been deleted.'));
    }


}
