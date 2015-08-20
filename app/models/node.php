<?php

/**
 * Load Model and AppModel
 */
App::import('Model', 'App');

/**
 * ACL Node
 *
 *
 * @package       cake
 * @subpackage    cake.cake.libs.model
 */
class Node extends AppModel {

	var $name = 'Node';

	var $saveFields = array('model','foreign_key','alias','parent_id');
	var $additionalFields = array('id');
/**
 * Explicitly disable in-memory query caching for ACL models
 *
 * @var boolean
 * @access public
 */
	var $cacheQueries = false;

/**
 * ACL models use the Tree behavior
 *
 * @var array
 * @access public
 */
	var $actsAs = array('Tree' => 'nested');

	
	var $useTable = 'nodes';

/**
 * Retrieves the Aro/Aco node for this model
 *
 * @param mixed $ref Array with 'model' and 'foreign_key', model object, or string value
 * @return array Node found in database
 * @access public
 */
 
	
	function humanReadableDisplayField($return = true){
		$displayField = 'IFNULL('.$this->alias.'.alias,CONCAT('.$this->alias.'.model, " : ", '.$this->alias.'.foreign_key))';
		if($return){
			return $displayField;
		}else{
			$this->virtualFields['display'] = $displayField;
		}
	}
	
 
	function getNode($ref = null, $fullPath = true, $incomplete = false, $cache = false) {
		$result = null;
		
		$ref = $this->fullRef($ref);
		if(empty($ref)){
			return false;
		}
		if($fullPath && empty($ref['path'])){
			$ref = array('path'=>array($ref));
		}
		if($cache){
			$cackeKey = $this->refCacheKey($ref);
			$cached = Cache::read('nodes');
			if(!empty($cached[$cackeKey])){
				$result = $cached[$cackeKey];
			}
		}
		if(empty($ref['path'])){
			if(empty($result)){
				$findOpts = array('conditions'=>$ref);
				$result = $this->find('first', $findOpts);
				
				if($cache){
					 $this->cacheNodes($cackeKey,$result);
				}
				return $result;
			}else{
				return $result;
			}
		}else{
			if(empty($result)){
				$db =& ConnectionManager::getDataSource($this->useDbConfig);
				$a = $this->alias;
				$queryData = array(
					'conditions' => array('or'=>array(array(
						$db->name("{$a}.lft") . ' <= ' . $db->name("{$a}0.lft"),
						$db->name("{$a}.rght") . ' >= ' . $db->name("{$a}0.rght")
					))),
					'fields' => array('id', 'parent_id', 'model', 'foreign_key', 'alias'),
					'order' => $db->name("{$a}.lft") . ' DESC'
				);
				foreach ($ref['path'] as $i => $subRef) {
					$j = $i - 1;
					
					$join = array(
						'table' => $db->fullTableName($this),
						'alias' => "{$a}{$i}",
						'type'  => 'LEFT'
					);
					foreach($subRef as $key => $val){
						$join['conditions'][$a.$i.'.'.$key] = $val;
					}
					if($i>0){
						$join['conditions'][] = $db->name("{$a}{$i}.lft") . ' > ' . $db->name("{$a}{$j}.lft");
						$join['conditions'][] = $db->name("{$a}{$i}.rght") . ' < ' . $db->name("{$a}{$j}.rght");
						$join['conditions'][] = $db->name("{$a}{$j}.id") . ' = ' . $db->name("{$a}{$i}.parent_id");
						
						$queryData['conditions']['or'][] = $db->name("{$a}.lft") . ' <= ' . $db->name("{$a}{$i}.lft") . ' AND ' . $db->name("{$a}.rght") . ' >= ' . $db->name("{$a}{$i}.rght");
					}
					$queryData['joins'][] = $join;
				}
				//debug($queryData);
				$result = $db->read($this, $queryData, -1);
				
				if($cache){
					 $this->cacheNodes($cackeKey,$result);
				}
			}
			//debug($result);
			if(!empty($result) && !count(array_diff_assoc($ref['path'][count($ref['path'])-1],$result[0][$a])) && count($result)>=count($ref['path'])){
				if($fullPath){
					return $result;
				}else{
					return $result[0];
				}
			}elseif($incomplete){
				$r2 = array();
				foreach($result	as $n){
					$r2[] = $n[$a];
				}
				$diff = array_udiff($ref['path'], $r2, array($this, "compareRef2"));
				foreach($diff as $n){
					array_unshift($result,array($a=>$n));
				}
				return $result;
			}
		}
		
		return false;
		
	}
	
