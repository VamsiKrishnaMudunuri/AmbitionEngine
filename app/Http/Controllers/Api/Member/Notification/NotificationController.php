<?php

namespace App\Http\Controllers\Api\Member\Notification;


use Exception;
use Auth;
use URL;
use Translator;
use Sess;
use Utility;
use SmartView;
use Illuminate\Http\Request;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Member;
use App\Models\User;
use App\Models\Sandbox;
use App\Models\MongoDB\ActivityStat;
use App\Models\MongoDB\Post;
use App\Models\MongoDB\Comment;
use App\Models\MongoDB\Like;
use App\Models\MongoDB\Activity;
use App\Models\MongoDB\Notification;

class NotificationController extends Controller
{
    private $skipActivityType = array();

    public function __construct()
    {

        $this->skipActivityType = array(
            Utility::constant('activity_type.17.slug'),
            Utility::constant('activity_type.18.slug'),
            Utility::constant('activity_type.19.slug'),
            Utility::constant('activity_type.20.slug'),
            Utility::constant('activity_type.21.slug'),
            Utility::constant('activity_type.22.slug'),
            Utility::constant('activity_type.23.slug'),
            Utility::constant('activity_type.24.slug'),
            Utility::constant('activity_type.25.slug'),
            Utility::constant('activity_type.26.slug')
        );

        parent::__construct(new Member());

    }

    private function formatData(&$notifications){

        if (Utility::isNativeAppResponse()) {

            $new_no = new Collection();
            foreach ($notifications as $no){

                $activity = new Activity();

                if($no->news instanceof Activity) {

                    $activity = $no->news;



                    $no->news->sender->setRelation('profileSandboxWithQuery', $no->news->sender->profileSandboxWithQuery()->first());
                    $no->news->receiver->setRelation('profileSandboxWithQuery', $no->news->receiver->profileSandboxWithQuery()->first());

                    Sandbox::s3()->generateImageLinks($no->news->sender, 'profileSandboxWithQuery', Arr::get(User::$sandbox, 'image.profile'), true);

                    Sandbox::s3()->generateImageLinks($no->news->receiver, 'profileSandboxWithQuery', Arr::get(User::$sandbox, 'image.profile'), true);

                    $activity->setRelation('sender', $no->news->sender);
                    $activity->setRelation('receiver', $no->news->receiver);
                    $activity->setRelation('action',  $no->news->action);
                    $activity->setRelation('edge',  $no->news->edge);

                    $message = $activity->attractiveText(false, array(Utility::constant('activity_type.13.slug') => 2));

                    if($message instanceof HtmlString){
                        $message = $message->toHtml();
                    }

                    $activity->setAttribute('message', $message);

                    $activity->setAttribute('url',   ($activity->target_url) ? $activity->target_url : "");

                    if(in_array($activity->type, $this->skipActivityType)){
                        continue;
                    }

                }


                $no->setRelation('reference', $activity);

                $new_no->add($no);

            }

            $notifications =  $new_no;

        }
    }

    public function myNotification(Request $request){


        try {

            ${$this->getModel()->singular()}  = Auth::user();
            $notification = new Notification();

            ${$notification->plural()} = $notification->feeds(${$this->getModel()->singular()}->getKey(), $request->get('notification-id'));

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $this->formatData(${$notification->plural()});

        return SmartView::render(true, compact( $notification->plural() ));

    }


    public function myLatestNotification(Request $request){

        try {

            ${$this->getModel()->singular()}  = Auth::user();
            $notification = new Notification();
            $notification->setPaging(10);
            ${$notification->plural()} = $notification->getLatest(${$this->getModel()->singular()}->getKey());

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $this->formatData(${$notification->plural()});


        return SmartView::render(true, compact( $notification->plural()));


    }

}