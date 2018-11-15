<?php

namespace App\Opts;

use Utility;
use Route;
use Config;
use Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class Domain{

    public function isRoot(){
        return strcasecmp(Config::get('app.root_url'), Request::root()) == 0;
    }

    public function isAdmin(){
        return strcasecmp(Config::get('app.admin_url'), Request::root()) == 0;
    }

    public function isMember(){
        return strcasecmp(Config::get('app.member_url'), Request::root()) == 0;
    }

	public function isAgent(){
		return strcasecmp(Config::get('app.agent_url'), Request::root()) == 0;
	}
	
	public function isSocket(){

        return strcasecmp(Config::get('socket.url'), Request::root()) == 0;
    }
	
	public function isCMS(){
  
		return !($this->isRoot() || $this->isAdmin() || $this->isMember() || $this->isAgent() || $this->isSocket());
		
	}

    public function isPortal(){
        return !$this->isCMS() && !$this->isSocket();
    }

    public function subdomain($url = null){

        $url = (is_null($url)) ? Request::root() : $url;
        $parsedUrl = parse_url($url);
        $host = explode('.', $parsedUrl['host']);
        $subdomain = Arr::first(array_slice($host, 0, count($host) - 2 ));

        return $subdomain;

    }

    public function route($name, $default = null){

        $subdomain = $this->subdomain();

        if(Utility::hasString($subdomain)){
            $name = sprintf('%s::%s', $subdomain, $name);
        }else{
            $name = ($default) ? sprintf('%s::%s', $default, $name) : sprintf('%s', $name);
        }

        return $name;

    }

}