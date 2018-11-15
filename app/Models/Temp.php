<?php

namespace App\Models;

use Translator;
use Exception;
use Utility;
use Hash;
use Config;
use CLDR;
use URL;
use Carbon\Carbon;
use Cache;
use GeoIP;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Str;

use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;
use App\Libraries\Model\IntegrityException;

use App\Models\MongoDB\Post;

class Temp extends Model
{
    protected $autoPublisher = true;
    protected $interval = 60;

    protected $cacheKey = array(
        'user_geoip' => 'user_geoip',
        'user_activity_preferences' => 'user_activity_preferences',
        'module_admin' => 'module_admin',
        'module_member' => 'module_member',
        'module_agent' => 'module_agent',
        'module_admin_acl' => 'module_admin_acl',
        'module_member_acl' => 'module_member_acl',
        'module_agent_acl' => 'module_agent_acl',
        'module_acl_right' => 'module_acl_right',
        'module_acl_user_right' => 'module_acl_user_right',
        'feed_master_filter_menu' => 'feed_master_filter_menu',
        'territories_menu' => 'territories_menu',
        'property_location_menu' => 'property_location_menu',
        'property_menu' => 'property_menu',
        'property_menu_with_country_and_state_grouping_list' => 'property_menu_with_country_and_state_grouping_list',
        'property_menu_if_has_package' => 'property_menu_if_has_package',
        'property_menu_across_venue' => 'property_menu_across_venue',
        'property_menu_all' => 'property_menu_all',
        'property_menu_sort_by_occupancy' => 'property_menu_sort_by_occupancy',
        'property_menu_country_sort_by_occupancy' => 'property_menu_country_sort_by_occupancy',
        'property_menu_site_visit_all' => 'property_menu_site_visit_all',
        'property_menu_country_site_visit_all' => 'property_menu_country_site_visit_all',
        'property_menu_with_only_country_and_state' => 'property_menu_with_only_country_and_state',
        'company_default' => 'company_default',
        'job_recommendation_activity' => 'job_recommendation_activity',
        'business_opportunity_activity' => 'business_opportunity_activity',
        'broadcast_activity' => 'broadcast_activity'
    );

    public function __construct(array $attributes = array())
    {

        static::$relationsData = array();


        parent::__construct($attributes);

    }


    public function getUserGeoip($user_id){

        $key = sprintf('%s_%s', $this->cacheKey['user_geoip'], $user_id);

        $list = Cache::get($key);

        if(is_null($list)){

            $clientIP = Utility::getClientIP();
            $geo = GeoIP::getLocation($clientIP);
            $geo = ($geo) ? $geo->toArray() : array();
            Cache::put($key, $geo, 30);

        }

        return $list;

    }

    public function getUserActivityPreferences($user_id){

        $key = sprintf('%s_%s', $this->cacheKey['user_activity_preferences'], $user_id);

        $list = Cache::get($key);

        if(is_null($list)){

            $list = (new User())->activityPreferences($user_id);
            $percentage = Arr::get($list, 'count') / 100 * 100;

            if($percentage >= 50) {

                Cache::put($key, $list, 30);

            }

        }

        return $list;

    }

    public function getAdminModules($id){

        $key = sprintf('%s', $this->cacheKey['module_admin']);

        $lists = Cache::get($key);

        if(is_null($lists)){
            $lists = new  SupportCollection();
        }

        $modules = $lists->get($id, new SupportCollection());

        if($modules->count() <= 0){

            $modules = (new Module())->getAllForAdmin($id);
            $lists->put($id, $modules);
            Cache::put($key, $lists, $this->interval);


        }

        return $modules;

    }

    public function getMemberModules($id){

        $key = sprintf('%s',  $this->cacheKey['module_member']);

        $lists = Cache::get($key);

        if(is_null($lists)){
            $lists = new  SupportCollection();
        }

        $modules = $lists->get($id, new SupportCollection());

        if($modules->count() <= 0){

            $modules = (new Module())->getAllForMember($id);
            $lists->put($id, $modules);
            Cache::put($key, $lists, $this->interval);


        }

        return $modules;

    }

