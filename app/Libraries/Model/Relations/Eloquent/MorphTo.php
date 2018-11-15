<?php

namespace App\Libraries\Model\Relations\Eloquent;

use Illuminate\Database\Eloquent\Relations\MorphTo as EloquentMorphTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Str;

class MorphTo extends EloquentMorphTo
{

    protected function getResultsByType($type)
    {
        $instance = $this->createModelByType($type);
        $key = $instance->getTable().'.'.$instance->getKeyName();
        $query = clone $this->query;
        $query->setEagerLoads($this->getEagerLoadsForInstance($instance));
        $query->setModel($instance);
        return $query->whereIn($key, $this->gatherKeysByType($type)->all())->get();
    }

    protected function getEagerLoadsForInstance(Model $instance)
    {
        $eagers = BaseCollection::make($this->query->getEagerLoads());
        $eagers = $eagers->filter(function ($constraint, $relation) {
            return Str::startsWith($relation, $this->relation.'.');
        });
        return $eagers->keys()->map(function ($key) {
            return Str::replaceFirst($this->relation.'.', '', $key);
        })->combine($eagers)->merge($instance->getEagerLoads())->all();
    }

}