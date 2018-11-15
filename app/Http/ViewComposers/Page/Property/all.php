<?php

namespace App\Http\ViewComposers\Page\Property;

use Auth;
use Request;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;

use App\Models\Temp;

class all{
    
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
       

        $location = (new Temp())->getPropertyLocationMenu();

        $view
            ->with('page_property_all', $location);
        
    }
    
}