    public function getAgentModules($id){

        $key = sprintf('%s',  $this->cacheKey['module_agent']);

        $lists = Cache::get($key);

        if(is_null($lists)){
            $lists = new  SupportCollection();
        }

        $modules = $lists->get($id, new SupportCollection());

        if($modules->count() <= 0){

            $modules = (new Module())->getAllForAgent($id);
            $lists->put($id, $modules);
            Cache::put($key, $lists, $this->interval);


        }

        return $modules;

    }

    public function getActiveModuleWithACLForAdmin($company_id, $module_name){

        $key = sprintf('%s',  $this->cacheKey['module_admin_acl']);

        $lists = Cache::get($key);

        if(is_null($lists)){
            $lists = new  SupportCollection();
        }

        $innerKey = sprintf('%s.%s', $company_id, $module_name);

        $module = $lists->get($innerKey, new Module());

        if(!$module->exists){
            $module = (new Module())->getOneActiveWithACLForAdmin($company_id, $module_name);
            $lists->put($innerKey, $module);
            Cache::put($key, $lists, $this->interval);
        }

        return $module;

    }

    public function getActiveModuleWithACLForMember($company_id, $module_name){


        $key = sprintf('%s',  $this->cacheKey['module_member_acl']);

        $lists = Cache::get($key);

        if(is_null($lists)){
            $lists = new  SupportCollection();
        }

        $innerKey = sprintf('%s.%s', $company_id, $module_name);

        $module = $lists->get($innerKey, new Module());

        if(!$module->exists){
            $module = (new Module())->getOneActiveWithACLForMember($company_id, $module_name);
            $lists->put($innerKey, $module);
            Cache::put($key, $lists, $this->interval);
        }

        return $module;

    }

    public function getActiveModuleWithACLForAgent($company_id, $module_name){


        $key = sprintf('%s',  $this->cacheKey['module_agent_acl']);

        $lists = Cache::get($key);

        if(is_null($lists)){
            $lists = new  SupportCollection();
        }

        $innerKey = sprintf('%s.%s', $company_id, $module_name);

        $module = $lists->get($innerKey, new Module());

        if(!$module->exists){
            $module = (new Module())->getOneActiveWithACLForAgent($company_id, $module_name);
            $lists->put($innerKey, $module);
            Cache::put($key, $lists, $this->interval);
        }

        return $module;

    }

    public function getModuleAclRights($pivot){


        $key = sprintf('%s',  $this->cacheKey['module_acl_right']);

        $lists = Cache::get($key);

        if(is_null($lists)){
            $lists = new  SupportCollection();
        }

        $innerKey = sprintf('%s.%s', $pivot->getTable(), $pivot->getKey());

        $acl= $lists->get($innerKey, new Acl());

        if(!$acl->exists){
            $acl = (new Acl())->getMyRights($pivot, false);
            $lists->put($innerKey, $acl);
            Cache::put($key, $lists, $this->interval);
        }

        return $acl;

    }

    public function getModuleAclUserRights($acl_user_model, $model, $user_id){


        $key = sprintf('%s',  $this->cacheKey['module_acl_user_right']);

        $lists = Cache::get($key);

        if(is_null($lists)){
            $lists = new  SupportCollection();
        }

        $innerKey = sprintf('%s.%s', $acl_user_model->getTable(), $user_id);

        $aclUser = $lists->get($innerKey, new AclUser());

        if(!$aclUser->exists){
            $aclUser = (new AclUser())->getRights($model, $user_id);
            $lists->put($innerKey, $aclUser);
            Cache::put($key, $lists, $this->interval);
        }

        return $aclUser;

    }

    public function getCompanyDefault(){

        $key = sprintf('%s', $this->cacheKey['company_default']);

        $model = Cache::get($key);

        if(is_null($model)){

            $model = (new Company())->getDefault();
            Cache::put($key, $model, $this->interval);

        }

        return $model;

    }

