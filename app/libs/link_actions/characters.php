<?php
class CharactersLinkAction extends LinkAction {
	var $uses = array('Character','User','Event');

	
	function my_characters() {
		//debug($this->user);
		
		if(!empty($this->controller->user)){
			$findOptions = array('conditions'=>array('user_id'=>$this->controller->user['User']['id']));
			$characters = $this->Character->triggerAction('linkRead',array($findOptions),$this->defaultAro);
			if($characters){
				$this->addItems($characters);
			}else{
				$this->addMsg(403);
			}
		}else{
			$this->addMsg(300);
		}
	}
	
	function select($character_id = null) {
		$warnings = array();
		if(!empty($this->params['character_id'])){
			$character_id = $this->params['character_id'];
		}
		if(!empty($character_id)){
			if(!empty($this->controller->user)){
				$userRef = $this->User->myNodeRef($this->controller->user['User']['id']);
				if(!$this->Character->Behaviors->attached('NodeLinked')){
					$this->Character->Behaviors->attach('NodeLinked');
				}
				$character = $this->Character->read(null,$character_id);
				$allowed = false;
				if($character['Character']['user_id'] == $this->controller->user['User']['id']){
					$allowed = true;
				}else{
					//possibly use an event for validation
				}
				if($allowed){
					if(
						$this->Character->NodeLink->unlinkAll(null,$userRef,'Character')
						&& $this->User->save(array('id'=>$this->controller->user['User']['id'],'controlled_character_id'=>$character_id))
						&& $this->Character->linkTo(null,$userRef,$character_id,'owned',array('context'=>'selectedCharacter'))
					){
						$findOptions = array('conditions'=>array('Character.id'=>$character_id),'contain'=>'Tile');
						$character = $this->Character->triggerAction('linkRead',array($findOptions),$this->defaultAro);
						$this->addItems($character);
						$this->addMsg(200);
					}else{
						$this->addMsg(402);
					}
				}else{
					$this->addMsg(403);
				}
			}else{
				$this->addMsg(300);
			}
		}
		if(empty($this->warnings)){
			$this->addMsg(301);
		}
	}

}
?>