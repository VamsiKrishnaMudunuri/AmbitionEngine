<?php

namespace App\Http\Controllers\Admin\Managing\Report\Reservation\Room;


use Exception;
use InvalidArgumentException;
use URL;
use Auth;
use Translator;
use Sess;
use Session;
use Carbon\Carbon;
use Utility;
use SmartView;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;

use App\Http\Controllers\Admin\Managing\ManagingController;

use App\Businesses\Admin\Report\Reservation\RoomController as ReportRoom;

use Illuminate\Database\Eloquent\Collection;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;



class RoomController extends ManagingController
{

    public function __construct()
    {

        parent::__construct();

    }

    public function listing(Request $request, $property_id){

        try {


            $result = (new ReportRoom())->listingByProperty($property_id, $request->get('from_date'), $request->get('to_date'));


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $view = null;

        if(Utility::isExportExcel()){
            $view = SmartView::excel(null, $result, Translator::transSmart('app.Meeting Room Report', 'Meeting Room Report'));
        }else{
            $view = SmartView::render(null, $result);
        }

        return $view;

    }



}