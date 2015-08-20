<?php
class Path extends AppModel {
	var $name = 'Path';
	var $actsAs = array(
		'Node','EventTrigger',
		'XmlLink.XmlLinked'=>array(
			'find'=>array(
				'fields'=>array('character_id','start_tile_id','start_time','end_tile_id','end_time','steps')
			)
		),
		'NodeLinked'=>array(
			'follow'=>array(
				'Character'=>array(
					'type' => 'invalidation',
					'owner' => 'owned',
					'startField' => 'start_time',
					'endField' => 'end_time',
				)
			)
		),
		'serialized'=>array('steps'),
		'Lifetime'
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Character' => array(
			'className' => 'Character',
			'foreignKey' => 'character_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'StartTile' => array(
			'className' => 'Tile',
			'foreignKey' => 'start_tile_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'EndTile' => array(
			'className' => 'Tile',
			'foreignKey' => 'end_tile_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'SkillInstance' => array(
			'className' => 'SkillInstance',
			'foreignKey' => 'skill_instance_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	function calculPath($character,$toTile=null,$range=0,$fromTile=null) {
		if(is_array($character) && !empty($character['toTile'])){
			$opt = $character;
			$defOpt = array('character'=>null,'toTile'=>null,'range'=>0,'fromTile'=>null);
			$opt = array_merge($defOpt,$opt);
			extract($opt);
		}
		App::import('Lib', 'PathCalculator');
		$pathing = new PathCalculator($character,$toTile,$range,$fromTile);
		$pathing->calculate();
		if($pathing->calculed && $pathing->success){
			return $pathing->toData();
		}
		return false;
	}
	
	
	function parentNode() {
		return $this->Node->buildPath('Model/Path',false,true);
	}
	
	function _calculTimings($data){
		if(!empty($data['Path']['character_id'])){
			$this->Character->id =$data['Path']['character_id'];
			$speed = $this->Character->field('speed');
			$totalTime = 0;
			$format = $this->getDataSource()->columns['datetime']['format'];
			//debug($data);
			foreach($data['Path']['steps'] as &$step){
				$step['time'] = ceil($totalTime);
				$step['delay'] = $step['length'] * 100 / $speed;
				$totalTime += $step['delay'];
			}
			$data['Path']['total_time'] = ceil($totalTime);
		}
		return $data;
	}
	
	function _preprocessUpdatePlanning($data){
		$this->Character->Planning->recursive = -1;
		$format = $this->getDataSource()->columns['datetime']['format'];
		$existing = array();
		if(!empty($data['Path']['steps'])){
			$steps = $data['Path']['steps'];
		}
		if(!empty($data['Path']['id'])){
			if(empty($steps)){
				$path = $this->find('first',array('conditions'=>array('id'=>$data['Path']['id']),'recursive' => -1));
				if(!empty($path)){
					$steps = $path['Path']['steps'];
				}
			}
			$contextPrefix = 'Path'.$data['Path']['id'].'Step';
			$existing = $this->Character->Planning->find('all',array('conditions'=>array('context Like'=>$contextPrefix.'%')));
		}else{
			$contextPrefix = 'Path%pathId%Step';
		}
		$operations = array('save'=>array(),'delete'=>$existing);
		App::import('Lib', 'SetMulti'); 
		
		foreach($steps as $key => $step){
			$op = $this->Character->savePlanning(array('tile_id'=>$step['tile_id']),date($format,strtotime($data['Path']['start_time'])+$step['time']),array('dry'=>true,'context'=>$contextPrefix.$key,'validationExclude'=>$existing));
			if($op === false){
				return false;
			}
			$operations = SetMulti::merge2($operations,$op);
		}
		//debug($operations);
		$linksModif = $this->Character->buildPlanifiedFollowingLinks($operations['save'], $operations['delete'],array("dry"=>true));
		
		
		//debug($linksModif);
		if($linksModif === false){
			return false;
		}
		$operations['linksModif'] = $linksModif;
		return $operations;
	}
	
