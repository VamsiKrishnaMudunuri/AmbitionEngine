<?php

namespace App\Http\Controllers\Api\Member\Mention;


use Exception;
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

class MentionController extends Controller
{

    public function __construct()
    {

        parent::__construct();

    }

    public function user(Request $request){

        $member = new Member();
        $list = $member->listForMention($request->get('query'));

        return SmartView::render(null, $list->all());


    }


}