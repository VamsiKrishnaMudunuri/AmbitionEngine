<?php

namespace App\Http\Middleware;

use Shortcode As WebwizoShortcode;
use Closure;


class Shortcode
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

        WebwizoShortcode::enable();

        return $next($request);

    }

}
