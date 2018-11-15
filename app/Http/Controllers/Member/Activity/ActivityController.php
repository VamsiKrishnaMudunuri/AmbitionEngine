<?php

namespace App\Http\Controllers\Member\Activity;

use App\Models\User;
use Exception;
use Translator;
use CLDR;
use Sess;
use Auth;
use Utility;
use SmartView;
use URL;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Company;
use App\Models\Member;
use App\Models\Sandbox;
use App\Models\MongoDB\Following;
use App\Models\MongoDB\Follower;
use App\Models\MongoDB\Group;
use App\Models\MongoDB\Post;
use App\Models\MongoDB\Comment;
use App\Models\MongoDB\Like;
use App\Models\MongoDB\Going;
use App\Models\MongoDB\Invite;
use App\Models\MongoDB\Join;
use App\Models\MongoDB\Work;
use App\Models\MongoDB\ActivityStat;
use App\Models\MongoDB\CompanyActivityStat;

class ActivityController extends Controller
{

    public function __construct()
    {
        parent::__construct(new Member());
    }

    public function postFollow(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = $this->getModel()->getOneForActivity(Auth::user()->getKey());
            $from = ${$this->getModel()->singular()}->getKey();
            $to = $id;

            if(!Following::hasAlreadyFollow($from, $to)){
                Following::follow($from, $to);
            }

            $stats = array('from' => ActivityStat::getStatsByUserID($from, true), 'to' => ActivityStat::getStatsByUserID($to, true));

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

        return SmartView::render(null, compact('stats'));

    }

    public function postUnfollow(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = $this->getModel()->getOneForActivity(Auth::user()->getKey());
            $from = ${$this->getModel()->singular()}->getKey();
            $to = $id;

            $following = new Following();

            if(Following::hasAlreadyFollow($from, $to)){
                $following = Following::unfollow($from, $to);
            }


            $stats = array('from' => ActivityStat::getStatsByUserID($from, true), 'to' => ActivityStat::getStatsByUserID($to, true));

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

        return SmartView::render(null, compact('stats'));
    }

