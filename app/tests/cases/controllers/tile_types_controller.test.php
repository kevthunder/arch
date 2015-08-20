<?php
/* TileTypes Test cases generated on: 2012-02-28 00:23:14 : 1330388594*/
App::import('Controller', 'TileTypes');

class TestTileTypesController extends TileTypesController {
	var $autoRender = false;

	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

class TileTypesControllerTestCase extends CakeTestCase {
	var $fixtures = array('app.tile_type', 'app.skin');

	function startTest() {
		$this->TileTypes =& new TestTileTypesController();
		$this->TileTypes->constructClasses();
	}

	function endTest() {
		unset($this->TileTypes);
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
