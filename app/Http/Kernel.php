<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\PreflightResponse::class,
        \Barryvdh\Cors\HandleCors::class,
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        //\App\Http\Middleware\EncryptCookies::class,
        //\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        //\Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Foundation\Http\Middleware\VerifyPostSize::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            //\App\Http\Middleware\PreflightResponse::class,
            //\Barryvdh\Cors\HandleCors::class,
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            //\Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class
        ],

        'api' => [
            'throttle:60,1',
            'bindings'
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        //'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'acl_root' => \App\Http\Middleware\AclRoot::class,
        'acl_admin' => \App\Http\Middleware\AclAdmin::class,
        'acl_member' => \App\Http\Middleware\AclMember::class,
	    'acl_member_pre_term' => \App\Http\Middleware\AclMemberPreTerm::class,
        'acl_agent' => \App\Http\Middleware\AclAgent::class,
        'json' => \App\Http\Middleware\Json::class,
        'shortcode' => \App\Http\Middleware\Shortcode::class,
        'cms_dns' => \App\Http\Middleware\CmsDns::class
    ];
}
