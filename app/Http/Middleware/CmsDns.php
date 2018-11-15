<?php

namespace App\Http\Middleware;


use Utility;
use Domain;
use Auth;
use Gate;
use Closure;
use Illuminate\Support\Arr;

class CmsDns
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


        return $next($request);

    }

}
