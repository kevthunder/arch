<?php
/* Characters Test cases generated on: 2011-06-28 22:28:28 : 1309300108*/
App::import('Controller', 'Characters');

class TestCharactersController extends CharactersController {
	var $autoRender = false;

	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

class CharactersControllerTestCase extends CakeTestCase {
	var $fixtures = array('app.character', 'app.tile', 'app.user');

	function startTest() {
		$this->Characters =& new TestCharactersController();
		$this->Characters->constructClasses();
	}

	function endTest() {
		unset($this->Characters);
		ClassRegistry::flush();
	}

	function testAdminIndex() {

	}

	function testAdminView() {

	}

	function testAdminAdd() {

	}

	function testAdminEdit() {

	}

	function testAdminDelete() {

	}

}
