<?php

namespace App\Http\Controllers\Member\Search;

use Exception;
use Translator;
use CLDR;
use Sess;
use Auth;
use Utility;
use SmartView;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Member;
use App\Models\Repo;
use App\Models\Sandbox;
use App\Models\MongoDB\Following;

class SearchController extends Controller
{

    public function __construct()
    {
        parent::__construct(new Member());
    }

    public function member(Request $request){

        try {

            ${$this->getModel()->singular()}  = Auth::user();
            $repo = new Repo();
            $sandbox = new Sandbox();
            $following = new Following();
            ${$repo->plural()} = $repo->searchForMemberByFeed($request->get('requery'));
            $last_id = Arr::get(Arr::last(${$repo->plural()}->all()), $repo->getKeyName());

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(null, compact($this->getModel()->singular(), $repo->singular(), $repo->plural(), $sandbox->singular(), $following->singular(), 'last_id'));

    }

    public function memberFeed(Request $request){

        try {

            ${$this->getModel()->singular()}  = Auth::user();
            $repo = new Repo();
            $sandbox = new Sandbox();
            $following = new Following();
            ${$repo->plural()} = $repo->searchForMemberByFeed($request->get('requery'), $request->get('member-id'));
            $last_id = (${$repo->plural()}->count() > $repo->getPaging()) ? Arr::get(${$repo->plural()}->get($repo->getPaging() - 1), $repo->getKeyName()) : Arr::get(Arr::last(${$repo->plural()}->all()), $repo->getKeyName());


        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(true, compact($this->getModel()->singular(), $repo->singular(), $repo->plural(), $sandbox->singular(), $following->singular(), 'last_id'));

    }

    public function company(Request $request){

        try {

            ${$this->getModel()->singular()}  = Auth::user();
            $repo = new Repo();
            $sandbox = new Sandbox();
            $following = new Following();
            ${$repo->plural()} = $repo->searchForCompanyByFeed($request->get('requery'));
            $last_id = Arr::get(Arr::last(${$repo->plural()}->all()), $repo->getKeyName());

        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(null, compact($this->getModel()->singular(), $repo->singular(), $repo->plural(), $sandbox->singular(), $following->singular(), 'last_id'));

    }

    public function companyFeed(Request $request){

        try {

            ${$this->getModel()->singular()}  = Auth::user();
            $repo = new Repo();
            $sandbox = new Sandbox();
            $following = new Following();
            ${$repo->plural()} = $repo->searchForCompanyByFeed($request->get('requery'), $request->get('company-id'));
            $last_id = (${$repo->plural()}->count() > $repo->getPaging()) ? Arr::get(${$repo->plural()}->get($repo->getPaging() - 1), $repo->getKeyName()) : Arr::get(Arr::last(${$repo->plural()}->all()), $repo->getKeyName());


        }catch(InvalidArgumentException $e){

            return Utility::httpExceptionHandler(500, $e);

        }catch(Exception $e){

            return Utility::httpExceptionHandler(500, $e);

        }


        return SmartView::render(true, compact($this->getModel()->singular(), $repo->singular(), $repo->plural(), $sandbox->singular(), $following->singular(), 'last_id'));

    }


}
