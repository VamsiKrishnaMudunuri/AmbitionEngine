<?php

namespace App\Console\Commands\Openexchangerates;

use Illuminate\Console\Command;
use GuzzleHttp\Client;

class Core extends Command
{
    protected $url = 'https://openexchangerates.org/api';
    protected $name = 'openexchanges';

    private function route($path){

        $arr = [$this->url];

        if(is_string($path)){
            $path = [$path];
        }

        if(count($path) > 0){
           $arr = array_merge($arr, $path);
        }

        $url = sprintf('%s?app_id=%s', implode('/', $arr), env('OPENEXCHANGERATES_APP_ID'));


        return $url;

    }

    protected function request($path, $method = 'GET', $options = array()){

        try{

            $client = new Client();

            $_defaults = [
                'verify' => false,
                'headers' => ['Accept-Encoding' => 'gzip']
            ];

            if(isset($options['headers'])){
                $_defaults['headers'] =  array_merge( $_defaults['headers'], $options['headers']);
                unset($options['headers']);
            }

            $options = array_merge($_defaults, $options);

            return $client->request($method, $this->route($path), $options);

        }catch(Exception $e){

            $this->error($e->getMessage());

        }

    }

}
