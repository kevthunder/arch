<?php

App::import('Lib', 'LocalEventManager');

class LocalEventDispatcher extends Object {

/**
 * 
 *
 * @var object $Listeners
 */
	protected $_eventManager;
	
/**
 * Constructor
 *
 *
 */
	public function __construct() {
		$this->_eventManager = new LocalEventManager();
	}
	
	public function addEventListener($callable, $eventKey, $options = array()) {
		$this->_eventManager->addListener($callable, $eventKey, $options);
	}
	public function removeEventListener($callable, $eventKey = null) {
		$this->_eventManager->removeListener($callable, $eventKey);
	}
	public function dispatchEvent($event) {
		$this->_eventManager->dispatch($event);
	}
}