    public function getFeedMasterFilterMenu(){

        $key = sprintf('%s', $this->cacheKey['feed_master_filter_menu']);

        $list = Cache::get($key);

        if(is_null($list)){


            $list = (new Post())->getFeedMasterFilterMenu();
            Cache::put($key, $list, $this->interval);

        }

        return $list;

    }

    public function getTerritoriesMenu(){

        $key = sprintf('%s', $this->cacheKey['territories_menu']);

        $list = Cache::get($key);

        if(is_null($list)){

            $list = (new Property())->getTerritoriesMenu();
            Cache::put($key, $list, $this->interval);

        }

        return $list;

    }

    public function getPropertyLocationMenu(){

        $key = sprintf('%s', $this->cacheKey['property_location_menu']);

        $list = Cache::get($key);
        if(is_null($list)){

            $list = (new Property())->getLocationsMenu();
            Cache::put($key, $list, $this->interval);

        }

        return $list;

    }

    public function getPropertyMenu(){

        $key = sprintf('%s', $this->cacheKey['property_menu']);

        $list = Cache::get($key);

        if(is_null($list)){

            $properties = (new Property())->getActiveMenu();

            $list = array();

            foreach($properties as $property){

                $name = sprintf('%s - %s', $property->country_slug_name, $property->state_slug_name);

                if(!isset($list[$name])){
                    $list[$name] = array();
                }


                $list[$name][$property->getKey()] = $property->location;


            }

            Cache::put($key, $list, $this->interval);

        }

        return $list;

    }
	
	public function getPropertyMenuWithCountryAndStateGroupingList(){
		
		$key = sprintf('%s', $this->cacheKey['property_menu_with_country_and_state_grouping_list']);
		
		$list = Cache::get($key);
		
		if(is_null($list)){
			
			$properties = (new Property())->getActiveMenu();
			
			$list = array();
			
			foreach($properties as $property){
				
				$name = sprintf('%s - %s', $property->country_slug_name, $property->state_slug_name);
				
				$otherKey = 'others';
				$country_slug = $property->country_slug ? : $otherKey;
				$country_slug_name =  $property->country_slug_name ? : 'Others';
				$state_slug = $property->state_slug;
				$state_slug_name = $property->state_slug_name;

				if(!isset($list[$country_slug_name])){
					if(strcasecmp($country_slug_name, $otherKey) == 0){
						$list[$country_slug_name] = array();
					}else{
						$list[$country_slug_name] = array(
							$country_slug => sprintf('All %s', $country_slug_name)
						);
					}
					
				}
				
				if(strcasecmp($country_slug_name, $otherKey) == 0){
					$list[$country_slug_name][$property->getKey()] = $property->location;
				}else{
					$indent_state_slug_name = html_entity_decode(sprintf('&nbsp;&nbsp;&nbsp;%s', $state_slug_name));
					
					if(!isset($list[$indent_state_slug_name])){
						$list[$indent_state_slug_name] = array();
					}
					
					$list[$indent_state_slug_name][$property->getKey()] =  html_entity_decode(sprintf('&nbsp;&nbsp;&nbsp;%s', $property->location));
					
				}
				
				
				
			}
			
			Cache::put($key, $list, $this->interval);
			
		}
		
		return $list;
		
	}
	
    public function getPropertyMenuAcrossVenue(){

        $key = sprintf('%s', $this->cacheKey['property_menu_across_venue']);

        $list = Cache::get($key);

        if(is_null($list)){

            $properties = (new Property())->getActiveMenu();

            $list = array(
                'All' => array((new Property())->defaultKeyValueForAll => (new Property())->defaultKeyNameForAll)
            );

            foreach($properties as $property){

                $name = sprintf('%s - %s', $property->country_slug_name, $property->state_slug_name);

                if(!isset($list[$name])){
                    $list[$name] = array();
                }


                $list[$name][$property->getKey()] = $property->location;


            }

            Cache::put($key, $list, $this->interval);

        }

        return $list;

    }

