<?php

namespace App\Http\Controllers\Member\Feed;

use Exception;
use Translator;
use CLDR;
use Sess;
use Auth;
use Utility;
use SmartView;
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
use App\Models\MongoDB\Like;
use App\Models\MongoDB\Going;
use App\Models\MongoDB\Notification;
use App\Models\MongoDB\BusinessOpportunity;

class FeedController extends Controller
{

    public function __construct()
    {
        parent::__construct(new Member());
    }

    public function index(Request $request){

        try {

            ${$this->getModel()->singular()} = Auth::user();
            $temp = new Temp();
            $feed = new Feed();
            $post = new Post();
            $filteredPost = new Post();
            $like = new Like();
            $going = new Going();
            $comment = new Comment();

            $sandbox =  new Sandbox();
            $business_opportunity = new BusinessOpportunity();

            $feed_master_filter_menu = $temp->getFeedMasterFilterMenu();

            if($request->get($feed->queryParams['filter']) && $feedID = $request->get($feed->queryParams['id'])){
                $filteredPost = $filteredPost->feed(${$this->getModel()->singular()}->getKey(), Utility::constant('post_type.0.slug'), $feedID);
            }

            if (Utility::isNativeAppResponse()) {
                ${$post->plural()} = $post->feeds(${$this->getModel()->singular()}->getKey(), Utility::constant('post_type.0.slug'), array(), $request->get('feed-id'));
            }else{
                ${$post->plural()} = $post->smartFeeds(${$this->getModel()->singular()}->getKey(), $request->get('feed-id'));
            }

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(null, compact($this->getModel()->singular(), 'filteredPost', $post->singular(), $post->plural(), $like->singular(), $going->singular(), $comment->singular(), $sandbox->singular(), $business_opportunity->singular(), 'feed_master_filter_menu'));


    }

}
