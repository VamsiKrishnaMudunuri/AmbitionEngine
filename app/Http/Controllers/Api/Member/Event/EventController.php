<?php

namespace App\Http\Controllers\Api\Member\Event;


use Exception;
use Auth;
use URL;
use Translator;
use Sess;
use Utility;
use SmartView;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Member;
use App\Models\Sandbox;
use App\Models\MongoDB\Post;

class EventController extends Controller
{

    public function __construct()
    {

        parent::__construct();

    }

    public function myUpcoming(Request $request){

        try {

            $member = Auth::user();
            $post = new Post();
            ${$post->plural()} = $post->upcomingEventsForMember($member->getKey());

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        if (Utility::isNativeAppResponse()) {

            foreach (${$post->plural()} as $post) {

                Sandbox::s3()->generateImageLinks($post, 'galleriesSandboxWithQuery', Arr::get(Post::$sandbox, 'image.gallery'), true);
                Sandbox::s3()->generateImageLinks($post->user, 'profileSandboxWithQuery', Arr::get(User::$sandbox, 'image.profile'), true);


            }

        }

        return SmartView::render(null, compact($post->plural()));

    }

    public function hottest(Request $request){

        try {

            $member = Auth::user();
            $post = new Post();
            ${$post->plural()} = $post->hottestEvents($member->getKey());

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }

        if (Utility::isNativeAppResponse()) {

            foreach (${$post->plural()} as $post) {

                Sandbox::s3()->generateImageLinks($post, 'galleriesSandboxWithQuery', Arr::get(Post::$sandbox, 'image.gallery'), true);
                Sandbox::s3()->generateImageLinks($post->user, 'profileSandboxWithQuery', Arr::get(User::$sandbox, 'image.profile'), true);


            }

        }

        return SmartView::render(null, compact($post->plural()));

    }

}