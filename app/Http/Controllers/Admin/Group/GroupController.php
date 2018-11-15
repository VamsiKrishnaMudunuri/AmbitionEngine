<?php

namespace App\Http\Controllers\Admin\Group;

use App\Facades\{
    Sess,
    Utility,
    SmartView,
    Translator
};
use App\Libraries\Model\ModelValidationException;
use Exception;
use App\Models\Member;
use App\Models\MongoDB\Following;
use App\Models\MongoDB\Invite;
use App\Models\MongoDB\Join;
use App\Models\Sandbox;
use App\Models\Subscription;
use App\Models\Temp;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use InvalidArgumentException;
use App\Models\MongoDB\Group;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GroupController extends Controller
{
    /**
     * GroupController constructor.
     */
    public function __construct()
    {
        parent::__construct(new Group());
    }

    /**
     * Display resource.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        try {
            $group = new Group();
            $menu = (new Temp())->getPropertyMenu();
            $join = new Join();
            ${$this->plural()} = $group->feeds(${$this->getModel()->singular()}->getKey(), $request->get('feed-id'), $request->get('property'), true, [Utility::constant('status.0.slug')]);

        } catch (ModelNotFoundException $e) {
            return Utility::httpExceptionHandler(404, $e);
        } catch(InvalidArgumentException $e) {
            return Utility::httpExceptionHandler(500, $e);
        } catch (Exception $e) {
            return Utility::httpExceptionHandler(500, $e);
        }

        return SmartView::render(null, compact($this->plural(), $group->singular(), 'menu', $join->singular()));
    }

    /**
     * Approve group.
     *
     * @param Request $request
     * @param $propertyId
     * @param $groupId
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postApproveGroup(Request $request, $propertyId, $groupId)
    {
        try {
            Group::approve($groupId, $propertyId);
        } catch (ModelNotFoundException $e) {
            return Utility::httpExceptionHandler(404, $e);
        } catch (ModelValidationException $e) {
            $this->throwValidationException($request, $e->validator);
        } catch (Exception $e) {
            return Utility::httpExceptionHandler(500, $e);
        }

        return redirect()
            ->route('admin::group::index', array())
            ->with(Sess::getKey('success'), Translator::transSmart(
                'app.Group has been approved.',
                'Group has been approved.')
            );
    }

    /**
     * Disapprove group.
     *
     * @param Request $request
     * @param $propertyId
     * @param $groupId
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postDisapproveGroup(Request $request, $propertyId, $groupId)
    {
        try {
            Group::disapprove($groupId, $propertyId);
        } catch (ModelNotFoundException $e) {
            return Utility::httpExceptionHandler(404, $e);
        } catch (ModelValidationException $e) {
            $this->throwValidationException($request, $e->validator);
        } catch (Exception $e) {
            return Utility::httpExceptionHandler(500, $e);
        }

        return redirect()
            ->route('admin::group::index', array())
            ->with(Sess::getKey('success'), Translator::transSmart(
                'app.Group has been disapproved.',
                'Group has been disapproved.'
            ));
    }

    /**
     * Delete group.
     *
     * @param Request $request
     * @param $propertyId
     * @param $groupId
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postDeleteGroup(Request $request, $propertyId, $groupId)
    {
        try {
            Group::del($groupId, $propertyId);
        } catch (ModelNotFoundException $e) {
            return Utility::httpExceptionHandler(404, $e);
        } catch (ModelValidationException $e) {
            $this->throwValidationException($request, $e->validator);
        } catch (Exception $e) {
            return Utility::httpExceptionHandler(500, $e);
        }finally{
	
	        $request->flush();
	
        }

        return redirect()
            ->route('admin::group::index', array())
            ->with(Sess::getKey('success'), Translator::transSmart(
                'app.Group has been deleted.',
                'Group has been deleted.'
            ));
    }

    public function edit(Request $request, $id)
    {
        try {
            $group = Group::retrieve($id);
            $sandbox = (is_null($group->profileSandboxWithQuery)) ? new Sandbox() : $group->profileSandboxWithQuery;
            $view = $request->get('view');
            $menu = (new Temp())->getPropertyMenuAcrossVenue();

        } catch (ModelNotFoundException $e) {
            return Utility::httpExceptionHandler(404, $e);
        }

        return SmartView::render(true, compact($group->singular(), $sandbox->singular(), 'view', 'menu'));
    }

    /**
     * Edit group.
     *
     * @param Request $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \App\Libraries\Model\ModelValidationException
     * @throws \Exception
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postEdit(Request $request, $id)
    {
        try {
            ${$this->getModel()->singular()} = Auth::user();
            Group::edit($id, ${$this->getModel()->singular()}->getKey(), $request->all());

        } catch (ModelNotFoundException $e) {
            return Utility::httpExceptionHandler(404, $e);
        } catch (ModelValidationException $e) {
            $this->throwValidationException(
                $request, $e->validator
            );
        } catch (Exception $e) {
            return Utility::httpExceptionHandler(500, $e);
        }

        return SmartView::render(null, ['message' => Translator::transSmart('app.Group has been updated', 'Group has been updated.')]);
    }

    /**
     * Getting group add form.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function add(Request $request)
    {
        try {
            $group = new Group();
            $sandbox = new Sandbox();
            $menu = (new Temp())->getPropertyMenuAcrossVenue();

        } catch(Exception $e) {
            return Utility::httpExceptionHandler(500, $e);
        }

        return SmartView::render(true, compact($group->singular(), $sandbox->singular(), 'menu'));
    }

    /**
     * Adding group.
     *
     * @param Request $request
     * @return mixed
     *
     * @throws \App\Libraries\Model\ModelValidationException
     * @throws \Exception
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postAdd(Request $request)
    {
        try {
            ${$this->getModel()->singular()} = Auth::user();
            $properties = [];
            $request->request->add(['status' => Utility::constant('status.1.slug')]);
            $group = Group::add(${$this->getModel()->singular()}->getKey(), $request->all(), $properties);
            $join = new Join();

        } catch (ModelValidationException $e) {
            $this->throwValidationException(
                $request, $e->validator
            );

        } catch(Exception $e) {
            return Utility::httpExceptionHandler(500, $e);
        }

        return SmartView::render(null, ['message' => Translator::transSmart('app.Group has been added. Page will refresh in 3 seconds.', 'Group has been added. Page will refresh in 3 seconds.')]);

    }

    /**
     * Select all groups members.
     *
     * @param Request $request
     * @param $id
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function joinGroupMembers(Request $request, $id)
    {
        try {

            $member  = (new Member())->getOneForActivity(Auth::user()->getKey());
            $auth_member = $member;
            $following = new Following();
            $join = new Join();
            $sandbox = new Sandbox();
            $group = Group::retrieve($id);
            ${$join->plural()} = $join->members($group);
            $last_id = Arr::get(Arr::last(${$join->plural()}->all()), $join->getKeyName());


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $edge = $join;
        $edges = ${$join->plural()};
        $url = URL::route('admin::group::join-group-member', array($group->getKeyName() => $group->getKey()));

        return SmartView::render('members', compact('auth_member', $sandbox->singular(), $group->singular(), $following->singular(),  $join->singular(), $join->plural(), 'edge', 'edges', 'url', 'last_id', 'group'));

    }

    /**
     * Show infinite loading of member list(paginate)
     *
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function joinGroupMember(Request $request, $id)
    {
        try {
            $member  = (new Member())->getOneForActivity(Auth::user()->getKey());
            $auth_member = $member;
            $following = new Following();
            $join = new Join();
            $sandbox = new Sandbox();
            $group = Group::retrieve($id);
            ${$join->plural()} = $join->members($group, $request->get('member-id'));
            $last_id = (${$join->plural()}->count() > $join->getPaging()) ? Arr::get(${$join->plural()}->get($join->getPaging() - 1), $join->getKeyName()) : Arr::get(Arr::last(${$join->plural()}->all()), $join->getKeyName());

        } catch(ModelNotFoundException $e) {
            return Utility::httpExceptionHandler(404, $e);

        } catch(InvalidArgumentException $e) {
            return Utility::httpExceptionHandler(500, $e);

        } catch(Exception $e) {
            return Utility::httpExceptionHandler(500, $e);

        }

        $edges = ${$join->plural()};

        return SmartView::render('member', compact('auth_member', $sandbox->singular(), $group->singular(), $following->singular(), $join->singular(), $join->plural(), 'edges', 'last_id'));
    }

    /**
     * Remove member from group.
     *
     * @param $id
     * @param $memberId
     *
     * @return mixed
     *
     * @throws \App\Libraries\Model\ModelValidationException
     * @throws \Exception
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postLeaveGroup(Request $request, $id, $memberId)
    {
        try {
            $member  = (new Member())->getOneForActivity($memberId);
            $model = (new Group())->findOrFail($id);

            (new Join())->leave($model, $member->getKey());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $edge = new Join();

        $member_key =  $memberId;
        $text = $edge->text($model);
        $count = $edge->number($model);

        return SmartView::render(null, [
            Translator::transSmart('app.status', 'status') => Translator::transSmart('app.success', 'success'),
            Translator::transSmart('app.message', 'message') => Translator::transSmart('app.Successfully remove member', 'Successfully remove member'),
                'count' => $count]
        );
    }

    /**
     * Showing invite data.
     *
     * @param Request $request
     * @param $id
     *
     * @return mixed
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function inviteGroup(Request $request, $id)
    {
        try {
            $group = new Group();
            $invite = new Invite();
            ${$group->singular()} = $group->feedOnlyOrFail($id);

        } catch (ModelNotFoundException $e) {
            return Utility::httpExceptionHandler(404, null, Translator::transSmart('app.Cannot invite member on inactive group', 'Cannot invite member on inactive group'));

        } catch (ModelValidationException $e) {
            $this->throwValidationException(
                $request, $e->validator
            );

        } catch (Exception $e) {
            return Utility::httpExceptionHandler(500, $e);
        }

        return SmartView::render(true, compact($this->getModel()->singular(), $group->singular(), $invite->singular()));
    }

    /**
     * Invite members to join group.
     *
     * @param Request $request
     * @param $id
     * @return mixed
     *
     * @throws \App\Libraries\Model\ModelValidationException
     * @throws \Exception
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postInviteGroup(Request $request, $id)
    {
        try {
            $member = (new Member())->getOneForActivity(Auth::user()->getKey());
            $model = (new Group())->feedOnlyOrFail($id);
            (new Invite())->add($model, $member->getKey(), $request->all());

        } catch(ModelNotFoundException $e) {
            return Utility::httpExceptionHandler(404, $e);
        } catch(ModelValidationException $e) {
            $this->throwValidationException(
                $request, $e->validator
            );

        } catch(Exception $e) {
            return Utility::httpExceptionHandler(500, $e);
        }

        return SmartView::render(null);
    }
}