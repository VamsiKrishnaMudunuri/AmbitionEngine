<?php

namespace App\Models;

use Domain;
use URL;
use Exception;
use Utility;

use Illuminate\Database\Eloquent\Collection;


use App\Libraries\FulltextSearch\Search;
use App\Libraries\Model\Model;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;

class Repo extends Model
{

    protected $autoPublisher = true;

    protected $paging  = 20;

    private $delimiter = ';';

    public static $rules = array(
        'model' => 'required|max:50',
        'model_id' => 'required|max:32',
        'keywords' => 'required',
    );

    public static $customMessages = array();

    protected static $relationsData = array();

    public function __construct(array $attributes = array())
    {
        static::$relationsData = array(

            'entity' => array(self::MORPH_TO, 'name' => 'entity', 'type' => 'model', 'id' => 'model_id'),

        );


        parent::__construct($attributes);

    }

    public function beforeSave(){


        return true;
    }

    public function getKeywordsInArrayFormatAttribute(){

        $arr = [];

        if($this->exists && Utility::hasString($this->keywords)){
            $arr = explode($this->delimiter, $this->keywords);
        }

        return $arr;

    }
	
    public function searchForMember($query, $limit = null){

        $col = new Collection();
        $user = new User();

        $search = new Search($query);
        $query = $search->GetSearchQueryString();

        $this->with = ['entity', 'entity.profileSandboxWithQuery'];

        $entities = $this
            ->where($this->entity()->getMorphType(), '=', $user->getTable())
            ->whereRaw('MATCH(keywords) AGAINST (? IN BOOLEAN MODE)', [$query])
            ->take((!is_null($limit)) ? $limit : $this->paging)
            ->get();


        foreach($entities as $entity){

            if(is_null($entity) || is_null($entity->entity)){
                continue;
            }

            $model = $entity->entity;


            $sandbox = new Sandbox();
            $config = $sandbox->configs(\Illuminate\Support\Arr::get(User::$sandbox, 'image.profile'));
            $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');

            $mem = new User();
            $mem->setAttribute($model->getKeyName(), $model->getKey());
            $mem->setAttribute('url', URL::Route(Domain::route('member::profile::index'), array('username' => $model->username)));
            $mem->setAttribute('type', 0);
            $mem->setAttribute('name', $model->full_name);
            //$mem->setAttribute('email', $model->email);
            $mem->setAttribute('username', $model->username);
            $mem->setAttribute('username_alias', $model->username_alias);
            $mem->setAttribute('profileSandboxWithQuery', $model->profileSandboxWithQuery);
            $mem->setAttribute('avatar', $sandbox::s3()->link($model->profileSandboxWithQuery, $model, $config, $dimension, array(), null, true));
            $col->add($mem);



        }


        return $col;

    }

    public function searchForMemberByFeed($query, $id = null){


        try {

            $user = new User();

            $search = new Search($query);
            $query = $search->GetSearchQueryString();

            $this->with = ['entity', 'entity.profileSandboxWithQuery'];

            $builder = $this
                ->where($this->entity()->getMorphType(), '=', $user->getTable());


            if(Utility::hasString($query)) {
                 $builder = $builder->
                    whereRaw('MATCH(keywords) AGAINST (? IN BOOLEAN MODE)', [$query]);
            }


            if (Utility::hasString($id)) {
                $builder = $builder->where($this->getKeyName(), '<', $id);
            }


            $builder = $builder->orderBy($this->getKeyName(), 'DESC');

            $instance = $builder->take($this->paging + 1)->get();

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }

    public function searchForCompany($query, $limit = null){

        $col = new Collection();
        $company = new Company();

        $search = new Search($query);
        $query = $search->GetSearchQueryString();

        $this->with = ['entity', 'entity.metaWithQuery', 'entity.logoSandboxWithQuery'];

        $entities = $this
            ->join($company->getTable(), function($query) use ($company) {
                $query
                    ->on(sprintf('%s.%s', $this->getTable(), $this->entity()->getForeignKey()), '=', sprintf('%s.%s', $company->getTable(), $company->getKeyName()))
                    ->where(sprintf('%s.%s', $this->getTable(), $this->entity()->getMorphType()), '=', $company->getTable())
                    ->where(sprintf('%s.name', $company->getTable()), '!=', '');
            })
            ->where(sprintf('%s.%s', $this->getTable(), $this->entity()->getMorphType()), '=', $company->getTable())
            ->where(sprintf('%s.name', $company->getTable()), '!=', '')
            ->whereRaw(sprintf('MATCH(%s.keywords) AGAINST (? IN BOOLEAN MODE)', $this->getTable()), [$query])
            ->take((!is_null($limit)) ? $limit : $this->paging)
            ->get();


        foreach($entities as $entity){

            if(is_null($entity) || is_null($entity->entity)){
                continue;
            }

            $model = $entity->entity;

            $sandbox = new Sandbox();
            $config = $sandbox->configs(\Illuminate\Support\Arr::get(Company::$sandbox, 'image.logo'));
            $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');

            $mem = new Company();
            $mem->setAttribute($model->getKeyName(), $model->getKey());
            $mem->setAttribute('url', URL::Route(Domain::route('member::company::index'), array('slug' => (!is_null($model->metaWithQuery) && $model->metaWithQuery->exists) ? $model->metaWithQuery->slug : '')));
            $mem->setAttribute('type', 1);
            $mem->setAttribute('name', $model->name);
            $mem->setAttribute('headline', $model->headline);
            $mem->setAttribute('logoSandboxWithQuery', $model->logoSandboxWithQuery);
            $mem->setAttribute('avatar', $sandbox::s3()->link($model->logoSandboxWithQuery, $model, $config, $dimension, array(), null, true));
            $col->add($mem);

        }


        return $col;
    }

    public function searchForCompanyByFeed($query, $id = null){


        try {

            $company = new Company();

            $search = new Search($query);
            $query = $search->GetSearchQueryString();

            $this->with = ['entity', 'entity.metaWithQuery', 'entity.logoSandboxWithQuery'];

            $builder = $this
                ->join($company->getTable(), function($query) use ($company) {
                    $query
                        ->on(sprintf('%s.%s', $this->getTable(), $this->entity()->getForeignKey()), '=', sprintf('%s.%s', $company->getTable(), $company->getKeyName()))
                        ->where(sprintf('%s.%s', $this->getTable(), $this->entity()->getMorphType()), '=', $company->getTable())
                        ->where(sprintf('%s.name', $company->getTable()), '!=', '');
                })
                ->where(sprintf('%s.%s', $this->getTable(), $this->entity()->getMorphType()), '=', $company->getTable())
                ->where(sprintf('%s.name', $company->getTable()), '!=', '');


            if(Utility::hasString($query)) {
                $builder = $builder->
                whereRaw(sprintf('MATCH(%s.keywords) AGAINST (? IN BOOLEAN MODE)', $this->getTable()), [$query]);
            }


            if (Utility::hasString($id)) {
                $builder = $builder->where(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), '<', $id);
            }

            $builder = $builder->orderBy(sprintf('%s.%s', $this->getTable(), $this->getKeyName()), 'DESC');

            $instance = $builder->take($this->paging + 1)->get();

        }catch(InvalidArgumentException $e){

            throw $e;

        }catch(Exception $e){

            throw $e;

        }

        return $instance;

    }
	
