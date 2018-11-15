<?php

namespace App\Http\Controllers\Root\Module;

use Exception;
use Translator;
use CLDR;
use Sess;
use Auth;
use Utility;
use SmartView;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Module;
use App\Models\Acl;
use App\Models\User;

class ModuleController extends Controller
{

    public function __construct()
    {
        parent::__construct(new Module());
    }

    public function index(Request $request){
        
        try {

            ${$this->plural()} = (new Module())->buildTree();


        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }
    
        $request->session()->flashInput([]);
        return SmartView::render(null, compact($this->plural()));

    }

    public function add(Request $request, $id = null){

        try {

            ${$this->singular()} = new Module();
            $acl = new Acl();
            ${$this->singular()}->rights = join($acl->delimiter, $acl->defaultRights);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), 'id'));

    }

    public function postAdd(Request $request, $id = null){

        try {

            $parent_id = $id;
            Module::add($request->all(), $parent_id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return redirect()->route('root::module::index', array())->with(Sess::getKey('success'), Translator::transSmart('app.New module added.', 'New module added.'));

    }

    public function edit(Request $request, $id){

        try {

            ${$this->singular()} = Module::retrieve($id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), 'id'));

    }

    public function postEdit(Request $request, $id){

        try {

            Module::edit($id, $request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

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

        return $this->responseIntended('root::module::index')->with(Sess::getKey('success'), Translator::transSmart('app.Module has been updated.', 'Module has been updated.'));

    }

    public function security(Request $request, $id){

        try {

            ${$this->singular()} = Module::findOrFail($id);
            $acl = new Acl();

            $roles = ${$this->singular()}->defaultRoles();

            $rights = $acl->getRightsForEachRole(${$this->singular()}, $roles, ${$this->singular()}->rights);
            
        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), 'id', 'acl', 'rights'));

    }
    
    public function postSecurity(Request $request, $id){
        
        try {
            
            ${$this->singular()} = Module::findOrFail($id);

            (new Acl())->apply(${$this->singular()}, [], ${$this->singular()}->rights, $request->all());
            
        }catch(ModelNotFoundException $e){
            
            return Utility::httpExceptionHandler(404, $e);
            
        }catch (ModelVersionException $e){
            
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
        
        return $this->responseIntended('root::module::index')->with(Sess::getKey('success'), Translator::transSmart('app.Permission has been updated.', 'Permission has been updated.'));
        
    }
    
    public function postDelete(Request $request, $id){

        try {

            Module::del($id);

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
        
        return $this->responseIntended('root::module::index')->with(Sess::getKey('success'), Translator::transSmart('app.Module has been deleted.', 'Module has been deleted.'));
    }
    
    public function postStatus(Request $request, $id){

        try {


            Module::toggleStatus($id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, ['message' => Translator::transSmart("app.Module's status has been updated.", "Module's status has been updated.")]);

    }

}
