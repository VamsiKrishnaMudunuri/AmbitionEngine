<?php

namespace App\Services;

use Session;
use Utility as Util;
use Sess as Ses;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Collective\Html\HtmlBuilder as CollectiveHtmlBuilder;

class HtmlBuilder extends CollectiveHtmlBuilder{

    public function title($val, $default = '', $delimiter = '|'){

        $v = Util::hasString($val) ? $val : $default;

        if(strcasecmp($val, $default) != 0){
            $v = sprintf('%s %s %s', $val, $delimiter, $default);
        }


        return $v;
    }
    
    
    public function linkRoute($name = null, $title = null, $parameters = [], $attributes = [], $isBasePathComparison = false)
    {
        $attributes = Util::activeRouteNameIfNecessary($name, $parameters, $attributes, $isBasePathComparison);

        return
            is_null($name) ?
            $this->toHtmlString('<a href="' . 'javascript:void(0);' . '"' . $this->attributes($attributes) . '>' . $this->entities($title) . '</a>') :
            $this->link($this->url->route($name, $parameters), $title, $attributes);
    }

    public function linkRouteWithIcon($name = null, $title = null, $icon = null, $parameters = [], $attributes = [], $isBasePathComparison = false)
    {

        $secure = null;
        $url = (is_null($name)) ? 'javascript:void(0);' : $this->url->to($this->url->route($name, $parameters), [], $secure);
    
        $attributes = Util::activeRouteNameIfNecessary($name, $parameters, $attributes, $isBasePathComparison);

        $content = '';

        if(is_null($icon)){
            $content = '<span>' . $this->entities($title) . '</span>';
        }else{
            $content = '<i class="fa ' . $icon . '"></i> <span>' .  $this->entities($title) . '</span>';
        }

        return $this->toHtmlString('<a href="' . $url . '"' . $this->attributes($attributes) . '>' . $content . '</a>');

    }
    
    public function linkRouteWithLRIcon($name = null, $title = null, $licon = null, $ricon = null, $parameters = [], $attributes = [], $isBasePathComparison = false)
    {
  
        $secure = null;
        $url = (is_null($name)) ? 'javascript:void(0);' : $this->url->to($this->url->route($name, $parameters), [], $secure);

        $attributes = Util::activeRouteNameIfNecessary($name, $parameters, $attributes, $isBasePathComparison);
        
        $content = '';
        
        if(is_null($licon) && is_null($ricon)){
            $content = '<span>' . $this->entities($title) . '</span>';
        }else{
            $arr = [];
            if(!is_null($licon)){
                $arr[] = '<i class="fa ' . $licon . '"></i>';
            }
            if(!is_null($title)){
                $arr[] = ' <span>' .  $this->entities($title) . '</span> ';
            }
            
            if(!is_null($ricon)){
                $arr[] = '<i class="fa ' . $ricon . '"></i>';
            }
            
            $content = implode('', $arr);
        }
        
        return $this->toHtmlString('<a href="' . $url . '"' . $this->attributes($attributes) . '>' . $content . '</a>');
        
    }

    public function linkRouteWithIconAndBadge($name = null, $title = null, $icon = null, $badge = null, $parameters = [], $attributes = [], $isBasePathComparison = false)
    {

        $secure = null;
        $url = (is_null($name)) ? 'javascript:void(0);' : $this->url->to($this->url->route($name, $parameters), [], $secure);
    
        $attributes = Util::activeRouteNameIfNecessary($name, $parameters, $attributes, $isBasePathComparison);
        
        $content = '';
        $badge = ((is_null($badge) || $badge <= 0) ? '' : '<span class="nice-badge">' . $badge . '</span>');

        if(is_null($icon)){
            $content = '<span>' . $this->entities($title) . '</span>' . $badge;
        }else{
            $content = '<i class="fa ' . $icon . '"></i> <span>' .  $this->entities($title) . '</span>' . $badge;
        }


        return $this->toHtmlString('<a href="' . $url . '"' . $this->attributes($attributes) . '>' . $content . '</a>');


    }

