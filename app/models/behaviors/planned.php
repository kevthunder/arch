<?php

class PlannedBehavior extends ModelBehavior {

	var $settings;
	var $defaultOptions = array(
		'needsUpdated' => false, //break plannig if updatedField is missing
		'extendFind' => true, //get plannings on find
		'saveAndUpdate' => true,
		'updatedField' => 'synced',
		'excludeFields' => array(),
		'equations' => array(),
		'equationsOnly' => false,
	);
	var $defEqOpt = array(
		'field' => null,
		'fn' => 'linear',
		'cond' => null,
	);
	var $queryTime = null;

	function setup(&$model, $settings = array()) {
		$this->settings[$model->alias] = array_merge($this->defaultOptions, (array)$settings);

		if(!isset($model->Planning)){
			if (PHP5) {
				$model->Planning = ClassRegistry::init('Planning');
			} else {
				$model->Planning =& ClassRegistry::init('Planning');
			}
		}
		if(!$model->Behaviors->attached('Node')){
			$model->Behaviors->attach('Node');
		}
		$model->Behaviors->attach('Containable');
	}
	
	function getQueryTime(&$model, $default = null, $formated = true){
		if(!empty($this->queryTime)){
			$time = $this->queryTime;
		}elseif(!is_null($default)){
			$time = $default;
		}else{
			$time = mktime();
		}
		if($formated && is_numeric($time)){
			$format = $model->getDataSource()->columns['datetime']['format'];
			$time = date($format,$time);
		}
		return $time;
	}
	
	
	/*function bindPlannings(&$model, $add = true, $reset = true){
		if($add){
			$model->bindNodes($add,$reset);
			$model->bindModel(
				array('hasMany' => array(
						'Planning' => array(
							'className' => 'Planning',
							'foreignKey' => false,
							'conditions' => array(
								'Planning.id = Node.id',
							),
							'order' => 'Planning.date ASC'
						)
					)
				),$reset
			);
		}else{
			$model->unbindModel(array('hasOne' => array('Planning')));
		}
	}
	*/
	
	
	function findAt(&$model, $method = 'all', $options = array (), $time = NULL){
		if(isset($options['time'])){
			$time = $options['time'];
			unset($options['time']);
		}
		if(is_numeric($time)){
			$format = $model->getDataSource()->columns['datetime']['format'];
			$time = date($format,$time);
		}
		$this->queryTime = $time;
		$res = $model->find($method,$options);
		$this->queryTime = null;
		return $res;
	}
	
	function getChanges(&$model, $data=null, $time = NULL, $fields = null, &$plans = array()){
		if(empty($data)){ return array(); }
		
		$model->Behaviors->attach('Util');
		list($data, $oldFormat) = $model->unifiedResult($data, null, null, true);
		
		$ids = array();
		foreach($data as $row){
			if(!empty($row[$model->alias][$model->primaryKey])){
				$ids[] = $row[$model->alias][$model->primaryKey];
			}
		}
		$cond = array($model->alias.'.id'=>$ids);
		$mapping = array_flip($ids);
		if(!is_null($fields)){
			$cond['Planning.field'] = $fields;
		}
		$plannings = $this->_getPlanningsToUpdate($model, $cond, $time);
		//debug($plannings);
		$res = array();
		foreach($plannings as $plan){
			if($plan['Planning']['applicable']){
				$res[$mapping[$plan[$model->alias][$model->primaryKey]]][$model->alias][$plan['Planning']['field']] = $plan['Planning']['value'];
			}
			$plans['Planning'][] = $plan;
		}
		
		foreach($data as $rkey => $row){ //_getEquationsToUpdate does not need database connection and can be called for each entry
			$equations = $this->_getEquationsToUpdate($model, $data, $time);
			if(!empty($equations)){
				foreach($equations as $eq){
					if($eq['applicable']){
						$res[$rkey][$model->alias][$eq['field']] = $eq['newValue'];
					}
					$plans['Equation'][] = $eq;
				}
			}
		}
		
		$res = $model->unifiedResult($res, null, $oldFormat);
		
		//debug($res);
		return $res;
	}
	
