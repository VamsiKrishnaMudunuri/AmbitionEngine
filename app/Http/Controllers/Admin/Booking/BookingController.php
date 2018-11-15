<?php

namespace App\Http\Controllers\Admin\Booking;

use Exception;
use Translator;
use Sess;
use Utility;
use Config;
use SmartView;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Temp;
use App\Models\Booking;
use App\Models\Property;

class BookingController extends Controller
{

    public function __construct()
    {
        
        parent::__construct(new Booking());

    }

    public function index(Request $request){
        
        try {


            $temp = new Temp();

            if(!($request->has('type'))){
                $request->merge(array('type' => 1));
            }

            $type = $request->get('type');

            $order = [];

            if($type){
                $order['schedule'] = 'DESC';
            }else{
                $order[$this->getModel()->getCreatedAtColumn()] = 'DESC';
            }

            ${$this->singular()} = $this->getModel();
            ${$this->plural()} = $this->getModel()->showAll($order, !Utility::isExportExcel());

        }catch(InvalidArgumentException $e){
        
            return Utility::httpExceptionHandler(500, $e);
        
        }catch(Exception $e){
        
            return Utility::httpExceptionHandler(500, $e);
        
        }

        $view = null;

        if(Utility::isExportExcel()){
            $view = SmartView::excel(null, compact($this->singular(), $this->plural()), Translator::transSmart('app.Bookings', 'Bookings'));
        }else{
            $view = SmartView::render(null, compact($this->singular(), $this->plural(), $temp->singular()));
        }

        return $view;

    }

    public function add(Request $request){

        ${$this->singular()} = $this->getModel();

        ${$this->singular()}->setupForNewEntry();

        ${$this->singular()}->type = 1;

        $property = new Property();
        $temp = new Temp();

        return SmartView::render(null, compact($this->singular(), $property->singular(), $temp->singular()));

    }

    public function postAdd(Request $request){

        try {

        	$booking = new Booking();
        	$attributes = $request->all();
            Booking::add($attributes, Arr::get($attributes, $booking->isNeedEmailNotificationField, false), true, true, Utility::constant('lead_source.admin.slug'));

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch (IntegrityException $e){

            $this->throwIntegrityException(
                $request, $e
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return redirect()->route('admin::booking::index', array())->with(Sess::getKey('success'), Translator::transSmart('app.Site visit has been added.', 'Site visit has been added.'));

    }

    public function edit(Request $request, $id){

        try {

            ${$this->singular()} = Booking::retrieve($id);
            $property = ( ${$this->singular()}->property) ?  ${$this->singular()}->property : new Property();
            $temp = new Temp();

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), $property->singular(), $temp->singular(), 'id'));

    }

    public function postEdit(Request $request,  $id){

        try {

            Booking::edit($id, $request->all());

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

        return $this->responseIntended('admin::booking::index', array())->with(Sess::getKey('success'), Translator::transSmart('app.Site visit has been updated.', 'Site visit has been updated.'));

    }

    public function postDelete(Request $request,$id){

        try {

            Booking::del($id);

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

        return $this->responseIntended('admin::booking::index', array())->with(Sess::getKey('success'), Translator::transSmart('app.Site visit has been deleted.', 'Site visit has been deleted.'));

    }

}
