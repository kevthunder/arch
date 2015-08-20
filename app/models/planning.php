<?php
class Planning extends AppModel {
	var $name = 'Planning';
	
	var $actsAs = array('Serialized' => array('operation'));
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Node' => array(
			'className' => 'Node',
			'foreignKey' => 'node_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
