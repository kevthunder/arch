<?php
class StructureType extends AppModel {
	var $name = 'StructureType';
	var $actsAs = array(
		'Tree','Node',
		'serialized'=>array('ui_behaviors','pathing'),
		'Inheritor'=>array('skin_id'=>'ParentStructureType','ui_behaviors'=>'ParentStructureType','pathing'=>'ParentStructureType')
	);
	var $displayField = 'title';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	function parentNode() {
		
		if(!empty($this->data[$this->alias]['parent_id'])){
			return $this->myNodeRef($this->data[$this->alias]['parent_id']);
		}
		return $this->Node->buildPath('Structure',false,true);
	}
	
	var $belongsTo = array(
		'ParentStructureType' => array(
			'className' => 'StructureType',
			'foreignKey' => 'parent_id',
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
		)
	);

	var $hasMany = array(
		'ChildStructureType' => array(
			'className' => 'StructureType',
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
		),
		'Structure' => array(
			'className' => 'Structure',
			'foreignKey' => 'structure_type_id',
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
