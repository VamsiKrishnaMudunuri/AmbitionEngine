<?php

namespace App\Http\Middleware;

use Utility;
use Closure;


class Json
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

        if(!Utility::isJsonRequest()){
            return Utility::httpExceptionHandler(405);
        }
        
        return $next($request);

    }

}
