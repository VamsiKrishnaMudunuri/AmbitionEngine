<?php

namespace App\Http\Controllers\Admin\Managing\Gallery;

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

class GalleryController extends ManagingController
{

    public function __construct()
    {

        parent::__construct();


    }

    public function index(Request $request, $property_id){

        try {

            ${$this->singular()} = $this->getModel()->showGalleries($property_id);

            URL::setAdvancedLandingIntended(Utility::routeName(), [$property_id]);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact($this->singular()));

    }

    public function addCover(Request $request, $property_id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $sandbox = new Sandbox();

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(true, compact($this->singular(), $sandbox->singular()));

    }

    public function postAddCover(Request $request, $property_id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $sandbox = ${$this->singular()}->addCoverPhoto($request->all());

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

    public function editCover(Request $request, $property_id, $id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $sandbox = ${$this->singular()}->retrievePhoto($id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(true, compact($this->singular(), $sandbox->singular()));

    }

    public function postEditCover(Request $request, $property_id, $id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $sandbox = ${$this->singular()}->editCoverPhoto($id, $request->all());

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

    public function postSortCover(Request $request, $property_id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $flag = ${$this->singular()}->sortCoverPhoto($request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact('flag'));

    }

    public function postDeleteCover(Request $request, $property_id, $id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$this->singular()}->delCoverPhoto($id);

        } catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        } catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, [Translator::transSmart("app.Cover photo has been deleted.", "Cover photo has been deleted.")]);

    }

    public function addProfile(Request $request, $property_id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $sandbox = new Sandbox();

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(true, compact($this->singular(), $sandbox->singular()));

    }

    public function postAddProfile(Request $request, $property_id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $sandbox = ${$this->singular()}->addProfilePhoto($request->all());

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

    public function editProfile(Request $request, $property_id, $id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $sandbox = ${$this->singular()}->retrievePhoto($id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(true, compact($this->singular(), $sandbox->singular()));

    }

    public function postEditProfile(Request $request, $property_id, $id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $sandbox = ${$this->singular()}->editProfilePhoto($id, $request->all());

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

    public function postSortProfile(Request $request, $property_id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $flag = ${$this->singular()}->sortProfilePhoto($request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact('flag'));

    }

    public function postDeleteProfile(Request $request, $property_id, $id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            ${$this->singular()}->delProfilePhoto($id);

        } catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        } catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, [Translator::transSmart("app.Profile photo has been deleted.", "Profile photo has been deleted.")]);

    }


}
