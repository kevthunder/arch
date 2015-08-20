<?php
class EventType extends AppModel {
	var $name = 'EventType';
	var $actsAs = array('Tree');
	var $displayField = 'name';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'ParentEventType' => array(
			'className' => 'EventType',
			'foreignKey' => 'parent_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'ChildEventType' => array(
			'className' => 'EventType',
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
	
	function getSupTypes($id,$getparent=true){
		if(is_numeric($id)){
			$typesFindCond = array(
				'Base.id' => $id
			);
		}else{
			$typesFindCond = array(
				'Base.name' => $id
			);
		}
		$res = $this->find('list',array(
			'joins'=>array(array(
				'table' => $this->useTable,
				'alias' => 'Base',
				'type' => 'INNER',
				'conditions' => array(
					$typesFindCond,
					($getparent?
						'Base.lft BETWEEN '.$this->alias.'.lft AND '.$this->alias.'.rght'
					:
						$this->alias.'.lft BETWEEN Base.lft AND Base.rght'
					),
				)
			)),
			'conditions' => 1,
			'order' => $this->alias.'.lft '.($getparent?'DESC':'ASC'),
		)); 
		return $res;
	}	

}
