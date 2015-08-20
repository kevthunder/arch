<?php
class SkillInstance extends AppModel {
	var $name = 'SkillInstance';
	var $actsAs = array(
		'serialized'=>array('data'),
		'XmlLink.XmlLinked'=>array(
			'find'=>array('fields'=>array(
				'skill_id',
				'caster_id',
				'main_target_model'=>'MainTarget.model',
				'main_target_key'=>'MainTarget.foreign_key',
			))
		),
		'Node',
		'EventTrigger'=>array(
			'timedEvent'=>array(
				'start_time' => array(
					'autoTrigger' => false,
					'eventType' => 17,
					'timeField' => 'start_time',
				),
				'cast_time' => 4,
				'recovered_time' => 18,
				'cooled_down_time' => 19,
			)
		),
		'NodeLinked'=>array(
			'follow'=>array(
				'Caster'=>array(
					'type' => 'invalidation',
					'owner' => 'owned',
					'endField' => 'cooled_down_time',
				)
			)
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Skill' => array(
			'className' => 'Skill',
			'foreignKey' => 'skill_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Caster' => array(
			'className' => 'Character',
			'foreignKey' => 'caster_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'MainTarget' => array(
			'className' => 'Node',
			'foreignKey' => 'main_target_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	
	function parentNode() {
		if(!empty($this->data[$this->alias]['id']) && $this->data[$this->alias]['id'] != $this->id){
			$this->read(array('id','skill_id'),$this->id);
			
		}
		if(!empty($this->data[$this->alias]['skill_id'])){
			return $this->Skill->myNodeRef($this->data[$this->alias]['skill_id']);
		}
		
		return $this->Node->buildPath('Skill',false);
	}
	
	function _getDelays($inst = null){
		$delays = array('cast_time'=>intval($inst['Skill']['cast_time']),'recovery_time'=>intval($inst['Skill']['recovery_time']),'cool_down'=>intval($inst['Skill']['cool_down']));
		if(empty($this->Event)) $this->Event = ClassRegistry::init('Event'); 
		//debug($inst);
		$opt = array(
			'action'=> 20,
			'acos' => $this->myNodeRef($inst),
			'phase' => 2,
			'return'=>$delays,
			'strict'=>false,
			'params'=>array(
				'start_time'=>$inst['SkillInstance']['start_time']
			)
		);
		$delays = $this->Event->dispatchEvent($opt);
		return $delays;
	}
	
	function updateDelays($inst,$dry = false){
		$this->Behaviors->attach('Util');
		$inst = $this->getMinimalData(array('id','start_time','Skill.cast_time','Skill.recovery_time','Skill.cool_down'), $inst, array(), false);
		$delays = $this->_getDelays($inst);
		if(!empty($delays)){
			$data = array('id'=>$inst['SkillInstance']['id']);
			$data['cast_time'] = strtotime($inst['SkillInstance']['start_time']) + $delays['cast_time'];
			$data['recovered_time'] = $data['cast_time'] + $delays['recovery_time'];
			$data['cooled_down_time'] = $data['recovered_time'] + $delays['cool_down'];
			$format = $this->getDataSource()->columns['datetime']['format'];
			$data['cast_time'] = date($format,$data['cast_time']);
			$data['recovered_time'] = date($format,$data['recovered_time']);
			$data['cooled_down_time'] = date($format,$data['cooled_down_time']);
			if($dry){
				return $data;
			}elseif($this->save($data)){
				return true;
			}
		}
		if($dry){
			return array();
		}else{
			return false;
		}
	}
	
	function beforeSave($options) {
		if(!empty($this->data[$this->alias]['start_time']) && (!empty($this->id) || !empty($this->data[$this->alias]['id']))){
			$this->data[$this->alias] = array_merge($this->data[$this->alias],$this->updateDelays($this->data,true));
		}
		return true;
	}
	
	
	function afterLinkRead($results){
		if(!empty($results['SkillInstance'])){
			foreach($results['SkillInstance'] as $key => $val){
				if(!empty($val['main_target_model'])){
					$val['main_target_model'] = Inflector::underscore($val['main_target_model']);
				}
				$results['SkillInstance'][$key] = $val;
			}
		}
		return $results;
	}
	
	function timedEventCreated($timedEvent,$skillInstance){
		$toInvalidate = array(18,19,4);
		if(in_array($timedEvent['TimedEvent']['event_type_id'],$toInvalidate)){
			$eventNode = array('model'=>'TimedEvent','foreign_key'=>$timedEvent['TimedEvent']['id']);
			$this->NodeLink = ClassRegistry::init('NodeLink');
			$nodeLink = $this->NodeLink->link('invalidation', $timedEvent['TimedEvent']['owner_id'], $eventNode, $opt = array('context'=> 'casting'));
		}
	}
	
	function queueSkill($params = array()){
		if(!empty($params['skill_id']) && !empty($params['caster_id'])){
			$aros = array('model'=>'Character','foreign_key'=>$params['caster_id']);
			$acos = array('model'=>'Skill','foreign_key'=>$params['skill_id']);

			
			$res = $this->triggerAction(array(
						'action' => 'queueSkill',
						'method' => '_queueSkill',
						'params'=> $params,
						'aros'=> $aros,
						'acos'=> $acos
			));
			return $res;
		}
	}
	
	function _queueSkill($data = array()){
		if(!empty($data['skill_id']) && !empty($data['caster_id'])){
			$format = $this->Skill->SkillInstance->getDataSource()->columns['datetime']['format'];
			$now = date($format);
			//////// Get conflict SkillInstance ////////
			$findOpt = array(
				'conditions'=>array(
					'caster_id' => $data['caster_id'],
					'or' => array(
						'recovered_time >' => $now,
						array(
							'skill_id' => $data['skill_id'],
							'cooled_down_time > ' => $now
						)
					)
				),
				'order'=> array('IF(`skill_id` = '.$data['skill_id'].',cooled_down_time,recovered_time) DESC')
			);
			$lastConflict = $this->Skill->SkillInstance->find('first',$findOpt);
			
			//////// Create SkillInstance ////////
			$allowed = array('skill_id','caster_id','main_target_id');
			$inst = array_intersect_key($data,array_flip($allowed));
			//debug($inst);
			$start_time = $now;
			if(!empty($lastConflict)){
				$inst['prev_id'] = $lastConflict['SkillInstance']['id'];
				$afterCooldown = ($lastConflict['SkillInstance']['skill_id'] == $data['skill_id']);
				if($afterCooldown){
					if(!empty($lastConflict['SkillInstance']['cooled_down_time'])){
						$start_time = $lastConflict['SkillInstance']['cooled_down_time'];
					}
				}else{
					if(!empty($lastConflict['SkillInstance']['recovered_time'])){
						$start_time = $lastConflict['SkillInstance']['recovered_time'];
					}
				}
			}
			$this->Skill->SkillInstance->create();
			$inst = $this->Skill->SkillInstance->save($inst);
			if($inst){
				$instance_id = $this->Skill->SkillInstance->id;
				$inst = Set::Merge($inst,$this->Skill->SkillInstance->save(array('id'=>$instance_id,'start_time'=>$start_time)));
				if(!empty($lastConflict)){
					$lastData = array(
						'id' => $lastConflict['SkillInstance']['id'],
						'next_id' => $instance_id,
						'next_need_cooldown' => $afterCooldown,
					);
					$this->Skill->SkillInstance->save($lastData);
				}
				
				if(!empty($inst['TimedEvent']['start_time']['TimedEvent']['id'])){
					$this->TimedEvent = ClassRegistry::init('TimedEvent');
					$this->TimedEvent->checkEvents($inst['TimedEvent']['start_time']);
				}
				
				return $inst;
			}
		}
		return null;
	}
	
	function getPos($entry){
		if(is_numeric($entry)){
			$this->Behaviors->attach('Containable');
			$entry = $this->find('first',array('conditions'=>array($this->alias.'.id'=>$entry),'contain'=>array('Caster'=>array('Tile'))));
		}
		if(!empty($entry[$this->alias]['Caster'])){
			return $this->Caster->getPos($entry[$this->alias]['Caster']);
		}
		if(!empty($entry['Caster'])){
			return $this->Caster->getPos($entry['Caster']);
		}
		if(!empty($entry[$this->alias]['caster_id'])){
			return $this->Caster->getPos($entry[$this->alias]['caster_id']);
		}
		if(!empty($entry['caster_id'])){
			return $this->Caster->getPos($entry['caster_id']);
		}
		return null;
	}
}
