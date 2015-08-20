<?php
class CharacterHandler extends Object {

	function damage_form(){
		$this->Element = ClassRegistry::init('Element');
		$elements = $this->Element->find('list');
		return array(
			'elemType'=>array('type'=>'select','options'=>$elements),
			'amount'=>array(),
			'target'=>array(),
			'targetIsNode'=>array('type'=>'checkbox'),
		);
	}
	
	function aiUpdate(&$eventOptions,$params){
		$this->Character = ClassRegistry::init('Character');
		
		$this->Node = ClassRegistry::init('Node');
		$nodeId = $eventOptions['acos'];
		if(is_array($nodeId)){
			$nodeId = $nodeId[0];
		}
		$character = $this->Node->getItem($nodeId,array('model'=>'Character'));
		//debug($character);
		
		if(!empty($character)){
			if($character['Character']['character_type_id'] != 11){
				$agroRange = 8;
				$closeChar = $this->Character->getInRect(array(
					'w'=>$agroRange*2-1,
					'h'=>$agroRange*2-1,
					'centerTile'=>$character['Tile'],
					'mode'=>'first',
					'conditions'=>array('Character.character_type_id'=>11),
					'order'=>'SQRT(POW(Tile.x - '.$character['Tile']['x'].',2)+POW(Tile.y - '.$character['Tile']['y'].',2))'
				));
			
				if($closeChar){
					//Target found, attack it
					$this->Skill = ClassRegistry::init('Skill');
					$skill = $this->Skill->find('first',array('conditions'=>array('Skill.id'=>6)));
					
					if($this->Character->Tile->dist($character,$closeChar) > $skill['Skill']['range']){
						$this->Character->move(array('character'=>$character,'toTile'=>$closeChar,'range'=>$skill['Skill']['range']-1));
					}
					$inst = $this->Character->cast(array('caster'=>$character,'skill_id'=>6,'character_id'=>$closeChar['Character']['id']));
					$this->Character->aiAfterSkillInstance($inst,$character);
				}else{
					//no target, move
					$maxMoveTry = 10;
					$moveRange = 10;
					$this->Tile = ClassRegistry::init('Tile');
					$i = 0;
					do{
						$moveTo = array('x'=>$character['Tile']['x']+rand(-$moveRange,$moveRange),'y'=>$character['Tile']['y']+rand(-$moveRange,$moveRange));
						$tile = $this->Tile->find('first',array('conditions'=>array('x'=>$moveTo['x'],'y'=>$moveTo['y']),'recursive'=>-1));
						$i++;
					}while(!$this->Tile->testForPathing($character,$tile) && $i<=$maxMoveTry);
					if($this->Tile->testForPathing($character,$tile)){
						$inst = $this->Character->move(array('character'=>$character,'toTile'=>$tile));
						$this->Character->aiAfterSkillInstance($inst,$character);
					}
				}
			}else{
			}
		}
	}
	
	function damage(&$eventOptions,$params){
		if(!in_array(24,Set::extract('/name',$eventOptions['subEvent']))){
			$defParams = array(
				'elemType' => 1,
				'amount' => 0,
				'target' => null,
				'targetIsNode' => false,
			);
			if(empty($params['target'])){
				$acos = (array)$eventOptions['acos'];
				$params['target'] = $acos[0];
				$params['targetIsNode'] = true;
			}
			$opt = array_merge($defParams,$params);
			$char_id = null;
			if($opt['targetIsNode']){
				$ref = $opt['target'];
			}else{
				$ref = array('model'=>'Character','foreign_key'=>$opt['target']);
			}
			if(empty($ref)) return false;
			
			$eventOpt = array(
				'name' => 24,
				'aros' => $eventOptions['aros'],
				'acos' => $ref,
				'params' => array(
					'damage' => $opt['amount'],
					'elemType' => $opt['elemType'],
				)
			);
			$eventOptions['subEvent'][] = $eventOpt;
		}
		/*$damage = $opt['amount'];
		if(!empty($eventOptions['damageOffset'])){
			$damage += $eventOptions['damageOffset'];
		}
		if(isset($eventOptions['damageMulti'])){
			$damage *= $eventOptions['damageMulti'];
		}
		if(!empty($eventOptions['damageAllowMinus']) && $damage < 0){
			$damage = 0;
		}
		if($damage != 0){
			$this->Character = ClassRegistry::init('Character');
			$this->Character->create();
			$char = $this->Character->find('first',(array('fields'=>array($this->Character->primaryKey,'hp'),'conditions'=>array($this->Character->primaryKey=>$char_id),'recursive'-1);
			if($char){
				$char[$this->Character->alias]['hp'] -= $damage;
				$this->Character->create();
				if($this->Character->save($char)){
					return true;
				}
			}
			return false;
		}*/
		return true;
	}

}
?>