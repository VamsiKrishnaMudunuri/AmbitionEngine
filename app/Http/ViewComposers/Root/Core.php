<?php

namespace App\Http\ViewComposers\Root;

use Auth;
use Request;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;

use App\Models\User;

class Core{
    
    public function __construct()
    {
     
    }
    
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {

        $module_auth_user = (new User())->getCompletedOne((Auth::check()) ? Auth::user()->getKey() : -1);

        $view
            ->with('root_module_auth_user', $module_auth_user);
        
    }
    
}