	function getConflictSolution(&$model, $curval, $field, $time = null, $id = null, $options = array()){
		$defaultOptions = array(
			'node' => null,
			'validationExclude' => null,
		);
		$options = array_merge($defaultOptions, (array)$options);
		if(is_null($time)){
			$time = mktime();
		}
		if(is_numeric($time)){
			$format = $model->getDataSource()->columns['datetime']['format'];
			$time = date($format,$time);
		}
		if(!empty($options['node'])){
			$node = $options['node'];
		}else{
			if(is_null($id)){
				$id = $model->id;
			}
			$node = $model->node($model->myNodeRef($id),false);
		}
		if(isset($node['Node'])){
			$node = $node['Node'];
		}
		$model->Planning->Behaviors->attach('Util');
		$excludeIds = $model->Planning->extractIds($options['validationExclude']);
		$model->Planning->recursive = -1;
		$conditions = array('node_id'=>$node['id'],'date >'=>$time);
		if(!empty($excludeIds)){
			$conditions['not']['id']=$excludeIds;
		}
		$subsequants = $model->Planning->find('all',array('conditions'=>$conditions,'order'=>array('date')));
		return $this->_getConflictSolution($model, $curval, $subsequants, $options);
	}
	
	function _getConflictSolution(&$model, $curval, $subsequants = null,$options = array()){
		$defaultOptions = array(
			'allowSubsequant' => true,
			'override' => true,
			'absolute' => false,
		);
		$options = array_merge($defaultOptions, (array)$options);
		$actions = array('save'=>array(),'delete'=>array());
		if(!empty($subsequants)){
			if(!$options['allowSubsequant']){
				return false;
			}
			foreach($subsequants as $sub){
				if(!empty($sub['Planning']['absolute'])){
					break;
				}
				if(empty($sub['Planning']['operation'])){
					if(!$options['override']){
						$actions['delete'][$sub['Planning']['id']] = $sub['Planning'];
					}else{
						return false;
					}
				}else{
					App::import('Lib', 'Operations');
					$curval = Operations::applyOperation($sub['Planning']['operation'],$curval);
					$plan = array(
						'id' => $sub['Planning']['id'],
						'value' => $curval,
					);
					$actions['save'][] = $plan;
				}
			}
		}
		return $actions;
	}
	
	function savePlanning(&$model, $data, $time, $options = array()){
		if(is_numeric($time)){
			$format = $model->getDataSource()->columns['datetime']['format'];
			$time = date($format,$time);
		}
		$defaultOptions = array(
			'stringOperators' => true,
			'break' => true,
			'absolute' => false,
			'requery' => true,
			'context' => null,
			'dry' => false,
			'validationExclude' => null,
		);
		$options = array_merge($defaultOptions, (array)$options);
		//debug($options);
		if(!empty($data[$model->alias])){
			$myData = $data[$model->alias];
		}else{
			$myData = $data;
		}
		App::import('Lib', 'Operations'); 
		if($options['stringOperators']){
			$myData = Operations::parseStringOperations($myData);
		}
		//debug($myData);
		$cacheData = $model->data;
		if(isset($myData['id'])){
			$model->id = $myData['id'];
		}
		if(!empty($model->id)){
			if($options['requery']){
				$model->Behaviors->attach('Containable');
				$oldData = $model->findAt('first',array('conditions'=>array($model->alias.'.id'=>$model->id),'time'=>$time,'contain'=>array('Node')));
			}else{
				$oldData = $cacheData;
			}
			//debug($oldData);
			if(isset($oldData['Node'])){
				$node = $oldData['Node'];
			}else{
				$node = $model->node(null,false);
				$node = $node['Node'];
			}
			$newData = Operations::applyOperations($myData,$oldData[$model->alias]);
			//$model->validates(array('time'=>$time));   // à faire
			//debug($newData);
			$diffData = array_diff_assoc($newData,$oldData[$model->alias]);
			//debug($diffData);
			$plans = array();
			$to_delete = array();
			foreach($diffData as $field => $val){
				$solution = $model->getConflictSolution($val, $field, $time, null, array_merge($options,array('node'=>$node)));
				if($solution === false){
					if($options['break']){
						return false;
					}else{
						continue;
					}
				}else{
					$plans = array_merge($plans, $solution['save']);
					$to_delete = array_merge($to_delete, $solution['delete']);
				}
				$plan = array(
					'field' => $field,
					'node_id' => $node['id'],
					'active' => 1,
					'value' => $val,
					'date' => $time,
					'absolute' => $options['absolute'],
					'context' => $options['context'],
				);
				if(is_array($myData[$field]) && !empty($myData[$field]['operator'])){
					$plan['operation'] = $myData[$field];
				}
				$plans[] = $plan;
			}
			
			//debug($this->buildPlanifiedFollowingLinks($model, $plans,$to_delete, array('dry'=>true)));
			if(!$options['dry']){
				$this->savePlanningChanges($model, array('save'=>$plans, 'delete'=>$to_delete));
			}else{
				return array('save'=>$plans,'delete'=>$to_delete);
			}
			return true;
		}
		return false;
	}
	
