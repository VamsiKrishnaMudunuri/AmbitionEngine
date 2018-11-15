<?php

namespace App\Console\Commands\Openexchangerates;

use Exception;
use Utility;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use App\Models\ExternalApiRequest;
use App\Models\Currency;

class Sync extends Core
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'openexchangerates:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync the latest currencies';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //

        try {

            $path = 'latest.json';

            $externalApiRequest = new ExternalApiRequest();

            $externalApiRequest->getConnection()->transaction(function () use ($externalApiRequest, $path) {

                $today = Carbon::now();

                $request = $externalApiRequest
                    ->where('name', '=', $this->name)
                    ->where('path', '=', $path)
                    ->lockForUpdate()
                    ->first();

                if(is_null($request)){
                    $request = new ExternalApiRequest();
                }

                if ( !$request->exists || !$today->isSameDay($request->getAttribute($request->getUpdatedAtColumn())) ) {

                    $options = array(
                        'headers' => array()
                    );

                    $headers = $request->headers;

                    if(array_key_exists('Etag', $headers) && array_key_exists('Last-Modified', $headers)){

                        $options['headers'] = array(
                            'If-None-Match' => Arr::first($headers['Etag']),
                            'If-Modified-Since' => Arr::first($headers['Last-Modified'])
                        );

                    }

                    $response = $this->request($path, 'GET', $options);
                    $code = $response->getStatusCode();

                    if($code == 200) {

                        $headers = $response->getHeaders();
                        $body = Utility::jsonDecode($response->getBody()->getContents());
                        $base = $body['base'];
                        $rates = $body['rates'];

                        foreach($rates as $quote => $rate){

                            $currency = (new Currency())
                                ->where('base', '=', $base)
                                ->where('quote', '=', $quote)
                                ->lockForUpdate()
                                ->first();

                            if(is_null($currency)){
                                $currency = new Currency();
                            }

                            $currency->fill(array(
                                'base' => $base,
                                'quote' => $quote,
                                'base_amount' => 1,
                                'quote_amount' => Utility::round($rate, 6)
                            ));

                            $currency->save();

                        }

                        $request->fill(array(
                            'name' => $this->name,
                            'path' => $path,
                            'code' => $code,
                            'headers' => $headers
                        ));

                        $request->save();

                    }

                }


            });

        }catch (Exception $ex){

            $this->error($ex->getMessage());

        }


    }

}