    public function getPropertyMenuWithOnlyCountryAndState(){

        $key = sprintf('%s', $this->cacheKey['property_menu_with_only_country_and_state']);

        $list = Cache::get($key);

        if(is_null($list)){

            $properties = (new Property())->getActiveMenuWithOnlyCountryStateLevel();

            $list = array();

            foreach($properties as $property){

                $name = $property->country_slug_name;

                if(!isset($list[$name])){
                    $list[$name] = array();
                }


                $list[$name][$property->state_slug] = $property->state_slug_name;

            }

            Cache::put($key, $list, $this->interval);

        }

        return $list;

    }

    public function getPropertyMenuAll(){

        $key = sprintf('%s', $this->cacheKey['property_menu_all']);

        $list = Cache::get($key);

        if(is_null($list)){

            $properties = (new Property())->getAllMenu();

            $list = array();

            foreach($properties as $property){

                $name = sprintf('%s - %s', $property->country_slug_name, $property->state_slug_name);

                if(!isset($list[$name])){
                    $list[$name] = array();
                }


                $list[$name][$property->getKey()] = $property->location;


            }

            Cache::put($key, $list, $this->interval);

        }

        return $list;

    }

    public function getPropertyMenuSortByOccupancy(){

        $key = sprintf('%s', $this->cacheKey['property_menu_sort_by_occupancy']);

        $list = Cache::get($key);

        if(is_null($list)){

            $properties = (new Property())->getActiveMenuSortByOccupancy();

            $list = array();

            foreach($properties as $property){

                $name = sprintf('%s - %s', $property->country_slug_name, $property->state_slug_name);

                if(!isset($list[$name])){
                    $list[$name] = array();
                }


                $list[$name][$property->getKey()] = $property->location; //ucwords(strtolower($property->location));


            }

            Cache::put($key, $list, $this->interval);

        }

        return $list;

    }

    public function getPropertyMenuCountrySortByOccupancy($country = null){

        $key = sprintf('%s', $this->cacheKey['property_menu_country_sort_by_occupancy']);
        $country_key = (Utility::hasString($country)) ? $country : 'default';

        $answer = array();

        $list = Cache::get($key);

        $list = !is_null($list) ? $list : array();

        if(!isset($list[$country_key])){
            $list[$country_key] = array();
        }

        if(empty($list[$country_key])){

            $properties = (new Property())->getActiveMenuByCountryandSortByOccupancy($country);

            foreach($properties as $property){

                $name = sprintf('%s - %s', $property->country_slug_name, $property->state_slug_name);

                if(!isset($list[$country_key][$name])){
                    $list[$country_key][$name] = array();
                }


                $list[$country_key][$name][$property->getKey()] = $property->location; //ucwords(strtolower($property->location));


            }

            $answer = $list[$country_key];

            Cache::put($key, $list, $this->interval);

        }else{

            $answer = $list[$country_key];

        }



        return $answer;

    }

    public function getPropertyMenuSiteVisitAll(){

        $key = sprintf('%s', $this->cacheKey['property_menu_site_visit_all']);

        $list = Cache::get($key);

        if(is_null($list)){

            $properties = (new Property())->getSiteVisitMenu();

            $list = array();

            foreach($properties as $property){

                if(!$property->readyForSiteVisitBooking()){
                    continue;
                }

                $name = sprintf('%s - %s', $property->country_slug_name, $property->state_slug_name);

                if(!isset($list[$name])){
                    $list[$name] = array();
                }


                $list[$name][$property->getKey()] = $property->location; //ucwords(strtolower($property->location));


            }

            Cache::put($key, $list, $this->interval);

        }

        return $list;

    }

