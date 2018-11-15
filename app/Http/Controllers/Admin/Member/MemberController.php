<?php

namespace App\Http\Controllers\Admin\Member;

use Exception;
use Illuminate\Support\Facades\Route;
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


use App\Models\Temp;
use App\Models\Member;
use App\Models\SignupInvitation;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Meta;
use App\Models\Sandbox;

use App\Models\MongoDB\Bio;

class MemberController extends Controller
{

    public function __construct()
    {
        
        parent::__construct(new Member());

    }

    public function index(Request $request){

        try {

            ${$this->plural()} = $this->getModel()->showAllWithPropertySubscribedIfAny((new Temp())->getCompanyDefault()->getKey(), [], !Utility::isExportExcel());
            $temp = new Temp();
            $properties = $temp->getPropertyMenuAll();
            $member = new Member();
            
        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){
        
            return Utility::httpExceptionHandler(500, $e);
        
        }catch(Exception $e){
        
            return Utility::httpExceptionHandler(500, $e);
        
        }

        $view = null;

        if(Utility::isExportExcel()){
            $view = SmartView::excel(null, compact($this->plural(), $temp->singular(), 'properties', 'member'), Translator::transSmart('app.Members', 'Members'));
        }else{
            $view = SmartView::render(null, compact($this->plural(), $temp->singular(), 'properties', 'member'));
        }

        return $view;

    }

    public function invite(Request $request){

        ${$this->singular()} = new Member();
        $signup_invitation = new SignupInvitation();

        return SmartView::render(null, compact($this->singular(),  $signup_invitation->singular() ));

    }

    public function postInvite(Request $request){

        try {

            $this->getModel()->invite($request->all());

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch (IntegrityException $e){

            $this->throwIntegrityException(
                $request, $e
            );


        }catch (Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $request->flush();
        return redirect()->route('admin::member::invite', array())->with(Sess::getKey('success'), Translator::transSmart('app.You have successfully sent out the invitation emails.', 'You have successfully sent out the invitation emails.'));

    }

    public function add(Request $request){


        ${$this->singular()} = new Member();
        ${$this->singular()}->status = Utility::constant('status.1.name');
        ${$this->singular()}->timezone = Config::get('app.timezone', 'UTC');
        $companyUser = new CompanyUser();
        $sandbox = new Sandbox();
	    
        $activeMenus = (new Temp())->getPropertyMenuAll();
	
	    $properties = [];
	
	    foreach($activeMenus as $countries){
		    foreach($countries as $office){
			    $properties[] = $office;
		    }
	    }

        return SmartView::render(null, compact($this->singular(), 'companyUser', 'sandbox', 'properties'));

    }

    public function postAdd(Request $request){

        try {

            Member::addAndAssignCompanyRole($request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return redirect()->route('admin::member::index', array())->with(Sess::getKey('success'), Translator::transSmart('app.Member has been added.', 'Member has been added.'));

    }

    public function edit(Request $request, $id){
        
        try {

            ${$this->singular()} = Member::retrieve($id);

            $companyUser = new CompanyUser();
            $sandbox = new Sandbox();
	
	        $activeMenus = (new Temp())->getPropertyMenuAll();
	        
	        $properties = [];
	
	        foreach($activeMenus as $countries){
		        foreach($countries as $office){
			        $properties[] = $office;
		        }
	        }
            
        }catch(ModelNotFoundException $e){
            
            return Utility::httpExceptionHandler(404, $e);
            
        }

        return SmartView::render(null, compact($this->singular(), 'companyUser', 'sandbox', 'properties', 'id'));
        
    }
    
    public function postEdit(Request $request,  $id){
        
        try {

            Member::editAndAssignCompanyRole($id, $request->all());
            
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

        return $this->responseIntended('admin::member::index', array())->with(Sess::getKey('success'), Translator::transSmart('app.Member has been updated.', 'Member has been updated.'));

    }

    public function editNetwork(Request $request, $id){

        try {

            ${$this->singular()} = Member::retrieve($id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular()));

    }

    public function postEditNetwork(Request $request,  $id){

        try {

            Member::updateNetwork($id, $request->all());

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

        return $this->responseIntended('admin::member::index', array())->with(Sess::getKey('success'), Translator::transSmart('app.Network configuration has been updated.', 'Network configuration has been updated.'));

    }

    public function editPrinter(Request $request, $id){

        try {

            ${$this->singular()} = Member::retrieve($id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular()));

    }

    public function postEditPrinter(Request $request,  $id){

        try {

            Member::updatePrinter($id, $request->all());

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

        return $this->responseIntended('admin::member::index', array())->with(Sess::getKey('success'), Translator::transSmart('app.Printer configuration has been updated.', 'Printer configuration has been updated.'));

    }

    public function postStatus(Request $request, $id)
    {

        try {

            Member::toggleStatus($id);

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (Exception $e) {


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, ['message' => Translator::transSmart("app.Member's status has been updated.", "Member's status has been updated.")]);

    }
	
	public function addCompany(Request $request){
		
		
		$company = new Company();
		$meta = new Meta();
		$bio = new Bio();
		$sandbox = new Sandbox();
		
		return SmartView::render(true, compact($company->singular(), $meta->singular(), $bio->singular(), $sandbox->singular()));
		
	}
	
	public function postAddCompany(Request $request){
		
		try {
			
			
			$company = Company::addWithoutOwner($request->all());
			
		}catch(ModelValidationException $e){
			
			$this->throwValidationException(
				$request, $e->validator
			);
			
		}catch(Exception $e){
			
			return Utility::httpExceptionHandler(500, $e);
			
		}
		
		return SmartView::render(null, compact($company->singular()));

	}
	
	public function editCompany(Request $request, $id){
		
		try {
			
			$company = Company::retrieve($id);
			$meta = $company->metaWithQuery;
			$bio = $company->bio;
			$sandbox = new Sandbox();
			
		}catch(ModelNotFoundException $e){
			
			return Utility::httpExceptionHandler(404, $e);
			
		}
		
		return SmartView::render(true, compact($company->singular(), 'meta', 'bio', $sandbox->singular(), 'id'));
		

	}
	
	public function postEditCompany(Request $request,  $id){
		
		try {
			
			
			$company = Company::edit($id, $request->all());
			$company = new Company($company->getAttributes());
			
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
		
		
		return SmartView::render(null, compact($company->singular()));
		
	}

	
	public function postDelete(Request $request,$id){
        
        try {

            Member::del($id);
            
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

        return $this->responseIntended('admin::member::index', array())->with(Sess::getKey('success'), Translator::transSmart('app.Member has been deleted.', 'Member has been deleted.'));

    }


    
}
