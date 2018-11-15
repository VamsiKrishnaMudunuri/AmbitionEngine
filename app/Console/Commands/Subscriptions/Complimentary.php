<?php

namespace App\Console\Commands\Subscriptions;

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

use App\Models\SubscriptionComplimentary;

class Complimentary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:reset-complimentary {--limit=20} {--test=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset complimentary credit';

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

            $limit = $this->option('limit');
            $test =  $this->option('test');

            $subscription_complimetary = new SubscriptionComplimentary();
            $subscription_complimetary->autoReset($limit, $test);

        }catch (Exception $ex){

            $this->error($ex->getMessage());

        }


    }

}
