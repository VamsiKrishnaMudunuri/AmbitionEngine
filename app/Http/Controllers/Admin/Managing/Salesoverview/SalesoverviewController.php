<?php

namespace App\Http\Controllers\Admin\Managing\Salesoverview;

use Carbon\Carbon;
use Exception;
use Auth;
use Route;
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

class SalesoverviewController extends ManagingController
{

    public function __construct()
    {

        parent::__construct();

    }

    public function index(Request $request, $property_id)
    {

        try {


            $user = Auth::user();
            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $sandbox = new Sandbox();

            $current_year =  ${$this->singular()}->today()->year;
            $year = $request->get('year', $current_year );
            $years = array();
            $stats = ${$this->singular()}->occupancyForMonthlyReport(${$this->singular()}, $year);

            for($i = $current_year  - 6; $i < $current_year  + 6; $i++){
                $years[$i] = $i;
            }

            URL::setAdvancedLandingIntended(Utility::routeName(), [$property_id]);

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (Exception $e) {

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact($this->singular(), $sandbox->singular(),  'year', 'years',  'stats'));

    }


}
