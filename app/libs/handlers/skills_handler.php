<?php
class SkillsHandler extends Object {
	function targetTile_form(){
		return array(
			'tile_id'=>array(),
		);
	}
	function targetTile(&$options,$params){
		if($options['phase'] == 1){
			//$skillInstance = $this->_getSkillInstance(&$options,$params);
			//if(!$skillInstance){
			//	return false;
			//}
			if(array_key_exists('tile_id',$params)){
				$tile_id = $params['tile_id'];
			}elseif(!empty($options['params']['tile_id'])){
				$tile_id = $options['params']['tile_id'];
			}
			if(!empty($tile_id)){
				$this->Tile = ClassRegistry::init('Tile');
				$tile = $this->Tile->read(null,$tile_id);
				if($tile){
					//$this->Skill = ClassRegistry::init('Skill');
					$options['targetedTile'] = $tile;
					$options['params']['main_target_id'] = $tile['Node']['id'];
					//$skillInstanceData = array(
					//	'id' => $skillInstance['SkillInstance']['id'],
					//	'main_target_id' => $tile['Node']['id'],
					//);
					//$this->SkillInstance->save($skillInstanceData);
					
					return true;
				}
			}
			return false;
		}
		//debug($options['targetedTile']);
		//$this->Tile->create();
		//$this->Tile->set($options['targetedTile']);
		//$this->Skill = ClassRegistry::init('Skill');
		//$res = $this->Tile->triggerAction('castOn',$triggerParams,$this->defaultAro);
		
	}
	function targetStructure(&$options,$params){
		return $this->_targetTiled($options,$params,'Structure');
	}
	function targetCharacter(&$options,$params){
		return $this->_targetTiled($options,$params,'Character');
	}
	
	function _targetTiled(&$options,$params,$modelName){
		if($options['phase'] == 1){
			//$skillInstance = $this->_getSkillInstance(&$options,$params);
			//if(!$skillInstance){
			//	return false;
			//}
			$Model = ClassRegistry::init($modelName);
			$this->{$modelName} = $Model;
			$pName = strtolower($Model->alias).'_id';
			if(array_key_exists($pName,$params) || array_key_exists('tile_id',$params)){
				if(!empty($params[$pName])){
					$entry_id = $params[$pName];
				}elseif(!empty($params['tile_id'])){
					$tile_id = $params['tile_id'];
				}
			}else{
				if(!empty($options['params'][$pName])){
					$entry_id = $options['params'][$pName];
				}elseif(!empty($options['params']['tile_id'])){
					$tile_id = $options['params']['tile_id'];
				}
			}
			if(!empty($entry_id)){
				$entries = $Model->find('all',array('conditions'=>array($Model->alias.'.id'=>$entry_id)));
			}elseif(!empty($tile_id)){
				$entries = $Model->find('all',array('conditions'=>array($Model->alias.'.tile_id'=>$tile_id)));
			}
			//debug($entries);
			if(!empty($entries)){
				foreach($entries as $entry){
					
					$options['params']['main_target_id'] = $entry['Node']['id'];
					//$skillInstanceData = array(
					//	'id' => $skillInstance['SkillInstance']['id'],
					//	'main_target_id' => $entry['Node']['id'],
					//);
					//$this->skillInstance->save($skillInstanceData);
				}
				return true;
			}
			$options['messages'][] = 441;
			return false;
		}
	}
	
	function testTargets(&$options,$params){
		$skillInstance = $this->_getSkillInstance(&$options,$params);
		if(!$skillInstance){
			return false;
		}
		$targets = array();
		if(!empty($skillInstance['MainTarget']['id'])){
			$targets[] = $skillInstance['MainTarget']['id'];
		}
		if(!empty($targets)){
			$subEvents = array();
			foreach($targets as $target){
				/*$eventType = 'castOn';
				if(!empty($skillInstance['Skill']['targetting_event_type_id'])){
					$eventType = $skillInstance['Skill']['targetting_event_type_id'];
				}*/
				$subEvent = array(
					'name' => 'castOn',
					'aros'=> $skillInstance['Node']['id'],
					'acos'=> $target,
				);
				
				$subEvents[] = $subEvent;
			}
			if(count($subEvents)>1){
				$subEvents['max'] = 1;
				$options['subEvent'][]['or'] = $subEvents;
			}else{
				$options['subEvent'][] = $subEvents[0];
			}
		}
	}
	
	function cast_log($data){
		if($data['final_data']['phase'] != 1 && !empty($data['id'])){
			$this->TimedEvent = ClassRegistry::init('TimedEvent');
			
			//todo : find first time event
			$timedEvent = $data;
			if(!array_key_exists('aro_id',$timedEvent)){
				$timedEvent = $this->TimedEvent->find('first',array('conditions'=>array($this->TimedEvent->alias.'.id'=>$data['id']),'recursive'=>-1));
				$timedEvent = $timedEvent['TimedEvent'];
			}
			
			$eventNode = array('model'=>'TimedEvent','foreign_key'=>$data['id']);
			$this->NodeLink = ClassRegistry::init('NodeLink');
			$nodeLink = $this->NodeLink->link('invalidation', $timedEvent['aro_id'], $eventNode, $opt = array('context'=> 'casting'));
			//debug($nodeLink);
		}
		return $data;
	}
	