	function getItem($ref,$opt=array()){
		
		$localOpt = array('model');
		$defaultOpt = array(
			'model'=>null
		);
		$options = Set::merge($defaultOpt,$opt);
		$ref = $this->getItemRef($ref,$options['model']);
		if(empty($ref)){
			return null;
		}
		$model = ClassRegistry::init($ref['model']);
		if(!$model){
			return  null;
		}
		$query = array('conditions'=>array($model->alias.'.id'=>$ref['foreign_key']));
		$query = Set::merge(array_diff_key($options, array_flip($localOpt)),$query);
		//debug($query);
		return $model->find('first',$query);
		return null;
	}
	
	function getItemId($ref,$model){
		if(empty($model)){
			return null;
		}
		$ref = $this->getItemRef($ref,$model);
		if(empty($ref)){
			return null;
		}
		return $ref['foreign_key'];
	}
	
	function getItemRef($ref,$model = null){
		$ref = $this->fullRef($ref);
		if(empty($ref)){
			return null;
		}
		if(empty($ref['model']) || empty($ref['foreign_key'])){
			$ref = $this->getNode($ref,false);
			$ref = $ref['Node'];
		}
		if(empty($ref['model']) || empty($ref['foreign_key'])){
			return null;
		}
		if(!empty($model) && $ref['model'] != $model){
			return null;
		}
		return $ref;
	}
	
	function nodeTreeList(){
		$this->humanReadableDisplayField(false);
		return $this->generatetreelist(null,null,'{n}.'.$this->alias.'.display');
	}
	
	function beforeSave($options){
		if(isset($this->data[$this->alias]['model']) && empty($this->data[$this->alias]['model'])){
			$this->data[$this->alias]['model'] = null;
		}
		if(isset($this->data[$this->alias]['foreign_key']) && empty($this->data[$this->alias]['foreign_key'])){
			$this->data[$this->alias]['foreign_key'] = null;
		}
		if(isset($this->data[$this->alias]['alias']) && empty($this->data[$this->alias]['alias'])){
			$this->data[$this->alias]['alias'] = null;
		}
		return true;
	}
	
	function afterSave($created){
		if(!$created){
			$cacheMap = Cache::read('nodesMap');
			if(!empty($cacheMap[$this->id])){
				$cached = Cache::read('nodes');
				foreach($cacheMap[$this->id] as $key){
					unset($cached[$key]);
				}
				unset($cacheMap[$this->id]);
				Cache::write('nodes',$cached);
				Cache::write('nodesMap',$cacheMap);
			}
		}
	}
	
	function buildPath($strpath, $fullOutput = true, $cache = false){
		$path = null;
		if($cache){
			$cackeKey = '{buildPath}'.$this->refCacheKey($strpath);
			$cached = Cache::read('nodes');
			if(!empty($cached[$cackeKey])){
				$path = $cached[$cackeKey];
			}
		}
		if(empty($path)){
			$path = $this->getNode($strpath,true,true);
			//debug($path);
			$path = array_reverse($path);
			$parent_id = null;
			foreach($path as $i => $node){
				if(!empty($node[$this->alias])){
					$node = $node[$this->alias];
				}
				$node = $this->fullRef($node);
				if(empty($node['id'])){
					if(!empty($parent_id)){
						$node['parent_id'] = $parent_id;
					}
					if($fullOutput){
						$node = $this->createNode($node,true);
					}else{
						$node = array('id'=>$this->createNode($node,false));
					}
				}
				$parent_id = $node['id'];
				$path[$i] = $node;
			}
			if($cache){
				$this->cacheNodes($cackeKey,$path);
			}
		}
		
		if($fullOutput){
			return $path;
		}else{
			return $path[count($path)-1]['id'];
		}
	}
	
	
	function appendParents($items){
		$query = $this->appendParentsQuery($items);
		if(empty($query)) {
			return null;
		}
		$allItems = $this->find('all', $query);
		foreach($allItems as &$item){
			$item = $item['Parent']['id'];
		}
		return $allItems;
	}
	
