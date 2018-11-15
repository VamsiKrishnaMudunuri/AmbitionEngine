<?php

namespace App\Libraries\Soap;

use SoapClient;


class CSoapClient extends SoapClient {

    public function __construct($wsdl, $options = array())
    {

       if(config('soap.proxy_host')){
           $options['proxy_host'] = config('soap.proxy_host');
       }

       if(config('soap.proxy_port')){
            $options['proxy_port'] = config('soap.proxy_port');
       }


       parent::__construct ($wsdl, $options);


    }

    public function __setEndpoint($url){

        $url = parse_url($url);

        $this->__setLocation(sprintf('%s://%s:%s%s', $url['scheme'], $url['host'], config('soap.service_port'), $url['path']));

    }


}