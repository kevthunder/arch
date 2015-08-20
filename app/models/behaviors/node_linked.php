<?php

class NodeLinkedBehavior extends ModelBehavior {

	var $settings;
	var $defaultOptions = array(
		'follow' => array(),
	);
	
	var $typeAlias = array(
		2 => 'both',
		1 => 'owner',
		0 => 'owned',
		'owner' => 'owner',
		'owned' => 'owned',
		'both' => 'both'
	);

	function setup(&$model, $settings = array()) {
		$this->settings[$model->alias] = array_merge($this->defaultOptions, (array)$settings);

		if(!isset($model->NodeLink)){
			if (PHP5) {
				$model->NodeLink = ClassRegistry::init('NodeLink');
			} else {
				$model->NodeLink =& ClassRegistry::init('NodeLink');
			}
		}
		if(!$model->Behaviors->attached('Node')){
			$model->Behaviors->attach('Node');
		}
	}
	
	function linkTo(&$model, $type, $targetRef, $id = null, $owner = 'owner', $opt = array()){
		$defaultOpt = array(
			'dry' => false,
		);
		$opt = Set::merge($defaultOpt,$opt);
		if(is_null($id)){
			$id = $model->id;
		}
		if(empty($id)){
			return false;
		}
		if(isset($this->typeAlias[strtolower($owner)])){
			$owner = $this->typeAlias[strtolower($owner)];
		}else{
			return false;
		}
		
		$myRef = $model->myNodeRef($id);
		if($owner == 'both'){
			if(!$opt['dry']){
				return $model->NodeLink->link($type, $myRef, $targetRef, $opt) && $model->NodeLink->link($type, $targetRef, $myRef, $opt);
			}else{
				return array($model->NodeLink->link($type, $myRef, $targetRef, $opt),$model->NodeLink->link($type, $targetRef, $myRef, $opt));
			}
		}elseif($owner == 'owner'){
			return $model->NodeLink->link($type, $myRef, $targetRef, $opt);
		}else{
			return $model->NodeLink->link($type, $targetRef, $myRef, $opt);
		}
		
		return false;
	}
	
	function getDirectLinks(&$model, $id = null, $type = false, $owner = 'both', $opt = array()){
		$localOpt = array('id', 'type', 'owner', 'target', 'mode');
		$defaultOpt = array(
			'mode' => 'all',
			'id' => $model->id,
			'conditions' => array(),
			'type' => false, 
			'owner' => 'both',
			'target' => null,
			'time' => true,
		);
		if(is_array($id)){
			$opt = $id;
		}else{
			$opt = array_merge($opt,compact('id', 'type', 'owner'));
			if(is_null($id)){
				unset($opt['id']);
			}
		}
		$opt = Set::merge($defaultOpt,$opt);
		$findOpt = Set::merge(array_diff_key($opt, array_flip($localOpt)));
		if(is_null($opt['type']) || $opt['type'] !== false){
			$findOpt['conditions']['type_id'] = $model->NodeLink->NodeLinkType->getTypeId($opt['type']);
		}
		$myref = $model->myNodeRef($opt['id']);
		$model->Behaviors->attach('Util');
		$tagetsCond = array();
		if($opt['owner'] == 'both'){
			
			$tagetsCond = array('or'=>array(
				$model->AddAliasToCond($myref,'OwnerNode'),
				$model->AddAliasToCond($myref,'OwnedNode')
			));
		}elseif($opt['owner'] == 'owner'){
			$tagetsCond = $model->AddAliasToCond($myref,'OwnerNode');
		}else{
			$tagetsCond = $model->AddAliasToCond($myref,'OwnedNode');
		}
		if(!empty($opt['target'])){
			if($opt['owner'] == 'both'){
				$tagetsCond = Set::merge($tagetsCond,array('or'=>array(
					$model->AddAliasToCond($opt['target'],'OwnedNode'),
					$model->AddAliasToCond($opt['target'],'OwnerNode')
				)));
			}elseif($opt['owner'] == 'owner'){
				$tagetsCond = Set::merge($tagetsCond,$model->AddAliasToCond($opt['target'],'OwnedNode'));
			}else{
				$tagetsCond = Set::merge($tagetsCond,$model->AddAliasToCond($opt['target'],'OwnerNode'));
			}
		}
		$findOpt['conditions'][] = $tagetsCond;
		$findOpt['conditions'][] = $model->NodeLink->getValidConditions(array('time'=>$opt['time']));
		//debug($findOpt);
		$links = $model->NodeLink->find($opt['mode'],$findOpt);
		//debug($links);
		return $links;
	}
	