	function appendParentsQuery($items, $opt = array()){
		if(empty($items)){
			return null;
		}
		
		$localOpt = array();
		$defaultOpt = array(
			'fields'=>'Parent.id',
			'recusive' => -1
		);
		$options = Set::merge($defaultOpt,$opt);
		
		if(Set::numeric((array)$items)){
			$conditions = array($this->alias.'.id'=>$items);
		}else{
			$conditions = $this->fullRefAll($items);
			$fields = $this->getFields(false);
			foreach($conditions as $key1 => $item){
				$item = array_intersect_key($item,array_flip($fields));
				$conditions[$key1] = array();
				foreach($item as $key2 =>$field){
					$conditions[$key1][$this->alias.'.'.$key2] = $field;
				}
			}
			$conditions = array('or'=>$conditions);//,'gfghf'=>''
		}
		$query = array(
				'conditions'=>$conditions, 
				'joins'=>array(
					$this->parentsJointOpt()
				),
				'group' => array('Parent.id')
			);
		$query = Set::merge(array_diff_key($options, array_flip($localOpt)),$query);
		return $query;
	}
	
	function parentsJointOpt($options = null){
		$defOpt = array(
			'alias'=>$this->alias,
			'type'=>'inner',
			'parentAlias'=>'Parent',
		);
		$opt = Set::merge($defOpt,$options);
		return array(
			'type'=>$opt['type'],
			'table'=>$this->useTable,
			'alias' => $opt['parentAlias'],
			'conditions' => array(
				$opt['alias'].'.lft >= '.$opt['parentAlias'].'.lft',
				$opt['alias'].'.rght <= '.$opt['parentAlias'].'.rght'
			)
		);
	}
	
	function getOrSave($ref,$fullOutput=true){
		$ref = $this->fullRef($ref);
		if(empty($ref)){
			return false;
		}
		$this->recursive = -1;
		
		$fields = $this->getFields(false);
		
		$findOpts = array('conditions'=>$ref);
		if(!$fullOutput){
			$findOpts['fields'] = array('id');
		}
		$node = $this->find('first', $findOpts);
		if(empty($node)){
			return $this->createNode($ref,$fullOutput);
		}else{
			if($fullOutput){
				return $node[$this->alias];
			}else{
				return $node[$this->alias]['id'];
			}
		}
		return false;
	}
	
	function createNode($ref,$fullOutput=true){
		$ref = $this->fullRef($ref);
		//debug($ref);
		if(empty($ref)){
			return false;
		}
		$this->recursive = -1;
		
		$saveFields = $this->getFields(true);
		$data = array_intersect_key($ref,array_flip($saveFields));
		if(!empty($ref['parent'])){
			$data['parent_id'] = $this->getOrSave($ref['parent'],false);
		}
		$this->create();
		if($this->save($data)){
			if($fullOutput){
				$node[$this->alias];
				return $node;
			}else{
				return $this->id;
			}
		}
		return false;
	}
	
	function compareRef($tested,$test){
		return count(array_diff_assoc($test,$tested)) < 1;
	}
	
	function compareRef2($test,$tested){
		return !$this->compareRef($tested,$test);
	}
	
	function getNodeId($aliase){
		$ref = $this->fullRef($aliase);
		if(!empty($ref)){
			if(isset($ref['id'])){
				return $ref['id'];
			}
			$this->recursive = -1;
			$node = $this->find('first',array('fields'=>array('id'),'conditions'=>$ref));
			if($node){
				return $node['Node']['id'];
			}
		}
		return null;
	}
	
