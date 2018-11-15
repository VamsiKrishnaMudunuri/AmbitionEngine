<?php

namespace App\Http\Controllers\Api\Member\Statistic;


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

class StatisticController extends Controller
{


    public function __construct()
    {


        parent::__construct(new Member());

    }

    public function member(Request $request){


        try {

            $user = Auth::user();
            $stats = $user->activityStat;

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(null, compact( 'stats' ));

    }



}