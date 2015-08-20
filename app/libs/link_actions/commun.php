<?php
class CommunLinkAction extends LinkAction {
	var $uses = array();

	
	function reset_buffer() {
		$this->controller->Link->reset();
		$this->addMsg(200);
	}
	
	function save() {
		$warnings = array();
		$this->autoRender = false;
		$dom = new DomDocument;
		$dom->preserveWhiteSpace = FALSE;
		if(empty($this->controller->user)){
			$this->addMsg(300);
		}
		if($dom->loadXML($this->requestXml)){
			foreach($dom->firstChild->childNodes as $child){
				if(in_array($child->nodeName,$this->controller->Link->linkedModels)){
					$flip =array_flip($this->controller->Link->linkedModels);
					$modelName = $flip[$child->nodeName];
					$model = ClassRegistry::init($modelName);
					if($model){
						$data = $model->xmlToData($child->ownerDocument->saveXML($child));
						$model->create();
						$aro = $this->defaultAro;
						$msg = array();
						if($model->triggerAction('save',array($data),$aro,array('messages'=>&$msg))){
							$this->set('item_id',$model->id);
							$old_id = $child->getAttribute('id');
							if($old_id && $model->id != $old_id){
								$this->controller->Link->createdItems[$old_id] = array($model->alias=>array_merge($data,array('id'=>$model->id)));
							}
						}else{
							if(!empty($msg)){
								$this->addMsg($msg);
							}else{
								$this->addMsg(402);
							}
						}
					}else{
						$this->addMsg(402);
					}
				}else{
					$this->addMsg(403);
				}
			}
		}
		
	}
	
	function test(){
		$this->set('test','lol');
	}
	

}
?>