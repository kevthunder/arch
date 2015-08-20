<?php
class TileType extends AppModel {
	var $name = 'TileType';
	var $displayField = 'title';
	var $actsAs = array(
		'Tree','Node',
		'Inheritor'=>array('skin_id'=>'ParentTileType')
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	function parentNode() {
		
		if(!empty($this->data[$this->alias]['parent_id'])){
			return $this->myNodeRef($this->data[$this->alias]['parent_id']);
		}
		return $this->Node->buildPath('Tile',false,true);
	}
	
	var $belongsTo = array(
		'Skin' => array(
			'className' => 'Skin',
			'foreignKey' => 'skin_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ParentTileType' => array(
			'className' => 'TileType',
			'foreignKey' => 'parent_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'ChildTileType' => array(
			'className' => 'TileType',
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
