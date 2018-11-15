<?php

namespace App\Http\Controllers\Api\Property;

use Exception;
use Illuminate\Routing\Route;
use URL;
use Log;
use Storage;
use SmartView;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Temp;
use App\Models\Property;


class PropertyController extends Controller
{

    public function __construct()
    {

        parent::__construct(new Property());

    }

    public function search(Request $request){

        $list = $this->getModel()->search($request->get('query'));


        return SmartView::render(null, $list->toArray());

    }

    public function listActive(Request $request){

        $temp = (new Temp())->getPropertyMenu();

        $properties = array();

        foreach($temp as $ctk => $cities){
            $arr = array(
                'name' => $ctk,
                'locations' => array()
            );
            foreach($cities as $ofk => $office){

                 $arr['locations'][]  = array('id' => $ofk, 'name' => $office);

            }

            $properties[] = $arr;

        }


        return SmartView::render(null, compact('properties'));

    }

}