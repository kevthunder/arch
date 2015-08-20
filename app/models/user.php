<?php
class User extends AppModel {
	var $name = 'User';
	var $actsAs = array('Node');
	var $displayField = 'username';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'ControlledCharacter' => array(
			'className' => 'Character',
			'foreignKey' => 'controlled_character_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);
	var $hasMany = array(
		'Character' => array(
			'className' => 'Character',
			'foreignKey' => 'user_id',
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
	
	
	function parentNode() {
		return $this->Node->buildPath('Model/User',false);
	}

}
