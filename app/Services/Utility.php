<?php

namespace App\Services;

use Exception;
use InvalidArgumentException;
use Oauth;
use URL as Link;
use Route;
use Config;
use Request;
use Translator as Translate;
use Session;
use Closure;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\Resource\Item as FractalItem;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class Utility{

    /*
     * Determine the whether it is testing environment.
     *
     * @return bool
     */
    public function isTestingEnvironment(){

        $flag = false;

        $environment = config('app.env');

        if(strcasecmp($environment, 'testing') == 0){
            $flag = true;
        }

        return $flag;
    }

    /*
     * Determine the whether it is development environment.
     *
     * @return bool
     */
    public function isDevelopmentEnvironment(){

        $flag = false;

        $environment = config('app.env');

        if(strcasecmp($environment, 'development') == 0){
            $flag = true;
        }

        return $flag;
    }

    /*
     * Determine the whether it is production environment.
     *
     * @return bool
     */
    public function isProductionEnvironment(){

        $flag = false;

        $environment = config('app.env');

        if(strcasecmp($environment, 'production') == 0){
            $flag = true;
        }

        return $flag;
    }


    public function isDebug(){

        return config('app.debug');

    }

    /**
     * Get value from config/constants.php.
     *
     * @param string $key
     * @return null|string
     *
     */

    public function constant($key = null, $isListFormat = false, $excludeListElement = [], $includeListElement = []){

        $namespace = 'setting';

        $key = strtolower($key);

        $fullname = sprintf('%s.%s', $namespace, $key);

        if(Str::endsWith($key, '.slug')){
            $list = Arr::get(Translate::transSmart($namespace), $key);
        }else{
            $list = Translate::transSmart($fullname);
        }

        if($isListFormat){

            if($this->hasArray($list)){

                $arr = [];

                foreach($list as $key => $value){
                    if(
                        in_array($value['slug'], $excludeListElement) ||
                        (count($includeListElement) > 0 && !in_array($value['slug'], $includeListElement))
                    ){
                        continue;
                    }
                    $arr[$value['slug']] = $value['name'];
                }

                $list = $arr;

            }else{

                $list = array();

            }

        }

        return $list;

    }

    public function constantDefault($key = null, $secondKey = null, $isListFormat = false, $excludeListElement = []){

        $namespace = 'default.' . $key ;

        $value = $this->constant($namespace);

        $arr =  $this->constant(sprintf('%s.%s', $key, $value), $isListFormat, $excludeListElement);

        if(!is_null($secondKey)){
            $arr = $arr[$secondKey];
        }

        return $arr;

    }

    public function rights($key = null, $isListFormat = false, $excludeListElement = []){

        $namespace = 'right';

        $key = strtolower($key);

        $fullname = sprintf('%s.%s', $namespace, $key);

        if(Str::endsWith($key, '.slug')){
            $list = Arr::get(Translate::transSmart($namespace), $key);

        }else{
            $list = Translate::transSmart($fullname);
        }


        if($isListFormat){

            if($this->hasArray($list)){
                $arr = [];

                foreach($list as $key => $value){
                    if(in_array($value['slug'], $excludeListElement)){
                        continue;
                    }
                    $arr[$value['slug']] = $value['name'];
                }

                $list = $arr;

            }

        }

        return $list;

    }

    public function rightsDefault($key = null, $secondKey = null, $isListFormat = false, $excludeListElement = []){

        $namespace = 'default.' . ((is_null($key)) ? 'right' : $key) ;

        $value = $this->rights($namespace);

        $arr =  $this->rights($value, $isListFormat, $excludeListElement);

        if(!is_null($secondKey)){
            $arr = $arr[$secondKey];
        }

        return $arr;

    }

    public function hasNull($str){

        return $str == null;

    }

    /**
     * Determine whether have string value.
     *
     * @param string $str
     * @return bool
     *
     */

    public function hasString($str){

        return ((is_string($str) && strlen($str) > 0)) ? true : false;
    }

    /**
     * Determine whether have array value.
     *
     * @param array $arr
     * @return bool
     *
     */
    public function hasArray($arr){

        return (is_array($arr) && count($arr) > 0) ? true : false;
    }

    /**
     * Determine whether has same route name.
     *
     * @param string $name
     * @return bool
     *
     */
    public function hasSameRouteName($name){

        return (strcasecmp(Route::currentRouteName(), $name) == 0) ? true : false;
    }


    public function htmlDecode($attribute){
        return trim(html_entity_decode($attribute), " '\"\t\n\r\0\x0B");
    }

    public function isJson($string){
        return !empty($string) && is_string($string) && is_array(json_decode($string, true)) && json_last_error() == 0;
    }

    public function jsonEncode($arr){

        $json = '';

        if(is_array($arr)){
            $json = json_encode($arr);
        }

        return $json;
    }

    public function jsonDecode($val){

        $arr = [];

        if($this->isJson($val)){
            $arr = json_decode($val, true);
        }

        return $arr;
    }

    public function replaceLaravelConventionNamespace($value){
        $blacklist = ['App\Http\Controllers'];

        if($this->hasString(($value))){
            $value = preg_replace('/^\\\\/', '', str_replace($blacklist, '', $value));
        }

        return $value;

    }

    public function replaceControllerName(&$str){
        if($this->hasString($str)){
            $str = str_ireplace('controller', '', $str);
        }
    }

    public function controllerNameByNamespace($namespace){


        $controller = new $namespace();

        $fullNamespace = Arr::last(explode("\\", get_class($controller)));
        $this->replaceControllerName($fullNamespace);

        return Str::lower($fullNamespace);

    }

    public function controllerFullNamespace(){

        $route = Route::getCurrentRoute();
        $action = $route->getAction();

        return isset($action['namespace']) ? $action['namespace'] : null;

    }

    public function controllerNamespace(){

        $value = $this->controllerFullNamespace();

        if($this->hasString(($value))){

            $value = $this->replaceLaravelConventionNamespace($value);

        }

        return $value;

    }

    public function controllerName($isExludeControllerSuffix = false){
        $action = class_basename(Route::currentRouteAction());
        $controller = substr($action, 0, (strpos($action, '@') - 0));

        if($isExludeControllerSuffix && $this->hasString($controller)){
            $this->replaceControllerName($controller);
        }
        return  $controller;
    }

    public function actionName(){
        $action = class_basename(Route::currentRouteAction());
        return  substr($action, (strpos($action, '@') + 1));
    }

    public function routeName(){
        return Route::currentRouteName();
    }

    public function module(){

        $controller_namespace = $this->controllerNamespace();
        $controller_name = $this->controllerName(true);
        $full = '';

        if($this->hasString($controller_namespace)){
            $full .= $controller_namespace . "\\";
        }

        if($this->hasString($controller_name)){
            $full .= $controller_name;
        }

        return strtolower(str_replace("\\", '_', $full));

    }

    public function isCurrentRouteName($name){

        return strcasecmp($this->routeName(), $name) == 0;

    }

    public function activeRouteNameIfNecessary($name, $parameters = [], $attributes = [], $isBasePathComparison = false){

        $flag = false;

        if($isBasePathComparison){

            $delimiter = '::';
            $arr = explode($delimiter, $name);

            $name = join($delimiter, array_slice($arr, 0, -1));
            $flag = Route::is(sprintf('%s*', $name));

        }else {

            if ($this->isCurrentRouteName($name)) {

                try {

                    $route_url = Link::route($name, $parameters);
                    $hasQuery = (strpos($route_url, '?') !== false) ? true : false;
                    if (strcasecmp(($hasQuery) ? Link::full() : Link::current(), $route_url) == 0) {
                        $flag = true;
                    }

                } catch (InvalidArgumentException $e) {

                }

            }

        }

        if($flag){

            if (isset($attributes['class']) && $this->hasString($attributes['class'])) {
                $attributes['class'] .= ' active';
            } else {
                $attributes['class'] = 'active';
            }

        }

        return $attributes;
    }

    public function JsonResponseFractalFormat($data, $status = 200, $headers = []){

        $fractal = new Manager();
        $arr = new FractalItem($data, function( $arr){
            return $arr;
        });

        return (new JsonResponse(null, $status, $headers))->setJson(($fractal->createData($arr)->toJson()));

    }

    public function isNativeAppResponse(){
        return strcasecmp(Request::get('_platform'), 'nm') == 0;
    }

    public function isJsonResponseFractalFormat(){
        return strcasecmp(Request::get('_output-format'), 'fractal') == 0;
    }


    public function isForceJsonReponse(){
        return strcasecmp(Request::get('_output'), 'json') == 0;
    }

    public function isJsonRequest(){
        return (Request::ajax() && ! Request::pjax()) || Request::wantsJson() || $this->isForceJsonReponse();
    }


    public function getHttpErrorMessage($code){

        $message = '';

        switch($code){
            case 401:
                $message = Translate::transSmart('exception.unauthorized');
                break;
            case 403:
                $message = Translate::transSmart('exception.forbidden');
                break;
            case 404:
                $message = Translate::transSmart('exception.no_found');
                break;
            case 405:
                $message = Translate::transSmart('exception.method_not_allowed');
                break;
            case 500:
                $message = Translate::transSmart('exception.internal_server_error');
                break;
            case 503:
                $message = Translate::transSmart('exception.service_unavailable');
                break;
        }

        return $message;
    }

    public function jsonErrorReponse($code, $message = null){

        $response = '';

        if($code == 422){

            $message = $message;

        }else {

            if(!$this->hasString($message)) {
                $message = $this->getHttpErrorMessage($code);
            }

        }


        if($this->isJsonResponseFractalFormat()){
            $response = $this->JsonResponseFractalFormat(array($message), $code);
        }else{
            $response = new JsonResponse($message, $code);
        }

        return $response;
    }

    public function abort($code, $message = '', array $headers = [])
    {

        if ($this->isJsonRequest()) {
            return $this->jsonErrorReponse($code, $message);
        }


        return app()->abort($code, $message, $headers);

    }

    public function httpExceptionHandler($code, $e = null, $message = '', array $headers = []){


        if($this->isDebug() && $code == 500 && !Oauth::isApiGuard()){

            if($e instanceof Exception){
                throw $e;
            }

        }


        return $this->abort($code, $message, $headers);

    }

    public function httpHandler(Closure $http, Closure $json){

        if ($this->isJsonRequest()) {
            return $json();
        }else{
            return $http();
        }
    }

    public function reservedRequestInputs(){
        $reserves = ['_token', '_excel', '_output', 'page', 'XDEBUG_SESSION_START'];
        return $reserves;
    }

    public function reservedRequestInputsIncludingOrder(){
        $reserves = $this->reservedRequestInputs();
        $reserves[] = $this->getOrderKeys()['order'];
        $reserves[] = $this->getOrderKeys()['direction'];

        return $reserves;
    }

    public function parseSearchQuery(Closure $callback){

        $inputs = Request::except($this->reservedRequestInputsIncludingOrder());

        foreach($inputs as $key => $value){
            if(!$this->hasString($value)){
                continue;
            }

            $callback($key, $value, function($newVal, $newKey = null, $is_unset = false) use (&$inputs, $key, $value){

                if($is_unset){
                    unset($inputs[$key]);
                }else {
                    if (is_null($newKey)) {
                        $inputs[$key] = $newVal;
                    } else {
                        unset($inputs[$key]);
                        $inputs[$newKey] = $newVal;
                    }
                }

            });
        }

        return $inputs;
    }

    public function parseQueryParams(){

        $request_inputs = Request::except($this->reservedRequestInputs());
        $inputs = array();

        foreach($request_inputs as $key => $value){
            if(!$this->hasString($value)){
                continue;
            }

            $inputs[$key] = $value;

        }

        return $inputs;
    }

    public function getOrderKeys(){

        return array('order' => 'sort', 'direction' => 'sort-direction', 'asc' => 'asc', 'desc' => 'desc');

    }

    public function parseOrderQueryParams(){

        $request_inputs = Request::except($this->reservedRequestInputs());
        $inputs = array();

        if(isset($request_inputs[$this->getOrderKeys()['order']])
            && isset($request_inputs[$this->getOrderKeys()['direction']])
        ){
            $inputs[$this->getOrderParam()] = Str::upper($this->getOrderDirectionParam());
        }

        return $inputs;

    }

    public function createOrderQueryParams($column_name){

        $request_inputs = Request::except($this->reservedRequestInputsIncludingOrder());

        $inputs = array();

        foreach($request_inputs as $key => $value){

            if(!$this->hasString($value)){
                continue;
            }

            $inputs[$key] = $value;

        }

        $inputs[$this->getOrderKeys()['order']] = $column_name;
        $inputs[$this->getOrderKeys()['direction']] = $this->getOrderKeys()['asc'];

        if($this->hasOrder($column_name)){

            if(strcasecmp($this->getOrderDirectionParam(), $this->getOrderKeys()['asc']) == 0)
            {
                $inputs[$this->getOrderKeys()['direction']] = $this->getOrderKeys()['desc'];
            }


            if(strcasecmp($this->getOrderDirectionParam(), $this->getOrderKeys()['desc']) == 0)
            {
                $inputs[$this->getOrderKeys()['direction']] = $this->getOrderKeys()['asc'];
            }

        }

        return $inputs;

    }

    public function getOrderParam(){
        return Request::get($this->getOrderKeys()['order']);
    }

    public function getOrderDirectionParam(){
        return Request::get($this->getOrderKeys()['direction']);
    }

    public function hasOrder($column_name){
        $order = $this->getOrderParam();
        return (!is_null($order) && strcasecmp($order, $column_name) == 0) ? true : false;
    }

    public function isExportExcel(){

        return array_key_exists('_excel', Request::all()) ? true : false;

    }

    public function guid($isRemoveCurlyBracket = false){

        $guid  = '';

        if (function_exists('com_create_guid')){
            $guid = com_create_guid();
        }else{
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = chr(123)// "{"
                .substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12)
                .chr(125);// "}"
            $guid = $uuid;
        }

        if($isRemoveCurlyBracket) {
            $guid = preg_replace("/[{}]/", "", $guid);
        }

        return $guid;

    }

    public function round($val, $precision = 2){

        return round($val, $precision);

    }

    public function roundDifference($val, $round_different_precision = 2, $precision = 2, $isOnlyNeedAbsoluteValue = false){

        $figure = 0;
        $int_val = intval($val);
        $decimal_val = $val - $int_val;

        if($decimal_val > 0){

            $round_val = $this->round($decimal_val, $round_different_precision);
            $is_round_up = ($round_val > $decimal_val) ? true : false;

            if($is_round_up){
                $figure = - ($round_val - $decimal_val);
            }else{
                $figure = $decimal_val - $round_val;
            }


        }

        $figure = $this->round($figure, $precision);

        return (!$isOnlyNeedAbsoluteValue) ? $this->round($figure, $precision) : abs($figure);
    }


    public function divide($dividend, $divisor){

        $val = 0;

        try{

            $val = $dividend / $divisor;

        }catch(Exception $e){


        }

        return $val;

    }

    public function strToArray($str, $delimiter){

        $arr = [];

        if($this->hasString($str)){
            $arr = explode($delimiter, $str);
        }

        return $arr;

    }

    function generateStrongKeys($length = 9, $add_dashes = false, $available_sets = 'luds')
    {
        $sets = array();
        if(strpos($available_sets, 'l') !== false)
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        if(strpos($available_sets, 'u') !== false)
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        if(strpos($available_sets, 'd') !== false)
            $sets[] = '0123456789';
        if(strpos($available_sets, 's') !== false)
            $sets[] = '!@#$%&*?';

        $all = '';
        $password = '';
        foreach($sets as $set)
        {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }
        $all = str_split($all);
        for($i = 0; $i < $length - count($sets); $i++)
            $password .= $all[array_rand($all)];
        $password = str_shuffle($password);
        if(!$add_dashes)
            return $password;
        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while(strlen($password) > $dash_len)
        {
            $dash_str .= substr($password, 0, $dash_len) . '-';
            $password = substr($password, $dash_len);
        }
        $dash_str .= $password;

        return $dash_str;
    }

    function generateRefNo($prefix = null, $length = 10){

        $ref = sprintf('%0' . $length . 'd', substr(mt_rand(1, intval('9'.round(microtime(true)))), 0, $length));

        if(!is_null($prefix)){
            $ref = $prefix . $ref;
        }

        return $ref;
    }

    function getClientIP(){

        $ip = '';

        if(isset($_SERVER["HTTP_CF_CONNECTING_IP"])){
            $ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }else{
            $ip = Request::ip();
        }


        return $ip;
    }

    public function getProxyUrl(){

        $proxy = [];

        if($this->hasString(config('proxy.host'))){

            $proxy[] = config('proxy.host');

        }

        if($this->hasString(config('proxy.port'))){

            $proxy[] = config('proxy.port');

        }

        return implode(':', $proxy);

    }

    public function inArrayWithCaseInsensitive($needle, $haystack){

        $haystack = $this->hasArray($haystack) ? $haystack : [];
        return in_array(strtolower($needle), array_map('strtolower', $haystack));

    }

    public function convertStringToLowerCaseInArray($haystack){

        $haystack = $this->hasArray($haystack) ? $haystack : [];
        return array_map('strtolower', $haystack);

    }
    
    public function display($value, $default = '-'){
    	
    	$val = ($value) ? $value : $default;
    	
    	return $val;
    	
    }
	
	public function splitFullNameToFirstLastName($name) {
		$name = trim($name);
		$last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
		$first_name = trim( preg_replace('#'.$last_name.'#', '', $name ) );
		return array($first_name, $last_name);
	}

}