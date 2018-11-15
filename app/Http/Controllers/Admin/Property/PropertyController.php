<?php

namespace App\Http\Controllers\Admin\Property;

use Exception;
use Translator;
use CLDR;
use Sess;
use Auth;
use Utility;
use SmartView;
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

class PropertyController extends Controller
{

    public function __construct()
    {
        parent::__construct(new Property());
    }

    public function index(Request $request){

        try {

            ${$this->plural()} = $this->getModel()->showAll(array(), !Utility::isExportExcel());

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

    public function add(Request $request){

        ${$this->singular()} = $this->getModel();
        $company = (new Temp())->getCompanyDefault();
        ${$this->singular()}->setAttribute(${$this->singular()}->company()->getForeignKey(), $company->getKey());
        $sandbox = new Sandbox();

        return SmartView::render(null, compact($this->singular(), $company->singular(), $sandbox->singular()));

    }

    public function postAdd(Request $request){

        try {

            Property::addOnly($request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return redirect()->route('admin::property::index', array())->with(Sess::getKey('success'), Translator::transSmart('app.Office has been added.', 'Office has been added.'));

    }

    public function edit(Request $request, $id){

        try {

            ${$this->singular()} = Property::retrieve($id);
            $company = ${$this->singular()}->company;
            $sandbox = new Sandbox();

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), $company->singular(), $sandbox->singular(), 'id'));

    }

    public function postEdit(Request $request,  $id){

        try {

            Property::edit($id, $request->all());

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

        return $this->responseIntended('admin::property::index', array())->with(Sess::getKey('success'), Translator::transSmart('app.Office has been updated.', 'Office has been updated.'));

    }

    public function security(Request $request, $id){

        try {

            $company = (new Temp())->getCompanyDefault();
            $property = (new Property())->findOrFail($id);
            $member = new Member();
            $members = $member->showAllForCompanyWithPropertyACL($company->getKey(), $property->getKey());



        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact('property', 'member', 'members', 'id'));

    }

    public function postSecurity(Request $request, $id, $user_id){

        try {

            $instance = Property::findOrFail($id);

            $member = new Member();
            $company = new Company();
            $member =  $member->with(['companies' => function($query) use ($member, $company) {
                $query
                   ->where(sprintf('%s.%s', $company->getTable(), $company->getKeyName()), '=', (new Temp())->getCompanyDefault()->getKey());
            }])->findOrFail($user_id);

            if($member->companies->count() > 0 && !is_null($member->companies->first()->pivot)) {
                if (strcasecmp($member->companies->first()->pivot->role, Utility::constant('role.super-admin.slug')) == 0) {
                    throw new IntegrityException($member, Translator::transSmart("app.Permission couldn't be saved because \"%s\" have all the access rights for the system", sprintf("Permission couldn't be saved because \"%s\" have all the access rights for the system", Utility::constant('role.super-admin.name'), false, ['role' => Utility::constant('role.super-admin.name')])));
                }
            }

            if($member->companies->count() <= 0){
                throw new IntegrityException($member, Translator::transSmart("app.Permission couldn't be saved because this user is no longer belong to company.", "Permission couldn't be saved because this user is no longer belong to company."));
            }

            (new AclUser())->apply($instance, $member->getKey(), $instance->rights, $request->all());

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

        } catch(IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        } catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, ['message' => Translator::transSmart('app.Permission has been saved.', 'Permission has been saved.')]);

    }

    public function postStatus(Request $request, $id){

        try {

            Property::toggleStatus($id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, ['message' => Translator::transSmart("app.Office's status has been updated.", "Office's status has been updated.")]);

    }

    public function postDelete(Request $request,$id){

        try {

           Property::del($id);

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

        }finally{
	
	        $request->flush();
	
        }

        return $this->responseIntended('admin::property::index', array())->with(Sess::getKey('success'), Translator::transSmart('app.Office has been deleted.', 'Office has been deleted.'));

    }

}