	public function searchForStaffs($query, $limit = null){
		
		$col = new Collection();
		$company = (new Temp())->getCompanyDefault();
		$company_user = new CompanyUser();
		$user = new User();
		
		
		$search = new Search($query);
		$query = $search->GetSearchQueryString();
		
		$entities = $user
			->selectRaw(sprintf('%s.*, %s.role AS company_role', $user->getTable(), $company_user->getTable()))
			->with(['profileSandboxWithQuery', 'work', 'work.company'], false)
			->join($company_user->getTable(), sprintf('%s.%s', $user->getTable(), $user->getKeyName()), '=', $user->companies()->getForeignKey())
			->where($company->users()->getForeignKey(), '=', $company->getKey())
			->whereRaw(sprintf('MATCH(%s.full_name, %s.first_name, %s.last_name, %s.username, %s.email) AGAINST (? IN BOOLEAN MODE)', $user->getTable(), $user->getTable(), $user->getTable(), $user->getTable(), $user->getTable()), [$query])
			->take((!is_null($limit)) ? $limit : $this->paging)
			->get();
		
		
		foreach($entities as $entity){
			
			$model = $entity;
			
			
			$sandbox = new Sandbox();
			$config = $sandbox->configs(\Illuminate\Support\Arr::get(User::$sandbox, 'image.profile'));
			$dimension =   \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');
			
			$mem = new User();
			$mem->setAttribute($model->getKeyName(), $model->getKey());
			$mem->setAttribute('url', URL::Route(Domain::route('member::profile::index'), array('username' => $model->username)));
			$mem->setAttribute('type', 0);
			
			$roles_name = [Utility::constant('role.user.name'), Utility::constant(sprintf('role.%s.name', $model->company_role))];
			
			$mem->setAttribute('role_name', implode(',', $roles_name));
			
			$mem->setAttribute('name', $model->full_name);
			$mem->setAttribute('email', $model->email);
			$mem->setAttribute('username', $model->username);
			$mem->setAttribute('username_alias', $model->username_alias);
			$mem->setAttribute('profileSandboxWithQuery', $model->profileSandboxWithQuery);
			$mem->setAttribute('avatar', $sandbox::s3()->link($model->profileSandboxWithQuery, $model, $config, $dimension, array(), null, true));
			
			$mem->setAttribute('company', ($model->smart_company_name) ? $model->smart_company_name : '');
			
			$col->add($mem);
			
			
			
		}
		
		
		return $col;
		
	}
	