	function getFieldFollowOptions(&$model, $field){
		foreach($this->settings[$model->alias]['follow'] as $alias => $opt){
			if(isset($model->belongsTo[$alias])){
				$association = $model->belongsTo[$alias];
				if($association['foreignKey'] == $field){
					break;
				}else{
					$association = null;
				}
			}
		}
		if(!empty($association)){
			return $this->_followOpts($model, $opt,$association,$alias);
		}
		return null;
	}
	
	function _followOpts(&$model, $opt, $association,$alias){
		if(!is_array($opt)){
			$opt = array('type' => $opt);
		}
		$defaultOpt = array(
			'alias' => $alias,
			'type' => null,
			'owner' => 'owner',
			'context' => $model->alias.$model->id.'Follow'.$alias,
			'className' => $association['className'],
			'foreignKey' => $association['foreignKey'],
			'association' => $association,
		);
		return Set::merge($defaultOpt,$opt);
	}
	
	function afterSave(&$model, $created){
		//debug($this->settings[$model->alias]['follow']);
		if(!empty($this->settings[$model->alias]['follow'])){
			$defaultOpt = array(
				'type' => null,
				'owner' => 'owner',
				'startField' => null,
				'endField' => null,
			);
			foreach($this->settings[$model->alias]['follow'] as $alias => $opt){
				if(isset($model->belongsTo[$alias])){
					$association = $model->belongsTo[$alias];
					$opt = $this->_followOpts($model, $opt,$association,$alias);
					//debug($opt);
					//debug($model->data);
					if(isset($model->data[$model->alias][$opt['foreignKey']]) || isset($model->data['originalData'][$opt['foreignKey']])){
						$target_id = isset($model->data[$model->alias][$opt['foreignKey']])?$model->data[$model->alias][$opt['foreignKey']]:$model->data['originalData'][$opt['foreignKey']];
						$target_ref = array('foreign_key'=>$target_id,'model'=>$opt['className']);
						$existing = $model->getDirectLinks(array(
							'type'=>$opt['type'],
							'owner'=>$opt['owner'],
							'target'=>$target_ref,
							'group'=>array('owned_node_id','owner_node_id'),
							'conditions'=>array('context'=>$opt['context']),
						));
						//debug($existing);
						$missing = null;
						if($opt['owner'] == 'both'){
							if(count($existing) == 0){
								$missing = $opt['owner'];
							}elseif(count($existing) == 1){
								if($existing['OwnedNode']['foreign_key'] == $target_ref['foreign_key'] && $existing['OwnedNode']['model'] == $target_ref['model']){
									$missing = 'owned';
								}else{
									$missing = 'owner';
								}
							}
						}elseif(count($existing) == 0){
							$missing = $opt['owner'];
						}
						$toUpdate = array();
						if(!empty($opt['startField']) && isset($model->data[$model->alias][$opt['startField']])){
							$toUpdate['start'] = $model->data[$model->alias][$opt['startField']];
						}
						if(!empty($opt['endField']) && isset($model->data[$model->alias][$opt['endField']])){
							$toUpdate['end'] = $model->data[$model->alias][$opt['endField']];
						}
						if($missing){
							$model->NodeLink->deleteAll(array('context'=>$opt['context']));
							$data = $toUpdate;
							$data['context'] = $opt['context'];
							$model->linkTo($opt['type'], $target_ref, null, $missing, $data);
						}
						if(!empty($toUpdate)){
							foreach($existing as $link){
								if(   (!empty($toUpdate['start']) && $toUpdate['start'] != $link['NodeLink']['start'])
									||(!empty($toUpdate['end']) && $toUpdate['end'] != $link['NodeLink']['end'])
								){
									$data = $toUpdate;
									$data['id'] = $link['NodeLink']['id'];
									$model->NodeLink->create();
									$model->NodeLink->save($data);
								}
							}
						}
					}
				}else{
					//only supports belongsTo
				}
			}
		}
	}
	
	function beforeDelete(&$model,$cascade){
		$model->NodeLink->deleteAll(array('context LIKE'=>$model->alias.$model->id.'Follow%'));
	}
	
}

?>