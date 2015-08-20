<?php
class Structure extends AppModel {
	var $name = 'Structure';
	var $displayField = 'title';
	var $actsAs = array(
		'serialized'=>array('ui_behaviors'),
		'XmlLink.XmlLinked'=>array('fields'=>array('strength','tile_id','structure_type_id','skin_id') ,'contain'=>array('Skin','StructureType'),'internal'=>array()),
		'Node','EventTrigger','Invalided',
		'Inheritor'=>array('skin_id'=>'StructureType','ui_behaviors'=>'StructureType'),
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
		'StructureType' => array(
			'className' => 'StructureType',
			'foreignKey' => 'structure_type_id',
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
		$struct_type_id = null;
		if(empty($this->data[$this->alias]) || !array_key_exists('structure_type_id',$this->data[$this->alias]) || (!empty($this->data[$this->alias]['id']) && $this->data[$this->alias]['id'] != $this->id)){
			$struct = $this->find('first',array('fields'=>array('id','structure_type_id'), 'conditions'=>array('id'=>$this->id), 'recursive' =>-1));
			$struct_type_id = $struct[$this->alias]['structure_type_id'];
		}else{
			$struct_type_id = $this->data[$this->alias]['structure_type_id'];
		}
		if(!empty($this->data[$this->alias]['structure_type_id'])){
			return $this->StructureType->myNodeRef($this->data[$this->alias]['structure_type_id']);
		}
		
		return $this->Node->buildPath('Structure',false);
	}
	
	/*function beforeSave($options){
		parent::afterSave($options);
		debug($options);
	}*/
	
	/*function afterFind($results, $primary){
		$results = parent::afterFind($results, $primary);
		return $results;
	}*/
	
	function afterSave($created){
		parent::afterSave($created);
		if($created){
			$this->updatePathing();
		}
	}
	
	function updatePathing($id = null){
		if(empty($id)){
			$id = $this->id;
		}
		$this->Behaviors->attach('Containable');
		$data = $this->find('first',array('conditions'=>array($this->alias.'.id'=>$id),'contain'=>array('Tile'=>array('Node'),'StructureType','Node')));
		debug($data);
		$pathingData = $data['StructureType']['pathing'];
		//$pathingData = array('4x4'=>10);<
		$this->Event = ClassRegistry::init('Event');
		$this->Event->recursive = -1;
		$existing = $this->Event->find('all',array(
			'conditions'=>array(
				'phase' => 1,
				'context' => 'Pathing',
				'owner_id' => $data['Node']['id']
			)
		));
		debug($existing);
		$toDelete = array();
		foreach($existing as $event){
			$ekey = $event['Event']['aco_id'].':'.$event['Event']['event_type_id'];
			if(empty($toDelete[$ekey])){
				$toDelete[$ekey] = $event['Event'];
			}else{
				$toDelete[] = $event['Event'];
			}
		}
		//debug($toDelete);
		if(!empty($pathingData)){
			foreach($tmp = $pathingData as $key=>$val){
				if(strpos($key,'x')!==false){
					list($w, $h) = explode('x',$key,2);
					for($x=0;$x<$w;$x++){
						for($y=0;$y<$h;$y++){
							$pathingData[$x.';'.$y] = $val;
						}
					}
					unset($pathingData[$key]);
				}elseif(strpos($key,';')===false){
					unset($pathingData[$key]);
				}
			}
			$maxX = $maxY = 0;
			foreach($pathingData as $key=>$val){
				list($x, $y) = explode(';',$key,2);
				$maxX = max($maxX,$x);
				$maxY = max($maxY,$y);
			}
			if($maxX > 0 || $maxY > 0){
				$tiles = $this->Tile->getRect(array('w'=>$maxX+1,'h'=>$maxY+1,'centerTile'=>$data['Tile'],'aliased'=>true));
			}else{
				$tiles = array('0;0' => $data['Tile']);
			}
			debug($tiles);
			$events = array();
			App::import('Lib', 'SetMulti');
			
			foreach($pathingData as $key=>$val){
				if(isset($tiles[$key])){
					foreach((array)$val as $t){
						$event = array(
							'aro_id' => null,
							'aco_id' => SetMulti::extractHierarchic(array('Node.id','Tile.Node.id'), $tiles[$key]),
							'handler' => 'false',
							'event_type_id' => $t,
							'phase' => 1,
							'active' => 1,
							'context' => 'Pathing',
							'owner_id' => $data['Node']['id']
						);
						$exist = false;
						$ekey = $event['aco_id'].':'.$event['event_type_id'];
						if(!empty($toDelete[$ekey])){
							$exist = $toDelete[$ekey];
							unset($toDelete[$ekey]);
						}
						if($exist && count(array_diff_assoc($event,$exist)) > 0){
							$exist = false;
						}
						if(!$exist){
							$events[] = $event;
						}
					}
				}
			}
			
			//debug($toDelete);
			debug($events);
			
			foreach($events as $event){
				$this->Event->create();
				$this->Event->save($event);
			}
			foreach($toDelete as $event){
				$this->Event->delete($event['id']);
			}
			$pathing = array();
		}
	}
}
