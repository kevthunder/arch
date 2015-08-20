<?php
/* Event Test cases generated on: 2011-06-28 21:43:36 : 1309297416*/
App::import('Model', 'Event');

class EventTestCase extends CakeTestCase {
	var $fixtures = array('app.event', 'app.aro', 'app.aco');

	function startTest() {
		$this->Event =& ClassRegistry::init('Event');
	}

	function endTest() {
		unset($this->Event);
		ClassRegistry::flush();
	}

}
