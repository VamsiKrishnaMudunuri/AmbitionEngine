<?php

namespace App\Http\Controllers\Admin\Managing\Property;

use Carbon\Carbon;
use Exception;
use Auth;
use Route;
use URL;
use Translator;
use Sess;
use Utility;
use SmartView;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Managing\ManagingController;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Property;
use App\Models\Sandbox;
use App\Models\Member;
use App\Models\Subscription;
use App\Models\Reservation;
use App\Models\Booking;
use App\Models\Guest;

use App\Models\MongoDB\Post;
use App\Models\MongoDB\Place;
use App\Models\MongoDB\Going;
use App\Models\MongoDB\Comment;
use App\Models\MongoDB\Invite;
use App\Models\MongoDB\Group;


class PropertyController extends ManagingController
{

    public function __construct()
    {

        parent::__construct();

    }

    public function index(Request $request, $property_id)
    {

        try {


            $user = Auth::user();
            ${$this->singular()} = $this->getModel()->getOneWithInvoiceStatisticsOrFail($property_id);
            $birthdays = (new Member())->upcomingBirthdaysByComingWeekAndGroupByDate(${$this->singular()}->timezone);
            $expiry_subscriptions = (new Subscription())->upcomingExpiryFacilitiesOnlyByPropertyAndComingWeeksAndGroupByDate(${$this->singular()} );
            $meeting_rooms = (new Reservation())->upcomingByPropertyAndComingWeekAndGroupByDate(${$this->singular()}, [Utility::constant('facility_category.3.slug')]);

            $site_visits = (new Booking())->upcomingVisitsByPropertyAndComingWeekAndGroupByDate(${$this->singular()});
            $post = new Post();
            ${$post->plural()} = $post->upcomingEventsForProperties($user->getKey(), ${$this->singular()}->getKey());
            $pending_events = $post->getDisapprovalByProperty(null, 20);
            $group = new Group();
            ${$group->plural()} = $group->getDisapprovalByProperty(${$this->singular()}->getKey(), 20);
            $going = new Going();
            $comment = new Comment();
            $sandbox = new Sandbox();
            $stats =  ${$this->singular()}->occupancyForLineChart(${$this->singular()});

            $guest = new Guest();
            ${$guest->plural()} = $guest->getUpcomingForProperty(${$this->singular()}->getKey(),20);

            URL::setAdvancedLandingIntended(Utility::routeName(), [$property_id]);

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (Exception $e) {

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact($this->singular(), 'birthdays', 'expiry_subscriptions', 'meeting_rooms', 'site_visits', $post->singular(), $post->plural(), $group->singular(), 'pending_events', $group->plural(),  $going->singular(), $comment->singular(), $sandbox->singular(), 'stats', $guest->plural()));

    }

    public function edit(Request $request, $property_id)
    {

        try {

            ${$this->singular()} = Property::retrieve($property_id);
            $company = ${$this->singular()}->company;
            $sandbox = new Sandbox();


        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), $company->singular(), $sandbox->singular()));

    }

    public function postEdit(Request $request, $property_id)
    {

        try {

            Property::edit($property_id, $request->all());

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (ModelVersionException $e) {

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        } catch (ModelValidationException $e) {

            $this->throwValidationException(
                $request, $e->validator
            );

        } catch (Exception $e) {

            return Utility::httpExceptionHandler(500, $e);

        }


        return $this->responseIntended('admin::managing::property::index', array('property_id' => $property_id))->with(Sess::getKey('success'), Translator::transSmart("app.Office has been updated.", "Office has been updated."));

    }

    public function page(Request $request, $id)
    {

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($id);

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular()));

    }

    public function postPage(Request $request, $property_id)
    {

        try {

            Property::updatePage($property_id, $request->all());

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (ModelVersionException $e) {

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        } catch (ModelValidationException $e) {

            $this->throwValidationException(
                $request, $e->validator
            );

        } catch (Exception $e) {

            return Utility::httpExceptionHandler(500, $e);

        }


        return redirect()->route('admin::managing::property::page', array('property_id' => $property_id))->with(Sess::getKey('success'), Translator::transSmart('app.Page has been updated.', 'Page has been updated.'));

    }

    public function setting(Request $request, $id)
    {

        try {

            ${$this->singular()} = Property::retrieve($id);
            $meta = ${$this->singular()}->metaWithQuery;

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->singular(), 'meta', 'id'));

    }

    public function postSetting(Request $request, $id)
    {

        try {

            Property::setting($id, $request->all());

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (ModelVersionException $e) {

            $this->throwValidationExceptionWithNoInput(
                $request, $e->validator
            );

        } catch (ModelValidationException $e) {

            $this->throwValidationException(
                $request, $e->validator
            );

        } catch (Exception $e) {

            return Utility::httpExceptionHandler(500, $e);

        }

        return $this->responseIntended('admin::managing::property::index', array('property_id' => $id))->with(Sess::getKey('success'), Translator::transSmart("app.Settings have been updated.", "Settings have been updated."));

        //return redirect()->route('admin::managing::property::setting', array('property_id' => $id))->with(Sess::getKey('success'), Translator::transSmart('app.Settings have been updated.', 'Settings have been updated.'));

    }

    public function postStatus(Request $request, $property_id)
    {

        try {

            Property::toggleStatus($property_id);

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (Exception $e) {


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, ['message' => Translator::transSmart("app.Office's status has been updated.", "Office's status has been updated.")]);

    }

    public function postComingSoon(Request $request, $property_id)
    {

        try {

            Property::toggleComingSoon($property_id);

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (Exception $e) {


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, ['message' => Translator::transSmart("app.Office's coming soon has been updated.", "Office's coming soon has been updated.")]);

    }

    public function postSiteVisitStatus(Request $request, $property_id)
    {

        try {

            Property::toggleSiteVisit($property_id);

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (Exception $e) {


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, ['message' => Translator::transSmart("app.This office is ready for site visit.", "This office is ready for site visit.")]);

    }

    public function postNewestSpaceStatus(Request $request, $property_id)
    {

        try {

            Property::toggleNewestSpace($property_id);

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (Exception $e) {


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, ['message' => Translator::transSmart("app.Office's newest space has been set.", "Office's newest space has been set.")]);

    }

    public function postIsPrimePropertyStatus(Request $request, $property_id)
    {

        try {

            Property::toggleIsPrimePropertyStatus($property_id);

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (Exception $e) {


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, ['message' => Translator::transSmart("app.Office's prime package has been set.", "Office's prime package has been set.")]);

    }

    public function siteVisit(Request $request, $property_id, $id)
    {

        try {

            $booking = new Booking();
            ${$booking->singular()} = $booking->getByPropertyAndID($property_id, $id);


        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (Exception $e) {


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($booking->singular()));


    }

    public function event(Request $request, $property_id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $user = Auth::user();
            $post = new Post();
            $comment = new Comment();
            $going = new Going();
            $order = array();

            $sandbox =  new Sandbox();

            $order[$post->getCreatedAtColumn()] = 'DESC';
            ${$post->plural()} = $post->showAllEvents([${$this->singular()}->getKey()], true, $order, !Utility::isExportExcel());

            URL::setAdvancedLandingIntended(Utility::routeName(), [${$this->singular()}->getKey()]);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $view = null;

        if(Utility::isExportExcel()){
            $view = SmartView::excel(null, compact($this->getModel()->singular(), $user->singular(), $post->singular(), $post->plural(), $comment->singular(), $going->singular(), $sandbox->singular()), Translator::transSmart('app.%s - Events', sprintf('%s - Events', ${$this->singular()}->smart_name, false, ['name' => ${$this->singular()}->smart_name ])));
        }else{
            $view = SmartView::render(null, compact($this->getModel()->singular(), $user->singular(), $post->singular(), $post->plural(), $comment->singular(), $going->singular(), $sandbox->singular()));
        }


        return $view;

    }

    public function addEvent(Request $request, $property_id)
    {

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $user = Auth::user();
            $post = new Post();
            $today = Carbon::now(${$this->getModel()->singular()}->timezone);
            $post->start = $today;
            $post->end = $today->copy()->addDays(1);
            $post->setAttribute('timezone', ${$this->getModel()->singular()}->timezone);
            $place = new Place();
            $going = new Going();
            $comment = new Comment();
            $sandbox = new Sandbox();


            $place->setAttribute('geo', $place->buildGeo(${$this->singular()}->latitude, ${$this->singular()}->longitude));
            $match = ${$this->singular()}->placeMapping;
            foreach($match as $property_field => $place_field){
                $place->setAttribute($place_field, ${$this->singular()}->getAttribute($property_field));
            }

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        } catch (Exception $e) {


            return Utility::httpExceptionHandler(500, $e);

        }

        if(Utility::isJsonRequest()){

            $view = SmartView::render('add_event_modal', compact($this->getModel()->singular(), $user->singular(), $post->singular(), $place->singular(), $going->singular(), $comment->singular(), $sandbox->singular()));

        }else{

            $view = SmartView::render(null, compact($this->getModel()->singular(), $user->singular(), $post->singular(), $place->singular(), $going->singular(), $comment->singular(), $sandbox->singular()));
        }
        return $view;

    }

    public function postAddEvent(Request $request, $property_id)
    {

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $user = Auth::user();
            $post = Post::addByEvent($user->getKey(), $request->all(), [${$this->singular()}->getKey()], true);
            $going = new Going();
            $comment = new Comment();
            $sandbox = new Sandbox();

        } catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelValidationException $e) {

            $this->throwValidationException(
                $request, $e->validator
            );

        } catch (Exception $e) {

            return Utility::httpExceptionHandler(500, $e);

        }

        if(Utility::isJsonRequest()){

            return SmartView::render('post_add_event_modal', compact($this->getModel()->singular(), $user->singular(), $post->singular(), $going->singular(), $comment->singular(), $sandbox->singular()));

        }else{


            return redirect()->route('admin::managing::property::event', array('property_id' => $property_id))
                ->with(Sess::getKey('success'),  Translator::transSmart("app.Event has been added.", "Event has been added."))
                ->with(Sess::getKey('create'), true);

        }


    }

    public function inviteEvent(Request $request,  $property_id,  $id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $user = Auth::user();
            $post = new Post();
            $invite = new Invite();
            ${$post->singular()} = $post->eventOnlyOrFail($id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(true, compact($this->getModel()->singular(), $post->singular(), $invite->singular()));

    }

    public function postInviteEvent(Request $request, $property_id, $id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $user = Auth::user();
            $model = (new Post())->eventOnlyOrFail($id);
            $model->isOpenOrFail();
            (new Invite())->add($model, ${$this->getModel()->singular()}->getKey(), $request->all(), ${$this->singular()}, true);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch (IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(null);

    }

    public function editEvent(Request $request, $property_id, $id)
    {


        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $user = Auth::user();
            $post = (new Post())->findEventOrFailForEdit($id);
            $post->start = $post->start->setTimezone($post->timezone);
            $post->end = $post->end->setTimezone($post->timezone);
            if(!is_null($post->registration_closing_date)) {
                $post->registration_closing_date = $post->registration_closing_date->setTimezone($post->timezone);
            }
            $going = new Going();
            $comment = new Comment();
            $place = ($post->hostWithQuery) ? $post->hostWithQuery : new Place();
            $sandbox = new Sandbox();

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (Exception $e) {


            return Utility::httpExceptionHandler(500, $e);

        }


        if(Utility::isJsonRequest()){
            $view =  SmartView::render('edit_event_modal', compact($this->getModel()->singular(), $user->singular(), $post->singular(), $place->singular(), $going->singular(), $comment->singular(), $sandbox->singular()));
        }else{
            $view =  SmartView::render(null, compact($this->getModel()->singular(), $user->singular(), $post->singular(), $place->singular(), $going->singular(), $comment->singular(), $sandbox->singular()));
        }

        return $view;


    }

    public function postEditEvent(Request $request, $property_id, $id)
    {

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $user = Auth::user();
            $sandbox = new Sandbox();
            $post = Post::editByEvent($id, $user->getKey(), $request->all());
            $going = new Going();
            $comment = new Comment();
            $sandbox = new Sandbox();

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (ModelValidationException $e) {

            $this->throwValidationException(
                $request, $e->validator
            );

        } catch (Exception $e) {

            return Utility::httpExceptionHandler(500, $e);

        }

        if(Utility::isJsonRequest()){
            return SmartView::render('post_edit_event_modal', compact($this->getModel()->singular(), $user->singular(), $sandbox->singular(), $post->singular(), $going->singular(), $comment->singular(), $sandbox->singular()));

        }else{
            return redirect()
                ->to(URL::getAdvancedLandingIntended('admin::managing::property::event', [$property_id],  URL::route('admin::managing::property::event', array('property_id' => $property_id))))
                ->with(Sess::getKey('success'), Translator::transSmart("app.Event has been updated.", "Event has been updated."));
        }



    }

    public function viewEvent(Request $request, $property_id, $id){

        try {

            $member = Auth::user();
            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $post = new Post();
            ${$post->singular()} = $post->eventWithoutStatusOrFail($id);
            $sandbox =  new Sandbox();

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return  SmartView::render(true, compact($this->getModel()->singular(), $post->singular(), $sandbox));

    }

    public function postApproveEvent(Request $request, $property_id, $id){

        try {

            Post::approve($id);

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (ModelValidationException $e) {

            $this->throwValidationException(
                $request, $e->validator
            );

        } catch (Exception $e) {

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(null);

    }

    public function postDisapproveEvent(Request $request, $property_id, $id){

        try {

            Post::disapprove($id);

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (ModelValidationException $e) {

            $this->throwValidationException(
                $request, $e->validator
            );

        } catch (Exception $e) {

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(null);

    }

    public function postDeleteEvent(Request $request, $property_id, $id)
    {

        try {

            Post::del($id);

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        } catch (Exception $e) {

            return Utility::httpExceptionHandler(500, $e);

        }

        if(Utility::isJsonRequest()){

            return SmartView::render(null);

        }else{

            return redirect()
                ->to(URL::getAdvancedLandingIntended('admin::managing::property::event', [$property_id],  URL::route('admin::managing::property::event', array('property_id' => $property_id))))
                ->with(Sess::getKey('success'), Translator::transSmart("app.Event has been deleted.", "Event has been deleted."));

        }


    }

    public function group(Request $request, $property_id, $id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $group = (new Group)->getFeedByPropertyOrFail( ${$this->singular()}->getKey(), $id);
            $sandbox =  new Sandbox();

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return  SmartView::render(true, compact($this->getModel()->singular(), $group->singular(), $sandbox));

    }

    public function postApproveGroup(Request $request, $property_id, $id){

        try {

            Group::approve($id, $property_id);

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (ModelValidationException $e) {

            $this->throwValidationException(
                $request, $e->validator
            );

        } catch (Exception $e) {

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(null);

    }

    public function postDisapproveGroup(Request $request, $property_id, $id){

        try {

            Group::disapprove($id, $property_id);

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (ModelValidationException $e) {

            $this->throwValidationException(
                $request, $e->validator
            );

        } catch (Exception $e) {

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(null);

    }

    public function postDeleteGroup(Request $request, $property_id, $id){

        try {

            Group::del($id, $property_id);

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        }  catch (Exception $e) {

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(null);

    }

    public function guest(Request $request, $property_id){

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $user = Auth::user();
            $guest = new Guest();

            ${$guest->plural()} = $guest->showAllByProperty([${$this->singular()}->getKey()], array(), !Utility::isExportExcel());

            URL::setAdvancedLandingIntended(Utility::routeName(), [${$this->singular()}->getKey()]);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $view = null;

        if(Utility::isExportExcel()){
            $view = SmartView::excel(null, compact($this->getModel()->singular(), $guest->singular(), $guest->plural()), Translator::transSmart('app.%s - Guest Visits', sprintf('%s - Guest Visits', ${$this->singular()}->smart_name, false, ['name' => ${$this->singular()}->smart_name ])));
        }else{
            $view = SmartView::render(null, compact($this->getModel()->singular(), $guest->singular(), $guest->plural()));
        }


        return $view;

    }

    public function addGuest(Request $request, $property_id)
    {
        ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
        $user = Auth::user();
        $guest = new Guest();

        try{

            if(Utility::isJsonRequest()){

                $view = SmartView::render('add_guest_modal', compact($this->getModel()->singular(), $guest->singular()));

            }else{

                $view = SmartView::render(null, compact($this->getModel()->singular(), $guest->singular()));
            }

        }catch(ModelNotFoundException $e){

           return Utility::httpExceptionHandler(404, $e);

        } catch (Exception $e) {


            return Utility::httpExceptionHandler(500, $e);

        }


        return $view;

    }

    public function postAddGuest(Request $request, $property_id)
    {

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $user = Auth::user();
            $guest = Guest::addByAdmin(${$this->singular()}->getKey(), $request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch (ModelValidationException $e) {

            $this->throwValidationException(
                $request, $e->validator
            );

        } catch (Exception $e) {

            return Utility::httpExceptionHandler(500, $e);

        }

        if(Utility::isJsonRequest()){

            return SmartView::render('post_add_guest_modal', compact($this->getModel()->singular(), $guest->singular()));

        }else{


            return redirect()->route('admin::managing::property::guest', array('property_id' => $property_id))
                ->with(Sess::getKey('success'),  Translator::transSmart("app.Guest visit has been added.", "Guest visit has been added."))
                ->with(Sess::getKey('create'), true);

        }

    }

    public function editGuest(Request $request, $property_id, $id)
    {


        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $user = Auth::user();
            $guest = Guest::retrieve($id);

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (Exception $e) {


            return Utility::httpExceptionHandler(500, $e);

        }


        if(Utility::isJsonRequest()){
            $view =  SmartView::render('edit_guest_modal', compact($this->getModel()->singular(), $guest->singular()));
        }else{
            $view =  SmartView::render(null, compact($this->getModel()->singular(),  $guest->singular()));
        }

        return $view;


    }

    public function postEditGuest(Request $request, $property_id, $id)
    {

        try {

            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $user = Auth::user();

            $guest = Guest::editByAdmin($id, $request->all());

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (ModelValidationException $e) {

            $this->throwValidationException(
                $request, $e->validator
            );

        } catch (Exception $e) {

            return Utility::httpExceptionHandler(500, $e);

        }

        if(Utility::isJsonRequest()){
            return SmartView::render('post_edit_guest_modal', compact($this->getModel()->singular(), $guest->singular() ));

        }else{
            return redirect()
                ->to(URL::getAdvancedLandingIntended('admin::managing::property::guest', [$property_id],  URL::route('admin::managing::property::guest', array('property_id' => $property_id))))
                ->with(Sess::getKey('success'), Translator::transSmart("app.Guest visit has been updated.", "Guest visit has been updated."));
        }



    }

    public function viewGuest(Request $request, $property_id, $id){

        try {

            $member = Auth::user();
            ${$this->singular()} = $this->getModel()->getOneOrFail($property_id);
            $guest = (new Guest())->getOneFromPropertyOrFail( $id, ${$this->singular()}->getKey());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return  SmartView::render(true, compact($this->getModel()->singular(), $guest->singular()));

    }

    public function postDeleteGuest(Request $request, $property_id, $id)
    {

        try {

            Guest::del($id);

        } catch (ModelNotFoundException $e) {

            return Utility::httpExceptionHandler(404, $e);

        } catch (IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        } catch (Exception $e) {

            return Utility::httpExceptionHandler(500, $e);

        }

        if(Utility::isJsonRequest()){

            return SmartView::render(null);

        }else{

            return redirect()
                ->to(URL::getAdvancedLandingIntended('admin::managing::property::guest', [$property_id],  URL::route('admin::managing::property::guest', array('property_id' => $property_id))))
                ->with(Sess::getKey('success'), Translator::transSmart("app.Guest visit has been deleted.", "Guest visit has been deleted."));

        }


    }

}
