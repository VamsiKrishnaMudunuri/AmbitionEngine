<?php

namespace App\Http\Controllers\Member\Post;

use Exception;
use Translator;
use CLDR;
use Sess;
use Auth;
use Utility;
use SmartView;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\User;
use App\Models\Member;
use App\Models\Property;
use App\Models\Subscription;
use App\Models\Sandbox;

use App\Models\MongoDB\Post;
use App\Models\MongoDB\Comment;
use App\Models\MongoDB\Group;
use App\Models\MongoDB\Place;
use App\Models\MongoDB\Like;
use App\Models\MongoDB\Going;
use App\Models\MongoDB\Join;
use App\Models\MongoDB\BusinessOpportunity;

class PostController extends Controller
{

    public function __construct()
    {
        parent::__construct(new Member());
    }

    public function feed(Request $request)
    {

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $post = new Post();
            $like = new Like();
            $going = new Going();
            $comment = new Comment();
            $sandbox = new Sandbox();

            $business_opportunity = new BusinessOpportunity();

            if (Utility::isNativeAppResponse()) {
                ${$post->plural()} = $post->feeds(${$this->getModel()->singular()}->getKey(), Utility::constant('post_type.0.slug'), array(), $request->get('feed-id'));
            }else{
                ${$post->plural()} = $post->smartFeeds(${$this->getModel()->singular()}->getKey(), $request->get('feed-id'));
            }


        } catch (InvalidArgumentException $e) {

            return Utility::httpExceptionHandler(500, $e);

        } catch (Exception $e) {

            return Utility::httpExceptionHandler(500, $e);

        }


        if (Utility::isNativeAppResponse()) {

            foreach (${$post->plural()} as $post) {

                Sandbox::s3()->generateImageLinks($post, 'galleriesSandboxWithQuery', Arr::get(Post::$sandbox, 'image.gallery'), true);
                Sandbox::s3()->generateImageLinks($post->user, 'profileSandboxWithQuery', Arr::get(User::$sandbox, 'image.profile'), true);


            }

        }


