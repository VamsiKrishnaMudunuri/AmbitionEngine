<?php

namespace App\Http\Controllers;

use App\Models\FacilityPrice;
use App\Models\FacilityUnit;
use App\Models\MongoDB\Going;
use App\Models\Package;
use App\Models\Wallet;
use Braintree\Util;
use Exception;
use View;
use Purifier;
use Translator;
use Domain;
use Mauth;
use CLDR;
use GeoIP;
use Cms;
use Sess;
use Auth;
use Utility;
use Storage;
use SmartView;
use Cache;
use App\Libraries\FulltextSearch\Search;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection as FractalCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Models\MongoDB\ActivityStat;
use App\Models\MongoDB\Follower;
use Braintree_ClientToken;
use DB;
use MongoDB;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;
use MongoDate;
use App\Console\Commands\Database\DataMigrateFromJobToBusinessOpportunity;
use App\Models\MongoDB\Post;
use App\Models\MongoDB\Activity;
use App\Models\MongoDB\Comment;
use App\Models\MongoDB\Like;
use App\Events\TestEvent;
use App\Models\Temp;
use App\Models\User;
use App\Models\Member;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Module;
use App\Models\Contact;
use App\Models\Sandbox;
use App\Models\MongoDB\Bio;
use App\Models\MongoDB\Following;
use App\Models\Facility;
use App\Models\Property;
use App\Models\PropertyUser;
use App\Models\Subscription;
use App\Models\SubscriptionUser;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionInvoiceTransaction;
use App\Models\SubscriptionRefund;
use App\Models\SubscriptionComplimentary;
use App\Models\Vault;
use App\Models\VaultPaymentMethod;
use App\Models\Currency;
use App\Models\Reservation;
use App\Models\MongoDB\NotificationJob;
use App\Models\MongoDB\Notification;
use App\Models\MongoDB\Place;
use App\Models\Redis\Online;
use App\Models\MongoDB\Group;
use App\Models\MongoDB\Join;
use App\Models\MongoDB\Invite;
use App\Models\Repo;
use App\Models\SignupInvitation;
use App\Models\MongoDB\NotificationSetting;
use App\Libraries\MongoDB\MongoDBCarbon;
use App\Models\MongoDB\Job;


use App\Models\Lead;
use App\Models\LeadPackage;
use App\Models\LeadActivity;
use App\Models\Commission;

use App\Models\MongoDB\BusinessOpportunity;


class DebugController extends Controller
{

    public function __construct(Cldr $cl)
    {
        parent::__construct(new Company());
    }

    public function index(Request $request){

        try {
        
        	
        }catch(ModelValidationException $e){
        	
            $this->throwValidationException(
                $request, $e->validator
            );

        }catch (IntegrityException $e){

            $this->throwIntegrityException(
                $request, $e
            );


        }catch (Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }



        return SmartView::render('debug');

    }

    public function broadcast(Request $request){
        (new NotificationJob())->broadcastActivities();
    }

    public function online(Request $request)
    {

        $user = (new Online)->users();
        return SmartView::render('debug');
    }

    public function json(Request $request){

        return SmartView::render(null);
    }

    public function notification(Request $request){
        (new NotificationJob())->broadcastActivities();
        return SmartView::render('debug');
    }

    public function signupInvitation(Request $request){


/**
        Mail::send('email/html/admin/signup_invitation_new', ['invitation' => new SignupInvitation()], function ($message) {
            $message->to('mgg8686@gmail.com');
        });
**/

        return View('email/html/admin/signup_invitation_new', ['invitation' => new SignupInvitation()]);

    }




}