	public function searchForMembers($query, $limit = null){
		
		$col = new Collection();
		$company = (new Temp())->getCompanyDefault();
		$company_user = new CompanyUser();
		$user = new User();
		
		
		$search = new Search($query);
		$query = $search->GetSearchQueryString();
		
		$entities = $user
			->selectRaw(sprintf('%s.*, %s.role AS company_role', $user->getTable(), $company_user->getTable()))
			->with(['profileSandboxWithQuery', 'work', 'work.company'], false)
			
			->leftJoin($company_user->getTable(), function($query) use ($user, $company, $company_user) {
				$query
					->on(sprintf('%s.%s', $user->getTable(), $user->getKeyName()), '=', $user->companies()->getForeignKey())
					->where($company->users()->getForeignKey(), '=', $company->getKey());
			})
			
			->whereRaw(sprintf('MATCH(%s.full_name, %s.first_name, %s.last_name, %s.username, %s.email) AGAINST (? IN BOOLEAN MODE)', $user->getTable(), $user->getTable(), $user->getTable(), $user->getTable(), $user->getTable()), [$query])
			->take((!is_null($limit)) ? $limit : $this->paging)
			->get();
		
		
		foreach($entities as $entity){
			
			$model = $entity;
			
			
			$sandbox = new Sandbox();
			$config = $sandbox->configs(\Illuminate\Support\Arr::get(User::$sandbox, 'image.profile'));
			$dimension =   \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');
			
			$mem = new User();
			$mem->setAttribute($model->getKeyName(), $model->getKey());
			$mem->setAttribute('url', URL::Route(Domain::route('member::profile::index'), array('username' => $model->username)));
			$mem->setAttribute('type', 0);
			
			$roles_name = [Utility::constant('role.user.name')];
			
			if($model->company_role){
				$roles_name[] =  Utility::constant(sprintf('role.%s.name', $model->company_role));
			}
			
			$mem->setAttribute('role_name', implode(',', $roles_name));
			$mem->setAttribute('name', $model->full_name);
			$mem->setAttribute('email', $model->email);
			$mem->setAttribute('username', $model->username);
			$mem->setAttribute('username_alias', $model->username_alias);
			$mem->setAttribute('profileSandboxWithQuery', $model->profileSandboxWithQuery);
			$mem->setAttribute('avatar', $sandbox::s3()->link($model->profileSandboxWithQuery, $model, $config, $dimension, array(), null, true));
			$mem->setAttribute('company', ($model->smart_company_name) ? $model->smart_company_name : '');
			
			$col->add($mem);
		
			
			
		}
		
		
		return $col;
		
	}
	
    public function upsertUser($model, $bio = null, $bio_business_opportunity = null){

        try {

            if(!is_null($model) && $model->exists) {

                $keywords = [$model->username, $model->full_name, $model->first_name, $model->last_name];

                if(!is_null($bio) && $bio->exists){
                    $keywords = array_merge($keywords, $bio->skills);
                    $keywords = array_merge($keywords, $bio->interests);
                    $keywords = array_merge($keywords, $bio->services);
                }

                if(!is_null($bio_business_opportunity) && $bio_business_opportunity->exists){
                    $keywords = array_merge($keywords, $bio_business_opportunity->types);
                    $keywords = array_merge($keywords, $bio_business_opportunity->opportunities);
                }


                $this->upsert($model, $keywords);

            }

        }catch(ModelValidationException $e){

            throw $e;

        } catch (Exception $e){

            throw $e;

        }

    }

    public function upsertCompany($model, $bio = null, $bio_business_opportunity = null){

        try {

            if(!is_null($model) && $model->exists) {

                $keywords = [$model->name, $model->address, $model->industry, $model->headline];

                if(!is_null($bio) && $bio->exists){
                    $keywords = array_merge($keywords, $bio->skills);
                    $keywords = array_merge($keywords, $bio->services);
                }

                if(!is_null($bio_business_opportunity) && $bio_business_opportunity->exists){
                    $keywords = array_merge($keywords, $bio_business_opportunity->types);
                    $keywords = array_merge($keywords, $bio_business_opportunity->opportunities);
                }

                $this->upsert($model, $keywords);

            }

        }catch(ModelValidationException $e){

            throw $e;

        } catch (Exception $e){

            throw $e;

        }

    }

    public function upsert($model, $keywords = array()){

        try {

            if(!is_null($model) && $model->exists && Utility::hasArray($keywords)) {

                $instance = $this
                    ->where($this->entity()->getMorphType(), '=', $model->getTable())
                    ->where($this->entity()->getForeignKey(), '=', $model->getKey())
                    ->first();


                if (is_null($instance)) {
                    $instance = new static();
                }

                $instance->fillable($instance->getRules([], false, true));
                $instance->setAttribute($instance->entity()->getMorphType(),  $model->getTable());
                $instance->setAttribute($instance->entity()->getForeignKey(),  $model->getKey());

                $instance->setAttribute('keywords', join($this->delimiter, $keywords));
                $instance->save();

            }

        }catch(ModelValidationException $e){

            throw $e;

        } catch (Exception $e){

            throw $e;

        }


    }

    public function del($model){

        $flag = null;

        if(!is_null($model)){

            $flag = $this
                ->where($this->entity()->getMorphType(), '=', $model->getTable())
                ->where( $this->entity()->getForeignKey(), '=', $model->getKey())
                ->delete();

        }

        return $flag;

    }




}