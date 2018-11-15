<?php

namespace App\Http\Controllers\Admin\Event;

use App\Facades\{
    Sess,
    Utility,
    SmartView,
    Translator,
    Url
};
use App\Models\MongoDB\{
    Post,
    Going,
    Place,
    Invite,
    Comment,
    Following
};
use App\Models\{
    Member,
    Sandbox,
    Property
};
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\{IntegrityException, ModelValidationException};

class EventController extends Controller
{
    /**
     * EventController constructor.
     */
    public function __construct()
    {
        parent::__construct(new Member());
    }

    /**
     * Show list of post(events).
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        try {
            $post = new Post();
            ${$post->plural()} = $post->showAllEvents([],false, [], true, [
                Utility::constant('status.0.slug')
            ]);
            $comment = new Comment();
            $going = new Going();
            $sandbox =  new Sandbox();

        } catch (InvalidArgumentException $e) {
            return Utility::httpExceptionHandler(500, $e);

        } catch(Exception $e) {
            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact($this->getModel()->singular(), $post->singular(), $post->plural(), $comment->singular(), $going->singular(), $sandbox->singular()));
    }

    /**
     * Approve post(event).
     *
     * @param Request $request
     *
     * @param Post $id
     *
     * @return mixed
     *
     * @throws \App\Libraries\Model\ModelValidationException
     * @throws \Exception
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postApproveEvent(Request $request, $id)
    {
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

    /**
     * Disapprove post(event).
     *
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postDisapproveEvent(Request $request, $id)
    {
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

    /**
     * Delete post(event).
     *
     * @param Request $request
     * @param Post $id
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws IntegrityException
     */
    public function postDeleteEvent(Request $request, $id)
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

        }finally{
	
	        $request->flush();
	
        }

        if (Utility::isJsonRequest()) {
            return SmartView::render(null);

        }else{
            return redirect()
                ->route('admin::event::index', array())
                ->with(Sess::getKey('success'), Translator::transSmart(
                    'app.Event has been deleted.',
                    'Event has been deleted.'
                ));
        }
    }

    /**
     * Show add post(event) form.
     *
     * @param Request $request
     * @return mixed
     */
    public function addEvent(Request $request)
    {
        try {
            ${$this->getModel()->singular()}  = Auth::user();
            $property = new Property();
            $post = new Post();
            $today = Carbon::now(${$this->getModel()->singular()}->timezone);
            $post->start = $today;
            $post->end = $today->copy()->addDays(1);
            $post->setAttribute('timezone', ${$this->getModel()->singular()}->timezone);
            $place = new Place();
            $going = new Going();
            $comment = new Comment();
            $sandbox = new Sandbox();

        } catch (ModelNotFoundException $e) {
            return Utility::httpExceptionHandler(404, $e);

        } catch (Exception $e) {
            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render('add_event_modal', compact($post->singular(), $property->singular(), $place->singular(), $going->singular(), $comment->singular(), $sandbox->singular()));
    }

    /**
     * Add post(event).
     *
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postAddEvent(Request $request)
    {
        try {
            $user = Auth::user();
            $post = Post::addByEvent($user->getKey(), $request->all(), [], true);
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

        return SmartView::render(null, ['message' => Translator::transSmart('app.Event has been added. Page will refresh in 3 seconds.', 'Event has been added. Page will refresh in 3 seconds.')]);
    }

    /**
     * Show edit post(event) form.
     *
     * @param Request $request
     * @param $id
     *
     * @return mixed
     */
    public function editEvent(Request $request, $id)
    {
        try {

            ${$this->getModel()->singular()}  = Auth::user();
            $property = new Property();
            $post = (new Post())->findEventOrFailForEdit($id);
            $post->start = $post->start->setTimezone($post->timezone);
            $post->end = $post->end->setTimezone($post->timezone);

            if(!is_null($post->registration_closing_date)) {
                $post->registration_closing_date = $post->registration_closing_date->setTimezone($post->timezone);
            }
            $going = new Going();
            $comment = new Comment();
            $place = ($post->hostWithQuery) ? $post->hostWithQuery  : new Place();
            $sandbox = new Sandbox();

        } catch(ModelNotFoundException $e) {
            return Utility::httpExceptionHandler(404, $e);

        } catch(Exception $e) {
            return Utility::httpExceptionHandler(500, $e);
        }

        return SmartView::render('edit_event_modal', compact($post->singular(), $property->singular(), $place->singular(), $going->singular(), $comment->singular(), $sandbox->singular()));
    }

    /**
     * Update post(event).
     *
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postEditEvent(Request $request, $id)
    {
        try {
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

        return SmartView::render(null, [
            'message' => Translator::transSmart('app.Event has been updated', 'Event has been updated.')
        ]);
    }

    /**
     * Show list of going members.
     *
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function goingEventMembers(Request $request, $id)
    {
        try {
            $type = 'event';
            ${$this->getModel()->singular()}  = $this->getModel()->getOneForActivity(Auth::user()->getKey());
            $auth_member = ${$this->getModel()->singular()};
            $following = new Following();
            $going = new Going();
            $sandbox = new Sandbox();
            $post = Post::retrieveForEvent($id);
            ${$going->plural()} =  $going->members($post);
            $last_id = Arr::get(Arr::last(${$going->plural()}->all()),  $going->getKeyName());

        } catch(ModelNotFoundException $e) {
            return Utility::httpExceptionHandler(404, $e);

        } catch (InvalidArgumentException $e) {
            return Utility::httpExceptionHandler(500, $e);

        } catch (Exception $e) {
            return Utility::httpExceptionHandler(500, $e);

        }

        $edge = $going;
        $edges = ${$going->plural()};
        $url = URL::route('admin::event::going-event-member', array($post->getKeyName() => $post->getKey()));

        return SmartView::render('members', compact('auth_member', $sandbox->singular(),  $post->singular(), $following->singular(),  $going->singular(),  $going->plural(), 'edge', 'edges', 'url', 'last_id', 'type'));
    }

    /**
     * Show infinite loading of member list(paginate)
     *
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function goingEventMember(Request $request, $id)
    {
        try {
            $member  = (new Member())->getOneForActivity(Auth::user()->getKey());
            $auth_member = $member;
            $following = new Following();
            $going = new Going();
            $sandbox = new Sandbox();
            $post = Post::retrieveForEvent($id);
            ${$going->plural()} =  $going->members($post, $request->get('member-id'));
            $last_id = (${$going->plural()}->count() > $going->getPaging()) ? Arr::get(${$going->plural()}->get($going->getPaging() - 1), $going->getKeyName()) : Arr::get(Arr::last(${$going->plural()}->all()), $going->getKeyName());

        } catch(ModelNotFoundException $e) {
            return Utility::httpExceptionHandler(404, $e);

        } catch(InvalidArgumentException $e) {
            return Utility::httpExceptionHandler(500, $e);

        } catch(Exception $e) {
            return Utility::httpExceptionHandler(500, $e);

        }

        $edges = ${$going->plural()};

        return SmartView::render('member', compact('auth_member', $sandbox->singular(),  $post->singular(), $following->singular(),  $going->singular(),  $going->plural(), 'edges', 'last_id'));
    }

    /**
     * Remove going member.
     *
     * @param Request $request
     * @param $id
     * @param $memberId
     *
     * @return mixed
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postDeleteGoingEvent(Request $request, $id, $memberId)
    {
        try {
            ${$this->getModel()->singular()} = $this->getModel()->getOneForActivity($memberId);
            $post = Post::deleteGoing(${$this->getModel()->singular()}->getKey(), $id);

        }catch(ModelNotFoundException $e){
            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){
            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){
            return Utility::httpExceptionHandler(500, $e);

        }

        $edge = new Going();
        $text = $edge->text($post);
        $count = $edge->number($post);

        return SmartView::render(null, [
                Translator::transSmart('app.status', 'status') => Translator::transSmart('app.success', 'success'),
                Translator::transSmart('app.message', 'message') => Translator::transSmart('app.Successfully remove member', 'Successfully remove member'),
                'count' => $count]
        );
    }

    /**
     * Show invitation form.
     *
     * @param Request $request
     * @param $id
     *
     * @return mixed
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function inviteEvent(Request $request, $id)
    {
        try {
            ${$this->getModel()->singular()} = $this->getModel()->getOneForActivity(Auth::user()->getKey());
            $post = new Post();
            $invite = new Invite();
            ${$post->singular()} = $post->eventOnlyOrFail($id);

        }catch(ModelNotFoundException $e){
            return Utility::httpExceptionHandler(404, null, Translator::transSmart('app.Cannot invite member on inactive event', 'Cannot invite member on inactive event'));

        }catch(ModelValidationException $e){
            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){
            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($this->getModel()->singular(), $post->singular(), $invite->singular()));
    }

    /**
     * Invite member to join event.
     *
     * @param Request $request
     * @param $id
     *
     * @return mixed
     *
     * @throws IntegrityException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postInviteEvent(Request $request, $id)
    {
        try {
            ${$this->getModel()->singular()} = $this->getModel()->getOneForActivity(Auth::user()->getKey());
            $model = (new Post())->eventOnlyOrFail($id);
            $model->isOpenOrFail();
            (new Invite())->add($model, ${$this->getModel()->singular()}->getKey(), $request->all());

        } catch (ModelNotFoundException $e) {
            return Utility::httpExceptionHandler(404, $e);

        } catch (ModelValidationException $e) {
            $this->throwValidationException(
                $request, $e->validator
            );

        } catch (IntegrityException $e) {
            $this->throwIntegrityException(
                $request, $e
            );

        }catch(Exception $e){
            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null);
    }
}