	function getNodeIds($aliases,$opt = array()){
		
		if(empty($aliases)){
			return array();
		}
		if(!is_array($aliases)){
			$aliases = array($aliases);
		}
		if(Set::numeric($aliases) && Set::numeric(array_keys($aliases))){
			return $aliases;
		}
		$defaultOpt = array(
			'dry'=>false,
		);
		$opt = array_merge($defaultOpt,$opt);
		
		$refs = $this->fullRefAll($aliases);
		$conditions = array();
		$ids = array();
		foreach($refs as $ref){
			if(isset($ref['id'])){
				$ids[] = $ref['id'];
			}else{
				$conditions['or'][] = $ref;
			}
		}
		
		if(!$opt['dry']){
			if(!empty($conditions)){
				$this->recursive = -1;
				$nodes = $this->find('list',array('fields'=>array('id','id'),'conditions'=>$conditions));
				$ids = array_merge($ids,array_keys($nodes));
			}
			return $ids;
		}else{
			return array('ids'=>$ids,'findCond'=>$conditions);
		}
		
	}
	
	function getNodes($items, $opt = array()){
		if(empty($items)){
			return array();
		}
		if(!is_array($items) || !Set::numeric(array_keys($items))){
			$items = array($items);
		}
		
		$localOpt = array('dry','mode','alias');
		$defaultOpt = array(
			'mode'=>'all',
			'recusive' => -1,
			'dry'=>false,
			'alias'=>$this->alias,
			'looseConformity' => false,
		);
		$options = Set::merge($defaultOpt,$opt);
		$defaultList = false;
		if($options['mode'] == 'list'){
			if(empty($options['fields'])){
				$defaultList = true;
			}else{
				$neededFields = array_unique(str_replace($options['alias'].'.','',$options['fields']));
				debug($neededFields);
				$defaultList = (count($neededFields) == 1 && $neededFields[0]=='id');
			}
		}
		if($defaultList){
			$options['fields'] = array($options['alias'].'.id',$options['alias'].'.id');
		}
		if(empty($options['fields'])){
			$options['fields'] = $this->getFields(false,$options['alias']);
		}
		
		$complete = array();
		$conditions = array();
		if(Set::numeric($items)){
			if($defaultList){
				return $items;
			}
			$conditions = array($options['alias'].'.id'=>$items);
		}else{
			$fields = $this->getFields(false);
			if(!isset($neededFields)){
				$neededFields = array_unique(str_replace($options['alias'].'.','',$options['fields']));
			}
			
			foreach($items as $key1 => $item){
				if(isset($item[$options['alias']])){
					if($options['looseConformity']){
						$complete[] = $item;
						continue;
					}
					$item = $item[$options['alias']];
				}
				if(is_array($item) && !$options['looseConformity']){
					//debug($item);
					$conformFields = array_intersect_key($item,array_flip($neededFields));
					
					if(count($conformFields) == count($neededFields)){
						$complete[][$this->alias] = $conformFields;
						continue;
					}
				}
				$item = $this->fullRef($item);
				$item = array_intersect_key($item,array_flip($fields));
				$cond = array();
				foreach($item as $key2 =>$field){
					$cond[$key1][$options['alias'].'.'.$key2] = $field;
				}
				$conditions['or'][] = $cond;
			}
		}
		if(empty($conditions)){
			return $complete;
		}
		$query = array(
				'conditions'=>$conditions
			);
		$query = Set::merge(array_diff_key($options, array_flip($localOpt)),$query);
		//debug($query);
		if(!$options['dry']){
			$allNodes = $this->find($options['mode'], $query);
			$allNodes = array_merge($allNodes,$complete);
			if($defaultList){
				return array_keys($allNodes);
			}
			return $allNodes;
		}else{
			return array('complete'=>$complete,'query'=>$query);
		}
		
	}
	
