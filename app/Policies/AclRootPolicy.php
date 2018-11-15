<?php

namespace App\Policies;

use Exception;
use Utility;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\GenericUser;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Models\User;
use App\Models\Root;

class AclRootPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function before(User $user, $ability)
    {

        return $this->{$ability}($user);

    }

    public function root(User $user){


       return $user->isRoot();

    }


}