    public function getPropertyMenuCountrySiteVisitAll( $country = null ){

        $key = sprintf('%s', $this->cacheKey['property_menu_country_site_visit_all']);
        $country_key = (Utility::hasString($country)) ? $country : 'default';

        $answer = array();

        $list = Cache::get($key);

        $list = !is_null($list) ? $list : array();

        if(!isset($list[$country_key])){
            $list[$country_key] = array();
        }

        if(empty($list[$country_key])){

            $properties = (new Property())->getSiteVisitMenuByCountry( $country );

            foreach($properties as $property){

                if(!$property->readyForSiteVisitBooking()){
                    continue;
                }

                $name = sprintf('%s - %s', $property->country_slug_name, $property->state_slug_name);

                if(!isset($list[$country_key][$name])){
                    $list[$country_key][$name] = array();
                }


                $list[$country_key][$name][$property->getKey()] = $property->location; //ucwords(strtolower($property->location));


            }

            $answer = $list[$country_key];

            Cache::put($key, $list, $this->interval);

        }else{

            $answer = $list[$country_key];

        }

        return $answer;

    }

    public function getPropertyMenuIfHasPackage(){

        $key = sprintf('%s', $this->cacheKey['property_menu_if_has_package']);

        $list = Cache::get($key);

        if(is_null($list)){

            $properties = (new Property())->getActiveMenuWithActivePackage();

            $list = array();

            foreach($properties as $property){

                $name = sprintf('%s - %s', $property->country_slug_name, $property->state_slug_name);

                if(!isset($list[$name])){
                    $list[$name] = array();
                }


                $list[$name][$property->getKey()] = $property->location;


            }

            Cache::put($key, $list, $this->interval);

        }

        return $list;

    }

    public function setRunningJobRecommendationActivity(){

        $key = sprintf('%s', $this->cacheKey['job_recommendation_activity']);

        Cache::put($key, true, $this->interval);


    }

    public function isRunningJobRecommendationActivity(){

        $key = sprintf('%s', $this->cacheKey['job_recommendation_activity']);


        return Cache::get($key);

    }

    public function flushRunningJobRecommendationActivity(){

        $key = sprintf('%s', $this->cacheKey['job_recommendation_activity']);

        Cache::forget($key);

    }

    public function setRunningBusinessOpportunityRecommendationActivity(){

        $key = sprintf('%s', $this->cacheKey['business_opportunity_activity']);

        Cache::put($key, true, $this->interval);


    }

    public function isRunningBusinessOpportunityRecommendationActivity(){

        $key = sprintf('%s', $this->cacheKey['business_opportunity_activity']);


        return Cache::get($key);

    }

    public function flushRunningBusinessOpportunityRecommendationActivity(){

        $key = sprintf('%s', $this->cacheKey['business_opportunity_activity']);

        Cache::forget($key);

    }

    public function setRunningBroadcastActivity(){

        $key = sprintf('%s', $this->cacheKey['broadcast_activity']);

        Cache::put($key, true, $this->interval);


    }

    public function isRunningBroadcastActivity(){

        $key = sprintf('%s', $this->cacheKey['broadcast_activity']);


        return Cache::get($key);

    }

    public function flushUserGeoip($user_id){

        $key = sprintf('%s_%s', $this->cacheKey['user_geoip'], $user_id);

        Cache::forget($key);

    }

    public function flushUserActivityPreferences($user_id){

        $key = sprintf('%s_%s', $this->cacheKey['user_activity_preferences'], $user_id);

        Cache::forget($key);

    }

    public function flushRunningBroadcastActivity(){

        $key = sprintf('%s', $this->cacheKey['broadcast_activity']);

        Cache::forget($key);

    }

    public function flushAdminModules(){

        $key = sprintf('%s',  $this->cacheKey['module_admin']);

        Cache::forget($key);

    }

    public function flushMemberModules(){

        $key = sprintf('%s',  $this->cacheKey['module_member']);

        Cache::forget($key);

    }

    public function flushAgentModules(){

        $key = sprintf('%s',  $this->cacheKey['module_agent']);

        Cache::forget($key);

    }

    public function flushActiveModuleWithAclforAdmin(){

        $key = sprintf('%s', $this->cacheKey['module_admin_acl']);

        Cache::forget($key);

    }

    public function flushActiveModuleWithAclforMember(){

        $key = sprintf('%s', $this->cacheKey['module_member_acl']);

        Cache::forget($key);

    }

