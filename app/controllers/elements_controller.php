<?php
class ElementsController extends AppController {

	var $name = 'Elements';

	function admin_index() {
		$this->Element->recursive = 0;
		$this->set('elements', $this->paginate());
	}

	function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid element', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('element', $this->Element->read(null, $id));
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->Element->create();
			if ($this->Element->save($this->data)) {
				$this->Session->setFlash(__('The element has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The element could not be saved. Please, try again.', true));
			}
		}
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid element', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Element->save($this->data)) {
				$this->Session->setFlash(__('The element has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The element could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Element->read(null, $id);
		}
	}

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for element', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Element->delete($id)) {
			$this->Session->setFlash(__('Element deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Element was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
