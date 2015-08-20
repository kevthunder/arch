<?php

class InvalidedBehavior extends ModelBehavior {

	var $settings;
	var $defaultOptions = array(
		'onCreate' => true,
		'fields' => true
	);

	function setup(&$model, $settings = array()) {
		$this->settings[$model->alias] = array_merge($this->defaultOptions, (array)$settings);

		if(!isset($model->Invalidation)){
			if (PHP5) {
				$model->Invalidation = ClassRegistry::init('Invalidation');
			} else {
				$model->Invalidation =& ClassRegistry::init('Invalidation');
			}
		}
		if(!$model->Behaviors->attached('Node')){
			$model->Behaviors->attach('Node');
		}
	}
	
	function invalidateEntry(&$model, $id = null, $fields = null){
		if(is_null($id)){
			$id = $model->id;
		}
		if(empty($id)){
			return false;
		}
		$node = $model->node($model->myNodeRef($id),false,true);
		if(!empty($node)){
			if(empty($fields)){
				$fields = array(0=>null);
			}
			$tmp = $model->Invalidation->belongsTo;
			$res = true;
			$model->Invalidation->belongsTo = array();
			App::import('Lib', 'TimeUtil'); 
			foreach((array)$fields as $field){
				$data = array('node_id'=>$node['Node']['id'],'field'=>$field);
				$opt = array('fields'=>array('id','id'),'conditions'=>array('node_id ' => $node['Node']['id']));
				if(!empty($field)){
					$opt['conditions']['field'] = $field;
				}
				$old = $model->Invalidation->find('list',$opt);
				if(!empty($old)){
					$old = array_keys($old);
					//debug($old);
					$data['id'] = array_shift($old);
					$model->Invalidation->deleteAll(array('id' => $old));
				}
				$model->Invalidation->create();
				$data['time'] = TimeUtil::relTime();
				if(!$model->Invalidation->save($data)){
					$res = false;
					break;
				}
			}
			$model->Invalidation->belongsTo = $tmp;
			return $res;
			//$data = array('node_id'=>$node['Node']['id'],'fields'=>$fields);
			//return $model->Invalidation->save($data);
		}
		return false;
	}
	
	function afterSave(&$model, $created) {
		$opt = $this->settings[$model->alias];
		if ($created) {
			if ($opt['onCreate']) {
				$model->invalidateEntry();
			}
		}else{
			if (!empty($opt['fields'])) {
				$fields = null;
				if ($opt['fields'] !== true) {
					$fields = array_keys($model->data);
					$fields = array_intersect((array)$opt['fields'],$fields);
				}
				$model->invalidateEntry(null,$fields);
			}
		}
		
		return true;
	}
	
	
}
