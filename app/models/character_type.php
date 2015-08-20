<?php
class CharacterType extends AppModel {
	var $name = 'CharacterType';
	var $displayField = 'title';
	var $actsAs = array(
		'Tree'
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Skin' => array(
			'className' => 'Skin',
			'foreignKey' => 'skin_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ParentCharacterType' => array(
			'className' => 'CharacterType',
			'foreignKey' => 'parent_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'ChildCharacterType' => array(
			'className' => 'CharacterType',
			'foreignKey' => 'parent_id',
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

}
