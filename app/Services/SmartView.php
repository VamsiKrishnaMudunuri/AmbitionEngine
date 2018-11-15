<?php

namespace App\Services;

use Utility as Util;
use Translator as Translate;
use ReflectionObject;
use PDF;
use Excel;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Http\JsonResponse;

class SmartView{


    public function createPath($view = null, $level = 0){

        $namespace = Util::controllerFullNamespace();
        $method = Util::actionName();

        if(!Util::hasString($namespace) || !Util::hasString($method)) {

            $stack = last(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $level));
            $instance = new $stack['class'];
            $reflection = new ReflectionObject($instance);
            $namespace = $reflection->getNamespaceName();
            $class = $reflection->getShortName();
            $method = $stack['function'];

        }

        $namespace = Util::replaceLaravelConventionNamespace($namespace);

        $directory = strtolower(($namespace) ? $namespace : '');
        $file = ($view) ? $view : snake_case($method);

        $arr = [];

        if($directory){
            array_push($arr, $directory);
        }

        if($file){
            array_push($arr, $file);
        }


        $path = implode(DIRECTORY_SEPARATOR, $arr);

        return $path;

    }

    public function pathToDotDelimiter($path){

        if(Util::hasString($path)){
            $path = str_replace(array("\\", DIRECTORY_SEPARATOR), '.', $path);
        }

        return $path;

    }

    /**
     * Render view base on namespace.
     *
     * @param  mixed   $view
     * @param  array   $data
     * @param  array   $mergeData
     * @return \Illuminate\View\View
     *
     */
    public function render($view = null, $data = [], $mergeData = []){

        if(Util::isJsonRequest()){

            if((!is_bool($view) && !Util::hasString($view) || Util::isForceJsonReponse())){

                $data = array_merge($data, $mergeData);

                //2017-07-29 martin: it shouldn't be like this and must remove slowly
                //$data = Arr::last($data);

                if(Util::isJsonResponseFractalFormat()){

                   return Util::JsonResponseFractalFormat($data);

                }else{

                    return new JsonResponse($data);

                }



            }

        }

        if(is_bool($view)){
            $view = null;
        }


        $path = $this->pathToDotDelimiter($this->createPath($view, 5));


        return view($path, $data, $mergeData);

    }

    /**
     * Render PDF base on namespace.
     *
     * @param  string  $view
     * @param  array   $data
     * @param  array   $mergeData
     * @return \Illuminate\View\View
     *
     */
    public function pdf($view = null, $data = [], $filename = null, $mergeData = [], $options = [], Closure $cb = null){

        $filename = is_null($filename) ? 'document.pdf' : $filename . '.pdf';

        $path = $this->pathToDotDelimiter($this->createPath($view, 6));

        $proxy = [];


        $defaults = [
            'margin-top' =>  10,
            'margin-right' => 10,
            'margin-bottom' => 10,
            'margin-left' => 10,
            'proxy' => Util::getProxyUrl()
        ];

        $defaults = array_merge($defaults, $options);

        if(!Util::hasString($defaults['proxy'])){
            unset($defaults['proxy']);
        }

        $pdf = PDF::loadView($path, $data, $mergeData)->setOptions($defaults)
            ->setOption('footer-spacing', 2)
            ->setOption('footer-center', sprintf("%s [page] %s [topage]", Translate::transSmart('app.Page', 'Page'), Translate::transSmart('app.of', 'of')))
            ->setOption('footer-font-size', 8);
        //->setOption('footer-html', sprintf("<div class=\"footer\" style=\"text-align:center;\">%s [page] %s [topage]</div>", Translate::transSmart('app.Page', 'Page'), Translate::transSmart('app.of', 'of')));

        return $cb($pdf, $filename);

    }

    public function excel($view = null, $data = [], $filename = null, $sheet_name =  null, $mergeData = [], $options = [], Closure $cb = null){

        $filename = is_null($filename) ? 'document' : $filename;
        $sheet_name = is_null($sheet_name) ? 'new sheet' : $sheet_name;

        $path = $this->pathToDotDelimiter($this->createPath($view, 6));

        if(is_null($view)){
            $path = sprintf('%s_excel', $path);
        }


        Excel::create($filename, function($excel) use ($data, $sheet_name, $mergeData, $cb, $path) {

            if(!is_null($cb)){

                $cb($excel);

            }else{

                $excel->sheet($sheet_name, function($sheet) use ($data, $mergeData, $path) {

                    $sheet->loadView($path, $data, $mergeData);

                });

            }


        })->export('xls');

    }

}