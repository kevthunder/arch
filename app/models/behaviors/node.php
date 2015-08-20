<?php

class NodeBehavior extends ModelBehavior {

		var $settings;
		var $defaultOptions = array(
			'autoUpdateParent' => false
		);

/**
 * Sets up the configuation for the model, and loads ACL models if they haven't been already
 *
 * @param mixed $config
 * @return void
 * @access public
 */
	function setup(&$model, $settings = array()) {
		$this->settings[$model->alias] = array_merge($this->defaultOptions, (array)$settings);

		/*if (PHP5) {
			$model->Node = ClassRegistry::init('Node');
		} else {
			$model->Node =& ClassRegistry::init('Node');
		}*/
		$this->bindNodes($model, true, false);
		
		if (!method_exists($model, 'parentNode')) {
			trigger_error(sprintf(__('Callback parentNode() not defined in %s', true), $model->alias), E_USER_WARNING);
		}
	}

/**
 * Retrieves the Node for this model
 *
 * @param mixed $ref
 * @return array
 * @access public
 */
	function node(&$model, $ref = null, $fullPath = true, $create = null) {
		if(is_numeric($ref)){
			$ref = $this->myNodeRef($model, $ref);
		}
		if(!empty($ref[$model->alias]['id'])){
			$ref = $this->myNodeRef($model, $ref[$model->alias]['id']);
		}
		if (empty($ref)) {
			if(is_null($create)){
				$create = true;
			}
			$ref = $model->myNodeRef(null,false,!$create);
		}
		$node = $model->Node->getNode($ref,$fullPath);
		if(empty($node) && $create){
			$ref = $model->updateNode(false,$ref);
			if($ref){
				$node = $model->Node->getNode($ref,$fullPath);
			}
		}
		return $node;
	}
	
	function nodeId(&$model, $ref = null, $create = null) {
		if(is_null($ref) && !empty($model->data['Node']['id'])){
			return $model->data['Node']['id'];
		}
		$node = $this->node($model, $ref,false, $create);
		return $node['Node']['id'];
	}
	
	function bindNodes(&$model, $add = true, $reset = true){
		if($add){
			$model->bindModel(
				array('hasOne' => array(
						'Node' => array(
							'className' => 'Node',
							'foreignKey' => 'foreign_key',
							'conditions' => array(
								'Node.model' => $model->name,
							),
							'fields' => '',
						)
					)
				),$reset
			);
		}else{
			$model->unbindModel(array('hasOne' => array('Node')));
		}
	}
	
	function nodesJoint(&$model, $options = null){
		$defOpt = array(
			'alias'=>$model->alias,
			'type'=>'inner',
			'nodeAlias'=>'Node',
			'model'=>$model->name,
		);
		$opt = Set::merge($defOpt,$options);
		return array(
			'type'=>$opt['type'],
			'table'=>$model->Node->useTable,
			'alias' => $opt['nodeAlias'],
			'conditions' => array(
				$opt['alias'].'.id = '.$opt['nodeAlias'].'.foreign_key',
				$opt['nodeAlias'].'.model' => $opt['model']
			)
		);
	}
	
	
	function findFromNode(&$model, $aliases, $findOpt = array()){
		$localOpt = array('mode','dry');
		$defaultOpt = array(
			'mode' => 'all',
			'conditions' => array(),
			'dry' => false,
		);
		$options = Set::merge($defaultOpt,$findOpt);
		
		$refs = $model->Node->fullRefAll($aliases);
		$cond = array('or'=>array());
		foreach($refs as $ref){
			if(!empty($ref['foreign_key'])){
				if($ref['model'] == $model->name){
					$cond['or'][$model->alias.'.id'][] = $ref['foreign_key'];
				}
			}elseif(!empty($ref['id'])){
				$cond['or']['Node.id'][] = $ref['foreign_key'];
			}else{
				$node = $model->node($ref, false);
				if($node['Node']['model'] == $model->name){
					$cond['or']['id'] = $node['Node']['foreign_key'];
				}
			}
		}
		
		if(!$options['dry']){
			$findOpt = array_diff_key($options, array_flip($localOpt));
			$findOpt['conditions'][] = $cond;
			return $model->find($options['mode'],$findOpt);
		}else{
			if(count($cond['or']) == 1){
				$cond = $cond['or'];
			}
			return $cond;
		}
	}
	