    public function linkRouteWithIconAndUndo($name = null, $title = null, $icon = null, $parameters = [], $attributes = [], $isDelete = false, $isBasePathComparison = false)
    {

        $secure = null;

        $defaultParameters = [
            'controller' => Util::controllerName(),
            'route' => Util::routeName(),
            'action' => ($isDelete) ? 'delete' : 'edit'
        ];

        foreach($parameters as $key => $value){
            $defaultParameters[$key] = $value;
        }

        $url = (is_null($name)) ? 'javascript:void(0);' : $this->url->to($this->url->route($name, $defaultParameters), [], $secure);
    
        $attributes = Util::activeRouteNameIfNecessary($name, $parameters,  $attributes, $isBasePathComparison);
        
        $content = '';

        if(is_null($icon)){
            $content = '<span>' . $this->entities($title) . '</span>';
        }else{
            $content = '<i class="fa ' . $icon . '"></i> <span>' .  $this->entities($title) . '</span>';
        }


        return $this->toHtmlString('<a href="' . $url . '"' . $this->attributes($attributes) . '>' . $content . '</a>');


    }

    public function skin($url, $attributes = [], $secure = null){

        $url = $this->url->skin($url, $secure);

        $extension = Str::lower(pathinfo($url, PATHINFO_EXTENSION));

        $val = '';

        if(strcasecmp($extension, 'css') == 0){
            $attributes = $attributes +  ['rel' => 'stylesheet', 'media' => 'all', 'charset' => 'utf-8'];
            $val = $this->style($url, $attributes, $secure);
        }else if(strcasecmp($extension, 'js') == 0){
            $attributes = $attributes +  ['charset' => 'utf-8'];
            $val = $this->script($url, $attributes, $secure);
        }else{
            $val = $this->image($url, null, $attributes, $secure);
        }


        return $val;
    }

    public function skinForVendor($url, $attributes = [], $secure = null){

        $url = $this->url->skinForVendor($url, $secure);

        $extension = Str::lower(pathinfo($url, PATHINFO_EXTENSION));

        $val = '';

        if(strcasecmp($extension, 'css') == 0){
            $attributes = $attributes +  ['charset' => 'utf-8'];
            $val = $this->style($url, $attributes, $secure);
        }else if(strcasecmp($extension, 'js') == 0){
            $attributes = $attributes +  ['type' => 'text/javascript', 'charset' => 'utf-8'];
            $val = $this->script($url, $attributes, $secure);
        }


        return $val;
    }

    public function image($url, $alt = null, $attributes = [], $secure = null)
    {
        $attributes['alt'] = $alt;

        return $this->toHtmlString('<img src="' . ((strpos($url, 'data:image') >= 0) ? $url : $this->url->asset($url, $secure)) .
            '"' . $this->attributes($attributes) . '>');
    }

    public function errorBox($message, $class = [], $dismiss = true){

        $str = '';

        if(!empty($message)) {

            $str = sprintf('<div class="%s">', implode(' ', array_merge(['alert alert-danger text-left'], $class)));

            $str .= '<a href="#" class="close"' . ($dismiss ? ' data-dismiss="alert"' : '') . ' aria-label="close"' . ($dismiss ? '' : ' onclick="$(this).parent().hide(); return false;"') .  '>&times;</a>';

            $str .= "<ul>";

            if (Util::hasString($message)) {

                $str .= "<li>" . $message . "</li>";

            } else {

                foreach ($message as $key => $value) {

                    $str .= "<li>" . $value . "</li>";
                }

            }
            $str .= "</ul>";

            $str .= "</div>";

        }

        return $this->toHtmlString($str);

    }

    public function successBox($message, $class = [], $dismiss = true){

        $str = '';

        if(!empty($message)) {

            $str = sprintf('<div class="%s">', implode(' ', array_merge(['alert alert-success text-left'], $class)));

            $str .= '<a href="#" class="close"' . ($dismiss ? ' data-dismiss="alert"' : '')  . ' aria-label="close"' . ($dismiss ? '' : ' onclick="$(this).parent().hide(); return false;"') .  '>&times;</a>';

            $str .= "<ul>";

            if(Util::hasString($message)){

                $str .= "<li>" . $message . "</li>";

            }else {

                foreach ($message as $key => $value) {

                    $str .= "<li>" . $value . "</li>";

                }

            }

            $str .= "</ul>";

            $str .= "</div>";

        }

        return $this->toHtmlString($str);

    }

