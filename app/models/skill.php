<?php
class Skill extends AppModel {
	var $name = 'Skill';
	var $displayField = 'title';
	var $actsAs = array(
		'Node','EventTrigger',
		'XmlLink.XmlLinked'=>array('find'=>array('fields'=>array('title','desc','range','ui_behaviors'),'contain'=>array('Effect'))),
		'serialized'=>array('data','ui_behaviors'),
	);
	
	var $belongsTo = array(
		'TargettingEventType' => array(
			'className' => 'EventType',
			'foreignKey' => 'targetting_event_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	var $hasMany = array(
		'Effect' => array(
			'className' => 'Effect',
			'foreignKey' => 'skill_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);
	function __construct( $id = false, $table = NULL, $ds = NULL ){
		parent::__construct( $id, $table, $ds);
		$this->SkillInstance = ClassRegistry::init('SkillInstance');
	}
	
	function getTargetHandlers($named = false){
		App::import('Lib', 'ClassCollection'); 
		App::import('Lib', 'SetMulti');
		$class = ClassCollection::getClass('handler','Skills');
		$methods = get_class_methods($class);
		$targetHandlers = SetMulti::pregFilter('/^target\w*$/',$methods);
		//debug($targetHandlers);
		if($named){
			$list = array();
			foreach($targetHandlers as $handler){
				$list[$handler] = Inflector::humanize(Inflector::underscore($handler));
			}
			$targetHandlers = $list;
		}
		return $targetHandlers;
	}
	
	function getSimpleUserEvent($id = null){
		$nodeId = $this->nodeId($id);
		$this->Event = ClassRegistry::init('Event');
		
		$findOpt = array('conditions'=>array(
			'aco_id'=>$nodeId,
			'event_type_id' => 17,
			'handler' => 'skills'
		));
		$userEvent = $this->Event->find('first',$findOpt);
		return $userEvent;
	}
	
	function getSimpleTargetEvent($id = null){
		$nodeId = $this->nodeId($id);
		$this->Event = ClassRegistry::init('Event');
	
		$findOpt = array('conditions'=>array(
			'aro_id'=>$nodeId,
			'event_type_id' => 5,
			'phase' => 1,
		));
		$targetEvent = $this->Event->find('first',$findOpt);
		return $targetEvent;
	}
	
	function saveSimpleEvents($id = null,$data){
		if(is_null($id)){
			$id = $this->id;
		}
		if(isset($data[$this->alias])){
			$data = $data[$this->alias];
		}
		if(!empty($id)){
			$nodeId = $this->nodeId($id);
			$this->Event = ClassRegistry::init('Event');
			$userEvent = $this->getSimpleUserEvent();
			$targetEvent = $this->getSimpleTargetEvent();
			if(!empty($data['user_node']) && !empty($data['target_handler'])){
				$userData = array(
					'aro_id' => $data['user_node'],
					'aco_id' => $nodeId,
					'function' => $data['target_handler'],
					'handler' => 'skills',
					'event_type_id' => 17,
				);
				if(!empty($userEvent)){
					$userData['id'] = $userEvent['Event']['id'];
				}else{
					$userData['active'] = 1;
				}
				$this->Event->create();
				$this->Event->save($userData);
			}
			if(!empty($data['target_node'])){
				$targetData = array(
					'aro_id' => $nodeId,
					'aco_id' => $data['target_node'],
					'event_type_id' => 5,
					'phase' => 1,
				);
				if(!empty($targetEvent)){
					$targetData['id'] = $targetEvent['Event']['id'];
				}else{
					$targetData['active'] = 1;
				}
				$this->Event->create();
				$this->Event->save($targetData);
			}
		}
	}
	
	
	
	function getSkillProvider($id = null){
		$nodeId = $this->nodeId($id);
		$this->NodeLink = ClassRegistry::init('NodeLink');
		
		$findOpt = array('conditions'=>array(
			'owned_node_id'=>$nodeId,
			'type' => 'skillProvider'
		));
		$provider = $this->NodeLink->find('first',$findOpt);
		return $provider;
	}
	
	function saveSkillProvider($providerNode,$id = null){
		if(is_null($id)){
			$id = $this->id;
		}
		if(!empty($id) && !empty($providerNode)){
			$nodeId = $this->nodeId($id);
			$this->NodeLink = ClassRegistry::init('NodeLink');
			$provider = $this->getSkillProvider();
			$providerData = array(
				'owned_node_id' => $nodeId,
				'owner_node_id' => $providerNode,
				'type' => 'skillProvider',
			);
			if(!empty($provider)){
				$providerData['id'] = $provider['NodeLink']['id'];
			}else{
				$providerData['active'] = 1;
				$providerData['context'] = 'System';
			}
			$this->NodeLink->create();
			$this->NodeLink->save($providerData);
		}
	}
	
	function parentNode() {
		return $this->Node->buildPath('Model/Skill',false,true);
	}
}
