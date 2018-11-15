<?php

namespace App\Models\Redis;

use Exception;
use Utility;
use Translator;
use Config;
use CLDR;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

use Redis;
use App\Models\User;

class Online
{

    public $connection = 'socket';
    public $redis;
    public $users;

    public function __construct()
    {

       $this->redis = Redis::connection($this->connection);
       $this->users = new Collection();

    }


    public function users(){

        $str = $this->redis->get(sprintf('presence-%s:members', config('socket.channels.online')));

        $users = Utility::jsonDecode($str);

        if(Utility::hasArray($users)){
            $this->users = new Collection($users);
        }

    }

    public function get($ids = array()){

        $user = new User();

        return $this->users->whereIn(sprintf('user_info.%s', $user->getKeyName()), $ids);

    }




}