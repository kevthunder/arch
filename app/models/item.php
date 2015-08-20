<?php
class Item extends AppModel {
	var $name = 'Item';
	var $displayField = 'title';
	var $actsAs = array(
		'serialized'=>array('ui_behaviors'),
		'XmlLink.XmlLinked'=>array('fields'=>array('title','strength','tile_id','item_type_id') ,'contain'=>array('Skin','ItemType'),'internal'=>array('skin_id')),
		'Node','EventTrigger',
		'Inheritor'=>array('skin_id'=>'ItemType','title'=>'ItemType','ui_behaviors'=>'ItemType'),
		'NodeLinked'=>array(
			'follow'=>array(
				'Tile'=>array(
					'type' => 'invalidation',
					'owner' => 'owned',
				)
			)
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Tile' => array(
			'className' => 'Tile',
			'foreignKey' => 'tile_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Character' => array(
			'className' => 'Character',
			'foreignKey' => 'character_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ItemType' => array(
			'className' => 'ItemType',
			'foreignKey' => 'item_type_id',
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
	
	
	
	function parentNode() {
		if(empty($this->data[$this->alias]['id']) || $this->data[$this->alias]['id'] != $this->id){
			$this->read(array('id','item_type_id'),$this->id);
		}
		if(!empty($this->data[$this->alias]['item_type_id'])){
			return $this->ItemType->myNodeRef($this->data[$this->alias]['item_type_id']);
		}
		
		return $this->Node->buildPath('Item',false,true);
	}
}