	function savePlanningChanges(&$model, $operations){
		$default = array(
			'save' => null,
			'delete' => null,
			'linksModif' => true,
		);
		$opt = array_merge($default, (array)$operations);
		//////// get links modif ////////
		if(is_array($opt['linksModif'])){
			$linksModif = $opt['linksModif'];
		}elseif($opt['linksModif']){
			$linksModif = $this->buildPlanifiedFollowingLinks($model, $opt['save'], $opt['delete'],array("dry"=>true));
			if($linksModif === false){
				return false;
			}
		}
		//////// save modif and added ////////
		if(!empty($opt['save'])){
			foreach($opt['save'] as &$plan){
				$model->Planning->create();
				if(!$model->Planning->save($plan)){
					return false;
				}else{
					if(isset($plan['Planning'])){
						$update =& $plan['Planning'];
					}else{
						$update =& $plan;
					}
					$update['id'] = $model->Planning->id;
				}
			}
		}
		//////// delete ////////
		if(!empty($opt['delete'])){
			$model->Planning->Behaviors->attach('Util');
			$deleteIds = $model->Planning->extractIds($opt['delete']);
			$model->Planning->deleteAll(array('Planning.id'=>$deleteIds));
		}
		
		//////// apply links modif ////////
		if(!empty($linksModif['save'])){
			foreach($linksModif['save'] as $link){
				if(isset($link['linkedToSave'])){
					App::import('Lib', 'SetMulti');
					$link = SetMulti::replaceTree('%planId%',$opt['save'][$link['linkedToSave']]['id'],$link);
				}
				$model->NodeLink->create();
				if(!$model->NodeLink->save($link)){
					return false;
				}
			}
		}
		if(!empty($linksModif['delete'])){
			$model->NodeLink->Behaviors->attach('Util');
			$deleteIds = $model->NodeLink->extractIds($linksModif['delete']);
			$model->NodeLink->deleteAll(array('NodeLink.id'=>$deleteIds));
		}
		return true;
	}
	
	function buildPlanifiedFollowingLinks(&$model, $add=array(), $delete=array(), $options = array()){
		$defaultOptions = array(
			'dry' => false,
		);
		$options = array_merge($defaultOptions, (array)$options);
		if($model->Behaviors->attached('NodeLinked')){
			$allModif = array();
			if(!empty($add)){
				foreach($add as $key => $plan){
					if(isset($plan['Planning'])){
						$plan = $plan['Planning'];
					}
					$allModif[] = array('Planning'=>$plan,'Action'=>array('name'=>'Save','at'=>$key));
				}
			}
			if(!empty($delete)){
				foreach($delete as $plan){
					if(!is_array($plan)){
						$plan = $model->Planning->read(null,$plan); // aaaaaaaaaaarrrrrrrrrgggggggggggg, utilise probablement trop de ressources
					}
					if(isset($plan['Planning'])){
						$plan = $plan['Planning'];
					}
					$allModif[] = array('Planning'=>$plan,'Action'=>'Delete');
				}
			}
			$applicableModif = array();
			foreach($allModif as $modif){
				$field = $modif['Planning']['field'];
				$applicable = false;
				if(isset($applicableModif[$field])){
					$applicable = true;
				}else{
					$opt = $model->getFieldFollowOptions($modif['Planning']['field']);
					if(!empty($opt)){
						$applicableModif[$field]['Options'] = $opt;
						$applicable = true;
					}
				}
				if($applicable){
					$modif['Options'] = $opt;
					if($opt['owner'] == 'both'){
						$applicableModif[$field]['owner'][] = $modif;
						$applicableModif[$field]['owned'][] = $modif;
					}else{
						$applicableModif[$field][$opt['owner']][] = $modif;
					}
				}
			}
			//debug($applicableModif);
			unset($allModif, $modif, $opt, $plan, $add, $delete, $field, $applicable); // free ressources
			
			$operations = array();
			App::import('Lib', 'SetMulti'); 
			foreach($applicableModif as $field => $fieldGroup){
				foreach($fieldGroup as $owner => $modifs){
					if($owner != 'Options'){
						$add = $this->_buildFollowLinksPlanGroup(&$model, $owner, $fieldGroup['Options'], $modifs);
						if($add === false){
							return false;
						}
						$operations = SetMulti::merge2($operations,$add);
					}
				}
			}
			if(!$options['dry']){
				if(!empty($operations['save'])){
					foreach($operations['save'] as $link){
						$model->NodeLink->create();
						if(!$model->NodeLink->save($link)){
							return false;
						}
					}
				}
				$deleteIds = array();
				if(!empty($operations['delete'])){
					foreach($operations['delete'] as $link){
						$deleteIds[] = $link['id'];
					}
					$model->NodeLink->deleteAll(array('NodeLink.id'=>$deleteIds));
				}
			}else{
				return $operations;
			}
		}
		return false;
	}
	
