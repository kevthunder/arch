<?php
class Message extends AppModel {
	var $name = 'Message';
	var $actsAs = array(
		'Node','EventTrigger',
		'XmlLink.XmlLinked'=>array(
			'fields'=>array('text','user_id','character_id','x'=>'cache_x','y'=>'cache_y','time'=>'created'),
			'internal' => array('to_user_id')
		),
	);
	
	function parentNode() {
		return $this->Node->buildPath('Model/Message',false);
	}
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Character' => array(
			'className' => 'Character',
			'foreignKey' => 'character_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ToUser' => array(
			'className' => 'User',
			'foreignKey' => 'to_user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Tile' => array(
			'className' => 'Tile',
			'foreignKey' => 'tile_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
