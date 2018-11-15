<?php

namespace App\Libraries\Model;

use LaravelArdent\Ardent\Ardent;
use App\Libraries\Model\Traits\Common;
use App\Libraries\Model\Relations\Eloquent\MorphTo;
use Jenssegers\Mongodb\Eloquent\HybridRelations;
use Jenssegers\Mongodb\Eloquent\EmbedsRelations;


class Model extends Ardent
{

    use Common;

    use HybridRelations {
        belongsTo as traitBelongsTo;
        morphTo as traitMorphTo;
    }

    use EmbedsRelations;


    protected $publiserNamespace = '';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     * @see Model::fillable
     *
     */
    protected $fillable = [];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     * @see Model::guarded
     *
     */
    protected $guarded = [];

    public function belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null){

        if (is_null($relation)) {
            $backtrace = debug_backtrace(false, 4);
            if ($backtrace[1]['function'] == 'handleRelationalArray') {
                $relation = $backtrace[1]['args'][0];
            } else {
                $relation = $backtrace[3]['function'];
            }
        }


        return $this->traitBelongsTo($related, $foreignKey, $otherKey, $relation);

    }

    public function morphTo($name = null, $type = null, $id = null){


        if (is_null($name))
        {
            $backtrace = debug_backtrace(false);
            $caller = ($backtrace[1]['function'] == 'handleRelationalArray')? $backtrace[3] : $backtrace[1];

            $name = snake_case($caller['function']);
        }

        list($type, $id) = $this->getMorphs($name, $type, $id);

        if(is_subclass_of($this, self::class)){


            if (is_null($class = $this->$type)) {
                return new MorphTo(
                    $this->newQuery(), $this, $id, null, $type, $name
                );
            }

            // If we are not eager loading the relationship we will essentially treat this
            // as a belongs-to style relationship since morph-to extends that class and
            // we will pass in the appropriate values so that it behaves as expected.
            else {

                $class = $this->getActualClassNameForMorph($class);

                $instance = new $class;

                return new MorphTo(
                    $instance->newQuery(), $this, $id, $instance->getKeyName(), $type, $name
                );
            }

        }else {

            return $this->traitMorphTo($name, $type, $id);

        }

    }

}