	function _buildFollowLinksPlanGroup(&$model, $owner, $followOpt, $modifs){
		$dates = array();
		foreach ($modifs as $key => $row) {
			$dates[$key]  = strtotime($row['Planning']['date']);
		}
		$format = $model->getDataSource()->columns['datetime']['format'];
		$min = date($format,min($dates));
		$max = date($format,max($dates));
		array_multisort($dates, SORT_ASC, $modifs);
		$neededFields = array('NodeLink.id','NodeLink.start','NodeLink.end','NodeLink.context');
		$sequence = $model->getDirectLinks(array(
			'fields' => $neededFields,
			'type'=>$followOpt['type'],
			'owner'=>$owner,
			'conditions'=>array(
				'NodeLink.start >=' => $min,
				'NodeLink.start <=' => $max,
			),
			'order' => array('NodeLink.start ASC'),
			'time' => false,
		));
		foreach($sequence as $key => $link){
			$sequence[$key]['Original'] = $link['NodeLink'];
		}
		//debug($sequence);
		$prev = $model->getDirectLinks(array(
			'mode'=>'first',
			'fields' => $neededFields,
			'type'=>$followOpt['type'],
			'owner'=>$owner,
			'conditions'=>array(
				array('or'=>array(
					'NodeLink.start <' => $min,
					'NodeLink.start IS NULL'
				)),
				array('or'=>array(
					'NodeLink.end >=' => $min,
					'NodeLink.end IS NULL'
				)),
			),
			'order' => array('NodeLink.end IS NULL DESC','NodeLink.end DESC'),
			'time' => false,
		));
		$next = $model->getDirectLinks(array(
			'mode'=>'first',
			'fields' => $neededFields,
			'type'=>$followOpt['type'],
			'owner'=>$owner,
			'conditions'=>array(
				'NodeLink.start >' => $max,
			),
			'order' => array('NodeLink.start ASC'),
			'time' => false,
		));
		//debug($next);
		
		$save = array();
		$delete = array();
		foreach($modifs as $modif){
			$i = 0;
			$insertAt = 0;
			if(isset($modif['Planning']['id'])){
				$context = 'Planning'.$modif['Planning']['id'].ucfirst($owner);
			}else{
				$context = 'Planning%planId%'.ucfirst($owner);
			}
			$existingKey = null;
			while($i < count($sequence) && is_null($existingKey)){
				if(isset($modif['Planning']['id']) && $sequence[$i]['NodeLink']['context'] == $context){
					$existingKey = $i;
				}
				if(strtotime($sequence[$i]['NodeLink']['start']) <= strtotime($modif['Planning']['date'])){
					$insertAt=$i+1;
				}
				$i++;
			}
			if(is_null($existingKey)){
				if($modif['Action'] != 'Delete'){
					$target_ref = array('foreign_key'=>$modif['Planning']['value'],'model'=>$followOpt['className']);
					$new = $model->linkTo($followOpt['type'], $target_ref, null, $owner, array('context'=>$context,'dry'=>true));
					if(!empty($new)){
						$new['start'] = $modif['Planning']['date'];
						if(!isset($modif['Planning']['id'])){
							$new['linkedToSave'] = $modif['Action']['at'];
						}
						$new = array('NodeLink'=>$new);
						array_splice( $sequence, $insertAt, 0 , array($new));
					}else{
						return false;
					}
				}
			}elseif($modif['Action'] == 'Delete'){
				$delete[] = $sequence[$existingKey]['NodeLink'];
				array_splice( $sequence, $existingKey, 1);
			}
		}
		if($prev){
			$prev['Original'] = $prev['NodeLink'];
			array_unshift($sequence,$prev);
		}
		if($next){
			$next['Original'] = $next['NodeLink'];
			array_push($sequence,$next);
		}
		//debug($sequence);
		for($i = 0;$i < count($sequence);$i++){
			if($i+1 < count($sequence)){
				$sequence[$i]['NodeLink']['end'] = $sequence[$i+1]['NodeLink']['start'];
			}elseif(empty($next)){
				$sequence[$i]['NodeLink']['end'] = null;
			}
			if(!isset($sequence[$i]['Original'])){
				$save[] = $sequence[$i]['NodeLink'];
			}else{
				$diff = array_diff_assoc($sequence[$i]['NodeLink'],$sequence[$i]['Original']);
				if(!empty($diff)){
					$diff['id'] = $sequence[$i]['NodeLink']['id'];
					$save[] = $diff;
				}
			}
		}
		//debug($sequence);
		return array('save'=>$save,'delete'=>$delete);
	}
	
