<?php

namespace App\Http\Controllers\Admin\Subscriber;

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

use App\Models\Mailchimp;
use App\Models\Subscriber;

class SubscriberController extends Controller
{

    public function __construct()
    {

        parent::__construct(new Subscriber());

    }

    public function index(Request $request){

        try {

            $mailchimp = new Mailchimp();
            ${$this->plural()} =  $mailchimp->subscribersListForDefault(array(), !Utility::isExportExcel());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $view = null;

        if(Utility::isExportExcel()){
            $view = SmartView::excel(null, compact($this->singular(), $this->plural()), Translator::transSmart('app.Subscribers', 'Subscribers'));
        }else{
            $view = SmartView::render(null, compact($this->singular(), $this->plural()));
        }

        return $view;

    }

    public function postDelete(Request $request,$id){

        try {

            Subscriber::del($id);

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

        return $this->responseIntended('admin::subscriber::index', array())->with(Sess::getKey('success'), Translator::transSmart('app.Subscriber has been deleted.', 'Subscriber has been deleted.'));

    }


}
