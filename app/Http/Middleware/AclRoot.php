<?php

namespace App\Http\Middleware;

use Utility;
use Gate;
use Closure;

use App\Models\Root as RootModel;

class AclRoot
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

        $flag = Gate::allows(Utility::rights('root.slug'), RootModel::class);

        if(!$flag){
            return Utility::httpExceptionHandler(403);
        }

        return $next($request);

    }
}