	function _getPlanningsToUpdate(&$model, $conditions = array(), $time = null){
		if(empty($time)){
			$time = mktime();
		}
		if(is_numeric($time)){
			$format = $model->getDataSource()->columns['datetime']['format'];
			$time = date($format,$time);
		}
		//$model->Behaviors->attach('Containable');
		//$model->bindPlannings();
		$findOpt = array(
			'fields'=>array(
				$model->alias.'.id',
				'Node.id',
				'Planning.id',
				'Planning.field',
				'Planning.value',
				'Planning.date',
			),
			'conditions'=>array_merge($conditions,array(
			)),
			'joins'=>array(
					array(
						'type'=>'INNER',
						'table'=>$model->Node->useTable,
						'alias' => 'Node',
						'conditions' => array(
							'Node.model' => $model->name,
							'Node.foreign_key = '.$model->alias.'.'.$model->primaryKey
						)
					),
					array(
						'type'=>'INNER',
						'table'=>$model->Planning->useTable,
						'alias' => 'Planning',
						'conditions' => array(
							'Planning.node_id = Node.id',
							'Planning.date <=' => $time,
						)
					)
				),
			'order'=>array('Planning.date ASC'),
			'recursive'=>-1,
		);
		
		$upField = $this->settings[$model->alias]['updatedField'];
		if($model->hasField( $upField )){
			$findOpt['fields'][] = $model->alias.'.'.$upField;
		}elseif($this->settings[$model->alias]['needsUpdated']){
			return false;
		}
		$tmp = $this->settings[$model->alias]['extendFind'];
		$this->settings[$model->alias]['extendFind'] = false;
		$plannings = $model->find('all',$findOpt);
		//debug($plannings);
		$this->settings[$model->alias]['extendFind'] = $tmp;
		$upField = $this->settings[$model->alias]['updatedField'];
		foreach($plannings as &$plan){
			$plan['Planning']['applicable'] = (
				!isset($plan[$model->alias][$upField])
				|| is_null($plan[$model->alias][$upField])
				|| strtotime($plan[$model->alias][$upField]) < strtotime($plan['Planning']['date'])
			);
		}
		return $plannings;
	}
	
	function _getEquationsToUpdate(&$model, $data = array(), $time = null){
		$eqs = array();
		$equations = $this->settings[$model->alias]['equations'];
		if(empty($equations) || empty($data)){
			return $eqs;
		}
		$time = strtotime($time);
		$model->Behaviors->attach('Util');
		$data = $model->unifiedResult($data,null,array('multiple'=>false,'named'=>false));
		$upField = $this->settings[$model->alias]['updatedField'];
		if(array_key_exists($upField,$data)){
			$oldTime = strtotime($data[$upField]);
			if(empty($oldTime)){
				$oldTime = $time;
			}
			foreach($equations as $key => $eq){
				if(!is_array($eq)){
					$eq = array('multi'=>$eq);
				}
				if(empty($eq['field']) && !is_numeric($key)){
					$eq['field'] = $key;
				}
				$eq['applicable'] = true;
				$eq = array_merge($this->defEqOpt,$eq);
				if(empty($eq['field']) || !array_key_exists($eq['field'],$data)){
					$eq['applicable'] = false;
				}
				if($time == $oldTime){
					$eq['applicable'] = false;
				}
				$eq['target'] = $data;
				$eq['curValue'] = $data[$eq['field']];
				$eq['oldTime'] = $oldTime;
				$eq['newTime'] = $time;
				if($eq['applicable'] && !empty($eq['cond'])){
					App::import('Lib', 'SetMulti');
					if(!SetMulti::testCond($eq['cond'], $data)){
						$eq['applicable'] = false;
					}
				}
				if($eq['applicable']){
					if(is_string($eq['fn']) && method_exists($this,'_eq_'.$eq['fn'])){
						$res = $this->{'_eq_'.$eq['fn']}($eq);
						$eq['newValue'] = $res;
					}else{
						$eq['applicable'] = false;
					}
				}
				$eqs[] = $eq;
			}
		}else{
			return false;
		}
		//debug($eqs);
		return $eqs;
	}
	
