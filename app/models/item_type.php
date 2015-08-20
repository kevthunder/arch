<?php
class ItemType extends AppModel {
	var $name = 'ItemType';
	var $displayField = 'title';
	var $actsAs = array(
		'Tree','Node',
		'serialized'=>array('ui_behaviors'),
		'Inheritor'=>array('skin_id'=>'ParentStructureType')
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	function parentNode() {
		
		if(!empty($this->data[$this->alias]['parent_id'])){
			return $this->myNodeRef($this->data[$this->alias]['parent_id']);
		}
		return $this->Node->buildPath('Item',false,true);
	}
	
	var $belongsTo = array(
		'Skin' => array(
			'className' => 'Skin',
			'foreignKey' => 'skin_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ParentItemType' => array(
			'className' => 'ItemType',
			'foreignKey' => 'parent_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'ChildItemType' => array(
			'className' => 'ItemType',
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
		'Item' => array(
			'className' => 'Item',
			'foreignKey' => 'item_type_id',
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
