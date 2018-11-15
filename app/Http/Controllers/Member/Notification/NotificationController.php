<?php

namespace App\Http\Controllers\Member\Notification;

use Exception;
use Translator;
use CLDR;
use Sess;
use Auth;
use Utility;
use SmartView;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Member;
use App\Models\Sandbox;
use App\Models\MongoDB\ActivityStat;
use App\Models\MongoDB\Post;
use App\Models\MongoDB\Comment;
use App\Models\MongoDB\Like;
use App\Models\MongoDB\Activity;
use App\Models\MongoDB\Notification;

class NotificationController extends Controller
{

    public function __construct()
    {
        parent::__construct(new Member());
    }

    public function index(Request $request){

        try {

            ${$this->getModel()->singular()}  = Auth::user();
            $notification = new Notification();
            $sandbox = new Sandbox();

            ${$notification->plural()} = $notification->feeds(${$this->getModel()->singular()}->getKey());

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(null, compact($this->getModel()->singular(), $notification->singular(), $notification->plural(), $sandbox->singular()));


    }

    public function feed(Request $request){

        try {

            ${$this->getModel()->singular()}  = Auth::user();
            $notification = new Notification();
            $sandbox = new Sandbox();

            ${$notification->plural()} = $notification->feeds(${$this->getModel()->singular()}->getKey(), $request->get('notification-id'));

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(true, compact($notification->singular(), $notification->plural(), $sandbox->singular()));


    }

    public function latest(Request $request){

        try {

            ${$this->getModel()->singular()}  = Auth::user();
            $notification = new Notification();
            $notification->setPaging(10);
            ${$notification->plural()} = $notification->getLatest(${$this->getModel()->singular()}->getKey());

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(true, compact($notification->singular(), $notification->plural()));


    }

    public function link(Request $request, $id){

        try {

            $url = $request->get('url');
            ${$this->getModel()->singular()}  = Auth::user();
            $notification = (new Notification())->feedOrFailForRedirection(${$this->getModel()->singular()}->getKey(), $id);
            $notification->setAttribute('is_read', Utility::constant('status.1.slug'));
            $notification->save();


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return redirect($url);
    }

    public function postRead(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $notification = (new Notification())->read(${$this->getModel()->singular()}->getKey(), $id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(null);

    }

    public function postUnread(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $notification = (new Notification())->unread(${$this->getModel()->singular()}->getKey(), $id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(null);

    }

    public function postResetStats(Request $request){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            ActivityStat::resetNotification(${$this->getModel()->singular()}->getKey());

        }catch (Exception $e){

        }

        return SmartView::render(null);
    }

}
