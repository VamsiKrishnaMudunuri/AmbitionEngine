<?php

namespace App\Http\Controllers\Admin\Company;

use Exception;
use Translator;
use CLDR;
use Sess;
use Auth;
use Utility;
use SmartView;
use Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Temp;
use App\Models\Sandbox;
use App\Models\User;
use App\Models\Meta;
use App\Models\Member;
use App\Models\AclUser;
use App\Models\Company;
use App\Models\Property;

use App\Models\MongoDB\Bio;

class CompanyController extends Controller
{

    public function __construct()
    {
        parent::__construct(new Company());
    }

    public function index(Request $request){

        try {

            ${$this->plural()} = $this->getModel()->showAllForInternal(array(), !Utility::isExportExcel());

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $view = null;

        if(Utility::isExportExcel()){
            $view = SmartView::excel(null, compact($this->plural()), Translator::transSmart('app.Companies', 'Companies'));
        }else{
            $view = SmartView::render(null, compact($this->plural()));
        }

        return $view;

    }

    public function add(Request $request){

        ${$this->singular()} = $this->getModel();
        $meta = new Meta();
        $bio = new Bio();
        $sandbox = new Sandbox();

        return SmartView::render(null, compact($this->singular(), $meta->singular(), $bio->singular(), $sandbox->singular()));

    }

    public function postAdd(Request $request){

        try {


            Company::addByInternal($request->all());

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return redirect()->route('admin::company::index', array())->with(Sess::getKey('success'), Translator::transSmart('app.Company has been added.', 'Company has been added.'));

    }

    public function edit(Request $request, $id){

        try {

            ${$this->singular()} = Company::retrieve($id);
            $meta = ${$this->singular()}->metaWithQuery;
            $bio = ${$this->singular()}->bio;
            $sandbox = new Sandbox();

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), 'meta', 'bio', $sandbox->singular(), 'id'));

    }

    public function postEdit(Request $request,  $id){

        try {


            Company::edit($id, $request->all());

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

        return $this->responseIntended('admin::company::index', array())->with(Sess::getKey('success'), Translator::transSmart('app.Company has been updated.', 'Company has been updated.'));

    }

    public function postDelete(Request $request,$id){

        try {


            Company::del($id);

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

        return $this->responseIntended('admin::company::index', array())->with(Sess::getKey('success'), Translator::transSmart('app.Company has been deleted.', 'Company has been deleted.'));

    }

}