	function getNodes(&$model, $findOpt = array()){
		$localOpt = array('mode');
		$defaultOpt = array(
			'fields'=> array('Node.id','Node.id'),
			'mode' => 'list',
			'recusive' => -1
		);
		$addOpt = array(
			'joins'=>array(array(
					'type'=>'inner',
					'table'=>$model->Node->useTable,
					'alias' => 'Node',
					'conditions' => array(
						'Node.model' => $model->name,
						'Node.foreign_key = '.$model->alias.'.'.$model->primaryKey
					)
				))
		);
		$options = Set::merge($defaultOpt,$findOpt);
		$findOpt = Set::merge(array_diff_key($options, array_flip($localOpt)),$addOpt);
		
		$nodes = $model->find($options['mode'],$findOpt);
		if($options['mode'] == 'list' && $findOpt['fields'] == $defaultOpt['fields']){
			$nodes = array_keys($nodes);
		}
		return $nodes;
	}
	
	function myNodeRef(&$model, $id=null, $create = false, $defaultParent= true){
		$ref = null;
		if(is_array($id)){
			if(!empty($id['Node']['id'])){
				$ref = array_intersect_key($id['Node'],array_flip(array('id','model','foreign_key')));
			}elseif(!empty($id[$model->alias]['id'])){
				$ref = array('model' => $model->name, 'foreign_key' => $id[$model->alias]['id']);
			}else{
				$id=null;
			}
		}
		if(is_null($id)){
			$id = $model->id;
		}
		if(empty($ref)){
			if (!empty($id)) {
				$ref = array('model' => $model->name, 'foreign_key' => $id);
			}elseif($defaultParent){
				$ref = $model->Node->fullRef($model->parentNode());
			}else{
				return false;
			}
		}
		if($create){
			$node = $model->Node->getNode($ref,false);
			if(empty($node)){
				$model->id = $id;
				if($ref = $model->updateNode(false,$ref)){
				}else{
					return false;
				}
			}
		}
		return $ref;
	}
	
	function myNodeId(&$model, $id=null, $create = false){
		if((empty($id) || !empty($model->data) && $model->data[$model->alias][$model->primaryKey] == $id) && !empty($model->data['Node']['id'])){
			return $model->data['Node']['id'];
		}
		return $model->Node->getNodeId($this->myNodeRef($model, $id, $create, false));
	}
	
	function updateNode(&$model, $tcheckCreated = true, $ref=null){
		if(is_null($ref)){
			$data = $model->myNodeRef(null,false,false);
			if(empty($data)){
				return false;
			}
		}else{
			$data = $model->Node->fullRef($ref);
		}
		
		$parent = $model->parentNode();
		if (!empty($parent)) {
			$parent = $model->Node->getNode($parent,false);
		}
		$data['parent_id'] = isset($parent['Node']['id']) ? $parent['Node']['id'] : null;
		
		if ($tcheckCreated) {
			$node = $this->node($model);
			$data['id'] = isset($node[0]['Node']['id']) ? $node[0]['Node']['id'] : null;
		}
		//debug($data);
		$model->Node->create();
		if($model->Node->save($data)){
			return $model->Node->fullRef($model->Node->id);
		}
		return false;
	}

/**
 * Creates a new Node bound to this record
 *
 * @param boolean $created True if this is a new record
 * @return void
 * @access public
 */
	function afterSave(&$model, $created) {
		$s = $this->settings[$model->alias];//shortcut
		$invalid = 
			$s['autoUpdateParent'] || 
			$created || 
			(!empty($s['usedField']) && count(array_intersect(array_keys($model->data[$model->alias]),(array)$s['usedField'])) )
		;
		if($invalid){
			$model->updateNode(!$created);
		}
	}
	
	

/**
 * Destroys the Node bound to the deleted record
 *
 * @return void
 * @access public
 */
	function afterDelete(&$model) {
		$node = Set::extract($this->node($model), "0.Node.id");
		if (!empty($node)) {
			$model->Node->delete($node);
		}
	}
}
