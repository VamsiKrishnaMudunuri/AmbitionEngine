<?php

namespace App\Http\ViewComposers\Page\Cms;

use Auth;
use Request;
use Cms as PortalCms;

use Illuminate\Support\Str;

use Illuminate\View\View;


class Cctld{
    
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
    	
        $cctld = Str::lower(PortalCms::landingCCTLDDomain(config('dns.default')));

        $view
            ->with('page_cctld_domain', $cctld);
        
    }
    
}