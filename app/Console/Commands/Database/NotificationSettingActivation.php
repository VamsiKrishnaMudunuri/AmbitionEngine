<?php

namespace App\Console\Commands\Database;

use Illuminate\Console\Command;

use Exception;
use Utility;
use Illuminate\Support\Arr;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

use App\Models\User;
use App\Models\MongoDB\NotificationSetting;

class NotificationSettingActivation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:notification-setting-activation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Activate all notification for all users";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //

        try {


            $users = (new User())->get();
            $notifications = Utility::constant('notification_setting');

            foreach($users as $user){

                foreach($notifications as $key => $notification) {
                    foreach ($notification['list'] as $key => $list) {

                        (new NotificationSetting())->activate($user->getKey(), $list['slug'], $user->getKey());

                    }
                }

            }



        }catch (Exception $ex){

            $this->error($ex->getMessage());

        }


    }

}
