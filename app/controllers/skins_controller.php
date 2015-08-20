<?php
class SkinsController extends AppController {

	var $name = 'Skins';

	function admin_index() {
		$this->Skin->recursive = 0;
		$this->set('skins', $this->paginate());
	}

	function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid skin', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('skin', $this->Skin->read(null, $id));
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->Skin->create();
			if ($this->Skin->save($this->data)) {
				$this->Session->setFlash(__('The skin has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The skin could not be saved. Please, try again.', true));
			}
		}
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid skin', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Skin->save($this->data)) {
				$this->Session->setFlash(__('The skin has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The skin could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Skin->read(null, $id);
		}
	}

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for skin', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Skin->delete($id)) {
			$this->Session->setFlash(__('Skin deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Skin was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
