<?php

namespace App\Services;

use Utility as Util;
use Session;
use Illuminate\Contracts\Support\MessageProvider;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

class Sess{

    private $keys = [
        'create' => 'create',
        'update' => 'update',
        'delete' => 'delete',
        'undo' => 'undo',
        'message' => 'message',
        'success' => 'success',
        'error' => 'error',
        'errors' => 'errors',
    ];

    private $bucketKey = [
        'undo' => 'undo_bucket'
    ];

    public function getKey($key){
        return $this->keys[$key];
    }

    public function isActionDone($key){

        return (Session::get($this->getKey($key))) ? true : false;
    }

    public function hasMessage(){

        return (Session::get($this->getKey('message'))) ? true : false;
    }

    public function getMessage(){

        return $this->hasMessage() ? Session::get($this->getKey('message')) : null;
    }

    public function getUndoKey($controllerName, $routeName, $id){


        return $this->bucketKey['undo'] . '.' . strtolower($controllerName)  . '.' . strtolower($routeName) . '.' . $id;

    }

    public function hasUndo($controllerName, $routeName, $id){

        $key = $this->getUndoKey($controllerName, $routeName, $id);

        $attributes = Session::get($key, []);

        return (Util::hasArray($attributes)) ? true : false;

    }

    public function setUndo($controllerName, $routeName, $id, $attributes, $originalAttributes){

        $key = $this->getUndoKey($controllerName, $routeName, $id);

        $attributes = array_intersect_key($originalAttributes, $attributes);

        Session::put($key, $attributes);

    }

    public function getUndo($controllerName, $routeName, $id){

        $key = $this->getUndoKey($controllerName, $routeName, $id);

        $attributes = Session::get($key, []);

        return $attributes;

    }

    public function cleanUndo($controllerName, $routeName, $id){

        $key = $this->getUndoKey($controllerName, $routeName, $id);

        Session::forget($key, []);

    }


    public function hasErrors(){

        $flag = false;

        $errors = $this->getErrors();

        if(isset($errors) && $errors->any()){
            $flag = true;
        }

        return $flag;

    }

    public function getErrors(){
        return  Session::get($this->getKey('errors'));
    }

    public function setErrors($message, $key = 'default'){

        $value = new MessageBag(array($this->getKey('errors') => (array) $message));

        $errors = Session::get($this->getKey('errors'), new ViewErrorBag);

        if($errors->hasBag($key)){

            $messageBag = $errors->getBag($key);

            $messageBag->merge($value);

        }else{
            $errors->put($key, $value);
        }

        Session::now($this->getKey('errors'), $errors);

    }

}