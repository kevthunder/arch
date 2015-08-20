<?php
class StructuresHandler extends Object {

	
	function createStructure_form(){
		$this->StructureType = ClassRegistry::init('StructureType');
		$structures = $this->StructureType->generatetreelist();
		return array(
			'type'=>array('type'=>'select','options'=>$structures),
		);
	}
	function createStructure(&$options,$params){
		$this->Structure = ClassRegistry::init('Structure');
		if(isset($params['type'])){
			$this->Structure->StructureType->recursive = -1;
			$variant = $type = $this->Structure->StructureType->read(null,$params['type']);
			do{
				$type = $variant;
				$variant = $this->Structure->StructureType->find('first',array(
					'conditions'=>array(
						'lft >'=>$type['StructureType']['lft'],
						'rght <'=>$type['StructureType']['rght'],
						'variant'=>1
					),
					'order'=>'RAND()'
				));
			}while(!empty($variant));
			//debug($type);
			
			$this->Node = ClassRegistry::init('Node');
			if(!empty($options['targetedTile'])){
				$tiles = array($options['targetedTile']);
			}else{
				$tiles = $this->Node->getAssociatedEntries($options['acos'],array('recursive'=>-1,'fields'=>array('id')),$models);
			}
			//debug($tiles);
			$data = array(
				'active' => 1,
				'structure_type_id' => $type['StructureType']['id'],
				'tile_id' => $tiles[0][$this->Structure->Tile->alias]['id']
			);
			$this->Structure->create();
			$this->Structure->save($data);
		}
		//debug($node);
		//debug($params);
	}
}
?>