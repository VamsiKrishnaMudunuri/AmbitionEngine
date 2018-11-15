<?php

namespace App\Providers;

use odannyc\Laravel\BraintreeServiceProvider as BTServiceProvider;

use Braintree_Configuration;
use Braintree_ClientToken;

use Blade;

class BraintreeServiceProvider extends BTServiceProvider {


	public function boot()
	{

		$this->publishes([
		    __DIR__.'/../../vendor/odannyc/laravel5-braintree/src/config/braintree.php' => config_path('odannyc.braintree.php'),
		]);

		Braintree_Configuration::environment(
			$this->app['config']->get('odannyc.braintree.environment')
		);
		
		Braintree_Configuration::merchantId(
			$this->app['config']->get('odannyc.braintree.merchantId')
		);

		Braintree_Configuration::publicKey(
			$this->app['config']->get('odannyc.braintree.publicKey')
		);

		Braintree_Configuration::privateKey(
			$this->app['config']->get('odannyc.braintree.privateKey')
		);

		$encryptionKey = $this->app['config']->get('odannyc.braintree.clientSideEncryptionKey');

        Blade::directive('braintreeClientSideEncryptionKey', function () use($encryptionKey){
           return $encryptionKey;
        });

        Blade::directive('braintreeClientToken', function () use($encryptionKey){
            return Braintree_ClientToken::generate();
        });

	}



}
