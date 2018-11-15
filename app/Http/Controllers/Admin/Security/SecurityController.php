<?php

namespace App\Http\Controllers\Admin\Security;

use Exception;
use Translator;
use CLDR;
use Sess;
use Auth;
use Utility;
use SmartView;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

use Request as Req;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Company;
use App\Models\Sandbox;
use App\Models\Module;
use App\Models\Acl;

class SecurityController extends Controller
{

  
    public function __construct()
    {
        
        parent::__construct(new Module());

    }
    
    public function index(Request $request){
        
        
        try {
            
            $module = new Module();
            $pivot = new Company();
            $pivotKey = $pivot->getDefault()->getKey();
            
            $module->assignToAdmin($pivotKey);
            $module->assignToMember($pivotKey);
            $module->assignToAgent($pivotKey);
           
            ${$this->plural()} = $module->getAllWithACL($pivotKey, [Utility::constant('module.company.slug')]);
            
        }catch(InvalidArgumentException $e){
        
            return Utility::httpExceptionHandler(500, $e);
        
        }catch(Exception $e){
        
            return Utility::httpExceptionHandler(500, $e);
        
        }
    
     
        $request->session()->flashInput([]);
        return SmartView::render(null, compact($this->plural(), 'pivot'));
        

    }
    
    public function edit(Request $request, $id){
        
        try {

            $pivot_id = $id;
            $module = new Module();
            $pivot =  $module->retrieveSecurityForPortal($pivot_id);
            ${$this->singular()} = $pivot->module;
            $roles = ${$this->singular()}->defaultRoles();

            $acl = new Acl();
            $rights = $acl->getRightsForEachRole($pivot, $roles, ${$this->singular()}->rights);
            
        }catch(ModelNotFoundException $e){
            
            return Utility::httpExceptionHandler(404, $e);
            
        }
        
        return SmartView::render(null, compact($this->singular(), 'pivot_id', 'acl', 'rights'));
        
    }
    
    public function postEdit(Request $request, $id){
        
        try {

            $pivot_id = $id;
            $module = new Module();
            $pivot =  $module->findOrFailSecurityForPortal($pivot_id);
            ${$this->singular()} = $pivot->module;
            (new Acl())->apply($pivot, [], ${$this->singular()}->rights, $request->all());
            
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
        
        
        return $this->responseIntended('admin::security::index', array())->with(Sess::getKey('success'), Translator::transSmart('app.Permission has been updated.', 'Permission has been updated.'));
        
    }
    
    public function postStatus(Request $request, $id){
        
        try {

            $pivot_id = $id;
            $module = new Module();
            $module->toggleStatusForPortal($pivot_id);
            
        }catch(ModelNotFoundException $e){
            
            return Utility::httpExceptionHandler(404, $e);
            
        }catch(Exception $e){
            
            
            return Utility::httpExceptionHandler(500, $e);
            
        }
        
        return SmartView::render(null, ['message' => Translator::transSmart("app.Module's status has been updated.", "Module's status has been updated.")]);
        
    }
    
}
