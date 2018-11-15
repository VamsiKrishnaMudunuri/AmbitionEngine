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

use App\Models\MongoDB\Job;
use App\Models\MongoDB\BusinessOpportunity;
use App\Models\MongoDB\Place;

class DataMigrateFromJobToBusinessOpportunity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:data-migrate-from-job-to-business-opportunity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Migrate data from job to business opportunity";

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


            $job = new Job();
            $jobs = $job
                ->with(['place'])
                ->orderBy($job->getCreatedAtColumn(), 'ASC')
                ->get();

            foreach($jobs as $job){

                $business_opportunity = new BusinessOpportunity();

                $business_opportunity->fillable($business_opportunity->getRules(['company_email', 'company_phone_country_code', 'company_phone_number'], true, true));

                $isExist = $business_opportunity
                    ->where('business_title', '=', $job->job_title)
                    ->count();

                if($isExist){
                    continue;
                }

                $job_user_id = $job->getAttribute($job->user()->getForeignKey());
                $job_company_id = $job->getAttribute($job->company()->getForeignKey());

                $business_opportunity->setAttribute($business_opportunity->user()->getForeignKey(), $job_user_id);

                if($job_company_id){
                    $business_opportunity->setAttribute($business_opportunity->company()->getForeignKey(), $job_company_id);
                }

                if(Utility::hasArray($job->offices)){
                    $business_opportunity->setAttribute('offices', $job->offices);
                }

                $companies =   ['company_name',
                            'company_industry',
                            'company_description',
                            'company_email',
                            'company_phone_country_code',
                            'company_phone_area_code',
                            'company_phone_number',
                            'company_city',
                            'company_state',
                            'company_postcode',
                            'company_country',
                            'company_address1',
                            'company_address2'];


                foreach ($companies as $company){
                    $business_opportunity->setAttribute($company, $job->getAttribute($company));
                }


                $business_opportunity->setAttribute('business_title', $job->getAttribute('job_title'));
                $business_opportunity->setAttribute('business_opportunity_type', Utility::constant('business_opportunity_type.fundraising.slug'));
                $business_opportunity->setAttribute('business_opportunities', $job->getAttribute('job_service'));
                $business_opportunity->setAttribute('business_description', $job->getAttribute('job_description'));

                $business_opportunity->setAttribute('stats', $job->getAttribute('stats'));

                $business_opportunity->setAttribute($business_opportunity->getCreatedAtColumn(), $job->getAttribute($job->getCreatedAtColumn()));
                $business_opportunity->setAttribute($business_opportunity->getUpdatedAtColumn(), $job->getAttribute($job->getCreatedAtColumn()));
                $business_opportunity->setAttribute($business_opportunity->getCreatorFieldName(), $job->getAttribute($job->getCreatorFieldName()));
                $business_opportunity->setAttribute($business_opportunity->getEditorFieldName(), $job->getAttribute($job->getEditorFieldName()));

                $business_opportunity->save();

                if(!is_null($job->place) && $job->place->exists){
                    $place = new Place();
                    $place->fill(Arr::except($job->place->getAttributes(), [$place->getKeyName(), $place->thing()->getMorphType(), $place->thing()->getForeignKey()]));
                    $place->setAttribute($place->thing()->getMorphType(), $business_opportunity->getTable());
                    $place->setAttribute($place->thing()->getForeignKey(), $place->objectID($business_opportunity->getKey()));
                    $place->save();
                }
            }



        }catch (Exception $ex){

            dd($ex);
            $this->error($ex->getMessage());

        }


    }

}
