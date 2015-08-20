<?php

class InheritorBehavior extends ModelBehavior {

	var $settings;
	var $defaultOptions = array(
		'fields' => array(),
		'fetch' => true,
	);

	function setup(&$model, $settings = array()) {
		if(!isset( $settings['fields'] )){
			$settings = array('fields'=>$settings);
		}
		$this->settings[$model->alias] = array_merge($this->defaultOptions, (array)$settings);
	}
	
	function autoInheritFetch(&$model, $val = true){
		$this->settings[$model->alias]['fetch'] = $val;
	}
	
	function _formatFieldsOpt($fieldsSetting){
		$defOpt = array(
			'alias' => null
		);
		$formated = array();
		foreach($fieldsSetting as $key => $opt){
			if(!is_array($opt)){
				$opt = array('alias' => $opt);
			}
			$opt = array_merge($defOpt,$opt);
			$formated[$key] = $opt;
		}
		return $formated;
	}
	
	function getIneritedFields(&$model, $data, $options = array()){
		$defOpt = array(
			'fields'=>null,
			'fetchAssociations'=>true,
		);
		$opt = array_merge($defOpt,$options);
		//debug($opt);
		if(!empty($this->settings[$model->alias]['fields']) && is_array($data)){
			App::import('Lib', 'SetMulti');
			if(isset($data[$model->alias])){
				$myData = &$data[$model->alias];
			}else{
				$myData = &$data;
			}
			$originalData = $myData;
			$fieldsOpt = $this->_formatFieldsOpt($this->settings[$model->alias]['fields']);
			if(!empty($opt['fields'])){
				$fieldsOpt = array_intersect_key($fieldsOpt,array_flip($opt['fields']));
			}
			$fieldsOpt = array_intersect_key($fieldsOpt,array_merge(array_filter($myData,'is_null'),array_filter($myData,'is_array')));
			if(!empty($fieldsOpt)){
				$fieldsOpt = SetMulti::group($fieldsOpt,'alias');
				//debug($fieldsOpt);
				foreach($fieldsOpt as $alias => $iopt){
					$hdata = null;
					//debug($model->{$alias}->name ."==".$model->name);
					if(isset($model->{$alias}) && $model->{$alias}->name == $model->name && $model->Behaviors->attached('Tree')){
						//tree inerit
						$d = $myData;
						if(!isset($d['lft']) || !isset($d['rght'])){
							$d = $model->read(null,$myData['id']);
						}
						$model->recursive = -1;
						$tmp = $this->settings[$model->alias]['fetch'];
						$this->settings[$model->alias]['fetch'] = false;
						$res = $model->find('all', array(
							'fields'=>array_merge(array('id'),array_keys($iopt)),
							'conditions'=>array(
								'lft'>$d['lft'],
								'rght'<$d['rght']
							),
							'order' => 'lft DESC'
						));
						//debug($res);
						$this->settings[$model->alias]['fetch'] = $tmp;
						$hdata = array();
						foreach($res as $item){
							$hdata = array_merge($hdata,array_filter($item[$model->alias]));
						}
						//debug($hdata);
					}else{
						if(isset($data[$alias])){
							$hdata = $data[$alias];
						}elseif(isset($data[$model->alias][$alias])){
							$hdata = $data[$model->alias][$alias];
						}
						//debug($hdata);
						if(isset($model->{$alias}) && $model->{$alias}->Behaviors->attached('Inheritor') && !empty($hdata)){
							$hdata = $model->{$alias}->getIneritedFields($hdata,array('fields'=>array_keys($iopt),'fetchAssociations'=>false));
						}
					}
					foreach($iopt as $key => $val){
						if(!isset($hdata[$key])){
							$myData[$key] = null;
						}elseif(is_array($myData[$key]) ){
							if(!empty($hdata[$key])){
								$myData[$key] = SetMulti::merge2($myData[$key],$hdata[$key]);
							}
						}else{
							$myData[$key] = $hdata[$key];
						}
					}
					if($opt['fetchAssociations']){
						if(!empty($model->lastAssociations['belongsTo'])){
							$associationFields = SetMulti::extractKeepKey('foreignKey',$model->lastAssociations['belongsTo']);
							$toFetch = array_intersect($associationFields,array_keys($iopt));
							foreach($toFetch as $aalias => $field){
								if($originalData[$field] != $myData[$field]){
									$findOpt = $model->lastAssociations['belongsTo'][$aalias];
									if(isset($model->{$aalias})){
										$amodel = $model->{$aalias};
									}else{
										$amodel = ClassRegistry::init($findOpt['className']); 
									}
									unset($findOpt['className'],$findOpt['foreignKey']);
									$findOpt['conditions'][$amodel->alias.'.'.$amodel->primaryKey] = $myData[$field];
									//debug($findOpt);
									$res = $amodel->find('first',$findOpt);
									$amodel->Behaviors->attach('Util');
									$res = $amodel->dataToContainedRelations($res);
									$data[$aalias] = $res;
								}
							}
						}
					}
				}
			}
		}
		return $data;
	}
	
	function afterFind(&$model, $results, $primary){
		if($this->settings[$model->alias]['fetch']){
			foreach($results as &$res){
				$res = $model->getIneritedFields($res);
			}
		}
		return $results;
	}
	
	function assocAfterFind(&$model, $results, $primary) {
		if($this->settings[$model->alias]['fetch']){
			//debug($results);
			if(!isset($results[0])){
				$results = $model->getIneritedFields($results);
			}
			/*foreach($results as &$res){
				$res = $model->getIneritedFields($res);
			}*/
		}
		return $results;
	}
}

?>