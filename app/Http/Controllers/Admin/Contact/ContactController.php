<?php

namespace App\Http\Controllers\Admin\Contact;

use Exception;
use Translator;
use Sess;
use Utility;
use Config;
use SmartView;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Contact;

class ContactController extends Controller
{

    public function __construct()
    {
        
        parent::__construct(new Contact());

    }

    public function index(Request $request){
        
        try {

            ${$this->plural()} =  $this->getModel()->showAll(array(), !Utility::isExportExcel());
            
        }catch(InvalidArgumentException $e){
        
            return Utility::httpExceptionHandler(500, $e);
        
        }catch(Exception $e){
        
            return Utility::httpExceptionHandler(500, $e);
        
        }


        $view = null;

        if(Utility::isExportExcel()){
            $view = SmartView::excel(null, compact($this->plural()), Translator::transSmart('app.Contacts', 'Contacts'));
        }else{
            $view = SmartView::render(null, compact($this->plural()));
        }

        return $view;

    }

    public function postDelete(Request $request,$id){

        try {

            Contact::del($id);

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

        return $this->responseIntended('admin::contact::index', array())->with(Sess::getKey('success'), Translator::transSmart('app.Contact has been deleted.', 'Contact has been deleted.'));

    }

}