	/*function getCompleteEvent($path = null,$options = array()){
		$defOpt = array(
			'create' => true,
		);
		$opt = array_merge($defOpt,$options);
		if(is_null($path)){
			$path = $this->id;
		}
		if(is_numeric($path)){
			$path = $this->find('first',array('conditions'=>array($this->alias.'.id'=>$path)));
		}
		if(!empty($path)){
			$this->TimedEvent = ClassRegistry::init('TimedEvent');
			$timedEvent = $this->TimedEvent->find('first',array('conditions'=>array('event_type_id' => 14,'owner_id' => $path['Node']['id'])));
			if(empty($timedEvent) && $opt['create']){
				$data = array(
					'event_type_id' => 14,
					'time' => $path['Path']['end_time'],
					'data' => null,
					'aro_id' => null,
					'aco_id' => $path['Node']['id'],
					'x' => $path['EndTile']['x'],
					'y' => $path['EndTile']['y'],
					'repeate' => null,
					'repeate_count' => null,
					'retroactive' => null,
					'owner_id' => $path['Node']['id'],
					'active' => 1
				);
				//debug($data);
				$this->TimedEvent->create();
				if($this->TimedEvent->save($data)){
					$timedEvent = array('TimedEvent'=>$data);
					$timedEvent['TimedEvent']['id'] = $this->TimedEvent->id;
				}else{
					return false;
				}
			}
			return $timedEvent;
		}
		return null;
	}*/
	
	function beforeSave($options) {
		if($this->data!=null){
			$this->unserialize();
			if(empty($this->data['Path']['total_time']) && !empty($this->data['Path']['steps'])){
				$this->data = $this->_calculTimings($this->data);
			}
			if(!empty($this->data['Path']['start_time']) && !empty($this->data['Path']['total_time']) || !empty($this->data['originalData']['total_time'])){
				$totalTime = !empty($this->data['Path']['total_time'])?$this->data['Path']['total_time']:$this->data['originalData']['total_time'];
				$format = $this->getDataSource()->columns['datetime']['format'];
				$this->data['Path']['end_time'] = date($format,strtotime($this->data['Path']['start_time'])+$totalTime);
				
				/*if(!isset($this->data['Path']['start_time'])){
					$this->set('start_time',date($format));
				}*/
			}
			if(!empty($this->data['Path']['start_time'])){
				$this->planningUpdate = $this->_preprocessUpdatePlanning($this->data);
				if($this->planningUpdate == null){
					unset($this->planningUpdate);
					return false;
				}
				//debug($this->planningUpdate);£
				//return false;
			}
			$this->serialize();
			//updateplanning
		}else{
			unset($this->planningUpdate);
		}
		//debug($this->data);
		return true;
	}
	
	function afterSave($created){
		if(!empty($this->planningUpdate)){
			if($created){
				App::import('Lib', 'SetMulti');
				//$this->log(var_export($this->planningUpdate, true),'debug');
				$this->planningUpdate = SetMulti::replaceTree('%pathId%', $this->id,$this->planningUpdate);
				//$this->log(var_export($this->planningUpdate, true),'debug');
				
				///// Binding Listening /////
				/*if(empty($this->Event)){
					$this->Event = ClassRegistry::init('Event');
				}
				$node_id = $this->Node->getNodeId($this->myNodeRef());
				$data = array(
					'aro_id' => null,
					'aco_id' => $node_id,
					'handler' => 'path',
					'function' => 'bindListening',
					'event_type_id' => 15,
					'owner_id' => $node_id,
					'phase' => 2,
					'context' => 'path'.$this->id.'BindingListening',
					'active' => 1,
				);
				//debug($data);
				$this->Event->save($data);*/
			}
			$this->Character->savePlanningChanges($this->planningUpdate);
		}
		unset($this->planningUpdate);
	}
}
