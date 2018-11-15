<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\AuthServiceProvider as IlluminateAuthServiceProvider;
use App\Libraries\Auth\AuthManager;
use App\Models\Root;
use App\Models\Admin;
use App\Models\Agent;
use App\Models\Member;
use App\Models\Company;
use App\Models\Acl;
use App\Policies\AclRootPolicy;
use App\Policies\AclAdminPolicy;
use App\Policies\AclMemberPolicy;
use App\Policies\AclAgentPolicy;


class AuthServiceProvider extends IlluminateAuthServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Root::class => AclRootPolicy::class,
        Admin::class => AclAdminPolicy::class,
        Member::class => AclMemberPolicy::class,
        Agent::class => AclAgentPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        //

        $this->registerPolicies();

        Passport::routes();

    }

    public function registerPolicies()
    {
        foreach ($this->policies as $key => $value) {
            Gate::policy($key, $value);
        }
    }

    protected function registerAuthenticator()
    {
        $this->app->singleton('auth', function ($app) {
            // Once the authentication service has actually been requested by the developer
            // we will set a variable in the application indicating such. This helps us
            // know that we need to set any queued cookies in the after event later.
            $app['auth.loaded'] = true;

            return new AuthManager($app);
        });

        $this->app->singleton('auth.driver', function ($app) {
            return $app['auth']->guard();
        });

    }


}
