<?php

namespace App\Http\Middleware;

use Utility;
use Oauth;
use Closure;
use Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{

    protected $auth;


    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

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

        $guard = Oauth::guessGuard($guard);

        if ($this->auth->guard($guard)->guest()) {

            if ($request->ajax() || $request->wantsJson()) {

                $code = 401;
                return new JsonResponse(Utility::getHttpErrorMessage($code), $code);

            } else {
                return redirect()->guest('login');
            }

        }else{

            $this->auth->shouldUse($guard);

        }



        return $next($request);

    }

}
