<?php
/* Character Test cases generated on: 2011-06-26 23:30:29 : 1309131029*/
App::import('Model', 'Character');

class CharacterTestCase extends CakeTestCase {
	var $fixtures = array('app.character', 'app.tile');

	function startTest() {
		$this->Character =& ClassRegistry::init('Character');
	}

	function endTest() {
		unset($this->Character);
		ClassRegistry::flush();
	}

}
