<?php

namespace App\Http\Controllers\Api\Search;

use URL;
use Log;
use Storage;
use SmartView;
use Exception;
use App\Models\User;
use App\Models\Company;
use App\Models\Sandbox;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\Repo;


class SearchController extends Controller
{

    public function __construct()
    {

        parent::__construct(new Repo());

    }

    public function member(Request $request){

        $list = $this->getModel()->searchForMember($request->get('query'));

        foreach ($list as $item) {
            Sandbox::s3()->generateImageLinks(
                $item,
                'profileSandboxWithQuery',
                Arr::get(User::$sandbox, 'image.profile'),
                true
            );
        }

        return SmartView::render(null, $list->toArray());

    }

    public function company(Request $request){

        $list = $this->getModel()->searchForCompany($request->get('query'));

        foreach ($list as $item) {
            Sandbox::s3()->generateImageLinks(
                $item,
                'logoSandboxWithQuery',
                Arr::get(Company::$sandbox, 'image.logo'),
                true
            );
        }


        return SmartView::render(null, $list->toArray());

    }
	
	
	public function userStaff(Request $request){
		
		$list = $this->getModel()->searchForStaffs($request->get('query'));
		
		foreach ($list as $item) {
			Sandbox::s3()->generateImageLinks(
				$item,
				'profileSandboxWithQuery',
				Arr::get(User::$sandbox, 'image.profile'),
				true
			);
		}
		
		return SmartView::render(null, $list->toArray());
		
	}
	
	public function userMember(Request $request){
		
		$list = $this->getModel()->searchForMembers($request->get('query'));
		
		foreach ($list as $item) {
			Sandbox::s3()->generateImageLinks(
				$item,
				'profileSandboxWithQuery',
				Arr::get(User::$sandbox, 'image.profile'),
				true
			);
		}
		
		return SmartView::render(null, $list->toArray());
		
	}
	
	
	
	
	
}