<?php

namespace App\Libraries\Model\Tree\Materialized;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

use App\Libraries\Model\Model;
use Langaner\MaterializedPath\MaterializedPathTrait;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\Model\IntegrityException;
use App\Libraries\Model\ModelVersionException;
use App\Libraries\Model\ModelValidationException;


class Materialized extends Model{

    use MaterializedPathTrait;

    private $my_rules = array(
        'parent_id' => 'nullable|integer',
        'path' => 'required|string',
        'position' => 'required|integer',
        'level' => 'required|integer'
    );

    public function __construct(array $attributes = array())
    {

        static::$rules = array_merge(static::$rules, $this->my_rules);

        parent::__construct($attributes);

    }
	
	public function fillable(array $fillable)
	{

		$default = [
			$this->getColumnTreePid(),
			$this->getColumnTreeOrder(),
			$this->getColumnTreePath(),
			$this->getColumnTreeRealPath(),
			$this->getColumnAlias(),
			$this->getColumnTreeDepth()
		];
		
		
		$fillable = array_merge($default, $fillable);

		return parent::fillable($fillable);
		
	}
	
    public function useRealPath()
    {
        $config = $this->getConfig();

        return $config->get('materialized_path.use_real_path');
    }
    
    
    public function scopeBuildTree($query, $parentId = null, $level = null)
    {
        $tree = new Collection;
        
        if (is_null($parentId)) {
            $roots = static::allRoot();
        } else {
            
            $config = $this->getConfig();
            
            if ($config->get('materialized_path.with_translations') === true && method_exists($query->getModel(), 'translations')) {
    
                $roots = $this->newQuery()->with('translations')->where('id', $parentId);
                
            }else{
                $roots =  $this->newQuery()->where('id', $parentId);
            }
           
        }
        
        if ($roots->count() > 0) {
            foreach ($roots->orderBy($this->getColumnTreeOrder(), 'ASC')->get() as $root) {
                $children = $root->buildChidrenTree($root->id, null, $level);
                $root->children = $children;
                $tree->add($root);
            }
        }
        
        return $tree;
    }
    
    public function scopeBuildRootTree($query, $level = null){
        
        $tree = new Collection();
        $roots = $query->whereNull($this->getColumnTreePid())->orderBy($this->getColumnTreeOrder(), 'ASC')->get();


        if ($roots->count() > 0) {
            foreach ($roots as $root) {
                $children = $root->setEagerLoads($query->getEagerLoads())->buildRootChidrenTree($root->id, null, $level);
                $root->children = $children;
                $tree->add($root);
            }
        }
        
        
        return $tree;
        
    }
    
    public function scopeBuildRootChidrenTree($query, $parentId = null, $nodes = null, $level = null)
    {
        $tree = new Collection;
        $parentId = $parentId === null ? $this->id : $parentId;
        
        if ($nodes === null) {
            $nodes = $this->childrenByDepth($level)->with($query->getEagerLoads())->orderBy($this->getColumnTreeOrder(), 'ASC')->get();
        }

        foreach ($nodes as $node) {
            if($node->parent_id == $parentId) {
                $children = $node->setEagerLoads($query->getEagerLoads())->buildRootChidrenTree($node->id, $nodes, $level);
                $node->children = $children;
                $tree->add($node);
            }
        }
        
        return $tree;
    }
    

    public function isRoot(){
        return is_null($this->getAttribute($this->getColumnTreePid()));
    }

    public function findParentOrFail($id){

        $parent = new static();

        try {

            $parent = $parent->findOrFail($id);

        }catch(ModelNotFoundException $e){

            throw $e;

        }

        return $parent;

    }

}