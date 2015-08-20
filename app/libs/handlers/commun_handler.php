<?php
class CommunHandler extends Object {

	function _targetMode_Direct(&$eventOptions,$params){
		if(array_key_exists('target',$params)){
			$defParams = array(
				'target' => null,
				'path' => null,
				'many' => true,
			);
			$opt = array_merge($defParams,$params);
			$val = $opt['target'];
			if(!empty($opt['path'])){
				Set::extract($opt['path'],$val);
			}
			if(!$opt['many'] || !is_array($val)){
				$val = array($val);
			}
			return $val;
		}
		return array();
	}
	
	function _targetMode_NodeRef(&$eventOptions,$params){
		if(array_key_exists('target',$params) && array_key_exists('field',$params)){
			$localOpt = array('target','modelName');
			$defParams = array(
				'target' => null,
				'modelName' => false,
				'field' => null,
			);
			$opt = array_merge($defParams,$params);
			
			$this->Node = ClassRegistry::init('Node');
			$nOpt = array(
				'fields'=>array('foreign_key','model')
			);
			if(!empty($opt['modelName'])){
				$nOpt['conditions']['Node.model'] = $opt['modelName'];
			}
			$nodes = $this->Node->getNodes($opt['target'],$nOpt);
			if($opt['field'] == 'id'){
				return Set::extract('{n}.Node.foreign_key');
			}else{
				App::import('Lib', 'SetMulti');
				$nodes = SetMulti::group($nodes,'Node.model',array('valPath'=>'Node.foreign_key'));
				
				$targets = array();
				foreach($nodes as $model=>$keys){
					$subOpt = array_diff_key($opt, array_flip($localOpt));
					$subOpt['target'] = $keys;
					$subOpt['modelName'] = $model;
					$targets = array_merge($targets,$this->_targetMode_ForeignKey($eventOptions,$subOpt));
				}
				return $targets;
			}
		}
		return array();
	}

	
	function _targetMode_ForeignKey(&$eventOptions,$params){
		if(array_key_exists('field',$params) && array_key_exists('modelName',$params)){
			$localOpt = array('target','modelName','field','checkVirtual');
			$defParams = array(
				'target' => null,
				'modelName' => false,
				'field' => null,
				'checkVirtual' => true,
			);
			$opt = array_merge($defParams,$params);
			
			$model = ClassRegistry::init($opt['modelName']);
			if($model && $model->hasField($opt['field'],$opt['checkVirtual'])){
				$findOpt = array(
					'fields' => array(
						$model->alias.'.'.$model->primaryKey,
						$model->alias.'.'.$opt['field'],
					),
					'recursive' => -1,
				);
				if(!empty($opt['target'])){
					$findOpt['conditions'][$model->alias.'.'.$model->primaryKey] = $opt['target'];
				}
				$findOpt = array_merge($findOpt,array_diff_key($opt, array_flip($localOpt)));
				//debug($findOpt);
				$targets = $model->find('list',$findOpt);
				if(empty($targets)){
					$targets = array();
				}
				return array_values($targets);
			}
		}
		return array();
	}
	
	function compare_form(){
		$operators = array('>', '<', '>=', '<=', '=');
		$targetModes = array('Direct', 'NodeRef', 'ForeignKey');
		return array(
			array(
				'type'=>'element',
				'val'=>'compare_form',
				'options'=>array(
					'operators' => array_combine($operators,$operators),
				)
			),
		);
	}
	function compare_deconstruct($data){
		App::import('Lib', 'SparkForm.SparkFormData'); 
		$data = SparkFormData::specialDeconstruct($data);
		return $data;
	}
	
