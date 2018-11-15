<?php

namespace App\Opts;

use Sess;
use Translator;
use Exception;
use Utility;
use Auth;
use Route;
use Config;
use Request;
use Session;
use GuzzleHttp\Client;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

use App\Models\Temp;

class Oauth{


    private $request;
    private $auth;
    private $cookie;
    private $db;
    private $guard = 'api';
    private $access_token_table = 'oauth_access_tokens';
    private $refresh_token_table = 'oauth_refresh_tokens';

    public function __construct(Application $app) {

        $this->request = $app->make('request');
        $this->auth = $app->make('auth');
        $this->cookie = $app->make('cookie');
        $this->db = $app->make('db');

    }

    public function isApiGuard(){
        return ($this->request->bearerToken() || $this->request->cookie(Passport::$cookie));
    }

    public function guessGuard($guard = null){

        if(!Utility::hasString($guard)){

            $guard =  $this->isApiGuard() ? $this->guard : $guard;
        }

        return $guard;


    }

    public function delAccessToken($id){
        return $this->db->table($this->access_token_table)->where('id', '=', $id)->delete();
    }

    public function delRefreshTokenByAccessToken($token_id){
        return $this->db->table($this->refresh_token_table)->where('access_token_id', '=', $token_id)->delete();
    }

    public function delAccessAndRefreshTokenByUser($user_id, $exclude_token_ids = []){

        $params = [config('passport.account.password.client_id'), $user_id];

        if(Utility::hasArray($exclude_token_ids)){
            $params[] = implode(',', $exclude_token_ids);
        }

        $this->db->delete(sprintf('DELETE FROM %s WHERE access_token_id IN (SELECT id FROM %s WHERE client_id = ? AND user_id = ? %s)', $this->refresh_token_table, $this->access_token_table, (!Utility::hasArray($exclude_token_ids)) ? '' : ' AND id NOT IN (?)'), $params);

        $token_builder = $this->db->table($this->access_token_table)
            ->where('client_id', '=', config('passport.account.password.client_id'))
            ->where('user_id', '=', $user_id);

        if(Utility::hasArray($exclude_token_ids)){
            $token_builder = $token_builder->whereNotIn('id', $exclude_token_ids);
        }


        $token_builder->delete();

        return true;

    }

    public function signin($username, $password){

        $data = [
            'grant_type' => 'password',
            'client_id' => config('passport.account.password.client_id'),
            'client_secret' =>   config('passport.account.password.client_secret'),
            'username' => $username,
            'password' => $password,
            'scope' => '*'
        ];


        $http = new Client();

        $response = array(
            'content' => array(),
            'code' => 200
        );


        try {

            $post = $http->post(sprintf('%s/oauth/token', config('app.url')), [
                'form_params' => $data,
                'proxy' => Utility::getProxyUrl()
            ]);

            $response['content'] = json_decode((string) $post->getBody(), true);


        }catch (Exception $e){

            $response['content'] = Translator::transSmart('auth.failed');
            $response['code'] = 401;

        }

        return $response;

    }

    public function refresh($refresh_token){


        $data = [
            'grant_type' => 'refresh_token',
            'client_id' => config('passport.account.password.client_id'),
            'client_secret' =>  config('passport.account.password.client_secret'),
            'refresh_token' => $refresh_token,
            'scope' => '*'
        ];


        $http = new Client();

        $response = array(
            'content' => array(),
            'code' => 200
        );


        try {

            $old_access_token = $this->auth->user()->token();

            $post = $http->post(sprintf('%s/oauth/token', config('app.url')), [
                'form_params' => $data,
                'proxy' => Utility::getProxyUrl()
            ]);

            $response['content'] = json_decode((string) $post->getBody(), true);

            $this->db->transaction(function () use ($old_access_token) {

                $this->delAccessToken($old_access_token->getKey());
                $this->delRefreshTokenByAccessToken($old_access_token->getKey());

            });

        }catch (Exception $e){

            $response['content'] = Translator::transSmart('app.Invalid refresh token.', 'Invalid refresh token');
            $response['code'] = 401;

        }

        return $response;


    }

    public function signout(){

        $response = array(
            'content' => array(),
            'code' => 200
        );

        try {

            $this->db->transaction(function () use(&$response) {

                $access_token = $this->auth->user()->token();

                $this->delAccessToken($access_token->getKey());
                $this->delRefreshTokenByAccessToken($access_token->getKey());

                $response['content'] =  Translator::transSmart('app.You have been logged out.', 'You have been logged out.');

            });

        }catch (Exception $e){

            $response['content'] = Utility::getHttpErrorMessage(500);
            $response['code'] = 500;


        }

        return $response;



    }
}