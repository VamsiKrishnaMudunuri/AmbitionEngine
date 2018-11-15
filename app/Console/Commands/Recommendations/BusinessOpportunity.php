<?php

namespace App\Console\Commands\Recommendations;

use Illuminate\Console\Command;

use Exception;
use Utility;
use Illuminate\Support\Arr;

use App\Libraries\MongoDB\MongoDBCarbon;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

use App\Models\Temp;
use App\Models\User;
use App\Models\Member;
use App\Models\MongoDB\Activity;
use App\Models\MongoDB\NotificationSetting;

use App\Models\MongoDB\BusinessOpportunity as MongoDBBusinessOpportunity;

class BusinessOpportunity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recommendations:business-opportunity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger Business Opportunity Recommendations';

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

        $temp = new Temp();

        if(!$temp->isRunningBusinessOpportunityRecommendationActivity()){

            $temp->setRunningBusinessOpportunityRecommendationActivity();

        }else{

            return true;
        }


        try {

            $jobSize = 30;
            $memberSize = 50;

            $job = new MongoDBBusinessOpportunity();
            $jobs = new Collection();
            $activity = new Activity();

            $today = MongoDBCarbon::now();
            $today_start = $today->copy()->startOfDay();
            $today_end = $today->copy()->endOfDay();
            $previous_day_start = $today->copy()->subDays(2)->startOfDay();
            $previous_day_end = $previous_day_start->copy()->endOfDay();

            $lastMonth = $today->copy()->subMonthNoOverflow(1);

            $cursor = MongoDBBusinessOpportunity::raw(function($collection) use ($jobSize, $memberSize, $job, &$jobs, $today, $lastMonth)
            {

                $cursor = $collection->aggregate(
                    [
                        //'_id' => array('$eq' => $job->objectID('5a6ef9f681d6f332a0000174'))
                        array('$match'=> array('created_at' => array('$gte' => $lastMonth->toUTCDateTime(), '$lte' => $today->toUTCDateTime()))),
                        array('$sample' => array('size' => $jobSize))

                    ]
                );

                $results = iterator_to_array($cursor, true);

                $jobs = MongoDBBusinessOpportunity::hydrate($results);

                return $jobs;

            });


            foreach($jobs as $job){


                $count = $activity
                    ->where($activity->action()->getMorphType(), '=', $job->getTable())
                    ->where($activity->action()->getForeignKey(), '=', $job->objectID($job->getKey()))
                    ->whereIn('type', [Utility::constant('activity_type.25.slug'), Utility::constant('activity_type.26.slug')])
                    ->where($activity->getCreatedAtColumn(), '>=', $previous_day_start)
                    ->where($activity->getCreatedAtColumn(), '<=', $today_end)
                    ->count();

                if($count){

                    continue;
                }


                $notification_setting = (new NotificationSetting());
                $job_owner = $job->getAttribute($job->user()->getForeignKey());
                $isNotifyOwner = $notification_setting
                    ->where($notification_setting->user()->getForeignKey(), '=', $job_owner )
                    ->where('type', '=', Utility::constant('notification_setting.business_opportunity.list.3.slug'))
                    ->where('status', '=', Utility::constant('status.1.slug'))
                    ->count();

                if($isNotifyOwner){
                    (new Activity())->add(Utility::constant('activity_type.26.slug'), $job, $job_owner , $job_owner );
                }

                $member = new Member();

                ${$member->plural()} = $member->showRandomByMatchingBioBusinessOpportunity($job->business_opportunity_type,$job->business_opportunities_matching_keys, $memberSize);

                foreach(${$member->plural()} as $member){

                    (new Activity())->add(Utility::constant('activity_type.25.slug'), $job, $job_owner , $member->getKey());

                }


            }


        }catch (Exception $ex){

            $this->error($ex->getMessage());

        }finally{

            $temp->flushRunningBusinessOpportunityRecommendationActivity();

        }

    }
}