	function compare(&$eventOptions,$params){
		$defParams = array(
			'targetMode' => 'Direct',//'Direct', 'NodeRef', 'ForeignKey'
			'operator' => '=',
			'val' => null,
			'or' => false,
			'emptyReturn' => false,
			'matchReturn' => null,
			'noMatchReturn' => false,
		);
		$localOpt = array_keys($defParams);
		$opt = array_merge($defParams,$params);
		//debug($opt);
		
		$targets = array();
		if(!empty($opt['targetMode']) && method_exists($this,'_targetMode_'.$opt['targetMode'])){
			$targets = $this->{'_targetMode_'.$opt['targetMode']}($eventOptions,array_diff_key($params, array_flip($localOpt)));
		}
		//debug($targets);
		if(!empty($targets)){
			$res = false;
			foreach($targets as $target){
				$val = false;
				switch($opt['operator']){
					case '>' :
						$val = $target > $opt['val'];
						break;
					case '<' :
						$val = $target < $opt['val'];
						break;
					case '>=' :
						$val = $target >= $opt['val'];
						break;
					case '<=' :
						$val = $target <= $opt['val'];
						break;
					case '=' :
					default :
						$val = $target == $opt['val'];
						break;
				}
				if(!($opt['or']) == $val){
					$res = $val;
				}else{
					return $val?$opt['matchReturn']:$opt['noMatchReturn'];
				}
			}
			return $res?$opt['matchReturn']:$opt['noMatchReturn'];
		}
		return $opt['emptyReturn'];
	}
	
	
	function _selectEntriesCond($options){
		if((array_key_exists('model',$options) || array_key_exists('key',$options))){
			$defOpt = array(
				'model' => 'NodeRef',
				'key' => null,
				'conditions' => array(),
				'returnModel' => false,
			);
			$opt = array_merge($defOpt,$options);
			if($opt['model'] == 'NodeRef'){
				//$ref = getItemRef($eventOptions->bindedEvent['Event'][$opt['type'].'_id']);
				$this->Node = ClassRegistry::init('Node');
				$ref = $this->Node->getItemRef($opt['key']);
			}else{
				$ref = array('Model'=>$opt['model'],'foreign_key'=>$opt['key']);
			}
			$model = ClassRegistry::init($ref['model']);
			$conditions = $opt['conditions'];
			if(!empty($ref['foreign_key'])){
				$conditions[$model->alias.'.'.$model->primaryKey] = $ref['foreign_key'];
			}
			if($opt['returnModel']){
				return array($model,$conditions);
			}else{
				return $conditions;
			}
		}
		return null;
	}
	
	function operations_form(){
		$operators = array('add'=>'Add','substract'=>'Substract','multiply'=>'Multiply');
		return array(
			array(
				'type'=>'element',
				'val'=>'operations_form',
				'options'=>array(
					'operators' => $operators,
				)
			),
		);
		return false;
	}
	function operations(&$eventOptions,$params){
		if(!empty($params['operations']) && is_array($params['operations'])){
			//debug($params);
			$defOpt = array(
				'operations' => array(),
			);
			$opt = array_merge($defOpt,$params);
			$opt['returnModel'] = true;
			list($model,$conditions) = $this->_selectEntriesCond($opt);
			if(!is_null($conditions)){
				$findOpt = array(
					'fields' => array_merge(array('id'),Set::extract('/field',$opt['operations'])),
					'conditions'=>$conditions,
					'recursive'=>-1,
				);
				//debug($findOpt);
				//$this->Event = ClassRegistry::init('Event');
				//$this->Event->debugEventStack($eventOptions);
		
				$entries = $model->find('all',$findOpt);
				App::import('Lib', 'Operations');
				$count = 0;
				foreach($entries as $entry){
					foreach($opt['operations'] as $op){
						$fld = $op['field'];
						$newVal = Operations::simpleOperation($entry[$model->alias][$fld],$op['operator'],$op['value']);
						if($newVal != $entry[$model->alias][$fld]){
							$entry[$model->alias][$fld] = $newVal;
						}else{
							unset($entry[$model->alias][$fld]);
						}
					}
					if(count($entry[$model->alias])>1){//only if there is more than the id in the data
						//debug($entry);
						if($model->save($entry)){
							$count++;
						}
					}
				}
				return $count;
			}
		}
		return false;
	}
	
	function set_form(){
		return array(
			array(
				'type'=>'element',
				'val'=>'set_form',
				'options'=>array(
				)
			),
		);
		return false;
	}
	
	function set(&$eventOptions,$params){
		if(array_key_exists('foreign_key',$params)){
			$params['key'] = $params['foreign_key'];
		}
		//debug($params);
		if(array_key_exists('data',$params)){
			$defOpt = array(
				'data' => array(),
				'updateAll' => false,
			);
			$opt = array_merge($defOpt,$params);
			$opt['returnModel'] = true;
			list($model,$conditions) = $this->_selectEntriesCond($opt);
			if(!is_null($conditions)){
				if($opt['updateAll']){
					$model->updateAll($opt['data'],$conditions);
					return $model->getAffectedRows();
				}else{
					if(count($conditions) == 1 && !empty($conditions[$model->alias.'.'.$model->primaryKey])){
						$entries = (array)$conditions[$model->alias.'.'.$model->primaryKey];
					}else{
						$entries = array_keys($model->find('list',array('conditions'=>$conditions,'recursive'=>-1)));
					}
					//debug($entries);
					$count = 0;
					foreach($entries as $id){
						$data = $opt['data'];
						$data[$model->primaryKey] = $id;
						debug($data);
						$model->create();
						if($model->save($data)){
							$count++;
						}
					}
					return $count;
				}
			}
		}
		return false;
	}	
	
	
}