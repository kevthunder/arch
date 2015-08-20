<?php

class CacheBehavior extends ModelBehavior {

	var $settings;
	var $defaultOptions = array(
		'fields' => array()
	);
	var $defFieldOpt = array(
		'name' => '',
		'association' => array(
			'name'=>'',
			'dependant'=>array(),
			'type'=>'belongsTo',
		),
	);

	function __construct() {
		parent::__construct();
		Cache::config('cacheBehavior', array(
			'engine' => 'File',
			'duration'=> '+1 year',
			'path' => CACHE,
			'prefix' => 'cake_cache_behavior_'
		));
	}
	
	function setup(&$model, $settings = array()) {
		if(!empty($settings) && empty($settings['fields'])){
			$settings['fields'] = $settings;
		}
		$this->settings[$model->alias] = array_merge($this->defaultOptions, (array)$settings);
		
		if(!empty($this->settings[$model->alias]['fields'])){
			$resetAll = array();
			foreach($this->settings[$model->alias]['fields'] as $key => $field){
				if(!is_numeric($key) && empty($field['name'])){
					if(!is_array($field)){
						$field = array('association' =>array('name'=>$field));
					}
					$field['name'] = $key;
				}
				
				$name = $this->setFieldCache($model, array_merge($field,array('dry'=>true)));
				if($name){
					$resetAll[] = $name;
				}
				
			}
			if(!empty($resetAll)){
				$resetData = array_combine($resetAll, array_fill(0, count($resetAll), null));
				//debug($resetData);
				$model->updateAll($resetData,1);
			}
		}
	}
	function isFieldCacheSet(&$model, $name,$className){
		$invalidations = Cache::read($className,'cacheBehavior');
		return !empty($invalidations[$model->name][$name]);
	}
	function setFieldCache(&$model, $name,$opt = null){
		if(is_null($opt)){
			$opt = $name;
		}else{
			$opt['name'] = $name;
		}
		$reset = false;
		$localOpt = array('dry','association','usedField');
		App::import('Lib', 'SetMulti');
		if(!empty($opt['association'])){
			if(!is_array($opt['association']) || SetMulti::isAssoc($opt['association'])){
				$opt['association'] = array($opt['association']);
			}
			foreach($opt['association'] as $assoc){
				$fieldOpt = array_diff_key($opt,array_flip($localOpt));
				$fieldOpt['association'] = $assoc;
				if(isset($fieldOpt['association']) && !is_array($fieldOpt['association'])){
					$fieldOpt['association'] = array('name'=>$fieldOpt['association']);
				}
				$fieldOpt = Set::merge($this->defFieldOpt,$fieldOpt);
				if(!empty($fieldOpt['name'])){
					if(isset($model->{$fieldOpt['association']['type']}[$fieldOpt['association']['name']])){
						$fieldOpt['association'] = Set::merge($model->{$fieldOpt['association']['type']}[$fieldOpt['association']['name']],$fieldOpt['association']);
					}
					if(empty($fieldOpt['association']['className'])){
						$fieldOpt['association']['className'] = $fieldOpt['association']['name'];
					}
					if(!empty($fieldOpt['association']['className'])){
						$className = $fieldOpt['association']['className'];
						$fieldOpt['model'] = $model->name;
						$invalidations = Cache::read($className,'cacheBehavior');
						//debug($fieldOpt);
						if (empty($invalidations[$model->name][$fieldOpt['name']]) || count(array_diff_assoc($invalidations[$model->name][$fieldOpt['name']],$fieldOpt))) {
							$reset = true;
							$invalidations[$model->name][$fieldOpt['name']] = $fieldOpt;
							Cache::write($className,$invalidations,'cacheBehavior');
						}
					}
				}
			}
		}
		if(!empty($opt['usedField'])){
			$invalidations = Cache::read($model->name,'cacheBehavior');
			$invalidations['usedField'][$opt['name']] = (array)$opt['usedField'];
			Cache::write($model->name,$invalidations,'cacheBehavior');
		}
		if(empty($opt['dry'])){
			if($reset){
				$model->updateAll(array($fieldOpt['name']=>null),1);
			}
			return true;
		}elseif($reset){
			return $fieldOpt['name'];
		}
		return false;
	}
	
	function beforeSave(&$model, $options) {
		if(empty($model->id)){
			return true;
		}
		$invalidations = Cache::read($model->name,'cacheBehavior');
		if(!empty($invalidations['usedField'])){
			$cachedFields = $invalidations['usedField'];
			unset($invalidations['usedField']);
			foreach($cachedFields as $cachedField => $usedFields){
				if(!array_key_exists($cachedField ,$model->data[$model->alias]) || !is_null($cachedField)){
					$invalid = count(array_intersect(array_keys($model->data[$model->alias]),$usedFields));
					if($invalid){
						$model->data[$model->alias][$cachedField] = null;
					}
				}
			}
		}
		//debug($invalidations);
		if(!empty($invalidations)){
			$model->Behaviors->attach('Util');
			foreach($invalidations  as $modelName => $fields){
				if(!empty($modelName) && !empty($fields)){
					$assocOpt = array();
					$toReset = array();
					foreach($fields  as $fieldOpt){
						$i = 0;
						$invalid = 
							empty($fieldOpt['dependant']) || 
							(!empty($options['fieldList']) && array_intersect($options['fieldList'],$fieldOpt['dependant'])) || 
							array_diff(array_keys($model->data[$model->alias]),$fieldOpt['dependant']);
						if($invalid){
							while($i< count($assocOpt) && $assocOpt[$i]['association'] != $fieldOpt['association']) {
								$i++;
							}
							if($i >= count($assocOpt)){
								$assocOpt[$i]['association'] = $fieldOpt['association'];
							}
							unset($fieldOpt['association']);
							$assocOpt[$i]['fields'][$fieldOpt['name']] = $fieldOpt;
						}
					}
					//debug($assocOpt);
					if(!empty($assocOpt)){
						$invalidatedModel = ClassRegistry::init($modelName);
						foreach($assocOpt as $aOpt){
							$customMethod = 'reverse'.ucFirst($aOpt['association']['type']).'Association';
							//debug($customMethod);
							if($model->hasMethod($customMethod)){
								$invalids = $model->{$customMethod}($modelName, $aOpt['association'], array($model->data), array('errorReturn'=>true,'mode'=>'list'));
							}else{
								$invalids = $model->reverseAssociation($aOpt['association']);
							}
							$resetData = array_combine(array_keys($aOpt['fields']), array_fill(0, count($aOpt['fields']), null));
							//debug($resetData);
							if($invalids === true){
								//debug('Reset all');
								$invalidatedModel->updateAll($resetData,1);
							}elseif(!empty($invalids)){
								$resetDataId = $invalidatedModel->extractIds($invalids);
								//debug($resetDataId);
								$invalidatedModel->updateAll($resetData,array($invalidatedModel->alias.'.id'=>$resetDataId));
							}
						}
					}
				}
			}
		}
		return true;
		//return false;
	}
	
	function reverseAssociation(&$model,$foreignClass,$assoc, $data, $opt){
		return true;
	}
	
	
}
