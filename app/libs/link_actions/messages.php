<?php
class MessagesLinkAction extends LinkAction {
	var $uses = array('Message','User');
	
	
	function post(){
		if(!empty($this->params['text'])){
			if(!empty($this->controller->user)){
				$data = array(
					'text' => $this->params['text'],
					'user_id' => $this->controller->user['User']['id'],
				);
				if(!empty($this->params['to']) && is_numeric($this->params['to'])){
					$data['to_user_id'] = $this->params['to'];
				}
				$this->User->Behaviors->attach('Containable');
				$this->User->contain(array('Node','ControlledCharacter'=>array('Tile')));
				$user = $this->User->read(null,$this->controller->user['User']['id']);
				if(!empty($user['User']['controlled_character_id'])){
					$data['character_id'] = $user['ControlledCharacter']['id'];
					$data['tile_id'] = $user['ControlledCharacter']['tile_id'];
					$data['cache_x'] = $user['ControlledCharacter']['Tile']['x'];
					$data['cache_y'] = $user['ControlledCharacter']['Tile']['y'];
				}elseif(empty($data['to_user_id'])){
					$this->addMsg(303);
					return;
				}
				$this->Message->create();
				$this->Message->save($data);
				$this->addMsg(200);
			}else{
				$this->addMsg(300);
			}
		}else{
			$this->addMsg(406);
		}
	}
	
	
	function keep_updated(){
		if(
			!empty($this->params['x']) &&
			!empty($this->params['y']) &&
			!empty($this->params['range']) &&
			!empty($this->params['since'])
		){
			if(!empty($this->controller->user)){
				$format = $this->Message->getDataSource()->columns['datetime']['format'];
				$findOptions = array('conditions'=>array(
					'created >=' => date($format ,$this->params['since']),
					'or' => array(
						array(
							'to_user_id' => $this->controller->user['User']['id']
						),
						array(
							'cache_x >=' => $this->params['x'] - $this->params['range'],
							'cache_x <=' => $this->params['x'] + $this->params['range'],
							'cache_y >=' => $this->params['y'] - $this->params['range'],
							'cache_y <=' => $this->params['y'] + $this->params['range'],
							'to_user_id' => null
						),
					)
				));
				if(!empty($this->params['exclude'])){
					$findOptions['conditions']['not']['id'] = explode(',',$this->params['exclude']);
				}
				$msgs = $this->Message->triggerAction('linkRead',array($findOptions),$this->defaultAro);
				if(!empty($msgs)){
					$this->addItems($msgs);
				}elseif(!is_array($msgs)){
					$this->addMsg(403);
				}else{
					$this->addMsg(204);
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