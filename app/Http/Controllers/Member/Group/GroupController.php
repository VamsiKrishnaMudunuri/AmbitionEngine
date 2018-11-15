<?php

namespace App\Http\Controllers\Member\Group;


use URL;
use CLDR;
use Sess;
use Auth;
use Utility;
use SmartView;
use Exception;
use Translator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Temp;
use App\Models\Member;
use App\Models\Subscription;
use App\Models\Sandbox;
use App\Models\MongoDB\Group;
use App\Models\MongoDB\Feed;
use App\Models\MongoDB\Join;
use App\Models\MongoDB\Following;
use App\Models\MongoDB\Invite;
use App\Models\MongoDB\Post;
use App\Models\MongoDB\Comment;
use App\Models\MongoDB\Like;

class GroupController extends Controller
{

    public function __construct()
    {
        parent::__construct(new Member());
    }

    public function index(Request $request){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $menu = (new Temp())->getPropertyMenuWithCountryAndStateGroupingList();

            $group = new Group();
            $join = new Join();
            $sandbox =  new Sandbox();

            ${$group->plural()} = $group->feeds(${$this->getModel()->singular()}->getKey(), $request->get('feed-id'), $request->get('property'));


        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(null, compact($this->getModel()->singular(), 'menu', $group->singular(), $group->plural(), $join->singular(), $sandbox->singular()));


    }

    public function discoverGroup(Request $request){

        try {

            ${$this->getModel()->singular()} = Auth::user();

            $group = new Group();
            $join = new Join();
            $sandbox =  new Sandbox();

            ${$group->plural()} = $group->feeds(${$this->getModel()->singular()}->getKey(), $request->get('feed-id'), $request->get('property'));

            if (Utility::isNativeAppResponse()) {

                foreach(${$group->plural()} as $item) {

                    Sandbox::s3()->generateImageLinks(
                        $item->user,
                        'profileSandboxWithQuery',
                        Arr::get(User::$sandbox, 'image.profile'),
                        true
                    );

                    Sandbox::s3()->generateImageLinks(
                        $item,
                        'profileSandboxWithQuery',
                        Arr::get(Group::$sandbox, 'image.profile'),
                        true
                    );
                }
            }


        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(true, compact($this->getModel()->singular(), $group->singular(), $group->plural(),$join->singular(), $sandbox->singular()));


    }

    public function myGroups(Request $request){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $menu = (new Temp())->getPropertyMenuWithCountryAndStateGroupingList();

            $group = new Group();
            $join = new Join();
            $sandbox =  new Sandbox();

            ${$group->plural()} = $group->myFeeds(${$this->getModel()->singular()}->getKey(), $request->get('feed-id'), $request->get('property'));


        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(null, compact($this->getModel()->singular(), 'menu', $group->singular(), $group->plural(), $join->singular(), $sandbox->singular()));


    }

    public function myGroup(Request $request){

        try {

            ${$this->getModel()->singular()} = Auth::user();

            $group = new Group();
            $join = new Join();
            $sandbox =  new Sandbox();

            ${$group->plural()} = $group->myFeeds(${$this->getModel()->singular()}->getKey(), $request->get('feed-id'), $request->get('property'));

            if (Utility::isNativeAppResponse()) {

                foreach(${$group->plural()} as $item) {

                    Sandbox::s3()->generateImageLinks(
                        $item->user,
                        'profileSandboxWithQuery',
                        Arr::get(User::$sandbox, 'image.profile'),
                        true
                    );

                    Sandbox::s3()->generateImageLinks(
                        $item,
                        'profileSandboxWithQuery',
                        Arr::get(Group::$sandbox, 'image.profile'),
                        true
                    );
                }
            }


        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(true, compact($this->getModel()->singular(), $group->singular(), $group->plural(),$join->singular(), $sandbox->singular()));


    }

    public function add(Request $request){

        try {

            $group = new Group();
            $sandbox = new Sandbox();
            $menu = (new Temp())->getPropertyMenuAcrossVenue();

        }catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($group->singular(), $sandbox->singular(), 'menu'));

    }

    public function postAdd(Request $request){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $properties = (new Subscription())->getActiveSubscribedPropertyIdListOnlyByUser( ${$this->getModel()->singular()}->getKey() );
            $group = Group::add(${$this->getModel()->singular()}->getKey(), $request->all(), $properties);
            $join = new Join();

        }catch(ModelValidationException $e){
            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){
            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(null, ['message' => Translator::transSmart('app.Thank for your group suggestion. We will review and approve it as soon as possible.', 'Thank for your group suggestion. We will review and approve it as soon as possible.')]);

        //return SmartView::render(null, ['url' => URL::route('member::group::group', array($group->getKeyname() => $group->getKey()))]);

        //return SmartView::render(true, compact($group->singular(), $join->singular()));

    }

    public function edit(Request $request, $id){


        try {

            $group = Group::retrieve($id);
            $sandbox = (is_null($group->profileSandboxWithQuery)) ? new Sandbox() : $group->profileSandboxWithQuery;
            $view = $request->get('view');
            $menu = (new Temp())->getPropertyMenuAcrossVenue();

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(true, compact($group->singular(), $sandbox->singular(), 'view', 'menu'));

    }

    public function postEdit(Request $request, $id){

        try {
            ${$this->getModel()->singular()} = Auth::user();
            $group = Group::edit($id, ${$this->getModel()->singular()}->getKey(), $request->all());
            $join = new Join();
            $view = $request->get('view');

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    $group->user,
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



        return SmartView::render(true, compact($group->singular(), $join->singular(), 'view'));

    }

    public function postDelete(Request $request, $id){

        try {

            Group::del($id);

        } catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        } catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, ['url' => URL::route('member::group::index')]);

    }

    public function group(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $group = (new Group())->feedOrFail(${$this->getModel()->singular()}->getKey(), $id);
            $feed = new Feed();
            $join = new Join();
            $post = new Post();
            $filteredPost = new Post();
            $like = new Like();
            $comment = new Comment();
            $sandbox =  new Sandbox();

            $event = new Post();

            $events = $event->groupEventsWithConventionPagination(${$this->getModel()->singular()}->getKey(), $group->getKey());

            if($request->get($feed->queryParams['filter']) && $feedID = $request->get($feed->queryParams['id'])){
                $filteredPost = $filteredPost->feed(${$this->getModel()->singular()}->getKey(), Utility::constant('post_type.1.slug'), $feedID,  array($post->group()->getForeignKey() => $group->getKey()));
            }

            ${$post->plural()} = $post->feeds(${$this->getModel()->singular()}->getKey(), Utility::constant('post_type.1.slug'), array($post->group()->getForeignKey() => $group->getKey()), $request->get('feed-id'));

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    $group->user,
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

                foreach (${$post->plural()} as $item) {

                    Sandbox::s3()->generateImageLinks(
                        $item,
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

        return SmartView::render(true, compact($this->getModel()->singular(), $group->singular(), $join->singular(), 'filteredPost', $post->singular(), $post->plural(), $like->singular(), $comment->singular(), $sandbox->singular(), 'event', 'events'));

    }

}