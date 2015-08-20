<?php
class Effect extends AppModel {
	var $name = 'Effect';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	
	var $actsAs = array(
		'Node',
		'XmlLink.XmlLinked'=>array('find'=>array(
			'fields'=>array('skill_id','skin_id','attachment',
				'requester_alias'=>'EventType.requester_alias',
				'controlled_alias'=>'EventType.controlled_alias',
				'requester_end_alias'=>'EndEventType.requester_alias',
				'controlled_end_alias'=>'EndEventType.controlled_alias',
			),
			'contain' => array(
				'Skin'=>array()
			),
			'internal'=>array(
				'event_type_id',
				'end_event_type_id',
				'alias',
				//'requester_alias'=>'EventType.requester_alias',
				//'controlled_alias'=>'EventType.controlled_alias'
			)
		)),
	);

	var $belongsTo = array(
		'Skill' => array(
			'className' => 'Skill',
			'foreignKey' => 'skill_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Skin' => array(
			'className' => 'Skin',
			'foreignKey' => 'skin_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'EventType' => array(
			'className' => 'EventType',
			'foreignKey' => 'event_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'EndEventType' => array(
			'className' => 'EventType',
			'foreignKey' => 'end_event_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	var $aliases = array(
		0 => 'Requester alias',
		1 => 'Controlled alias',
	);
	
	function parentNode() {
		return $this->Node->buildPath('Model/Effect',false,true);
	}
	
	function afterLinkRead($results){
		foreach($results['Effect'] as $key => $val){
			if($key !== 'internal'){
				$val['event_name'] = $val['internal']['alias']? $val['controlled_alias']:$val['requester_alias'];
				$val['end_event_name'] = $val['internal']['alias']? $val['controlled_end_alias']:$val['requester_end_alias'];
				$results['Effect'][$key] = $val;
			}
		}
		return $results;
	}
}