	function getAssociatedEntries($nodes,$opt = array(),&$models = null){
		$localOpt = array();
		$defaultOpt = array(
		);
		$opt = Set::merge($defaultOpt,$opt);
		$nodes = $this->getNodes($nodes,array('fields'=>array('foreign_key','model')));
		$entries = array();
		foreach($nodes as $node){
			if(!empty($node['Node']['model'])){
				if(empty($models[$node['Node']['model']])){
					$model = ClassRegistry::init($node['Node']['model']);
					$models[$node['Node']['model']] = $model;
				}else{
					$model = $models[$node['Node']['model']];
				}
				if($model){
					$findOptions = array_diff_key($opt, array_flip($localOpt));
					$findOptions['conditions'][$model->alias.'.'.$model->primaryKey] = $node['Node']['foreign_key'];
					$entry = $model->find('first', $findOptions);
					if($entry){
						$entries[] = $entry;
					}
				}
			}
		}
		return $entries;
	}
	
	function fullRefAll($aliases){
		if(!is_array($aliases) or !Set::numeric(array_keys($aliases)) ){
			$aliases = array($aliases);
		}
		return array_map("Node::fullRef",$aliases);
	}
	
	function refCacheKey($ref){
		if(is_string($ref)){
			return $ref;
		}
		$key = "";
		if(!empty($ref['path'])){
			foreach($ref['path'] as $part){
				$key .= $this->refCacheKey($part).'/';
			}
		}
		if(!empty($ref['id'])){
			$key .= $ref['id'];
		}if(!empty($ref['alias'])){
			$key .= $ref['alias'];
		}elseif(!empty($ref['model']) && !empty($ref['foreign_key'])){
			$key .= $ref['model'].':'.$ref['foreign_key'];
		}
	}
	
	function cacheNodes($ref,$nodes){
		if(!is_string($ref)){
			$ref = $this->refCacheKey($ref);
		}
		$single = !Set::numeric(array_keys($nodes));
		if($single){
			$nodes = array($nodes);
		}
		$cached = Cache::read('nodes');
		$cacheMap = Cache::read('nodesMap');
		foreach($nodes as $key => $node){
			$aliased = !empty($node['Node']);
			if($aliased) $node = $node['Node'];
			unset($node['lft']);
			unset($node['rght']);
			$cacheMap[$node['id']][] = $ref;
			if($aliased) $node = array('Node'=>$node);
			$nodes[$key] = $node;
		}
		if($single){
			$nodes = $nodes[0];
		}
		$cached[$ref] = $nodes;
		Cache::write('nodes',$cached);
		Cache::write('nodesMap',$cacheMap);
	}
	
	function fullRef($ref){
		if(is_array($ref)){
			if(isset($ref['Node'])){
				$ref = $ref['Node'];
			}
		}elseif (is_object($ref) && is_a($ref, 'Model')) {
			$ref = array('model' => $ref->name, 'foreign_key' => $ref->id);
		}elseif(!is_numeric($ref)){
			if(strpos($ref, '/') !== false){
				$ref = array('path'=>$ref);
			}elseif(strpos($ref, ':') !== false){
				//debug(explode(':', $ref,2));
				$ref = array_combine(array('model','foreign_key'),explode(':', $ref,2));
			}else{
				$ref = array('alias'=>$ref);
			}
		}else{
			$ref = array('id'=>$ref);
		}
		if(empty($this->_refFields)){
			$fields = $this->getFields(false);
			$fields[] = 'path';
			$this->_refFields = $fields;
		}else{
			$fields = $this->_refFields;
		}
		if(Set::numeric(array_keys($ref))){
			//debug($ref);
			$ref = array('path'=>$ref);
		}elseif(count($ref) == 1){
			$key = key($ref);
			if(!in_array($key,$fields) && $key){
				$ref = array('model' => $key, 'foreign_key' => $ref[$key]);
			}
		}
		if(!empty($ref['path'])){
			if(!is_array($ref['path'])){
				$ref['path'] = explode('/', $ref['path']);
			}
			foreach($ref['path'] as $i => $subRef){
				$ref['path'][$i] = $this->fullRef($subRef);
			}
		}
		$ref = array_intersect_key($ref,array_flip($fields));
		return $ref;
	}
	
	
	function getFields($save = false, $alias = null){
		if($save){
			$fields = $this->saveFields;
		}else{
			$fields = array_merge($this->additionalFields,$this->saveFields);
		}
		if(!empty($alias)){
			foreach($fields as &$field){
				$field = $alias.'.'.$field;
			}
		}
		return $fields;
	}
	
}
