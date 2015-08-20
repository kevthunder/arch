<?php
/* CharacterType Test cases generated on: 2012-05-02 22:33:39 : 1335998019*/
App::import('Model', 'CharacterType');

class CharacterTypeTestCase extends CakeTestCase {
	var $fixtures = array('app.character_type', 'app.skin');

	function startTest() {
		$this->CharacterType =& ClassRegistry::init('CharacterType');
	}

	function endTest() {
		unset($this->CharacterType);
		ClassRegistry::flush();
	}

}
