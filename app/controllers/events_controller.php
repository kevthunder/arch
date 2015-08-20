<?php
class EventsController extends AppController {

	var $name = 'Events';
	var $components = array('RequestHandler');
	
	function admin_test() {
		//$this->Event->EventType->recover();
		
		/*$event = $this->Event->read(null,38);
		debug($event);
		echo '<pre>'.var_export($event['Event']['params'],true). '</pre>';*/
		
		
		//$event['Event']['handler'] = 'commun';
		//$event['Event']['function'] = 'compare';
		/*$event['Event']['params'] = array ( 
			'field' => 'pathing_cache',
			'eventType' => 9,
		);*/
		//$this->Event->save($event);
		
		//$eventType = 12;
		//debug($this->Event->dispatchEvent($eventType,342,62,null,1));
		
		//$this->Event->recursive = -1;
		//$event = $this->Event->read(null,27);
		//$this->Event->save($event);
		
		$this->TimedEvent = ClassRegistry::init('TimedEvent');
		//$this->TimedEvent->save(array('id'=>'14','event_type_id'=>'16'));
		
		//$this->TimedEvent->checkLifetime(false);
		//$timedEvent = $this->TimedEvent->read(null,1757);
		//debug($timedEvent);
		//debug($timedEvent['TimedEvent']['final_data']);
		//$this->TimedEvent->triggerUnlocalizedEvents();
		
		$this->render(false);
	}
	
	function admin_index() {
		$this->Event->Aro->humanReadableDisplayField(false);
		$this->Event->Aco->humanReadableDisplayField(false);
		$this->Event->recursive = 0;
		if(!empty($this->params['named']['node'])){
			$this->paginate['conditions'][] = array('or'=>array(
				'Event.aco_id'=>$this->params['named']['node'],
				'Event.aro_id'=>$this->params['named']['node'],
			));
		}
		if(!empty($this->params['named']['event_type'])){
			$this->paginate['conditions']['event_type_id'] = $this->params['named']['event_type'];
		}
		if(!empty($this->params['named']['handler'])){
			$this->paginate['conditions']['handler'] = $this->params['named']['handler'];
		}
		$events = $this->paginate();
		//debug($events);
		$this->set('events', $events);
	}

	function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid event', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('event', $this->Event->read(null, $id));
		
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->Event->create();
			if ($this->Event->save($this->data)) {
				$this->Session->setFlash(__('The event has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The event could not be saved. Please, try again.', true));
			}
		}
		$aros = $acos = $this->Event->Aro->nodeTreeList();
		$eventTypes = $this->Event->EventType->generatetreelist();
		$handlers = $this->Event->handlersList();
		$functions = array();
		if(!empty($this->data['Event']['handler'])){
			$functions = $this->Event->handlersFunctionList($this->data['Event']['handler']);
		}
		$this->set(compact('eventTypes','aros','acos','handlers','functions'));
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid event', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Event->save($this->data)) {
				$this->Session->setFlash(__('The event has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The event could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Event->read(null, $id);
		}
		$aros = $acos = $this->Event->Aro->nodeTreeList();
		$eventTypes = $this->Event->EventType->generatetreelist();
		$handlers = $this->Event->handlersList();
		$functions = array();
		if(!empty($this->data['Event']['handler'])){
			$functions = $this->Event->handlersFunctionList($this->data['Event']['handler']);
		}
		$paramInputs = null;
		if(!empty($this->data['Event']['handler']) && !empty($this->data['Event']['function'])){
			$paramInputs = $this->Event->getHandlerInputs($this->data['Event']['handler'],$this->data['Event']['function']);
		}
		$this->set(compact('eventTypes','aros','acos','handlers','functions','paramInputs'));
	}
	
	function admin_function_list($handler = null) {
		if($this->RequestHandler->isAjax() || !empty($this->params['named']['ajax'])){
			$this->layout = 'ajax';
			$this->ajax = true;
			$this->set('ajax',true);
		}else{
			$this->Session->setFlash(__('This is ajax only method', true));
			$this->redirect(array('action'=>'index'));
		}
		$functions = array();
		if(!empty($handler)){
			$functions = $this->Event->handlersFunctionList($handler);
		}
		$this->set(compact('functions'));
	}
	
	
	function admin_handler_form($handlerAlias = null,$function = null, $id = null) {
		if($this->RequestHandler->isAjax() || !empty($this->params['named']['ajax'])){
			$this->layout = 'ajax';
			$this->ajax = true;
			$this->set('ajax',true);
		}else{
			//$this->Session->setFlash(__('This is ajax only method', true));
			//$this->redirect(array('action'=>'index'));
		}
		$paramInputs = $this->Event->getHandlerInputs($handlerAlias,$function);
		if (!empty($id)) {
			$this->data = $this->Event->read(null, $id);
		}
		$this->set(compact('paramInputs'));
	}
	

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for event', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Event->delete($id)) {
			$this->Session->setFlash(__('Event deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Event was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
	
}
