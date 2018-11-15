<?php

namespace App\Exceptions;

use Sess;
use Lang;
use Utility;
use Exception;
use Illuminate\Support\MessageBag;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Illuminate\Http\Exception\PostTooLargeException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
        \App\Libraries\Model\ModelValidationException::class,
        \App\Libraries\Model\ModelVersionException::class,
        \App\Libraries\Model\IntegrityException::class,
        \App\Libraries\Model\PaymentGatewayException::class,
        \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {

        if($exception instanceof \Illuminate\Http\Exception\PostTooLargeException){

            return Utility::httpHandler(function() use ($request){
                return redirect($request->fullUrl())->withErrors(new MessageBag(array(Sess::getKey('errors') => Lang::get('exception.request_entity_large')) ));
            }, function(){
                return Utility::jsonErrorReponse(413);
            });

        }


        if ($exception instanceof \Illuminate\Session\TokenMismatchException){
            return Utility::httpHandler(function() use ($request){
                return redirect($request->fullUrl())->withErrors(new MessageBag(array('csrf_error' => Lang::get('exception.csrf_error')) ));
            }, function(){
                return Utility::jsonErrorReponse(401);
            });

        }

        if($exception instanceof \App\Libraries\Model\IntegrityException){
            return $exception->getResponse();
        }

        if($exception instanceof \App\Libraries\Model\PaymentGatewayException){
            return $exception->getResponse();
        }

        if(Utility::isJsonRequest()){

            if ($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException){

                return Utility::jsonErrorReponse(405);
            }
        }

        return parent::render($request, $exception);

    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([Sess::getKey('error') => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }
}