	function _eq_linear($eq){
		$def = array(
			'multi' => 1,
		);
		$eq = array_merge($def,$eq);
		return (int)$eq['curValue'] + ($eq['newTime'] - $eq['oldTime']) * $eq['multi'];
	}
	
	function updateNow(&$model, $conditions = array(), $time = null){
		if(empty($time)){
			$time = mktime();
		}
		if(is_numeric($time)){
			$format = $model->getDataSource()->columns['datetime']['format'];
			$time = date($format,$time);
		}
		$items = $this->_getPlanningsToUpdate($model, $conditions, $time);
		$this->_applyPlans($model,$items,$time);
	}
	
	function _applyPlans(&$model, $plans = array(), $syncedTime = null){
		if(!empty($plans)){
			$save = array();
			if(!empty($plans['Planning'])){
				foreach($plans['Planning'] as $item){
					$id = $item[$model->alias]['id'];
					if($item['Planning']['applicable']){
						$save[$id]['id'] = $id;
						if($this->settings[$model->alias]['updatedField']!= 'modified' && !empty($syncedTime)){
							$save[$id][$this->settings[$model->alias]['updatedField']] = $syncedTime;
						}
						$save[$id][$item['Planning']['field']] = $item['Planning']['value'];
					}
				}
			}
			if(!empty($plans['Equation'])){
				foreach($plans['Equation'] as $eq){
					$id = $eq['target']['id'];
					if(!empty($id) && $eq['applicable']){
						$save[$id]['id'] = $id;
						if($this->settings[$model->alias]['updatedField']!= 'modified' && !empty($syncedTime)){
							$save[$id][$this->settings[$model->alias]['updatedField']] = $syncedTime;
						}
						$save[$id][$eq['field']] = $eq['newValue'];
					}
				}
			}
			//debug($save);
			$count = 0;
			$tmp = $this->settings[$model->alias]['saveAndUpdate'];
			$this->settings[$model->alias]['saveAndUpdate'] = false;
			foreach($save as $item){
				$model->create();
				if($model->save($item)){
					$count++;
				}
			}
			if(!empty($plans['Planning'])){
				$this->savePlanningChanges($model, array('delete'=>$plans['Planning']));
			}
			$this->settings[$model->alias]['saveAndUpdate'] = $tmp;
			return $count;
		}
		return 0;
	}
	
	
	function updateDataNow(&$model, $data, $time = null, $options = array()){
		App::import('Lib', 'SetMulti');
		
		if(empty($data)){
			return $data;
		}
		
		if(is_array($time)){
			$options = $time;
		}else{
			$options['time'] = $time;
		}
		$defOpt = array(
			'time' => null,
			'belongsTo' => array(),
		);
		$opt = array_merge($options,$defOpt);
		
		if(empty($opt['time'])){
			$opt['time'] = mktime();
		}
		
		////// Normalize Data //////
		//todo : add lateral relation normalisation
		$model->Behaviors->attach('Util');
		$oldFormat = array();
		//$data = $model->unifiedResult($data, null, array(), $oldFormat);
		list($data, $oldFormat) = $model->unifiedResult($data, null, null, true);
		//debug($oldFormat);
		
		////// get relation key fields //////
		$relationKeyFields = array();
		$belongsTo = array_merge($model->belongsTo, $opt['belongsTo']);
		foreach($model->belongsTo as $bkey => $belongs){
			if(!empty($data[0][$bkey])){
				$relationKeyFields[] = $belongs['foreignKey'];
			}
		}
		
		
		////// get updated Data //////
		$plans = array();
		$fields = array_merge(array_keys($data[0][$model->alias]),$relationKeyFields);
		$changes = $this->getChanges($model, $data, $opt['time'], $fields, $plans);
		
		////// get updated relations //////
		foreach($model->belongsTo as $bkey => $belongs){
			$toUpdate = array();
			foreach($changes as $rkey => $row){
				if(!empty($data[$rkey][$bkey]) && !empty($row[$model->alias][$belongs['foreignKey']])){
					$toUpdate[$rkey] = $row[$model->alias][$belongs['foreignKey']];
				}
			}
			$mapping = SetMulti::flip($toUpdate);
			
			if(!empty($toUpdate)){
				$excludeFields = array('name','className','counterCache');
				$findOptions = array_diff_key($belongs,array_flip($excludeFields));
				$bModel = $model->{$bkey};
				$findOptions['conditions'][$bModel->alias.'.id'] = $toUpdate;
				$findOptions['recursive'] = -1; // in the futur we may want to detect contain option
				$newBinded = $bModel->find('all',$findOptions);
				//$newBinded = $bModel->dataToContainedRelations($new); //see comment on recursive
				foreach($newBinded as $n){
					$nid = $n[$bkey][$bModel->primaryKey];
					foreach((array)$mapping[$nid] as $rkey){
						$changes[$rkey][$bkey] = $n[$bkey];
					}
				}
			}
		}
		//debug($changes);
		$data = Set::merge($data, $changes);
		
		//todo : recursive relations update
		
		////// Painless update //////
		if(strtotime($opt['time']) <= mktime()){
			$this->_applyPlans($model, $plans, $opt['time']);
		}
		
		
		////// Un-normalize Data //////
		$data = $model->unifiedResult($data, null, $oldFormat);
		return $data;
	}
	
