<?php
class ItemTypesController extends AppController {

	var $name = 'ItemTypes';

	function admin_index() {
		$this->ItemType->recursive = 0;
		$this->set('itemTypes', $this->paginate());
	}

	function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid item type', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('itemType', $this->ItemType->read(null, $id));
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->ItemType->create();
			if ($this->ItemType->save($this->data)) {
				$this->Session->setFlash(__('The item type has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The item type could not be saved. Please, try again.', true));
			}
		}
		$skins = $this->ItemType->Skin->find('list');
		$parents = $this->ItemType->ParentItemType->find('list');
		$this->set(compact('skins', 'parents'));
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid item type', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->ItemType->save($this->data)) {
				$this->Session->setFlash(__('The item type has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The item type could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->ItemType->read(null, $id);
		}
		$skins = $this->ItemType->Skin->find('list');
		$parentItemTypes = $this->ItemType->ParentItemType->find('list');
		$this->set(compact('skins', 'parentItemTypes'));
	}

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for item type', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->ItemType->delete($id)) {
			$this->Session->setFlash(__('Item type deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Item type was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
