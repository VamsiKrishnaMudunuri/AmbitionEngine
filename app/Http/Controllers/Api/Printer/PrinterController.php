<?php

namespace App\Http\Controllers\Api\Printer;


use Exception;
use Illuminate\Routing\Route;
use URL;
use Log;
use Storage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Libraries\Soap\CSoapServer;
use App\Libraries\Soap\CSoapClient;
use App\WebServices\Printer\Auth\Login;
use App\WebServices\Printer\Auth\InterfaceVersion;
use App\WebServices\Printer\Auth\ValidationField;
use App\WebServices\Printer\Auth\ValidationFieldResponse;
use App\WebServices\Printer\Auth\XrxValidationRequest;
use App\WebServices\Printer\Auth\XrxValidationResponse;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;


class PrinterController extends Controller
{

    public function __construct()
    {

        parent::__construct();

    }

    public function auth(Request $request){

        $wsdlStoragePrefixPath = Storage::disk('wsdl')->getDriver()->getAdapter()->getPathPrefix();
        $tempStoragePrefixPath = Storage::disk('tmp')->getDriver()->getAdapter()->getPathPrefix() ;
        $wsdl = '/printer/auth.wsdl';

        $soap = new CSoapServer(
            $wsdlStoragePrefixPath . $wsdl, array(
                'cache_wsdl' => WSDL_CACHE_DISK,
                'soap:address' => URL::route('api::printer::auth'),
                'soap:address-file' => $tempStoragePrefixPath  . '/' . basename($wsdlStoragePrefixPath) . $wsdl
            )
        );

        $login = new Login();

        $soap->setObject($login);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml; charset=ISO-8859-1');

        ob_start();
        $soap->handle();
        $response->setContent(ob_get_clean());


        return $response;

    }

    public function test(Request $request)
    {


        try{

            ///ini_set("soap.wsdl_cache_enabled", 0);

            $client = new CSoapClient(URL::route('api::printer::auth', array('wsdl')), array(
                'exceptions' => true,
                'trace' => true,
                'cache_wsdl_option' => WSDL_CACHE_NONE,
                'classmap' => array(
                    'XrxValidationRequest' => '\App\WebServices\Printer\Auth\XrxValidationRequest',
                    'XrxValidationResponse' =>     '\App\WebServices\Printer\Auth\XrxValidationResponse',
                    'ValidationFieldResponses' => '\App\WebServices\Printer\Auth\ValidationFieldResponses',
                    'SystemFieldResponses' => '\App\WebServices\Printer\Auth\SystemFieldResponses'
            )));



            $request = new XrxValidationRequest();
            $request->clientVersion = new InterfaceVersion();
            $request->clientVersion->MajorVersion = 1;
            $request->clientVersion->MinorVersion = 0;
            $request->clientVersion->Revision = 1;

            $validationFields = array();
            $validationFields[0] = new ValidationField();
            $validationFields[0]->name = 'test';
            $validationFields[0]->value = 'test';

            $request->parameters = $validationFields;

            //dd($client->__getTypes());
            //dd($client->__getFunctions());
            $result = $client->XrxValidation($request); //$client->hello('dfs');

           dd($result);

        } catch (Exception $e){
            //dd($client->__getLastRequest());

           dd($e);


            dd($e->xdebug_message);
            var_dump($client->__getLastRequest());
            var_dump($client->__getLastResponse());
        }


        return;

    }

    public function test1(Request $request){

        $soapUrl = URL::route('api::printer::auth', array('wsdl')); // asmx URL of WSDL
        $soapUser = "username";  //  username
        $soapPassword = "password"; // password

             // xml post structure
            $xml_post_string = '<?xml version="1.0" encoding="UTF-8"?>
            <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
            xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
            xmlns:xrxpath="http://www.xerox.com/webservices/office/validation/1/">
            <SOAP-ENV:Body id="_0">
             <XrxValidationRequest
            xmlns="http://www.xerox.com/webservices/office/validation/1/">
            <clientVersion xsi:type="xrxpath:InterfaceVersion">
            <MajorVersion xsi:type="xsd:short">1</MajorVersion>
            <MinorVersion xsi:type="xsd:short">0</MinorVersion>
            <Revision xsi:type="xsd:short">7</Revision>
            </clientVersion>
            <parameters xsi:type="xrxpath:ValidationFields">
            <ValidationField xsi:type="xrxpath:ValidationField">
            <name xsi:type="xrxpath:AttributeName">Alpha</name>
            <value xsi:type="xrxpath:AttributeValue">aval_101</value>
            </ValidationField>
            </parameters>
            <systemParameters xsi:type="xrxpath:SystemFields">
            <SystemField xsi:type="xrxpath:ValidationField">
            <name xsi:type="xrxpath:AttributeName">UserName</name>
            <value xsi:type="xrxpath:AttributeValue">jsmith</value>
            </SystemField>
            </systemParameters>
            </XrxValidationRequest>
            </SOAP-ENV:Body>
            </SOAP-ENV:Envelope>';

           $headers = array(
                        "Content-type: text/xml;charset=\"utf-8\"",
                        "Accept: text/xml",
                        "Cache-Control: no-cache",
                        "Pragma: no-cache",
                        "SOAPAction: http://connecting.website.com/WSDL_Service/GetPrice", 
                        "Content-length: ".strlen($xml_post_string),
                    ); //SOAPAction: your op URL

            $url = $soapUrl;

            // PHP cURL  for https connection with auth
            $ch = curl_init();
            //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_PROXY, sprintf('%s:%s', config('soap.proxy_host'), config('soap.proxy_port')));
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // converting
            $response = curl_exec($ch); 
            curl_close($ch);

            dd($response);
            // converting
            $response1 = str_replace("<soap:Body>","",$response);
            $response2 = str_replace("</soap:Body>","",$response1);

            // convertingc to XML
            $parser = simplexml_load_string($response2);

            dd($parser);

            exit;
    }


}