    public function postLikePost(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = $this->getModel()->getOneForActivity(Auth::user()->getKey());
            $post = Post::like(${$this->getModel()->singular()}->getKey(), $id);

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    ${$this->getModel()->singular()},
                    'profileSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.profile'),
                    true
                );

            }

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        $edge = new Like();
        $member_key =  ${$this->getModel()->singular()}->getKey();
        $text = $edge->text($post);
        $count = $edge->number($post);

        return SmartView::render(null, compact($this->getModel()->singular(), 'member_key', 'text', 'count'));

    }

    public function postDeleteLikePost(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = $this->getModel()->getOneForActivity(Auth::user()->getKey());
            $post = Post::deleteLike(${$this->getModel()->singular()}->getKey(), $id);

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    ${$this->getModel()->singular()},
                    'profileSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.profile'),
                    true
                );

            }

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $edge = new Like();
        $member_key =  ${$this->getModel()->singular()}->getKey();
        $text = $edge->text($post);
        $count = $edge->number($post);

        return SmartView::render(null, compact($this->getModel()->singular(), 'member_key', 'text', 'count'));

    }

    public function likePostMembers(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = $this->getModel()->getOneForActivity(Auth::user()->getKey());
            $auth_member = ${$this->getModel()->singular()};
            $following = new Following();
            $like = new Like();
            $sandbox = new Sandbox();
            $post = Post::retrieve($id);
            ${$like->plural()} =  $like->members($post);
            $last_id = Arr::get(Arr::last(${$like->plural()}->all()),  $like->getKeyName());


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $edge = $like;
        $edges = ${$like->plural()};
        $url = URL::route('member::activity::like-post-member', array($post->getKeyName() => $post->getKey()));

        return SmartView::render('members', compact('auth_member', $sandbox->singular(),  $post->singular(), $following->singular(),   $like->singular(),  $like->plural(), 'edge', 'edges', 'url', 'last_id'));


    }

    public function likePostMember(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = $this->getModel()->getOneForActivity(Auth::user()->getKey());
            $auth_member = ${$this->getModel()->singular()};
            $following = new Following();
            $like = new Like();
            $sandbox = new Sandbox();
            $post = Post::retrieve($id);
            ${$like->plural()} = $like->members($post, $request->get('member-id'));
            $last_id = (${$like->plural()}->count() > $like->getPaging()) ? Arr::get(${$like->plural()}->get($like->getPaging() - 1), $like->getKeyName()) : Arr::get(Arr::last(${$like->plural()}->all()), $like->getKeyName());

            if (Utility::isNativeAppResponse()) {

                $models = [$auth_member, $post->user];

                foreach ($models as $model) {

                    Sandbox::s3()->generateImageLinks(
                        $model,
                        'profileSandboxWithQuery',
                        Arr::get(User::$sandbox, 'image.profile'),
                        true
                    );

                }

                foreach ($post->galleriesSandboxWithQuery as $gallery) {
                    Sandbox::s3()->generateImageLinks(
                        $post,
                        'galleriesSandboxWithQuery',
                        Arr::get(Post::$sandbox, 'image.gallery'),
                        true
                    );
                }
            }


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        $edges = ${$like->plural()};


        return SmartView::render('member', compact('auth_member', $sandbox->singular(),  $post->singular(), $following->singular(),   $like->singular(),  $like->plural(), 'edges', 'last_id'));


    }

    public function postJoinGroup(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = $this->getModel()->getOneForActivity(Auth::user()->getKey());
            $model = (new Group())->findOrFail($id);
            (new Join())->join($model, ${$this->getModel()->singular()}->getKey());

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    ${$this->getModel()->singular()},
                    'profileSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.profile'),
                    true
                );

            }

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
        $member_key =  ${$this->getModel()->singular()}->getKey();
        $member_view =  view('templates.widget.social_media.member.circle', array('member' => ${$this->getModel()->singular()}))->render();
        $text = $edge->text($model);
        $count = $edge->number($model);


        return SmartView::render(null, compact($this->getModel()->singular(), 'member_key', 'member_view', 'text', 'count'));

    }

    public function postLeaveGroup(Request $request, $id){

        try {

            ${$this->getModel()->singular()}  = $this->getModel()->getOneForActivity(Auth::user()->getKey());
            $model = (new Group())->findOrFail($id);

            (new Join())->leave($model, ${$this->getModel()->singular()} ->getKey());

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    ${$this->getModel()->singular()},
                    'profileSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.profile'),
                    true
                );

            }


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
        $member_key =  ${$this->getModel()->singular()}->getKey();
        $member_view =  view('templates.widget.social_media.member.circle', array('member' => ${$this->getModel()->singular()}))->render();
        $text = $edge->text($model);
        $count = $edge->number($model);

        return SmartView::render(null, compact($this->getModel()->singular(), 'member_key', 'member_view', 'text', 'count'));

    }

    public function inviteGroup(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = $this->getModel()->getOneForActivity(Auth::user()->getKey());
            $group = new Group();
            $invite = new Invite();
            ${$group->singular()} = $group->feedOnlyOrFail($id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(true, compact($this->getModel()->singular(), $group->singular(), $invite->singular()));

    }

    public function postInviteGroup(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = $this->getModel()->getOneForActivity(Auth::user()->getKey());
            $model = (new Group())->feedOnlyOrFail($id);
            (new Invite())->add($model, ${$this->getModel()->singular()}->getKey(), $request->all());

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

    public function postDeleteInviteGroup(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = $this->getModel()->getOneForActivity(Auth::user()->getKey());
            $model = (new Group())->feedOnlyOrFail($id);
            (new Invite())->delByReceiverAndHideActivityAndNotification($model, $request->all());

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

    public function joinGroupMembers(Request $request, $id){

        try {

            ${$this->getModel()->singular()}  = $this->getModel()->getOneForActivity(Auth::user()->getKey());
            $auth_member = ${$this->getModel()->singular()};
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
        $url = URL::route('member::activity::join-group-member', array($group->getKeyName() => $group->getKey()));


        return SmartView::render('members', compact('auth_member', $sandbox->singular(), $group->singular(), $following->singular(),  $join->singular(), $join->plural(), 'edge', 'edges', 'url', 'last_id'));


    }

    public function joinGroupMember(Request $request, $id){

        try {

            ${$this->getModel()->singular()}  = $this->getModel()->getOneForActivity(Auth::user()->getKey());
            $auth_member = ${$this->getModel()->singular()} ;
            $following = new Following();
            $join = new Join();
            $sandbox = new Sandbox();
            $group = Group::retrieve($id);
            ${$join->plural()} = $join->members($group, $request->get('member-id'));
            $last_id = (${$join->plural()}->count() > $join->getPaging()) ? Arr::get(${$join->plural()}->get($join->getPaging() - 1), $join->getKeyName()) : Arr::get(Arr::last(${$join->plural()}->all()), $join->getKeyName());

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    $auth_member,
                    'profileSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.profile'),
                    true
                );

                Sandbox::s3()->generateImageLinks(
                    $group,
                    'profileSandboxWithQuery',
                    Arr::get(Group::$sandbox, 'image.profile'),
                    true
                );

                foreach (${$join->plural()} as $item) {
                    Sandbox::s3()->generateImageLinks(
                        $item->user,
                        'profileSandboxWithQuery',
                        Arr::get(User::$sandbox, 'image.profile'),
                        true
                    );
                }
            }


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $edges = ${$join->plural()};

        return SmartView::render('member', compact('auth_member', $sandbox->singular(), $group->singular(), $following->singular(), $join->singular(), $join->plural(), 'edges', 'last_id'));


    }

    public function inviteGroupMember(Request $request, $id){

        try {

            ${$this->getModel()->singular()}  = $this->getModel()->getOneForActivity(Auth::user()->getKey());
            $auth_member = ${$this->getModel()->singular()} ;
            $following = new Following();
            $invite = new Invite();
            $sandbox = new Sandbox();
            $group = Group::retrieve($id);
            ${$invite->plural()} = $invite->members($group, $request->get('page-no'));
            $last_id = (${$invite->plural()}->count() > $invite->getPaging()) ? Arr::get(${$invite->plural()}->get($invite->getPaging() - 1), $invite->getKeyName()) : Arr::get(Arr::last(${$invite->plural()}->all()), $invite->getKeyName());

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    $auth_member,
                    'profileSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.profile'),
                    true
                );

                Sandbox::s3()->generateImageLinks(
                    $group,
                    'profileSandboxWithQuery',
                    Arr::get(Group::$sandbox, 'image.profile'),
                    true
                );

                foreach (${$invite->plural()} as $item) {
                    Sandbox::s3()->generateImageLinks(
                        $item['user'],
                        'profileSandboxWithQuery',
                        Arr::get(User::$sandbox, 'image.profile'),
                        true
                    );
                }

            }

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $edges = ${$invite->plural()};


        return SmartView::render('member', compact('auth_member', $sandbox->singular(), $group->singular(), $following->singular(), $invite->singular(), $invite->plural(), 'edges', 'last_id'));


    }

    public function postGoingEvent(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = $this->getModel()->getOneForActivity(Auth::user()->getKey());
            $post = Post::going(${$this->getModel()->singular()}->getKey(), $id);

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    ${$this->getModel()->singular()},
                    'profileSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.profile'),
                    true
                );
            }

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

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

        $edge = new Going();
        $member_key =  ${$this->getModel()->singular()}->getKey();
        $member_view =  view('templates.widget.social_media.member.circle', array('member' => ${$this->getModel()->singular()}))->render();
        $text = $edge->text($post);
        $count = $edge->number($post);

        return SmartView::render(null, compact($this->getModel()->singular(), 'member_key', 'member_view', 'text', 'count'));

    }

    public function postDeleteGoingEvent(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = $this->getModel()->getOneForActivity(Auth::user()->getKey());
            $post = Post::deleteGoing(${$this->getModel()->singular()}->getKey(), $id);

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    ${$this->getModel()->singular()},
                    'profileSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.profile'),
                    true
                );
            }

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
        $member_key =  ${$this->getModel()->singular()}->getKey();
        $member_view =  view('templates.widget.social_media.member.circle', array('member' => ${$this->getModel()->singular()}))->render();
        $text = $edge->text($post);
        $count = $edge->number($post);

        return SmartView::render(null, compact($this->getModel()->singular(), 'member_key', 'member_view', 'text', 'count'));

    }

    public function inviteEvent(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = $this->getModel()->getOneForActivity(Auth::user()->getKey());
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

    public function postInviteEvent(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = $this->getModel()->getOneForActivity(Auth::user()->getKey());
            $model = (new Post())->eventOnlyOrFail($id);
            $model->isOpenOrFail();
            (new Invite())->add($model, ${$this->getModel()->singular()}->getKey(), $request->all());

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

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

    public function goingEventMembers(Request $request, $id){

        try {

            ${$this->getModel()->singular()}  = $this->getModel()->getOneForActivity(Auth::user()->getKey());
            $auth_member = ${$this->getModel()->singular()};
            $following = new Following();
            $going = new Going();
            $sandbox = new Sandbox();
            $post = Post::retrieveForEvent($id);
            ${$going->plural()} =  $going->members($post);
            $last_id = Arr::get(Arr::last(${$going->plural()}->all()),  $going->getKeyName());


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $edge = $going;
        $edges = ${$going->plural()};
        $url = URL::route('member::activity::going-event-member', array($post->getKeyName() => $post->getKey()));

        return SmartView::render('members', compact('auth_member', $sandbox->singular(),  $post->singular(), $following->singular(),  $going->singular(),  $going->plural(), 'edge', 'edges', 'url', 'last_id'));



    }

    public function goingEventMember(Request $request, $id){

        try {

            ${$this->getModel()->singular()}  = $this->getModel()->getOneForActivity(Auth::user()->getKey());
            $auth_member = ${$this->getModel()->singular()} ;
            $following = new Following();
            $going = new Going();
            $sandbox = new Sandbox();
            $post = Post::retrieveForEvent($id);
            ${$going->plural()} =  $going->members($post, $request->get('member-id'));
            $last_id = (${$going->plural()}->count() > $going->getPaging()) ? Arr::get(${$going->plural()}->get($going->getPaging() - 1), $going->getKeyName()) : Arr::get(Arr::last(${$going->plural()}->all()), $going->getKeyName());

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    $auth_member,
                    'profileSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.profile'),
                    true
                );

                foreach (${$going->plural()} as $item) {
                    Sandbox::s3()->generateImageLinks(
                        $item->user,
                        'profileSandboxWithQuery',
                        Arr::get(User::$sandbox, 'image.profile'),
                        true
                    );
                }

            }



        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        $edges = ${$going->plural()};

        return SmartView::render('member', compact('auth_member', $sandbox->singular(),  $post->singular(), $following->singular(),  $going->singular(),  $going->plural(), 'edges', 'last_id'));


    }

    public function workMembers(Request $request, $id){

        try {

            ${$this->getModel()->singular()}  = $this->getModel()->getOneForActivity(Auth::user()->getKey());
            $auth_member = ${$this->getModel()->singular()};
            $following = new Following();
            $work = new Work();
            $sandbox = new Sandbox();
            $company = (new Company())->getOneOrFail($id);
            ${$work->plural()} = $work->members($company);
            $last_id = Arr::get(Arr::last(${$work->plural()}->all()), $work->getKeyName());

            $empty_text = Translator::transSmart('app.Not have employee', 'Not have employee');


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $edge = $work;
        $edges = ${$work->plural()};
        $url = URL::route('member::activity::work-member', array($company->getKeyName() => $company->getKey()));


        return SmartView::render('members', compact('auth_member', $sandbox->singular(), $company->singular(), $following->singular(),  $work->singular(), $work->plural(), 'edge', 'edges', 'url', 'last_id', 'empty_text'));


    }

    public function workMember(Request $request, $id){

        try {

            ${$this->getModel()->singular()}  = $this->getModel()->getOneForActivity(Auth::user()->getKey());
            $auth_member = ${$this->getModel()->singular()} ;
            $following = new Following();
            $work = new Work();
            $sandbox = new Sandbox();
            $company = (new Company())->getOneOrFail($id);
            ${$work->plural()} = $work->members($company, $request->get('member-id'));
            $last_id = (${$work->plural()}->count() > $work->getPaging()) ? Arr::get(${$work->plural()}->get($work->getPaging() - 1), $work->getKeyName()) : Arr::get(Arr::last(${$work->plural()}->all()), $work->getKeyName());

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    $auth_member,
                    'profileSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.profile'),
                    true
                );

                if (${$work->plural()}->isNotEmpty()) {
                    foreach (${$work->plural()} as $item) {
                        Sandbox::s3()->generateImageLinks(
                            $item->user,
                            'profileSandboxWithQuery',
                            Arr::get(User::$sandbox, 'image.profile'),
                            true
                        );
                    }
                }
            }


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        $edges = ${$work->plural()};

        return SmartView::render('member', compact('auth_member', $sandbox->singular(), $company->singular(), $following->singular(), $work->singular(), $work->plural(), 'edges', 'last_id'));


    }


}