    public function validation($model, $key, $default = null){

        $str = '';

        if(Ses::hasErrors()) {
            
            $errors = ses::getErrors()->toArray();

            if(!is_null($model) && array_key_exists($model->getTable(), $errors) && Util::hasArray($errors[$model->getTable()])){

                if ($first = Arr::first($errors[$model->getTable()])) {
                    if (Util::hasArray($first)) {
                        unset($errors[$model->getTable()]);
                        $errors = array_merge($errors, $first);
                    }
                }

            }

            $arr = [];
            $keys = is_string($key) ? array($key) : (Util::hasArray($key) ? $key : []);

            foreach($keys as $k => $v){
        
                $message = Arr::get($errors, $v);
        
                if(Util::hasString($message)){
                    array_push($arr, $message);
                }else if(Util::hasArray($message)){
                    $arr = $message;
                }
                
            }
            
            if(Util::hasArray($arr)) {
                $str = $this->errorBox($arr);
            }

        }

        if(empty($str) && Util::hasString($default)){
            $str = $this->errorBox($default);
        }

        return $this->toHtmlString($str);
        
    }

    public function error($default = null){

        return $this->validation(null, Ses::getKey('errors'), $default);
    }
    
    public function success(){
    
        $success = Session::get(Ses::getKey('success'));
        
        return Util::hasString($success) ? $this->successBox($success) : '';
    }
    
    public function action($actions = [], $cols = 3){

        $str = '';

        $numberOfAction = count($actions);

        if($numberOfAction  > 0) {

            $rows = ceil($numberOfAction / $cols);

            if($rows <= 0){
                $rows = 1;
            }

            for($row = 0; $row < $rows; $row++){
                $startCols = $row * $cols;
                $endCols = (($row * $cols) + $cols);
                for($col = $startCols; $col < $endCols; $col++){
                    if($col < $numberOfAction) {
                        $str .= $actions[$col] . ' ';
                    }
                }
                if($row + 1 < $rows) {
                    $str .= '<hr />';
                }
            }

        }

        return $this->toHtmlString($str);
    }
    
    public function breadcrumb($arr){
        $str = '';
        
        if($arr) {
            $count = count($arr);
            $str = '<ol class="breadcrumb">';

                foreach ($arr as $index => $route) {
           
                    $str .= '<li' . (($index == ($count - 1)) ? ' class="active"' : '') . '>'  . (
                            ($index == ($count - 1)) ? (isset($route[1]) ? $route[1] : '' )  :
                            (
                                (filter_var(Arr::get($route, 0), FILTER_VALIDATE_URL)) ?
                                $this->toHtmlString('<a href="' . Arr::get($route, 0) . '"' . $this->attributes(Arr::get($route, 3, array())) . '>' . $this->entities(Arr::get($route, 1)) . '</a>')
                                :
                                call_user_func_array(array($this, 'linkRoute'), $route)
                            )

                        ) . '</li>';
                    
                }
                
            $str .= '</ol>';
            
        }
      
        return $this->toHtmlString($str);
        
    }

    public function order($name = null, $field = null, $title = null, $parameters = [], $attributes = []){

        $icon = (Util::hasOrder($field)) ? sprintf('fa-sort-%s', Util::getOrderDirectionParam($field)) : 'fa-sort';
        $params = Util::createOrderQueryParams($field);
        $parameters = array_merge($parameters, $params);

        if(isset($attributes['class'])){
            $attributes['class'] .= ' sorting-field';
        }else{
            $attributes['class'] = 'sorting-field';
        }

        return $this->linkRouteWithLRIcon($name, $title, null, $icon, $parameters, $attributes);

    }

}