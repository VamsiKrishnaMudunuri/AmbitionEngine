<?php

namespace App\Portals;

use Exception;
use DateTime;
use DateTimeZone;
use App;
use Auth;
use Config;
use Utility;
use Domain;
use CLDR;
use Request;
use Translator as Translate;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;


class Cms{

	public function isSupportCCTLDDomain($cctldd){
		
		$flag = (Utility::inArrayWithCaseInsensitive($cctldd, config('dns.support'))) ? true : false;
		
		return $flag;
		
	}
	
    public function landingCCTLDDomain($default = null){

        $default = Utility::hasString($default) ? Str::lower($default) : $default;

        $landing = Str::lower(Domain::subdomain());

        $decide = (Utility::inArrayWithCaseInsensitive($landing, config('dns.support'))) ? $landing : $default;

        return $decide;

    }

    public function cctldDomainInfo($country_code){

        $name = CLDR::getCountryByCode($country_code);
        $replace = sprintf('%s://%s.', config('app.protocol'), str::lower($country_code));
        $target = config('app.url');
        $url =  preg_replace('/https?:\/\/(www\.)?/i', $replace,  $target);

        $arr = [
            'name' => $name,
            'url' => $url
        ];

        return $arr;


    }

    
    
}