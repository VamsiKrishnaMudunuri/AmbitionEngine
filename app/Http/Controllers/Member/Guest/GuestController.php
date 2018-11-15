<?php

namespace App\Http\Controllers\Member\Guest;

use Exception;
use Translator;
use CLDR;
use Sess;
use Session;
use Auth;
use Utility;
use SmartView;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Member;
use App\Models\Property;
use App\Models\Guest;
use App\Models\Sandbox;
use App\Models\Temp;



class GuestController extends Controller
{
    public function __construct()
    {
        parent::__construct(new Member());
    }

    public function index(Request $request)
    {
        try {

            ${$this->getModel()->singular()} = Auth::user();
            $property = new Property();
            $guest = new Guest();
            ${$guest->plural()} = $guest->showAllByUser( ${$this->getModel()->singular()}->getKey() );

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact($property->singular(), $guest->plural()));

    }

    public function add(Request $request)
    {
        try {

            $property = new Property();
            $guest = new Guest();
            ${$this->getModel()->singular()} = Auth::user();

            $temp = new Temp();

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($property->singular(), $guest->singular(), $temp->singular(), $this->getModel()->singular()));

    }

    public function postAdd(Request $request)
    {
        try{


            ${$this->getModel()->singular()} = Auth::user();
            $attributes = $request->all();
            $attributes[(new Guest())->user()->getForeignKey()] = ${$this->getModel()->singular()}->getKey();
            $guest = Guest::add($attributes);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){
                $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $message = Translator::transSmart('app.guest visit has been added.', 'guest visit has been added.');

        Session::flash(Sess::getKey('success'), $message);

        return SmartView::render(null, ['message' => $message]);
    }

    public function edit(Request $request, $id)
    {
        try {


            $guest = Guest::retrieve($id);
            $property = $guest->property;
            $temp = new Temp();


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(true, compact($property->singular(), $guest->singular(), $temp->singular()));
    }

    public function postEdit(Request $request, $id)
    {
        try {

            $guest = Guest::edit($id, $request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){
            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $message = Translator::transSmart('app.Guest visit has been updated.', 'Guest visit has been updated.');
        Session::flash(Sess::getKey('success'), $message);

        return SmartView::render(null, ['message' => $message]);

    }

    public function postDelete(Request $request, $id)
    {
        try {

            Guest::del($id);

        } catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        } catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return redirect()->route('member::guest::index', array())->with(Sess::getKey('success'), Translator::transSmart('app.Guest visit has been deleted.', 'Guest visit has been deleted.'));

    }



}
