<?php
class PathsController extends AppController {

	var $name = 'Paths';

	function admin_test() {
		App::import('Lib', 'PathCalculator');
		$pathing = new PathCalculator(1,1);
		$pathing->calculate();
		
		debug($this->Path->read(null,286));
		
		$path = array();
		foreach ($pathing->_steps as $step) {
			$path[] = $step->getTileId();
		}
		
		$x=-16;
		$y=-16;
		$w=32;
		$h=32;
		$this->Tile = ClassRegistry::init('Tile');
		$tiles = $this->Tile->getRect(array(
			'x'=>$x,
			'y'=>$y,
			'w'=>$w,
			'h'=>$h,
			'zone_id'=>1,
			'aliased'=>true
		));
		$this->set('tiles', $tiles);
		$this->set('x', $x);
		$this->set('y', $y);
		$this->set('w', $w);
		$this->set('h', $h);
		$this->set('styled', array('path'=>$path));
	}
	
	function admin_index() {
		$this->Path->recursive = 0;
		$this->set('paths', $this->paginate());
	}

	function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid path', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('path', $this->Path->read(null, $id));
	}

	/*function admin_add() {
		if (!empty($this->data)) {
			$this->Path->create();
			if ($this->Path->save($this->data)) {
				$this->Session->setFlash(__('The path has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The path could not be saved. Please, try again.', true));
			}
		}
		$characters = $this->Path->Character->find('list');
		$startTiles = $this->Path->StartTile->find('list');
		$endTiles = $this->Path->EndTile->find('list');
		$this->set(compact('characters', 'startTiles', 'endTiles'));
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid path', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Path->save($this->data)) {
				$this->Session->setFlash(__('The path has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The path could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Path->read(null, $id);
		}
		$characters = $this->Path->Character->find('list');
		$startTiles = $this->Path->StartTile->find('list');
		$endTiles = $this->Path->EndTile->find('list');
		$this->set(compact('characters', 'startTiles', 'endTiles'));
	}*/

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for path', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Path->delete($id)) {
			$this->Session->setFlash(__('Path deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Path was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