	function beforeFind(&$model, $queryData){
		if(!empty($model->belongsTo)){
			$model->__originalBelongsTo = $model->belongsTo;
		}else{
			unset($model->__originalBelongsTo);
		}
	}
	
	
	function afterFind(&$model, $results, $primary){
		if($this->settings[$model->alias]['extendFind']){
			$time = $this->getQueryTime($model);
			
			$belongsTo = array();
			if(!empty($model->__originalBelongsTo)){
				$belongsTo = $model->__originalBelongsTo;
			}
			//debug($belongsTo);
			
			//debug($results);
			if(!empty($results) && !empty($results[0][$model->alias])){
				$results = $this->updateDataNow($model, $results, $time, array('belongsTo'=>$belongsTo));
			}
			//debug($results);
		}
		return $results;
	}
	
	
	function beforeSave(&$model, $options){
		if(!empty($model->id) && $this->settings[$model->alias]['saveAndUpdate']){
			$time = mktime();
			$format = $model->getDataSource()->columns['datetime']['format'];
			$time = date($format,$time);
			
			$oldPlans = array();
			$internalChanges = $this->getChanges($model ,null, $time, null, $oldPlans);
			if(!empty($oldPlans['Planning'])){
				$oldPlans = $oldPlans['Planning'];
				if(isset($oldPlans[0]['Node'])){ //if there is a change, node is accesible from the first $oldPlans. We dont need node if there is no change
					$node = $oldPlans[0]['Node'];
				}
			}else{
				$oldPlans = array();
			}
			$conficts = array_diff_assoc(array_intersect_key($model->data[$model->alias],$internalChanges),$internalChanges);
			$addValues = array_diff_key($internalChanges,$model->data[$model->alias]);
			if($this->settings[$model->alias]['updatedField']!= 'modified'){
				$addValues[$this->settings[$model->alias]['updatedField']] = $time;
			}
			//debug($addValues);
			$model->set($addValues);
			$plans = array();
			$to_delete = array();
			//debug($conficts);
			foreach($conficts as $field => $val){
				$solution = $model->getConflictSolution($val, $field, $time,null,array_merge($options,array('node'=>$node)));
				if($solution === false){
					return false;
				}else{
					$plans = array_merge($plans, $solution['save']);
					$to_delete = array_merge($to_delete, $solution['delete']);
				}
			}
			//debug($oldPlans);
			foreach($oldPlans as $plan){
				$to_delete[] = $plan['Planning']['id'];
			}
			//debug($plans);
			//debug($to_delete);
			$this->savePlanningChanges($model, array('save'=>$plans, 'delete'=>$to_delete));
		}
		return true;
	}
	
}
