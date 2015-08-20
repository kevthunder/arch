<?php
class ItemsLinkAction extends LinkAction {
	var $uses = array('Item','User');

	
	function my_inventory() {
		//debug($this->user);
		
		if(!empty($this->controller->user)){
			$this->User->Behaviors->attach('Containable');
			$this->User->contain(array('Node','ControlledCharacter'));
			$user = $this->User->read(null,$this->controller->user['User']['id']);
			
			if(!empty($user['User']['controlled_character_id'])){
				$items = $this->Item->linkRead(array('conditions'=>array('character_id'=>$user['User']['controlled_character_id'])));
				if(!empty($items)){
					$this->addItems($items);
				}else{
					$this->addMsg(204);
				}
			}else{
				$this->addMsg(303);
			}
		}else{
			$this->addMsg(300);
		}
	}
	
	

}
?>