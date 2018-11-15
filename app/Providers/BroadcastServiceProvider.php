<?php

namespace App\Providers;

use Auth;
use Request;
use Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes();

        /*
         * Authenticate the user's personal channel...
         */
        Broadcast::channel(config('socket.channels.online'), function ($user) {

            $auth_user = Auth::user();
            $flag = false;

            if(!is_null($auth_user) && $user->getKey() == $auth_user->getKey()){
                $flag = true;
            }

            if($flag){
                return ['id' => $user->getKey(), 'username' => $user->username, 'full_name' => $user->full_name];
            }

            return $flag;

        });


        Broadcast::channel('new-feed-notification', function ($user) {

            $flag = false;

            if(Auth::check()){
                $flag = true;
            }

            return $flag;

        });

        Broadcast::channel('new-comment', function ($user) {

            $flag = false;

            if(Auth::check()){
                $flag = true;
            }

            return $flag;

        });

        Broadcast::channel('new-notification-*', function ($user, $socketID) {

            $flag = false;

            if(Auth::check()){
                $flag = true;
            }

            return $flag;

        });

        /**
        Broadcast::channel('test.*', function ($user, $userId) {
            return true;
        });
         * */
    }
}
