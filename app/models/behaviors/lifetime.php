<?php

class LifetimeBehavior extends ModelBehavior {

	var $settings;
	var $defaultOptions = array(
		'delete' => true,
		'disable' => true,
		'timeout' => 0,
		'start_field' => 'start_time',
		'end_field' => 'end_time',
		'active_field' => 'active',
		'allways_postprocess' => false,
		'active' => true,
		'valid' => false,
	);
	
	
	function setup(&$model, $settings = array()) {
		$this->settings[$model->alias] = array_merge($this->defaultOptions, (array)$settings);
		$this->settings[$model->alias]['valid'] =($model->hasField($this->settings[$model->alias]['start_field']) || $model->hasField($this->settings[$model->alias]['end_field']));
	}
	
	function _getQueryTime(&$model, $formated = true){
		if($model->Behaviors->attached('Planned')){
			$time = $model->getQueryTime(null,$formated);
		}else{
			$time = mktime();
		}
		if($formated && is_numeric($time)){
			$format = $model->getDataSource()->columns['datetime']['format'];
			$time = date($format,$time);
		}
		return $time;
	}
	
	function _modifTime(&$model,$time,$modif){
		$format = $model->getDataSource()->columns['datetime']['format'];
		$time = strtotime($time);
		$time += $modif;
		return date($format,$time);
	}
	
	function checkLifetime(&$model, $val = null){
		if(!is_null($val)){
			$this->settings[$model->alias]['active'] = $val;
			$this->settings[$model->alias]['valid'] = ($model->hasField($this->settings[$model->alias]['start_field']) || $model->hasField($this->settings[$model->alias]['end_field']));
		}
		return $this->settings[$model->alias]['active'] && $this->settings[$model->alias]['valid'];
	}
	
	function beforeFind(&$model, $queryData){
		$postprocess = $this->settings[$model->alias]['delete'] || $this->settings[$model->alias]['disable'];
		if($this->checkLifetime($model) && (!empty($queryData['fields']) || !empty($queryData['limit']) || !$postprocess)){
			$ef = $this->settings[$model->alias]['end_field'];
			$neededfields = array('id',$ef);
			if($this->settings[$model->alias]['allways_postprocess']){
				if(empty($queryData['fields'])){
					foreach($neededfields as $needed){
						if(!count(array_intersect($queryData['fields'],array($needed,$model->alias.'.'.$needed)))){
							$queryData['fields'][] = $model->alias.'.'.$needed;
							break;
						}
					}
				}
			}else{
				$postprocess = $postprocess && empty($queryData['limit']);
				if($postprocess){
					foreach($neededfields as $needed){
						if(!count(array_intersect((array)$queryData['fields'],array($needed,$model->alias.'.'.$needed)))){
							$postprocess = false;
							break;
						}
					}
				}
				if(!$postprocess){ // postprocess impossible or insecure
					$time = $this->_getQueryTime(&$model);
					$sf = $this->settings[$model->alias]['start_field'];
					$timeout = $this->settings[$model->alias]['timeout'];
					App::import('Lib', 'SetMulti'); 
					if($model->hasField($sf) && (empty($queryData['conditions']) || !count(SetMulti::pregFilterKey('/'.$sf.'/',$queryData['conditions']))) ){
						$queryData['conditions'][] = array('or'=>array(
							$model->alias.'.'.$sf.' IS NULL',
							$model->alias.'.'.$sf.' <=' => $time
						));
					}
					if($model->hasField($ef) && (empty($queryData['conditions']) || !count(SetMulti::pregFilterKey('/'.$ef.'/',$queryData['conditions']))) ){
						$queryData['conditions'][] = array('or'=>array(
							$model->alias.'.'.$ef.' IS NULL',
							$model->alias.'.'.$ef.' >=' => $this->_modifTime($model,$time,-$timeout),
						)); 
					}
				}
			}
		}
		return $queryData;
	}
	
	function _applyExpired(&$model,$expired){
		if(empty($expired)){
			return;
		}
		$tmp = $this->settings[$model->alias]['active'];
		$this->settings[$model->alias]['active'] = false;
		$model->Behaviors->attach('Util');
		//$expired = $model->unifiedResult($expired);
		$expiredIds = $model->extractIds($expired);
		$af = $this->settings[$model->alias]['active_field'];
		foreach($expiredIds as $id){
			if($this->settings[$model->alias]['delete']){
				$model->delete($id);
			}elseif($this->settings[$model->alias]['disable']){
				$model->save(array('id'=>$id,$af=>0));
			}
		}
		$this->settings[$model->alias]['active'] = $tmp;
	}
	
	function afterFind(&$model, $results, $primary){
		if($this->checkLifetime($model)){
			$time = $this->_getQueryTime(&$model,false);
			
			if(!Set::numeric(array_keys($results))){
				$tmp = array(&$results);
				$myResults =& $tmp;
			}else{
				$myResults =& $results;
			}
			$expired = array();
			$timeout = $this->settings[$model->alias]['timeout'];
			$ef = $this->settings[$model->alias]['end_field'];
			$i = 0;
			while($i < count($myResults) ){
				$resRoot = $myResults[$i];
				if(isset($resRoot[$model->alias])){
					$res = $resRoot[$model->alias];
				}else{
					$res = $resRoot;
				}
				if(isset($res[$ef]) && strtotime($res[$ef]) < strtotime($time) - $timeout){
					if(strtotime($res[$ef]) < mktime() - $timeout){
						$expired[] = $resRoot;
					}
					array_splice($myResults,$i,1);
				}else{
					$i++;
				}
			}
			$this->_applyExpired($model,$expired);
		}
		return $results;
	}
	
	
}