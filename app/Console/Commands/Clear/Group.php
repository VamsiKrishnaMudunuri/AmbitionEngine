<?php

namespace App\Console\Commands\Clear;

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

use App\Models\MongoDB\Group as MongoDBGroup;

class Group extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:group';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Clean up deleted groups and their related data";

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


            $group = new MongoDBGroup();
            $group->cleanup();

        }catch (Exception $ex){

            $this->error($ex->getMessage());

        }


    }

}
