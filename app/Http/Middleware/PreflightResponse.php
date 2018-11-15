<?php

namespace App\Http\Middleware;

use Utility;
use Domain;
use Auth;
use Gate;
use Closure;
use Illuminate\Support\Arr;



class PreflightResponse
{


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        $method = $request->getMethod();
        $header = $request->header('Access-Control-Request-Headers');

        if(strcasecmp($method, 'OPTIONS') == 0 && strcasecmp($header, 'X-XSRF-TOKEN') == 0){
            return response('')->header('Access-Control-Allow-Headers', 'X-XSRF-TOKEN');
        }

        return $next($request);

    }



    
}
