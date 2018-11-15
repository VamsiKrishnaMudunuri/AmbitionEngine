<?php

namespace App\Http\Middleware;

use Oauth;
use Closure;
use Translator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
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
        if (Auth::guard(Oauth::guessGuard($guard))->check()) {

            if ($request->ajax() || $request->wantsJson()) {

                $code = 409;

                return new JsonResponse(Translator::transSmart('app.You have already logged in.', 'You have already logged in.'), $code);

            }else {

                return redirect('/');

            }

        }

        return $next($request);
    }
}
