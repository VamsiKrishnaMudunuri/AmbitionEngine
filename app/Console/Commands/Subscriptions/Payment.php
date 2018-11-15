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

use App\Models\SubscriptionInvoice;

class Payment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:pay-invoice {--limit=20}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pay recurring invoices by credit card';

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

            $subscription_invoice = new SubscriptionInvoice();
            $subscription_invoice->autoPayForOutstandingInvoices($limit);

        }catch (Exception $ex){

            $this->error($ex->getMessage());

        }


    }

}
