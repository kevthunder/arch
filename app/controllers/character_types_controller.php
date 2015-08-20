<?php
class CharacterTypesController extends AppController {

	var $name = 'CharacterTypes';

	function admin_index() {
		$this->CharacterType->recursive = 0;
		$this->set('characterTypes', $this->paginate());
	}

	function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid character type', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('characterType', $this->CharacterType->read(null, $id));
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->CharacterType->create();
			if ($this->CharacterType->save($this->data)) {
				$this->Session->setFlash(__('The character type has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The character type could not be saved. Please, try again.', true));
			}
		}
		$skins = $this->CharacterType->Skin->find('list');
		$parents = $this->CharacterType->generatetreelist();
		$this->set(compact('skins', 'parents'));
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid character type', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->CharacterType->save($this->data)) {
				$this->Session->setFlash(__('The character type has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The character type could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->CharacterType->read(null, $id);
		}
		$skins = $this->CharacterType->Skin->find('list');
		$parents = $this->CharacterType->generatetreelist();
		$this->set(compact('skins', 'parents'));
	}

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for character type', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->CharacterType->delete($id)) {
			$this->Session->setFlash(__('Character type deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Character type was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
