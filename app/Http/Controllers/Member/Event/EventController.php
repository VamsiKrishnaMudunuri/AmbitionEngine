<?php

namespace App\Http\Controllers\Member\Event;

use CLDR;
use Sess;
use Auth;
use Utility;
use SmartView;
use Exception;
use Translator;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Temp;
use App\Models\Member;
use App\Models\Sandbox;
use App\Models\MongoDB\Feed;
use App\Models\MongoDB\Post;
use App\Models\MongoDB\Comment;
use App\Models\MongoDB\Going;
use App\Models\MongoDB\Notification;

class EventController extends Controller
{

    public function __construct()
    {
        parent::__construct(new Member());
    }

    public function index(Request $request){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $feed = new Feed();
            $post = new Post();
            $filteredPost = new Post();
            $comment = new Comment();
            $going = new Going();

            $sandbox =  new Sandbox();
	
	        $menu = (new Temp())->getPropertyMenuWithCountryAndStateGroupingList();
	        
	        if($request->get($feed->queryParams['filter']) && $feedID = $request->get($feed->queryParams['id'])){
                $filteredPost = $filteredPost->event(${$this->getModel()->singular()}->getKey(), $feedID);
            }

            ${$post->plural()} = $post->eventsMixedWithConventionPagination(${$this->getModel()->singular()}->getKey(), $request->get('page-no'), [], $request->get('property'));

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


        return SmartView::render(null, compact($this->getModel()->singular(), 'menu', 'filteredPost', $post->singular(), $post->plural(), $comment->singular(), $going->singular(), $sandbox->singular()));

    }

    public function event(Request $request, $id, $name = null){

        try{


            ${$this->getModel()->singular()} =  Auth::check() ?  Auth::user() : new Member();
            $feed = new Feed();
            $post = new Post();
            $filteredPost = new Post();
            $comment = new Comment();
            $going = new Going();

            $sandbox =  new Sandbox();

            ${$post->singular()} = $post->eventOrFail(${$this->getModel()->singular()}->getKey(), $id);

            if (Utility::isNativeAppResponse()) {

                Sandbox::s3()->generateImageLinks(
                    $post->user,
                    'profileSandboxWithQuery',
                    Arr::get(User::$sandbox, 'image.profile'),
                    true
                );

                foreach (${$post->singular()}->galleriesSandboxWithQuery as $item) {
                    Sandbox::s3()->generateImageLinks(
                        ${$post->singular()},
                        'galleriesSandboxWithQuery',
                        Arr::get(Post::$sandbox, 'image.gallery'),
                        true
                    );
                }

            }

        }catch(ModelNotFoundException $e){

            return Utility::httpExceptionHandler(404, $e);

        }

        return SmartView::render(null, compact($this->getModel()->singular(), $post->singular(), $post->plural(), $comment->singular(), $going->singular(), $sandbox->singular()));

    }


}
