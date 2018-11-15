<?php

namespace App\Http\Controllers\Auth;

use Exception;
use URL;
use Domain;
use Mauth;
use Oauth;
use Gate;
use Auth;
use Utility;
use Redirect;
use SmartView;
use Translator;
use Session;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

use App\Models\Temp;
use App\Models\Acl;
use App\Models\User;
use App\Models\Company;
use App\Models\Property;
use App\Models\Subscription;
use App\Models\Package;
use App\Models\Facility;
use App\Models\FacilityPrice;
use App\Models\FacilityUnit;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionInvoiceTransaction;
use App\Models\SubscriptionInvoiceTransactionPackage;
use App\Models\SubscriptionInvoiceTransactionDeposit;
use App\Models\SubscriptionRefund;
use App\Models\Transaction;
use App\Models\Sandbox;
use App\Models\SignupInvitation;

class AuthController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    //use AuthenticatesUsers, ThrottlesLogins;
    use AuthenticatesUsers;


    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     *
     */
    protected $redirectTo;

    public function __construct()
    {
        $this->redirectTo = URL::route('page::index');
        parent::__construct(new User());
    }

    public function username()
    {
        return config('auth.login.main');
    }

    protected function credentials(Request $request)
    {

        $arr = $request->only($this->username(), 'password');

        $arr['status'] = Utility::constant('status.1.slug');

        return $arr;

    }


    protected function authenticated($request, $user)
    {

    }

    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        Mauth::sync($request->session()->getId(), $this->guard()->user()->getKey());

        if($this->authenticated($request, $this->guard()->user())){

        }else{

            return $this->resolveLoginResponse($request);

        }

    }

    public function resolveLoginResponse(Request $request){
    	
        $company = (new Temp())->getCompanyDefault();
        $user = Auth::user();
        $member = false;

        if(Domain::isRoot()){

            $member = !Acl::isRootRight();

        }else if(Domain::isAdmin()){

            $member = !(Acl::isRootRight() || $user->isMyCompanyWithoutPartner($company->getKey()));

        }else if(Domain::isAgent()){
        	
        	$member = !(Acl::isRootRight() || $user->isMyCompanyWithAgentOnly($company->getKey()));
        	
        }else if(Domain::isCMS()){

            $member = true;

        }

        //20180803 martin: force to redirect to Agent portal if account is with company agent role.
        if($user->isMyCompanyWithAgentOnly($company->getKey())) {
	        return redirect()->to(config('app.agent_url'));
        }
 
        return $member ? redirect()->to(config('app.member_url')) :  redirect()->intended($this->redirectPath());

    }

    public function logout(Request $request)
    {

        Mauth::revokeBySession($this->guard()->user()->getKey(), $request->session()->getId());

        $this->guard()->logout();

        $request->session()->flush();

        $request->session()->regenerate();

        return Redirect::route(Domain::route('auth::signin'));
    }

    public function inviteSignup(Request $request, $token)
    {

        ${$this->singular()} = new User();
        $temp = new Temp();
        $property = new Property();
        $signup_invitation = new SignupInvitation();
        $isValidToken =  $signup_invitation->isValid($token);

        return SmartView::render(null,  compact($this->singular(), $temp->singular(), $property->singular(), $signup_invitation->singular(), 'token', 'isValidToken'));

    }

    public function postInviteSignupStep1(Request $request, $token)
    {
        try {

            User::inviteSignupForStep1($token, $request->all());

        } catch(IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        } catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        } catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null);

    }

    public function postInviteSignupStep2(Request $request, $token)
    {

        try {

            User::inviteSignupForStep2($token, $request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        }catch(PaymentGatewayException $e){

            $this->throwPaymentGatewayException($request, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null);
    }

    public function inviteSignupStep3(Request $request, $token)
    {

        try {

            $user = User::inviteSignupForStep3($token);

        }  catch(IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        } catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        } catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        Session::set('seo_signup', 1);

        $this->guard()->login($user);

        return $this->resolveLoginResponse($request);

    }

    public function signup(Request $request)
    {
        ${$this->singular()} = new User();
        $temp = new Temp();
        $property = new Property();
        $transaction = new Transaction();

        $transaction->initializeClientTokenOrFail($property->merchant_account_id);

        return SmartView::render(null,  compact($this->singular(), $temp->singular(), $property->singular(), $transaction->singular()));
    }

    public function postSignupStep1(Request $request)
    {
        try {

            User::signupForStep1($request->all());

        } catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        } catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null);

    }

    public function postSignupStep2(Request $request)
    {

        try {

            User::signupForStep2($request->all());

        } catch(IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        } catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null);
    }

    public function postSignupStep3(Request $request)
    {

        try {

           User::signupForStep3($request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        }catch(PaymentGatewayException $e){

            $this->throwPaymentGatewayException($request, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null);

    }

    public function signupStep4(Request $request)
    {

        try {

           $user = User::signupForStep4();

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        Session::set('seo_signup', 1);

        $this->guard()->login($user);

        return $this->resolveLoginResponse($request);

    }

    public function postSignupAgent(Request $request)
    {
    
        try {

            $user = User::signupAgent($request->all());

        } catch (ModelNotFoundException $e) {
        
            return Utility::httpExceptionHandler(404, $e);
        
        } catch (ModelVersionException $e) {
        
            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );
        
        } catch(ModelValidationException $e){
    
            $this->throwValidationException(
                $request, $e->validator
            );
    
        } catch (IntegrityException $e) {
        
            $this->throwIntegrityException(
                $request, $e
            );
        
        } catch (Exception $e) {
        
            return Utility::httpExceptionHandler(500, $e);
        }

        Session::set('seo_signup', 1);

        if(Utility::isJsonRequest()){
    
            return SmartView::render(null, array('message' => Translator::transSmart("app.THANK YOU FOR SIGN UP AS AGENT WITH US. YOU'LL HEAR FROM US SOON!", "THANK YOU FOR SIGN UP AS AGENT WITH US. YOU'LL HEAR FROM US SOON!")));
            
        }else{
    
            $this->guard()->login($user);

            return $this->resolveLoginResponse($request);
            
        }
        
        
    }

    public function signupPrimeMember(Request $request)
    {


        ${$this->singular()} = new User();
        $temp = new Temp();
        $property = new Property();
        $subscription = new Subscription();

        return SmartView::render(null, compact($this->singular(), $temp->singular(), $property->singular(), $subscription->singular()));

    }

    public function postSignupPrimeMember(Request $request)
    {

        try {

            $user = User::signupPrimeMember($request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        }catch(PaymentGatewayException $e){

            $this->throwPaymentGatewayException($request, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        Session::set('seo_signup', 1);

        if(Utility::isJsonRequest()){

            return SmartView::render(null, array('message' => Translator::transSmart("app.THANK YOU FOR SIGN UP AS PRIME MEMBER WITH US.", "THANK YOU FOR SIGN UP AS PRIME MEMBER WITH US.")));

        }else{

            $this->guard()->login($user);

            return $this->resolveLoginResponse($request);

        }


    }

    public function signin(Request $request)
    {

        ${$this->singular()} = new User();
        return SmartView::render(null,  compact($this->singular()));
    }

    public function postSignin(Request $request)
    {

        return $this->login($request);

    }



}
