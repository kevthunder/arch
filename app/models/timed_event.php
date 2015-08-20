<?php
class TimedEvent extends AppModel {
	var $name = 'TimedEvent';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $actsAs = array(
		'Node','EventTrigger',
		'XmlLink.XmlLinked'=>array(
			'find'=>array('fields'=>array(
				'time',
				'aro_model'=>'MainAro.model',
				'aro_key'=>'MainAro.foreign_key',
				'aco_model'=>'MainAco.model',
				'aco_key'=>'MainAco.foreign_key',
				'requester_alias'=>'EventType.requester_alias',
				'controlled_alias'=>'EventType.controlled_alias',
			))
		),
		'Lifetime' => array(
			'delete' => false,
			'disable' => false,
			'timeout' => 60,
			'end_field' => 'time',
		),
		'Serialized'=>array('data','final_data','result','output_define'),
	);
	

	var $belongsTo = array(
		'EventType' => array(
			'className' => 'EventType',
			'foreignKey' => 'event_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Owner' => array(
			'className' => 'Node',
			'foreignKey' => 'owner_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'MainAro' => array(
			'className' => 'Node',
			'foreignKey' => 'aro_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'MainAco' => array(
			'className' => 'Node',
			'foreignKey' => 'aco_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	var $maxEvents = 5;
	
	
	function parentNode() {
		return $this->Node->buildPath('Model/TimedEvent',false,true);
	}
	
	function triggerEvents($events){
		if(empty($this->Event)){
			$this->Event = ClassRegistry::init('Event');
		}
		$toDelete = array();
		$toSave = array();
		foreach($events as $event){
			$eventOpt = $event[$this->alias]['data'];
			$eventOpt['timed_event_id'] = $event[$this->alias]['id'];
			$eventOpt['name'] = $event[$this->alias]['event_type_id'];
			if(!empty($event[$this->alias]['aro_id'])){
				$eventOpt['aros'][] = $event[$this->alias]['aro_id'];
			}
			if(!empty($event[$this->alias]['aco_id'])){
				$eventOpt['acos'][] = $event[$this->alias]['aco_id'];
			}
			//debug($event);
			$data = array('id'=>$event[$this->alias]['id']);
			if($event[$this->alias]['repeate']){
				if($event[$this->alias]['repeate_count']===1){
					$data['active'] = 0;
				}else{
					if($event[$this->alias]['repeate_count']>1){
						$data['repeate_count'] = $event[$this->alias]['repeate_count']-1;
					}
					$format = $this->getDataSource()->columns['datetime']['format'];
					if($event[$this->alias]['retroactive']){
						$data['time'] = date($format,strtotime($event[$this->alias]['time'])+$event[$this->alias]['repeate']);
					}else{
						$data['time'] = date($format,mktime()+$event[$this->alias]['repeate']);
					}
				}
			}else{
				$data['active'] = 0;
			}
			$toSave[] = $data;
			$this->Event->dispatchEventPhases($eventOpt);
		}
		$this->Behaviors->attach('Util');
		//debug(array('delete'=>$toDelete,'save'=>$toSave));
		$this->bulkModify(array('delete'=>$toDelete,'save'=>$toSave));
	}
	
	function triggerUnlocalizedEvents(){
		$format = $this->getDataSource()->columns['datetime']['format'];
		$findOpt = array(
			'conditions'=>array(
				$this->alias.'.active' => 1,
				$this->alias.'.x'=>null,
				$this->alias.'.y'=>null,
				$this->alias.'.time <'=>date($format)
			),
			'recursive' => -1
		);
		$events = $this->find('all',$findOpt);
		if(count($events) > $this->maxEvents){
			$events = array_slice($events, 0, $maxEvents);
		}
		
		$this->triggerEvents($events);
	}
	function triggerLocalizedEvents($x, $y, $zone_id, $w, $h){
		$format = $this->getDataSource()->columns['datetime']['format'];
		$findOpt = array(
			'conditions'=>array(
				$this->alias.'.active' => 1,
				$this->alias.'.x + '.$this->alias.'.range >= '.$x,
				$this->alias.'.x - '.$this->alias.'.range <= '.($x+$w),
				$this->alias.'.y + '.$this->alias.'.range >= '.$y,
				$this->alias.'.y - '.$this->alias.'.range <= '.($y+$h),
				$this->alias.'.time <'=>date($format),
				$this->alias.'.zone_id'=>$zone_id
			),
			'recursive' => -1
		);
		//debug($findOpt);
		$events = $this->find('all',$findOpt);
		//debug($events);
		if(count($events) > $this->maxEvents){
			$events = array_slice($events, 0, $maxEvents);
		}
		
		$this->triggerEvents($events);
	}
	
	function checkEvents($events){
		$toTrigger = array();
		if(!Set::numeric(array_keys($events))){
			$events = array($events);
		}
		foreach($events as $event){
			if(empty($event[$this->alias])){
				$event = array($this->alias=>$event);
			}
			if(!empty($event[$this->alias]['time']) && strtotime($event[$this->alias]['time']) <= mktime()){
				$toTrigger[] = $event;
			}
		}
		if(!empty($toTrigger)){
			$this->triggerEvents($toTrigger);
		}
	}
	
	
	function afterLinkRead($results){
		if(!empty($results['TimedEvent'])){
			foreach($results['TimedEvent'] as $key => $val){
				if(!empty($val['aro_model'])){
					$val['aro_model'] = Inflector::underscore($val['aro_model']);
				}
				if(!empty($val['aco_model'])){
					$val['aco_model'] = Inflector::underscore($val['aco_model']);
				}
				$results['TimedEvent'][$key] = $val;
			}
		}
		return $results;
	}
}
