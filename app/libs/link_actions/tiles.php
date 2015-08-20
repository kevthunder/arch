<?php
class TilesLinkAction extends LinkAction {
	var $uses = array('Tile');

	
	function keep_updated($rect = null) {
		if(
			isset($this->params['x']) &&
			isset($this->params['y']) &&
			isset($this->params['zone_id']) &&
			!empty($this->params['width']) &&
			!empty($this->params['height'])
		){
			$rect = array(
				'x' => $this->params['x'],
				'y' => $this->params['y'],
				'zone_id' => $this->params['zone_id'],
				'width' => $this->params['width'],
				'height' => $this->params['height']
			);
		}
		if(!empty($rect)){
			$this->TimedEvent = ClassRegistry::init('TimedEvent');
			//$this->TimedEvent->triggerLocalizedEvents($rect['x'], $rect['y'], $rect['width'], $rect['height']);
			
			$conditions = array(
				'x >=' => $this->params['x'],
				'y >=' => $this->params['y'],
				'x <=' => $this->params['x'] + $this->params['width'],
				'y <=' => $this->params['y'] + $this->params['height'],
				'zone_id' => $this->params['zone_id'],
			);
			$nodes = $this->Tile->getNodes(array('conditions'=>$conditions));
			$data = $this->controller->Link->getInvalidationData($nodes);
			//debug($data);
			if(!empty($data)){
				$this->addItems($data);
			}
			/*$tiles = $this->Tile->find('list',array('fields'=>array('id','id'),'conditions'=>$conditions));
			foreach($tiles as $id){
				$this->Tile->myNodeRef($id, true, false);
			}*/
		}else{
			$this->addMsg(301);
		}
	}
	

}
?>