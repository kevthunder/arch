<?php
class ItemsController extends AppController {

	var $name = 'Items';

	function test(){
		
		$items = $this->Item->ItemType->find('all');
		foreach ($items as $item) {
			$this->Item->ItemType->id = $item['ItemType']['id'];
			$this->Item->ItemType->updateNode();
		}
		
	
		$this->render(false);
	}
	
	function admin_index() {
		$this->Item->recursive = 0;
		$this->set('items', $this->paginate());
	}

	function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid item', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('item', $this->Item->read(null, $id));
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->Item->create();
			if ($this->Item->save($this->data)) {
				$this->Session->setFlash(__('The item has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The item could not be saved. Please, try again.', true));
			}
		}
		$tiles = $this->Item->Tile->find('list');
		$characters = $this->Item->Character->find('list');
		$itemTypes = $this->Item->ItemType->find('list');
		$this->set(compact('tiles', 'characters', 'itemTypes'));
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid item', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Item->save($this->data)) {
				$this->Session->setFlash(__('The item has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The item could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Item->read(null, $id);
		}
		$tiles = $this->Item->Tile->find('list');
		$characters = $this->Item->Character->find('list');
		$itemTypes = $this->Item->ItemType->find('list');
		$this->set(compact('tiles', 'characters', 'itemTypes'));
	}

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for item', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Item->delete($id)) {
			$this->Session->setFlash(__('Item deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Item was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
