<?php

namespace App\Libraries\Auth;

use Illuminate\Auth\EloquentUserProvider as IlluminateEloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class EloquentUserProvider extends IlluminateEloquentUserProvider
{

    public function updateRememberToken(UserContract $user, $token)
    {
        $user->setRememberToken($token);
        $user->safeForceSave();
    }

}