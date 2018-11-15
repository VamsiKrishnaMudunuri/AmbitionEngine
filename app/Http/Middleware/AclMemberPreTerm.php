<?php

namespace App\Http\Middleware;

use Utility;
use Domain;
use Auth;
use Gate;
use Closure;
use Illuminate\Support\Arr;

use App\Libraries\Model\Model;
use App\Libraries\Model\MongoDB;
use App\Models\Temp;
use App\Mail\Member;

class AclMemberPreTerm
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
	

	    $block = false;
        $user = Auth::user();
	    $company = (new Temp())->getCompanyDefault();

	    if( $user->isMyCompanyWithAgentOnly($company->getKey())){
	    	$block = true;
	    }

        if($block) {
	        return Utility::httpExceptionHandler(403);
        }

	    return $next($request);

    }

  
}
