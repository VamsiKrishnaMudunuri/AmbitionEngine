<?php

namespace App\WebServices\Printer\Auth;

use App\WebServices\Printer\Base\Core;
use App\WebServices\Printer\Auth\XrxValidationRequest;
use App\WebServices\Printer\Auth\ValidationFieldResponse;

use Utility;
use Exeption;
use SoapFault;


class Login extends Core{


    public function __construct()
    {

    }


    public function XrxValidation($xrxValidationRequest){

         $soapRequest =  $xrxValidationRequest;
         $soapResponse = new XrxValidationResponse();

         try {

             If ($soapRequest == null) {
                 $se = new SoapFault('NULL request was unexpected.', 'Server');
                 throw $se;
             }

             if (($soapRequest->clientVersion != null)
                 && (($soapRequest->clientVersion->MajorVersion != 1)
                     || ($soapRequest->clientVersion->MinorVersion > 99)
                 )
             ) {

                 $se = new SoapFault('Fault occurred.', 'Client');

                 throw $se;

             }

             $invalidParametersKeyValuePair = false;
             $invalidSystemParametersKeyValuePair = false;

             if ($soapRequest->parameters != null){

                 $result = false;
                 $validationFieldResponsesArr = array();

                 foreach($soapRequest->parameters as $key => $parameter){
                     $validationFieldResponse = new ValidationFieldResponse();
                     $validationFieldResponse->name = $parameter->name;
                     $result = true;
                     $validationFieldResponse->validated = $result;
                     $validationFieldResponsesArr[] = $validationFieldResponse;
                 }

                 if(!$result){
                     $invalidParametersKeyValuePair = true;
                 }


                 $validationFieldResponses = new ValidationFieldResponses();
                 $validationFieldResponses->ValidationFieldResponse =  $validationFieldResponsesArr;
                 $soapResponse->ValidationFieldResponses = $validationFieldResponses;

             }

             if ($invalidParametersKeyValuePair || $invalidSystemParametersKeyValuePair)
             {
                 $soapResponse->Authorization = 0;
                 $soapResponse->ErrorDescription = "Login Failed! Please try again.";
             }
             else
             {
                 $soapResponse->Authorization = 0xFFFFFFFF;
                 $soapResponse->ErrorDescription = "";
             }



         }catch(SoapFault $ex){

             throw $ex;

         }catch(Exception $ex){

             $se = new SoapFault('Server fault!', 'Server');

             throw $se;
         }

        return $soapResponse;

    }

    public function hello($name)
    {


        return 'Hello1, '.$name;
    }

}