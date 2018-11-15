<?php

namespace App\Libraries\Auth;

use App\Libraries\Auth\EloquentUserProvider;
use Illuminate\Auth\AuthManager as IlluminateAuthManager;

class AuthManager extends IlluminateAuthManager
{

    protected function createEloquentProvider($config)
    {
        return new EloquentUserProvider($this->app['hash'], $config['model']);
    }
}