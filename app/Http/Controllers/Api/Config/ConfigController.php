<?php

namespace App\Http\Controllers\Api\Config;

use Exception;
use ReflectionObject;
use Translator;
use CLDR;
use Sess;
use Auth;
use Utility;
use SmartView;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class ConfigController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function versionStatus(Request $request){


            try {


                $arr   = array(
                    'version' => config('api.version'),
                    'force_update' => 0

                );

                $given_version = $request->get('version');

            if($given_version < $arr['version']){
                $arr['force_update'] = 1;
            }

        }catch(Exception $e){


                return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(null, $arr);



    }

    public function version(Request $request){

        try {


            $arr   = array(
                'version' => config('api.version'),

            );



        }catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, $arr);

    }

    public function sandboxes(Request $request){

        try {

            $public = config('filesystems.disks.s3.root');

            $arr = array(
                'config' => [
                    'cdn' => config('app.cdn'),
                    'image' => ['mainPath' => $public . '/' . config('sandbox.image.mainPath')],
                    'file' => ['mainPath' => $public . '/' . config('sandbox.file.mainPath')]
                ],
                'sandboxes' => array()
            );

            $namespace = "\\App\\Models\\";

            $entries = array(

                'mysql' => ['namespace' => "\\App\\Models\\", 'models' => glob(base_path('app/Models/*.php'))],
                'mongodb' => ['namespace' => "\\App\\Models\\MongoDB\\", 'models' => glob(base_path('app/Models/MongoDB/*.php'))]


            );

            foreach($entries as $key => $entry){

                $namespace = $entry['namespace'];

                foreach($entry['models'] as $model) {

                    $class_name = basename($model, ".php");
                    $full_qualified_class_name = $namespace . $class_name;
                    $instance = new $full_qualified_class_name;
                    $ref = new ReflectionObject($instance );

                    if (!isset($arr['sandboxes'][$key])) {
                        $arr['sandboxes'][$key] = array();
                    }

                    if (isset($instance::$sandbox)) {

                        if($ref->getProperty('sandbox')->getDeclaringClass()->getShortName() == $class_name){
                            if (Utility::hasArray($instance::$sandbox)) {
                                $arr['sandboxes'][$key][$class_name] = $instance::$sandbox;
                            }
                        }

                    }

                }

            }

        }catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, $arr);

    }

    public function categories(Request $request){

        try {

            $arr = array();

            $lists = Utility::constant('post_categories');

            foreach($lists as $key => $value){
                $arr[] = array(
                    'id' => $value['slug'],
                    'name' => $value['name'],
                );
            }

            $arr = array('categories' => $arr);

        }catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, $arr);

    }

    public function wallet(Request $request){

        try {

            $arr = array('wallet' => config('wallet'));


        }catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, $arr);


    }

    public function businessOpportunitiesType(Request $request){

        try {

            $arr = array();

            $lists = Utility::constant('business_opportunity_type');

            foreach($lists as $key => $value){
                $arr[] = array(
                    'id' => $value['slug'],
                    'name' => $value['name'],
                );
            }

            $arr = array('business_opportunity_type' => $arr);

        }catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, $arr);

    }

    public function businessOpportunitiesIndustry(Request $request){

        try {

            $arr = array();

            $lists = Utility::constant('industries');

            foreach($lists as $key => $value){
                $arr[] = array(
                    'id' => $value['slug'],
                    'name' => $value['name'],
                );
            }

            $arr = array('business_opportunity_industry' => $arr);

        }catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, $arr);

    }


}
