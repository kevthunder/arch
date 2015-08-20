<?php
class Character extends AppModel {
	var $name = 'Character';
	var $displayField = 'name';
	var $actsAs = array(
		'Node','EventTrigger','Planned','Tiled',
		'XmlLink.XmlLinked'=>array('find'=>array('fields'=>array('name','speed','tile_id','skin_id'),'contain'=>array('Skin'),'internal'=>array())),
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
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'CharacterType' => array(
			'className' => 'CharacterType',
			'foreignKey' => 'character_type_id',
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
		),
	);
	
	
	var $hasMany = array(
		'Item' => array(
			'className' => 'Item',
			'foreignKey' => 'character_id',
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
	
	var $hasAndBelongsToMany = array(
		'CharacterSubtype' => array(
			'className' => 'CharacterType',
			'joinTable' => 'characters_subtypes',
			'foreignKey' => 'character_id',
			'associationForeignKey' => 'character_type_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);
	
	function parentNode() {
		return $this->Node->buildPath('Model/Character',false,true);
	}
	
	var $basicStats = array(
		'total_hp', 'speed'
	);
	
	
	function beforeSave($options = array() ){
		if(isset($this->data[$this->alias]['hp'])){
			$this->Behaviors->attach('Util');
			$this->data[$this->alias]['originalData']['hp'] = $this->getOriginalData('hp');
		}
		$create = (empty($this->id) && empty($this->data[$this->alias]['id']));
		if($create && !empty($this->data[$this->alias]['total_hp']) && empty($this->data[$this->alias]['hp']) ){
			$this->data[$this->alias]['hp'] = $this->data[$this->alias]['total_hp'];
		}
		return true;
	}
	
	function afterSave(){
		if(isset($this->data[$this->alias]['hp']) && isset($this->data[$this->alias]['originalData']['hp'])){
			if($this->data[$this->alias]['hp'] <= 0 && $this->data[$this->alias]['originalData']['hp'] > 0){
				$this->triggerAction(25 /*dead*/);
			}
		}
	}
	
	function getStats($character, $options = array()){
		$defOpt = array(
			'stats' => $this->basicStats
		);
		$opt = array_merge($defOpt);
		if(empty($opt['stats'])){
			return array();
		}
		if(!is_array($character) && is_numeric($character)){
			$character = $this->find('first',array('conditions'=>array($this->alias.'.id'=>$character),'contain'=>array('CharacterSubtype')));
		}
		$typeIds = array();
		if(isset($character[$this->alias])){
			$character = $character[$this->alias];
		}
		if(!empty($character['character_type_id'])){
			$typeIds[] = $character['character_type_id'];
		}
		if(!empty($character['CharacterSubtype'])){
			$typeIds = array_merge($typeIds,array_filter(Set::extract($character['CharacterSubtype'],'{n}.id')));
		}
		if(empty($typeIds)){
			return null;
		}
		$this->CharacterType->Behaviors->attach('Util');
		$findOpt = array(
			'conditions'=>array(
			),
			'joins' => array(
				$this->CharacterType->treeJointOpt(false,array('conditions'=>array('ChildCharacterType.id'=>$typeIds)))
			),
			'order' => array('lft ASC'),
			'recursive' => -1
		);
		$types = $this->CharacterType->find('all',$findOpt);
		$stats = array_combine($opt['stats'],array_fill(0, count($opt['stats']), 0));
		foreach($types as $type){
			foreach($opt['stats'] as $stat){
				if(isset($type['CharacterType'][$stat.'_multi'])){
					$stats[$stat] *= $type['CharacterType'][$stat.'_multi'];
				}
				if(isset($type['CharacterType'][$stat])){
					$stats[$stat] += $type['CharacterType'][$stat];
				}
			}
		}
		return $stats;
	}
	
	function getGetAiUpdater($character=null){
		if(empty($character)){
			$character = $this->id;
		}
		if(!is_array($character) && is_numeric($character)){
			if(!empty($this->data) && $this->data[$this->alias]['id'] == $character){
				$character = $this->data;
			}else{
				$character = $this->find('first',array('conditions'=>array($this->alias.'.id'=>$character)));
			}
		}
		if(empty($character[$this->alias])){
			$character = array($this->alias=>$character);
		}
		//debug($character);
		$nodeId = $this->myNodeId($character);
		$findOpt = array(
			'conditions'=>array(
				'event_type_id'=>26,
				'owner_id'=>$nodeId,
			),
			'recursive' => -1,
		);
		$this->TimedEvent = ClassRegistry::init('TimedEvent');
		$this->TimedEvent->checkLifetime(false);
		$updater = $this->TimedEvent->find('first',$findOpt);
		if(empty($updater)){
			$format = $this->TimedEvent->getDataSource()->columns['datetime']['format'];
			$this->Tile = ClassRegistry::init('Tile');
			$pos = $this->getPos($character);
			$updater = array('TimedEvent'=>array(
				'time'=> date($format),
				'event_type_id'=>26,
				'aco_id'=>$nodeId,
				'x'=>$pos['x'],
				'y'=>$pos['y'],
				'range'=>$this->Tile->chunkSize/2,
				'repeate'=> 10,
				'active' => 1,
				'owner_id'=>$nodeId,
				'context'=>"AiUpdater",
			));
			$this->TimedEvent->create();
			$this->TimedEvent->save($updater);
			$updater['TimedEvent']['id'] = $this->TimedEvent->id;
		}
		//debug($updater);
		return $updater;
	}
	
	function aiAfterSkillInstance($inst,$character=null){
		if(empty($character)){
			$character = $inst['SkillInstance']['caster_id'];
		}
		$aiUpdater = $this->getGetAiUpdater($character);
		$this->TimedEvent = ClassRegistry::init('TimedEvent');
		$this->TimedEvent->save(array('id'=>$aiUpdater['TimedEvent']['id'],'time'=>$inst['SkillInstance']['cooled_down_time']));
	}
	
	function move($options){
		$defOpt = array('character'=>null,'toTile'=>null,'range'=>0,'fromTile'=>null);
		$opt = array_merge($defOpt,$options);
		if(empty($opt['character'])){
			if(!empty($this->data)){
				$opt['character'] = $this->data;
			}elseif(!empty($this->id)){
				$opt['character'] = $this->id;
			}
		}
		$Path = ClassRegistry::init('Path');
		$pathData = $Path->calculPath($opt);
		
		if($pathData && $Path->save($pathData)){
			$path_id = $Path->id;
			
			$castOpt = array(
				'skill_id' => 2,
				'caster' => $opt['character']
			);
			$inst = $this->cast($castOpt);
			
			if($inst){
				$res = $Path->save(array(
					'skill_instance_id' => $inst['SkillInstance']['id'],
					'id'=> $path_id,
				));
				if($res){
					$Skill = ClassRegistry::init('Skill');
					$res = $Skill->SkillInstance->updateDelays($inst);
					return $res;
				}
			}
		}
		return false;
	}
	
	function cast($options){
		$defOpt = array('character'=>null);
		$opt = array_merge($defOpt,$options);
		if(empty($opt['caster_id'])){
			if(!empty($opt['caster'])){
				if(is_numeric($opt['caster'])){
					$opt['caster_id'] = $opt['caster'];
				}elseif(!empty($opt['caster']['id'])){
					$opt['caster_id'] = $opt['caster']['id'];
				}elseif(!empty($opt['caster']['Character']['id'])){
					$opt['caster_id'] = $opt['caster']['Character']['id'];
				}elseif(!empty($opt['caster'][$this->alias]['id'])){
					$opt['caster_id'] = $opt['caster'][$this->alias]['id'];
				}
			}elseif(!empty($this->data)){
				$opt['caster_id'] = $this->data[$this->alias]['id'];
			}elseif(!empty($this->id)){
				$opt['caster_id'] = $this->id;
			}
		}
		
		$Skill = ClassRegistry::init('Skill');
		$res = $Skill->SkillInstance->queueSkill($opt);
		
		return $res;
	}
}