    public function flushActiveModuleWithAclforAgent(){

        $key = sprintf('%s', $this->cacheKey['module_agent_acl']);

        Cache::forget($key);

    }

    public function flushModulesAclRightsByOnePivot($pivot){

        $key = sprintf('%s',  $this->cacheKey['module_acl_right']);

        $lists = Cache::get($key);

        if(is_null($lists)){
            $lists = new  SupportCollection();
        }

        $innerKey = sprintf('%s.%s', $pivot->getTable(), $pivot->getKey());

        $acl= $lists->get($innerKey, new Acl());

        if($acl->exists){

            $lists->forget($innerKey);
            Cache::put($key, $lists, $this->interval);

        }

    }

    public function flushModulesAclUserRights($acl_user_model, $user_id){

        $key = sprintf('%s',  $this->cacheKey['module_acl_user_right']);

        $lists = Cache::get($key);

        if(is_null($lists)){
            $lists = new  SupportCollection();
        }

        $innerKey = sprintf('%s.%s', $acl_user_model->getTable(), $user_id);

        $acl= $lists->get($innerKey, new Acl());

        if($acl->exists){

            $lists->forget($innerKey);
            Cache::put($key, $lists, $this->interval);

        }

    }

    public function flushModules(){

        $this->flushAdminModules();
        $this->flushMemberModules();
        $this->flushAgentModules();
        $this->flushActiveModuleWithAclforAdmin();
        $this->flushActiveModuleWithAclforMember();
        $this->flushActiveModuleWithAclforAgent();

    }

    public function flushCompanyDefault(){

        $key = sprintf('%s',  $this->cacheKey['company_default']);

        Cache::forget($key);

    }

    public function flushFeedMasterFilterMenu(){

        $key = sprintf('%s',  $this->cacheKey['feed_master_filter_menu']);

        Cache::forget($key);

    }

    public function flushTerritoriesMenu(){

        $key = sprintf('%s',  $this->cacheKey['territories_menu']);

        Cache::forget($key);

    }

    public function flushPropertyLocationMenu(){

        $key = sprintf('%s',  $this->cacheKey['property_location_menu']);

        Cache::forget($key);

    }

    public function flushPropertyMenu(){

        $key = sprintf('%s',  $this->cacheKey['property_menu']);

        Cache::forget($key);

    }
	
	public function flushPropertyMenuWithCountryAndStateGroupingList(){
		
		$key = sprintf('%s',  $this->cacheKey['property_menu_with_country_and_state_grouping_list']);
		
		Cache::forget($key);
		
	}


    public function flushPropertyMenuAcrossVenue(){

        $key = sprintf('%s',  $this->cacheKey['property_menu_across_venue']);

        Cache::forget($key);

    }


    public function flushPropertyMenuWithOnlyCountryAndState(){

        $key = sprintf('%s',  $this->cacheKey['property_menu_with_only_country_and_state']);

        Cache::forget($key);

    }

    public function flushPropertyMenuAll(){

        $key = sprintf('%s',  $this->cacheKey['property_menu_all']);

        Cache::forget($key);

    }

    public function flushPropertyMenuSortByOccupancy(){

        $key = sprintf('%s',  $this->cacheKey['property_menu_sort_by_occupancy']);

        Cache::forget($key);

    }

    public function flushPropertyMenuCountrySortByOccupancy(){

        $key = sprintf('%s',  $this->cacheKey['property_menu_country_sort_by_occupancy']);

        Cache::forget($key);

    }

    public function flushPropertyMenuSiteVisitAll(){

        $key = sprintf('%s',  $this->cacheKey['property_menu_site_visit_all']);

        Cache::forget($key);

    }

    public function flushPropertyMenuCountrySiteVisitAll(){

        $key = sprintf('%s',  $this->cacheKey['property_menu_country_site_visit_all']);

        Cache::forget($key);

    }

    public function flushPropertyMenuIfHasPackage(){

        $key = sprintf('%s',  $this->cacheKey['property_menu_if_has_package']);

        Cache::forget($key);

    }

}