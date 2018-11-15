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

class Mauth{


    private $session_prefix = 'user:session:';
    private $request;
    private $auth;
    private $cookie;
    private $db;
    private $redis;


    public function __construct(Application $app) {

        $this->request = $app->make('request');
        $this->auth = $app->make('auth');
        $this->cookie = $app->make('cookie');
        $this->db = $app->make('db');
        $this->redis = $app->make('redis')->connection();

    }

    private function generateSessionName($user_id){

        return sprintf('%s%s', $this->session_prefix, $user_id);
    }

    public function sync($current_session_id, $user_id){

        $this->redis->sadd($this->generateSessionName($user_id), $current_session_id);

    }

    public function revokeBySession($user_id, $current_session_id){

        $this->redis->srem($this->generateSessionName($user_id), $current_session_id);

    }

    public function revokeByLaravelSession($current_session_id){

        $this->redis->del(sprintf('%s:%s', config('cache.prefix'), $current_session_id));

    }


    public function revokeAll($user_id, $exclude_session_ids = []){

        $user_sessions = $this->redis->smembers($this->generateSessionName($user_id));

        foreach($user_sessions as $session_id){

            if(in_array($session_id, $exclude_session_ids)){
                continue;
            }

            $this->revokeBySession($user_id, $session_id);
            $this->revokeByLaravelSession($session_id);

        }


    }

}