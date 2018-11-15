<?php

namespace App\Models;

use Exception;
use Translator;
use Gate;
use Utility;
use Closure;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Libraries\Model\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;

class AclUser extends Model
{

    protected $autoPublisher = true;

    public $defaultRights = array();

    public static $rules = array(
        'model' => 'required|max:50',
        'model_id' => 'required|integer',
        'user_id' => 'required|integer',
        'rights' => 'required'
    );

    public $delimiter = ',';

    public static $customMessages = array();

    protected static $relationsData = array();

    public function __construct(array $attributes = array())
    {

        static::$relationsData = array(
            'property' => array(self::BELONGS_TO, Property::class, 'foreignKey' => 'model_id'),
            'user' => array(self::BELONGS_TO, User::class)
        );

        static::$customMessages = array(
            'rights.required' => Translator::transSmart('app.Please select at least one access right.', 'Please select at least one access right')
        );

        $this->defaultRights = array_keys(Utility::rightsDefault(null, null, true));

        parent::__construct($attributes);

    }

    public function getModelKey(){

        return 'model';

    }
    
    public function scopeModel($query, Model $model){
        return $query->where(sprintf('%s.%s', $this->getTable(), $this->getModelKey()), '=', $model->getTable());
    }

    public function getRightsAttribute($value){
        $arr = [];

        $arr = Utility::jsonDecode($value);

        return $arr;
    }

    public function setRightsAttribute($value){

        $val = Utility::jsonEncode($value);

        $this->attributes['rights'] = $val;

    }


    public function shadow(&$rights){

        $rights = is_array($rights) ? $rights : Utility::strToArray($rights, $this->delimiter);

        if(!Utility::hasArray($rights)){
            $rights = $this->defaultRights;
        }

        $newStruc = array();

        foreach ($rights as $right){
            $rgt = Str::lower($right);
            $newStruc[$rgt] = array(
                'slug' => $rgt,
                'name' => Translator::transSmart(sprintf('right.%s.name', $rgt), Str::ucfirst($right)),
                'checked' => 0
            );
        }

        $rights = $newStruc;
    }

    public function getRights($model, $user_id){

        $instance = $this
            ->where('model', '=', $model->getTable())
            ->where('model_id', '=', $model->getKey())
            ->where('user_id', '=', $user_id)
            ->first();

       if(is_null($instance)){
           $instance = new static();
       }

       return $instance;

    }

    public function apply($model, $user_id, $rights, $postRights)
    {
        
        try {

            $user = (new User())->findOrFail($user_id);

            $postRights = (Utility::hasArray($postRights) && (isset($postRights['acl']) && Utility::hasArray($postRights['acl']))) ? $postRights['acl'] : [];

            $instance = $this->getRights($model, $user->getKey());

            if(!Utility::hasArray($postRights)){

                if($instance->exists){
                    (new Temp())->flushModulesAclUserRights($instance, $user->getKey());
                    $instance->delete();
                }

            }else {

                $this->shadow($rights);
                $reflectedRights = [];

                foreach ($rights as $rkey => $rvalue) {
                    $right = $rvalue['slug'];
                    $reflectedRights[$right] = (isset($postRights[$right]) && $postRights[$right]) ? 1 : 0;
                }

                $instance->setAttribute('model', $model->getTable());
                $instance->setAttribute('model_id', $model->getKey());
                $instance->setAttribute('user_id', $user->getKey());

                $instance->setAttribute('rights', $reflectedRights);

                $instance->save();

                (new Temp())->flushModulesAclUserRights($instance, $user->getKey());

            }

        }catch(ModelNotFoundException $e){
            
            throw $e;
            
        }catch (ModelVersionException $e){
            
            
            throw $e;
            
        } catch(ModelValidationException $e){
            
            throw $e;
            
        }catch(Exception $e){
            
            throw $e;
            
        }

        return $instance;
    }

    public function del($model, $user_id){

        return $this
            ->where('model', '=', $model->getTable())
            ->where('model_id', '=', $model->getKey())
            ->where('user_id', '=', $user_id)
            ->delete();

    }

    public function batchDel($model){

        return $this
            ->where('model', '=', $model->getTable())
            ->where('model_id', '=', $model->getKey())
            ->delete();

    }

    public function batchDelByUser($user_id){

        return $this
            ->where('user_id', '=', $user_id)
            ->delete();

    }

    public static function hasRights($user, $model, $roles, $right, Closure $beforeCallback = null){

        $self = new static();

        $flag = false;

        $flag = Acl::isRootRight();

        if(!$flag && $model->exists){

            if(!is_null($beforeCallback)){

                $flag = $beforeCallback();

                if(is_bool($flag)){
                    return $flag;
                }

            }

            if(in_array(Utility::constant('role.super-admin.slug'), $roles)){

                $flag = true;

            }else{

                $instance = (new Temp())->getModuleAclUserRights( $self, $model, $user->getKey());
                $rights = ($instance->exists) ? $instance->rights : array();

                if (Utility::hasArray($rights)) {
                   if(isset($rights[$right]) && $rights[$right]){
                       $flag = true;
                   }
                }


            }

        }

        return $flag;

    }

    public static function hasAdminRights($user, $companyModel, $model, $right){

        $company = new Company();

        if(!is_null($companyModel->users) && $companyModel->users->count() > 0){
            $company = $companyModel;
            $company->setRelation('pivot', $company->users->first()->pivot);
        }

        $roles = [$user->role];

        if(Utility::hasString($role = $company->roleForThisActiveUser($user->getKey()))){
            $roles[] = $role;
        }

        return static::hasRights($user, $model, $roles, $right, function() use($user, $company){

            if($company->isSuperAdminForThisActiveUser($user->getKey())){
                return true;
            }

            if(!$company->isBelongToThisActiveUser($user->getKey())){
                return false;
            }

        });

    }

}
