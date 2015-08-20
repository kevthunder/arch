<?php
class TileTypesController extends AppController {

	var $name = 'TileTypes';

	function admin_index() {
		$this->TileType->recursive = 0;
		$this->set('tileTypes', $this->paginate());
	}

	function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid tile type', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('tileType', $this->TileType->read(null, $id));
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->TileType->create();
			if ($this->TileType->save($this->data)) {
				$this->Session->setFlash(__('The tile type has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The tile type could not be saved. Please, try again.', true));
			}
		}
		$skins = $this->TileType->Skin->find('list');
		$parents = $this->TileType->find('list');
		$this->set(compact('skins', 'parents'));
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid tile type', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->TileType->save($this->data)) {
				$this->Session->setFlash(__('The tile type has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The tile type could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->TileType->read(null, $id);
		}
		$skins = $this->TileType->Skin->find('list');
		$parents = $this->TileType->find('list');
		$this->set(compact('skins', 'parents'));
	}

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for tile type', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->TileType->delete($id)) {
			$this->Session->setFlash(__('Tile type deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Tile type was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
