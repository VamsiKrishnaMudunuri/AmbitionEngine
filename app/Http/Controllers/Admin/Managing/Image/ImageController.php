<?php

namespace App\Http\Controllers\Admin\Managing\Image;

use Exception;
use Translator;
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

class ImageController extends ManagingController
{

    public function __construct()
    {

        parent::__construct();


    }

    public function index(Request $request, $property_id){

        try {

            $sandbox = new Sandbox();
            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$sandbox->plural()} = ${$this->singular()}->showImages();


            URL::setAdvancedLandingIntended(Utility::routeName(), [$property_id]);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

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
            $sandbox = ${$this->singular()}->addImage($request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($this->singular(), $sandbox->singular()));

    }

    public function edit(Request $request, $property_id, $id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $sandbox = ${$this->singular()}->retrievePhoto($id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(true, compact($this->singular(), $sandbox->singular()));

    }

    public function postEdit(Request $request, $property_id, $id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $sandbox = ${$this->singular()}->editImage($id, $request->all());

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

        return SmartView::render(true, compact($this->singular(), $sandbox->singular()));

    }

    public function postDelete(Request $request, $property_id, $id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$this->singular()}->delImage($id);

        } catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        } catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, [Translator::transSmart("app.Image has been deleted.", "Image has been deleted.")]);

    }

}
