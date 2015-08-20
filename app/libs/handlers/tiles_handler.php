<?php
class TilesHandler extends Object {
	function test(){
		debug(func_get_args());
	}
	
	function chunkUpdate(&$options,$params){
		if($options['phase'] == 2){
			$this->Tile = ClassRegistry::init('Tile');
			//var_dump($options);
			$this->_updateVoidTiles(&$options,$params);
		}
		return true;
	}
	
	function _updateVoidTiles(&$options,$params){
		$findOpt = array(
			'conditions'=>array(
				'tile_type_id'=>2,
				'x >=' => $options['left'],
				'x <=' => $options['right'],
				'y >=' => $options['top'],
				'y <=' => $options['bottom'],
			),
			'recursive' => -1,
			'limit'=>1,
			'order'=>'presence'//'Rand()*(-1/presence)'
		);
		$tileToUpdate = $this->Tile->find('first',$findOpt);
		if(!empty($tileToUpdate)){
			//debug($tileToUpdate);
			$modif = array();
			
			$genRange = 5;
			$genMulti = 0.002;
			
			$this->Charge = ClassRegistry::init('Charge');
			$findOpt = array('conditions'=>array(
				'context' => 'voidMonster',
				'x >=' => $tileToUpdate['Tile']['x']-$genRange,
				'x <=' => $tileToUpdate['Tile']['x']+$genRange,
				'y >=' => $tileToUpdate['Tile']['y']-$genRange,
				'y <=' => $tileToUpdate['Tile']['y']+$genRange,
			));
			$generator = $this->Charge->find('first',$findOpt);
			
			$powerToAdd = $tileToUpdate['Tile']['presence']*-1*$genMulti;
			//debug($generator);
			
			if(empty($generator)){
				$findOpt = array(
					'conditions'=>array(
						'tile_type_id'=>1,
						'x >=' => $tileToUpdate['Tile']['x']-$genRange,
						'x <=' => $tileToUpdate['Tile']['x']+$genRange,
						'y >=' => $tileToUpdate['Tile']['y']-$genRange,
						'y <=' => $tileToUpdate['Tile']['y']+$genRange,
					),
					'recursive' => -1,
					'limit'=>1,
					'order'=>'Rand()'
				);
				$genTile = $this->Tile->find('first',$findOpt);
				debug($genTile);
				
				$data = array(
					'context' => 'voidMonster',
					'value' => $powerToAdd,
					'updates_needed' => rand(1,400),
					'holder_id' =>  $genTile['Tile']['id'],
					'x' =>  $genTile['Tile']['x'],
					'y' =>  $genTile['Tile']['y'],
				);
				
				$this->Charge->create();
				$data = $this->Charge->save($data);
				
				
				$modif['presence'] = 0;
			}else{
				if($generator['Charge']['update_count']+1 >= $generator['Charge']['updates_needed']){
					$remainingEnergy = $generator['Charge']['value']+$powerToAdd;
					$this->Character = ClassRegistry::init('Character');
					$a =  $this->Character->CharacterType->alias;
					$findOpt = array(
						'fields' => array(
							$a.'.id',$a.'.needed_presence'
						),
						'conditions'=>array(
							$a.'.complete_type' => 1,
							$a.'.needed_presence <=' => $remainingEnergy,
							$a.'.needed_presence >' => $powerToAdd,
						),
						'recursive' => -1,
						'order' => $a.'.needed_presence DESC',
						'joins' => array(
							array(
								'alias' => 'ParentType',
								'table'=> $this->Character->CharacterType->useTable,
								'type' => 'INNER',
								'conditions' => array(
									'ParentType.id' => 3, 
									'ParentType.lft < '.$a.'.lft',
									'ParentType.rght > '.$a.'.rght',
								)
							)
						)
					);
					$types = $this->Character->CharacterType->find('all',$findOpt);
					$toCreateCount = array();
					foreach($types as $type){
						if($type['CharacterType']['needed_presence'] <= $remainingEnergy){
							$toCreateCount[$type['CharacterType']['id']] = floor($remainingEnergy/$type['CharacterType']['needed_presence']);
						}
					}
					$total = array_sum($toCreateCount);
					//get close tiles
					$t = $this->Tile->alias;
					$findOpt = array(
						'fields'=> array($t.'.id'),
						'conditions' => array(
							$t.'.tile_type_id'=>1,
							'ABS('.$t.'.y - Target.y) <= '.($total+3),
							'ABS('.$t.'.x - Target.x) <= '.($total+3),
						),
						'joins' => array(
							array(
								'alias' => 'Target',
								'table'=> $this->Tile->useTable,
								'type' => 'INNER',
								'conditions' => array(
									'Target.id' => $generator['Charge']['holder_id'],
								)
							)
						),
						'order' => 'GREATEST(ABS('.$t.'.y - Target.y),ABS('.$t.'.x - Target.x))',
						'limit' => $total,
						'recursive' => -1,
					);
					$tiles = array_values($this->Tile->find('list',$findOpt));
					//debug($tiles);
					
					//debug($toCreateCount);
					//debug(array_keys($toCreateCount));
					$types = $this->Character->CharacterType->find('all',array('conditions'=>array($a.'.id'=>array_keys($toCreateCount)),'recursive'=>-1));
					$toCreate = array();
					$t = 0;
					foreach($types as $type){
						$nb = $toCreateCount[$type['CharacterType']['id']];
						$data = array(
							'active' => 1,
							'character_type_id'=>$type['CharacterType']['id'],
							'character_type_id'=>$type['CharacterType']['skin_id'],
						);
						$data = array_merge($data,$this->Character->getStats($data));
						for ($i = 0; $i < $nb; $i++) {
							$cdata = $data;
							$cdata['tile_id'] =  $tiles[$t];
							$toCreate[] = $cdata;
							$t++;
						}
					}
					$this->Character->Behaviors->attach('Util');
					debug($toCreate);
					$this->Character->bulkModify($toCreate);
					$modif['presence'] = $remainingEnergy / $genMulti;
					$this->Charge->delete($generator['Charge']['id']);
				}else{
					$data = array(
						'id' => $generator['Charge']['id'],
						'value' => $generator['Charge']['value'] + $powerToAdd,
						'update_count' => $generator['Charge']['update_count'] + 1,
					);
					$modif['presence'] = 0;
					$data = $this->Charge->save($data);
				}
			}
			if(!empty($modif)){
				$modif['id'] = $tileToUpdate['Tile']['id'];
				//debug($modif);
				$this->Tile->save($modif);
			}
		}
	}
}
?>