	function _getSkillInstance(&$options,$params,$role='aco'){
		$skillI = null;
		$skillINode = $options[$role.'s'];
		if(!empty($skillINode)){
			if(!empty($options['skillInstance']['Node']['id']) && $options['skillInstance']['Node']['id'] == $skillINode){
				$skillI = $options['skillInstance'];
			}else{
				$this->Node = ClassRegistry::init('Node');
				$skillI = $this->Node->getItemId($skillINode,'SkillInstance');
			}
		}
		
		if(!empty($skillI) && is_numeric($skillI)){
			$this->SkillInstance = ClassRegistry::init('SkillInstance');
			$skillI = $this->SkillInstance->find('first',array('conditions'=>array($this->SkillInstance->alias.'.id'=>$skillI)));
			$options['skillInstance'] = $skillI;
		}
		return $skillI;
	}
	
	function startSkill(&$options,$params){
		$skillInstance = $this->_getSkillInstance(&$options,$params);
		if(!empty($skillInstance)){
			if($options['phase'] == 1){
				$subEvent = $options;
				unset($subEvent['all_types']);
				$subEvent['name'] = 4;
				if(!empty($skillInstance['Skill']['cast_time'])){
					$subEvent['filterPhase'] = 1;
				}
				
				$options['subEvent'][] = $subEvent;
			}else{
				if(empty($skillInstance['SkillInstance']['start_time'] )){
					$format = $this->SkillInstance->getDataSource()->columns['datetime']['format'];
					$toSave = array(
						'start_time' => date($format,mktime()),
						'id' => $skillInstance['SkillInstance']['id'],
					);
					$res = $this->SkillInstance->save($toSave);
					
					/*$toSave['cast_time'] = $toSave['start_time'] + $skillInstance['Skill']['cast_time'];
					$toSave['recovered_time'] = $toSave['cast_time'] + $skillInstance['Skill']['recovery_time'];
					$toSave['cooled_down_time'] = $toSave['recovered_time'] + $skillInstance['Skill']['cool_down'];*/
					
					//debug($res);
					if(!empty($res['TimedEvent']['start_time']['TimedEvent']['id'])){
						$options['timed_event_id'] = $res['TimedEvent']['start_time']['TimedEvent']['id'];
					}
				}
			}
		}
		return true;
	}
	
	function moveTimings(&$options,$params){
		$skillInstance = $this->_getSkillInstance(&$options,$params);
		$this->Path = ClassRegistry::init('Path');
		$path = $this->Path->find('first',array('conditions'=>array('skill_instance_id'=>$skillInstance['SkillInstance']['id'])));
		
		if(!empty($options['params']['start_time'])){
			$start_time = $options['params']['start_time'];
		}elseif(!empty($skillInstance['SkillInstance']['start_time'])){
			$start_time = $skillInstance['SkillInstance']['start_time'];
		}
		if(!empty($start_time) && !empty($path)){
			$path = Set::merge($path,$this->Path->save(array(
				'originalData' => $path['Path'],
				'Path' => array(
					'id' => $path['Path']['id'],
					'start_time' => $start_time,
				)
			)));
			if(!empty($path['Path']['total_time'])){
				$options['return']['recovery_time'] += $path['Path']['total_time'];
			}
		}else{
			$options['return'] = false;
			return false;
		}
	}
	
	
	function inRange(&$options,$params){
		$skillInstance = $this->_getSkillInstance(&$options,$params,'aro');
		if(!$skillInstance){
			return false;
		}
		
		if(empty($skillInstance['Skill']['id'])){
			debug('Skill not found');
			return false;
		}
		
		if(empty($skillInstance['Skill']['range'])){
			return null;
		}
		
		if(empty($skillInstance['Caster']['id'])){
			debug('Caster not found');
			return false;
		}
		
		
		$this->Node = ClassRegistry::init('Node');
		$ref = $this->Node->getItemRef($options['acos']);
		$model = ClassRegistry::init($ref['model']);
		$model->Behaviors->attach('Util');
		if(!$model->hasMethod('getPos')){
			return null;
		}
		$targetPos = $model->getPos($ref['foreign_key']);
		//debug($targetPos);
		
		if(!$targetPos){
			debug('Target position could not be determined');
			return false;
		}
		
		$casterPos = $this->SkillInstance->Caster->getPos($skillInstance['Caster']);
		if(empty($casterPos)){
			debug('Caster position could not be determined');
			return false;
		}
		
		//////////// calcul distance ////////////
		$dx = $targetPos['x'] - $casterPos['x'];
		$dy = $targetPos['y'] - $casterPos['y'];
		$distance = sqrt( $dx * $dx + $dy * $dy );
		//debug($distance);
		if($distance <= $skillInstance['Skill']['range']){
			return null;
		}
		
		debug('out of range');
		$options['messages'][] = 431;
		return false;
	}
	function modifyTarget_form(){
		return array(
			'operation'=>array(),
			'name'=>array(),
		);
	}
	function modifyTarget(&$options,$params){
		debug($params);
		$this->Node = ClassRegistry::init('Node');
		$nodes = $this->Node->getNodes($options['acos'],array('fields'=>array('foreign_key','model')));
		foreach($nodes as $node){
			if(!empty($node['Node']['model'])){
				$model = ClassRegistry::init($node['Node']['model']);
				if($model){
					$target = $model->read(array($model->primaryKey,$params['name']),$node['Node']['foreign_key']);
					if($target){
						App::import('Lib', 'Operations');
						if(isset($params['operation'])){
							//debug($params['operation']);
							$target[$model->alias][$params['name']] = Operations::applyOperation($params['operation'],$target[$model->alias][$params['name']], true);
						}
						//debug($target);
						$model->save($target);
					}
				}
			}
		}
		//debug($node);
		//debug($params);
	}
}
?>