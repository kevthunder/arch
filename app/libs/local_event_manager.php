<?php


App::import('Lib', 'LocalEvent');


class LocalEventManager {

/**
 * 
 *
 * @var int
 */
	public static $defaultPriority = 10;

/**
 * 
 *
 * @var object $Listeners
 */
	protected $_listeners = array();




/**
 * 
 *
 * @param callback $callable 
 *
 * @param string $eventKey 
 *
 * @param array $options 
 *
 * @return void

 */
	public function addListener($callable, $eventKey, $options = array()) {
		$defOpt = array(
			'priority' => self::$defaultPriority, 
		);
		$opt = array_merge($defOpt,$options);
		$this->_listeners[$eventKey][$opt['priority']][] = array(
			'callable' => $callable,
		);
	}



/**
 * 
 *
 * @param callback $callable 
 * @return void
 */
	public function removeListener($callable, $eventKey = null) {
		if (empty($eventKey)) {
			foreach (array_keys($this->_listeners) as $eventKey) {
				$this->removeListener($callable, $eventKey);
			}
			return;
		}
		if (empty($this->_listeners[$eventKey])) {
			return;
		}
		foreach ($this->_listeners[$eventKey] as $priority => $callables) {
			foreach ($callables as $k => $callback) {
				if ($callback['callable'] === $callable) {
					unset($this->_listeners[$eventKey][$priority][$k]);
					break;
				}
			}
		}
	}

/**
 * Dispatches a new event to all configured listeners
 *
 * @param string|CakeEvent $event the event key name or instance of CakeEvent
 * @return void
 */
	public function dispatch($event) {
		if (is_string($event)) {
			$event = new LocalEvent($event);
		}

		if (empty($this->_listeners[$event->name()])) {
			return;
		}

		foreach ($this->listeners($event->name()) as $listener) {
			if ($event->isStopped()) {
				break;
			}
			$result = call_user_func($listener['callable'], $event);
			if ($result === false) {
				$event->stopPropagation();
			}
			if ($result !== null) {
				$event->result = $result;
			}
			continue;
		}
	}

/**
 * Returns a list of all listeners for a eventKey in the order they should be called
 *
 * @param string $eventKey
 * @return array
 */
	public function listeners($eventKey) {
		if (empty($this->_listeners[$eventKey])) {
			return array();
		}
		ksort($this->_listeners[$eventKey]);
		$result = array();
		foreach ($this->_listeners[$eventKey] as $priorityQ) {
			$result = array_merge($result, $priorityQ);
		}
		return $result;
	}

}
