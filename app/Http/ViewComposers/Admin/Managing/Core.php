<?php

namespace App\Http\ViewComposers\Admin\Managing;


use Config;
use Illuminate\View\View;

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


        $view
            ->with('managing_module', Config::get('acl.admin.managing.listing.listing'));

        
    }
    
}