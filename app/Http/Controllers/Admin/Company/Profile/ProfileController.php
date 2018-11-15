<?php

namespace App\Http\Controllers\Admin\Company\Profile;

use Exception;
use Translator;
use CLDR;
use Sess;
use Auth;
use Utility;
use Request as req;
use SmartView;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Company;
use App\Models\Sandbox;
use App\Models\Module;
use App\Models\MongoDB\CompanyBio;

class ProfileController extends Controller
{

    public function __construct()
    {
        
        parent::__construct(new Company());

    }

    public function index(Request $request){
    
    
        try {

            ${$this->singular()} = Company::getProfileForDefault();

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(Exception $e){
        
            return Utility::httpExceptionHandler(500, $e);
        
        }
    

        return SmartView::render(null, compact($this->singular()));
        

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
        
        
        return SmartView::render(null, compact($this->singular(), 'meta', 'bio', 'sandbox', 'id'));
        
        
    }
    
    public function postEdit(Request $request, $id){
        
        
        try {
            
            $attributes = $request->all();
            ${$this->singular()} = Company::edit($id, $attributes);
            
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

        return $this->responseIntended('admin::company::profile::index', array())->with(Sess::getKey('success'), Translator::transSmart('app.Company has been updated.', 'Company  has been updated'));

    }

}
