<?php

namespace App\Http\Controllers\Api\Member\Room;

use Exception;
use Session;
use Translator;
use CLDR;
use Sess;
use Auth;
use Utility;
use SmartView;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\PaymentGatewayException;

use App\Models\Temp;
use App\Models\Acl;
use App\Models\Sandbox;
use App\Models\Member;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Property;
use App\Models\Subscription;
use App\Models\SubscriptionComplimentary;
use App\Models\Reservation;
use App\Models\Facility;
use App\Models\FacilityUnit;
use App\Models\FacilityPrice;
use App\Models\Currency;


class RoomController extends Controller
{

    private $pricing_rule;
    private $facility_category;

    public function __construct()
    {

        $this->pricing_rule = Utility::constant('pricing_rule.0.slug');
        $this->facility_category = [Utility::constant('facility_category.3.slug')];

        parent::__construct(new Member());

    }

    public function myUpcoming(Request $request){

        try {

            $user = Auth::user();
            $now = Carbon::now();
            $reservation = new Reservation();
            ${$reservation->plural()} = $reservation->upcomingByUser($user->getKey(), $now, $this->facility_category );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        if (Utility::isNativeAppResponse()) {

            foreach (${$reservation->plural()} as $reservation) {

                Sandbox::s3()->generateImageLinks($reservation->facility, 'profileSandboxWithQuery', Arr::get(Facility::$sandbox, 'image.profile'), true);


            }

        }

        return SmartView::render(null, compact($reservation->plural()));

    }

    public function myPast(Request $request){

        try {

            $user = Auth::user();
            $now = Carbon::now();
            $reservation = new Reservation();
            ${$reservation->plural()} = $reservation->pastByUser($user->getKey(), $now, $this->facility_category );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        if (Utility::isNativeAppResponse()) {

            foreach (${$reservation->plural()} as $reservation) {

                Sandbox::s3()->generateImageLinks($reservation->facility, 'profileSandboxWithQuery', Arr::get(Facility::$sandbox, 'image.profile'), true);


            }

        }

        return SmartView::render(null, compact($reservation->plural()));

    }

    public function myCancelled(Request $request){

        try {

            $user = Auth::user();
            $now = Carbon::now();
            $reservation = new Reservation();
            ${$reservation->plural()} = $reservation->cancelledByUser($user->getKey(), $this->facility_category );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        if (Utility::isNativeAppResponse()) {

            foreach (${$reservation->plural()} as $reservation) {

                Sandbox::s3()->generateImageLinks($reservation->facility, 'profileSandboxWithQuery', Arr::get(Facility::$sandbox, 'image.profile'), true);


            }

        }

        return SmartView::render(null, compact($reservation->plural()));

    }


}