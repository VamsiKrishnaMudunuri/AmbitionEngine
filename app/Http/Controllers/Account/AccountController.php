<?php

namespace App\Http\Controllers\Account;

use Exception;
use Translator;
use Oauth;
use Mauth;
use CLDR;
use Sess;
use Auth;
use Utility;
use SmartView;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\User;
use App\Models\Member;
use App\Models\Sandbox;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Property;
use App\Models\Subscription;
use App\Models\MongoDB\NotificationSetting;

class AccountController extends Controller
{

    public function __construct()
    {
        parent::__construct(new User());
    }

    public function account(Request $request){

        try {

            ${$this->singular()} = User::retrieve(Auth::id());
            $member = new Member();
            $sandbox = new Sandbox();

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    ${$this->singular()},
                    'coverSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.cover'),
                    true
                );

                Sandbox::s3()->generateImageLinks(
                    ${$this->singular()},
                    'profileSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.profile'),
                    true
                );
            }

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), 'member', 'sandbox'));

    }

    public function postAccount(Request $request){

        try {

            User::updateAccount(Auth::id(), $request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        }catch(ModelValidationException $e){


            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        $message = Translator::transSmart('app.Your account has been updated.', 'Your account has been updated.');
        $response = redirect()->back()->with(Sess::getKey('success'), $message);

        if(Utility::isJsonRequest()){
            $response = new JsonResponse($message);
        }

        return $response;

    }

    public function notification(Request $request){

        try {

            ${$this->singular()} = User::findWithNotificationSettingsOrFail(Auth::id());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular()));

    }

    public function postNotification(Request $request, $type){

        try {

            ${$this->singular()} = User::retrieve(Auth::id());
            (new NotificationSetting())->upsertOrToggleStatus(${$this->singular()}->getKey(), $type);


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        }catch(ModelValidationException $e){


            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return new JsonResponse('');

    }

    public function password(Request $request){

        try {

            ${$this->singular()} = User::retrieve(Auth::id());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);
        }


        return SmartView::render(null, compact($this->singular()));

    }

    public function postPassword(Request $request){

        try {

            User::updatePassword(Auth::id(), $request->all());

            Mauth::revokeAll(Auth::id(), [$request->session()->getId()]);
            Oauth::delAccessAndRefreshTokenByUser(Auth::id());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        }catch(ModelValidationException $e){


            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        $message = Translator::transSmart('app.Your password has been updated.', 'Your password has been updated.');
        $response = redirect()->back()->with(Sess::getKey('success'), $message);

        if(Utility::isJsonRequest()){
            $response = new JsonResponse($message);
        }

        return $response;

    }

    public function networking(Request $request, $property_id = null){

        try {


            $member = Auth::user();

            $manuals = new Collection();
            $property = new Property();
            $first_property = new Property();
            $subscription = new Subscription();
            ${$property->plural()} = $subscription->getActiveSubscribedPropertiesByUser($member->getKey());

            if( !${$property->plural()}->isEmpty()) {

                if (!Utility::hasString($property_id)) {

                    $first_property = ${$property->plural()}->first(null, new Property());

                }else{

                    $first_property = ${$property->plural()}->find($property_id, new Property());
                }

            }

            if($first_property->exists){
                $manuals = $first_property->getManualByProperty($first_property->getKey());
            }


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($member->singular(), 'first_property', $property->plural(), 'manuals'));

    }

    public function viewNetworking(Request $request){

        try {


            $member = Auth::user();

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact('member'));

    }

    public function postViewNetworking(Request $request){

        try {

            $member = Auth::user();

            if(!$member->checkPassword($request->get('password', null))){
                throw new IntegrityException($member, Translator::transSmart('app.The password you entered is incorrect.', 'The password you entered is incorrect.'));
            }

        }catch(IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, array('wifi' => array('username' => $member->network_username, 'password' => $member->network_password), 'printer' => array('username' => $member->printer_username, 'password' => $member->printer_password)));

    }

    public function setting(Request $request){

        try {

            ${$this->singular()} = User::retrieve(Auth::id());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular()));

    }

    public function postSetting(Request $request){

        try {

            User::updateSetting(Auth::id(), $request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelVersionException $e){

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        }catch(ModelValidationException $e){


            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        $message = Translator::transSmart('app.Your settings have been updated.', 'Your settings have been updated.');
        $response = redirect()->back()->with(Sess::getKey('success'), $message);

        if(Utility::isJsonRequest()){
            $response = new JsonResponse($message);
        }

        return $response;

    }

}