        return SmartView::render(true, compact($this->getModel()->singular(), $post->singular(), $post->plural(), $like->singular(),
            $going->singular(), $comment->singular(), $sandbox->singular(), $business_opportunity->singular()));

    }

    public function newFeed(Request $request){

        try {

            ${$this->getModel()->singular()}  = Auth::user();
            $post = new Post();
            $like = new Like();
            $comment = new Comment();
            $sandbox =  new Sandbox();

            ${$post->plural()} = $post->newFeeds(${$this->getModel()->singular()}->getKey(), Utility::constant('post_type.0.slug'), array(), $request->get('feed-id'));

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render('feed', compact($this->getModel()->singular(), $post->singular(), $post->plural(), $like->singular(), $comment->singular(), $sandbox->singular()));


    }

    public function postFeed(Request $request){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $properties = (new Subscription())->getActiveSubscribedPropertyIdListOnlyByUser( ${$this->getModel()->singular()}->getKey() );
            $post = Post::add(${$this->getModel()->singular()}->getKey(), Utility::constant('post_type.0.slug'),  $request->all(), $properties);
            $like = new Like();
            $comment = new Comment();

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    $post->user,
                    'profileSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.profile'),
                    true
                );

                foreach ($post->galleriesSandboxWithQuery as $gallery) {
                    Sandbox::s3()->generateImageLinks(
                        $post,
                        'galleriesSandboxWithQuery',
                        Arr::get(Post::$sandbox, 'image.gallery'),
                        true
                    );
                }
            }

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($this->getModel()->singular(), $post->singular(), $like->singular(), $comment->singular()));

    }

    public function editFeed(Request $request, $id){


        try {

            ${$this->getModel()->singular()} = Auth::user();
            $post = Post::retrieve($id);
            $sandbox = new Sandbox();


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        } catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($post->singular(), $sandbox->singular()));

    }

    public function postEditFeed(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $post = Post::edit($id, ${$this->getModel()->singular()}->getKey(), $request->all());
            $like = new Like();


            $id = $post->getKey();
            $message = $post->message;
            $time = CLDR::showRelativeDateTime($post->getAttribute($post->getCreatedAtColumn()), config('social_media.datetime.datetime.full.format'));
            $randomPhotos = $post->getRandomGalleryPhotos();
            $layout = $randomPhotos['layout'];
            $photos = $randomPhotos['photos'];

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    $post->user,
                    'profileSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.profile'),
                    true
                );

                foreach ($post->galleriesSandboxWithQuery as $item) {
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

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, compact('id', 'message', 'time', 'layout', 'photos', 'post'));

    }

    public function group(Request $request, $group_id){

        try {

            ${$this->getModel()->singular()}  = Auth::user();
            $post = new Post();
            $join = new Join();
            $like = new Like();
            $comment = new Comment();
            $sandbox =  new Sandbox();

            ${$post->plural()} = $post->feeds(${$this->getModel()->singular()}->getKey(), Utility::constant('post_type.1.slug'), array($post->group()->getForeignKey() => $group_id), $request->get('feed-id'));

            if (Utility::isNativeAppResponse()) {

                foreach (${$post->plural()} as $item) {
                    Sandbox::s3()->generateImageLinks(
                        $item->user,
                        'profileSandboxWithQuery',
                        Arr::get(User::$sandbox, 'image.profile'),
                        true
                    );

                    Sandbox::s3()->generateImageLinks(
                        $item,
                        'galleriesSandboxWithQuery',
                        Arr::get(Post::$sandbox, 'image.gallery'),
                        true
                    );
                }

            }


        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(true, compact($this->getModel()->singular(), $post->plural(), $join->singular(), $like->singular(), $comment->singular(), $sandbox->singular()));


    }

    public function newGroupFeed(Request $request, $group_id){

        try {

            ${$this->getModel()->singular()}  = Auth::user();
            $post = new Post();
            $join = new Join();
            $like = new Like();
            $comment = new Comment();
            $sandbox =  new Sandbox();

            ${$post->plural()} = $post->newFeeds(${$this->getModel()->singular()}->getKey(), Utility::constant('post_type.1.slug'), array($post->group()->getForeignKey() => $group_id), $request->get('feed-id'));

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render('group', compact($this->getModel()->singular(), $post->plural(), $join->singular(), $like->singular(), $comment->singular(), $sandbox->singular()));


    }

    public function postGroup(Request $request, $group_id){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $properties = (new Subscription())->getActiveSubscribedPropertyIdListOnlyByUser( ${$this->getModel()->singular()}->getKey() );
            $post = Post::addByGroup(${$this->getModel()->singular()}->getKey(), $group_id, $request->all(), $properties);
            $join = new Join();
            $like = new Like();
            $comment = new Comment();

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    $post->user,
                    'profileSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.profile'),
                    true
                );

                foreach ($post->galleriesSandboxWithQuery as $item) {
                    Sandbox::s3()->generateImageLinks(
                        $post,
                        'galleriesSandboxWithQuery',
                        Arr::get(Post::$sandbox, 'image.gallery'),
                        true
                    );
                }

            }

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($this->getModel()->singular(), $post->singular(), $join->singular(), $like->singular(), $comment->singular()));

    }

    public function groupEvent(Request $request, $group_id){

        try {

            ${$this->getModel()->singular()}  = Auth::user();
            $group = new Group();
            $post = new Post();
            $going = new Going();
            $comment = new Comment();
            $sandbox =  new Sandbox();

            ${$post->plural()} = $post->groupEventsWithConventionPagination(${$this->getModel()->singular()}->getKey(), $group_id, $request->get('page-no'));

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(true, compact($this->getModel()->singular(), $group->singular(), $post->plural(), $going->singular(), $comment->singular(), $sandbox->singular()));


    }

    public function addGroupEvent(Request $request, $group_id){


        try {

            ${$this->getModel()->singular()}  = Auth::user();
            $group = (new Group())->findOrFail($group_id);
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

        }catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($post->singular(), $group->singular(), $property->singular(), $place->singular(), $going->singular(), $comment->singular(), $sandbox->singular()));

    }

    public function postAddGroupEvent(Request $request, $group_id){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $group = (new Group())->findOrFail($group_id);
            $properties = (new Subscription())->getActiveSubscribedPropertyIdListOnlyByUser( ${$this->getModel()->singular()}->getKey() );
            $post = Post::addByEventGroup(${$this->getModel()->singular()}->getKey(), $group_id, $request->all(), $properties);
            $going = new Going();
            $comment = new Comment();
            $sandbox = new Sandbox();

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, ['message' => Translator::transSmart('app.Thank you for your event suggestion for this group. We will review and approve it as soon as possible.', 'Thank you for your event suggestion for this group. We will review and approve it as soon as possible.')]);


        //return SmartView::render(true, compact($this->getModel()->singular(), $post->singular(), $going->singular(), $comment->singular(), $sandbox->singular()));

    }

    public function editGroupEvent(Request $request, $id){


        try {

            ${$this->getModel()->singular()}  = Auth::user();
            $property = new Property();
            $post = (new Post())->findEventOrFailForEdit($id);
            $group = new Group();
            $post->start = $post->start->setTimezone($post->timezone);
            $post->end = $post->end->setTimezone($post->timezone);
            if(!is_null($post->registration_closing_date)) {
                $post->registration_closing_date = $post->registration_closing_date->setTimezone($post->timezone);
            }
            $going = new Going();
            $comment = new Comment();
            $place = ($post->hostWithQuery) ? $post->hostWithQuery  : new Place();
            $sandbox = new Sandbox();

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        } catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($post->singular(), $group->singular(), $property->singular(), $place->singular(), $going->singular(), $comment->singular(), $sandbox->singular()));

    }

    public function postEditGroupEvent(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $sandbox = new Sandbox();
            $post = Post::editByEventGroup($id, ${$this->getModel()->singular()}->getKey(), $request->all());
            $group = new Group();
            $going = new Going();
            $comment = new Comment();
            $sandbox = new Sandbox();

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($this->getModel()->singular(), $sandbox->singular(), $post->singular(), $group->singular(), $going->singular(), $comment->singular(), $sandbox->singular()));

    }

    public function event(Request $request){

        try {

            ${$this->getModel()->singular()}  = Auth::user();
            $post = new Post();
            $going = new Going();
            $comment = new Comment();
            $sandbox =  new Sandbox();

            ${$post->plural()} = $post->eventsMixedWithConventionPagination(${$this->getModel()->singular()}->getKey(), $request->get('page-no'), [], $request->get('property'));

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(true, compact($this->getModel()->singular(), $post->plural(), $going->singular(), $comment->singular(), $sandbox->singular()));


    }

    public function newEvent(Request $request){

        try {

            ${$this->getModel()->singular()}  = Auth::user();
            $post = new Post();
            $going = new Going();
            $comment = new Comment();
            $sandbox =  new Sandbox();

            ${$post->plural()} = $post->newEventsMixed(${$this->getModel()->singular()}->getKey(), $request->get('feed-id'));

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render('event', compact($this->getModel()->singular(), $post->plural(), $going->singular(), $comment->singular(),  $sandbox->singular()));


    }

    public function addEvent(Request $request){


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

        }catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($post->singular(), $property->singular(), $place->singular(), $going->singular(), $comment->singular(), $sandbox->singular()));

    }

    public function postAddEvent(Request $request){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $properties = (new Subscription())->getActiveSubscribedPropertyIdListOnlyByUser( ${$this->getModel()->singular()}->getKey() );
            $post = Post::addByEvent(${$this->getModel()->singular()}->getKey(), $request->all(), $properties);
            $going = new Going();
            $comment = new Comment();
            $sandbox = new Sandbox();

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null, ['message' => Translator::transSmart('app.Thank you for your event suggestion. We will review and approve it as soon as possible.', 'Thank you for your event suggestion. We will review and approve it as soon as possible.')]);


        //return SmartView::render(true, compact($this->getModel()->singular(), $post->singular(), $going->singular(), $comment->singular(), $sandbox->singular()));

    }

    public function editEvent(Request $request, $id){


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

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        } catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($post->singular(), $property->singular(), $place->singular(), $going->singular(), $comment->singular(), $sandbox->singular()));

    }

    public function postEditEvent(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $sandbox = new Sandbox();
            $post = Post::editByEvent($id, ${$this->getModel()->singular()}->getKey(), $request->all());
            $going = new Going();
            $comment = new Comment();
            $sandbox = new Sandbox();

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    $post->user,
                    'profileSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.profile'),
                    true
                );

                foreach ($post->galleriesSandboxWithQuery as $item) {
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

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($this->getModel()->singular(), $sandbox->singular(), $post->singular(), $going->singular(), $comment->singular(), $sandbox->singular()));

    }

    public function editEventMix(Request $request, $id){


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

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        } catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($post->singular(), $property->singular(), $place->singular(), $going->singular(), $comment->singular(), $sandbox->singular()));

    }

    public function postEditEventMix(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $sandbox = new Sandbox();
            $post = Post::editByEvent($id, ${$this->getModel()->singular()}->getKey(), $request->all());
            $going = new Going();
            $comment = new Comment();
            $sandbox = new Sandbox();

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    $post->user,
                    'profileSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.profile'),
                    true
                );

                foreach ($post->galleriesSandboxWithQuery as $item) {
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

        }catch(ModelValidationException $e){

            $this->throwValidationException(
                $request, $e->validator
            );

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($this->getModel()->singular(), $sandbox->singular(), $post->singular(), $going->singular(), $comment->singular(), $sandbox->singular()));

    }

    public function comment(Request $request, $id){

        try {

            ${$this->getModel()->singular()}  = Auth::user();
            $post = new Post();
            $comment = new Comment();
            $like = new Like();
            $sandbox =  new Sandbox();

            ${$comment->plural()} = $comment->feeds($id, ${$this->getModel()->singular()}->getKey(), $request->get('comment-id'));

            if (Utility::isNativeAppResponse()) {

                foreach (${$comment->plural()} as $item) {
                    Sandbox::s3()->generateImageLinks(
                        $item->user,
                        'profileSandboxWithQuery',
                        Arr::get(User::$sandbox, 'image.profile'),
                        true
                    );
                }
            }

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(true, compact($this->getModel()->singular(), $post->plural(), $comment->singular(), $comment->plural(), $like->singular(), $sandbox->singular()));


    }

    public function postComment(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $comment = Comment::add($id,  ${$this->getModel()->singular()}->getKey(),  $request->all());
            $like = new Like();

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    $comment->user,
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


        return SmartView::render(true, compact($this->getModel()->singular(), $comment->singular(), $like->singular()));

    }

    public function editComment(Request $request, $id){


        try {

            ${$this->getModel()->singular()} = Auth::user();
            $comment = Comment::retrieve($id);

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        } catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($comment->singular()));

    }

    public function postEditComment(Request $request, $id){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $comment = Comment::edit($id, ${$this->getModel()->singular()}->getKey(), $request->all());
            $like = new Like();

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    $comment->user,
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

        return SmartView::render(true, compact($this->getModel()->singular(), $comment->singular(), $like->singular()));

    }

    public function postDeleteComment(Request $request, $id){

        try {


            ${$this->getModel()->singular()} = Auth::user();
            $comment = Comment::del($id);


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

    public function postDelete(Request $request, $id){

        try {

            Post::del($id);

        } catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }catch(IntegrityException $e) {

            $this->throwIntegrityException(
                $request, $e
            );

        } catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(null);

    }

    public function caseFeed(Request $request, $id){


        try {

            ${$this->getModel()->singular()} = Auth::user();
            $post = Post::retrieve($id);
            $sandbox = new Sandbox();

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    $post->user,
                    'profileSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.profile'),
                    true
                );

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

        } catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($post->singular(), $sandbox->singular()));

    }

    public function caseComment(Request $request, $id){


        try {

            ${$this->getModel()->singular()} = Auth::user();
            $comment = Comment::retrieve($id);
            $sandbox = new Sandbox();

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    $comment->user,
                    'profileSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.profile'),
                    true
                );

            }


        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        } catch(Exception $e){


            return Utility::httpExceptionHandler(500, $e);

        }

        return SmartView::render(true, compact($comment->singular(), $sandbox->singular()));

    }

}
