<?php

namespace App\Http\Controllers\Admin\Managing\Listing;

use Exception;
use URL;
use Translator;
use Auth;
use Utility;
use SmartView;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Managing\ManagingController;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Temp;

class ListingController extends ManagingController
{

    public function __construct()
    {

        parent::__construct();

    }

    public function index(Request $request){

        try {

            $user = Auth::user();
            ${$this->plural()} = $this->getModel()->showAllForUser($user->getKey(), (new Temp())->getCompanyDefault()->getKey(), array(), !Utility::isExportExcel());

            URL::setAdvancedLandingIntended(Utility::routeName());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $view = null;

        if(Utility::isExportExcel()){
            $view = SmartView::excel(null, compact($this->plural()), Translator::transSmart('app.Offices', 'Offices'));
        }else{
            $view = SmartView::render(null, compact($this->plural()));
        }

        return $view;

    }

}