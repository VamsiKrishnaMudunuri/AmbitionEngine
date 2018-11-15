<?php

namespace App\Http\Controllers;

use Utility;
use Sess;
use URL;
use View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Major model used for the controller
     *
     * @var \App\Libraries\Model\Model;
     */
    private $model;

    public function __construct($model = null)
    {
        $this->model = $model;
        
        $actions = ['index'];
        
        if(in_array(Utility::actionName(), $actions)){
           $this->setResponseIntended();
        }

        $this->setResponseIntendedForView();

    }
    
    public function getModel(){
       return $this->model;
    }

    public function setResponseIntended(){
        URL::setLandingIntended($this);
    }

    public function setResponseIntendedForView(){
        View::share('url_intended', URL::getLandingIntended($this));
    }

    public function responseIntended($route, $parameters = array()){
        return redirect()->to(URL::pullLandingIntended($this, URL::route($route, $parameters, true)));
    }

    /**
     * Format the validation errors to be returned.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return array
     */
    protected function formatValidationErrors(Validator $validator)
    {
        return isset($validator->errors()->nice_messages) ? $validator->errors()->nice_messages : $validator->errors()->getMessages();
    }
    
    /**
     * Throw the failed validation exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function throwValidationExceptionWithNoInput(Request $request, $validator)
    {
        $request->replace([]);

        if(Utility::isJsonRequest()) {
            Session::flashInput([]);
        }

        call_user_func_array(array($this, 'throwValidationException'), func_get_args());
    }

    protected function buildFailedValidationResponse(Request $request, array $errors)
    {
        if (Utility::isJsonRequest()) {
            if(Utility::isJsonResponseFractalFormat()){
                return Utility::JsonResponseFractalFormat($errors, 422);
            }else{
                return new JsonResponse($errors, 422);
            }

        }

        return redirect()->to($this->getRedirectUrl())
            ->withInput($request->input())
            ->withErrors($errors, $this->errorBag());
    }

    protected function throwIntegrityException(Request $request, $integrity)
    {
        throw new IntegrityException($integrity->getModel(), $integrity->getMessage(), $this->buildUserFriendlyExceptionResponse($request, $integrity->getMessage()));
    }

    protected function throwPaymentGatewayException(Request $request, $paymentGateway)
    {

        throw new PaymentGatewayException($paymentGateway->getModel(), $paymentGateway->getMessage(), $this->buildUserFriendlyExceptionResponse($request, $paymentGateway->getMessage()));
    }

    protected function buildUserFriendlyExceptionResponse(Request $request, $message)
    {
        $errors = array( Sess::getKey('errors') => $message);

        if (Utility::isJsonRequest()) {
            if(Utility::isJsonResponseFractalFormat()){
                return Utility::JsonResponseFractalFormat($errors, 422);
            }else{
                return new JsonResponse($errors, 422);
            }

        }

        return redirect()->to($this->getRedirectUrl())
            ->withInput($request->input())
            ->withErrors($errors);
    }

    protected function singular(){

        return (!is_null($this->model)) ? $this->model->singular() : '';
    }

    protected function plural(){

        return (!is_null($this->model)) ? $this->model->plural() : '';
    }
    
}