<?php
class SkillsLinkAction extends LinkAction {
	var $uses = array('Skill','NodeLink','User','Event');

	
	function my_listing() {
		//debug($this->user);
		
		if(!empty($this->controller->user)){
			$skillsNodes = $this->NodeLink->getLinked($this->defaultAro,'skillProvider',array('filter' => array('model' => 'Skill')));
			
			//debug($this->Skill->findFromNode($skillsNodes));
			$findOptions = array('conditions'=>$this->Skill->findFromNode($skillsNodes,array('dry'=>true)));
			$skills = $this->Skill->triggerAction('linkRead',array($findOptions),$this->defaultAro);
			if($skills){
				//debug($skills);
				$this->addItems($skills);
			}else{
				$this->addMsg(403);
			}
		}else{
			$this->addMsg(300);
		}
	}
	
	function move(){
		try {
			$xml = new SimpleXMLElement($this->requestXml);
		} catch (Exception $e) {
			
		}
		
		if(!empty($this->controller->user)){
		
		
		
			$this->Path = ClassRegistry::init('Path');
			$data = $this->Path->xmlToData($xml->path);
			if(!empty($data)){
				
				if($this->Path->save($data)){
					$path_id = $this->Path->id;
					
					$this->params['skill_id'] = 2;
					$inst = $this->_cast();
					
					//if(!empty($res['TimedEvent']['start_time']['TimedEvent']['id'])){
					//	$options['timed_event_id'] = $res['TimedEvent']['start_time']['TimedEvent']['id'];
					//}
					
					if($inst){
						$res = $this->Path->save(array(
							'skill_instance_id' => $inst['SkillInstance']['id'],
							'id'=> $path_id,
						));
						if($res){
							$res = $this->Skill->SkillInstance->updateDelays($inst);
						}
						if($res){
							$inst = $this->Skill->SkillInstance->linkRead($inst['SkillInstance']['id']);
							$this->addItems($inst);
							$this->addMsg(200);
						}
					}
				}
			}else{
			
			}
		}else{
				$this->addMsg(300);
		}
	}
	
	function _cast(){
		$localParams = array('no','handler');
		$triggerParams = array_diff_key($this->params, array_flip($localParams));
		$triggerParams['action'] = $this;
		$this->User->Behaviors->attach('Containable');
		$this->User->contain(array('Node','ControlledCharacter'));
		$triggerParams['user'] = $this->User->read(null,$this->controller->user['User']['id']);
		$triggerParams['caster_id'] = $triggerParams['user']['ControlledCharacter']['id'];
		
		$res = $this->Skill->SkillInstance->queueSkill($triggerParams);
		
		return $res;
	}
	
	function cast(){
		if(!empty($this->params['skill_id'])){
			if(!empty($this->controller->user)){
				$res = $this->_cast();
				if($res){
					$inst = $this->Skill->SkillInstance->linkRead($res['SkillInstance']['id']);
					$this->addItems($inst);
					$this->addMsg(200);
				}else{
					$this->addMsg(403);
				}
			}else{
				$this->addMsg(300);
			}
		}else{
			$this->addMsg(406);
		}
	}
	

}
?>