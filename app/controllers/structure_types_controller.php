<?php
class StructureTypesController extends AppController {

	var $name = 'StructureTypes';

	function admin_index() {
		$this->StructureType->recursive = 0;
		$this->set('structureTypes', $this->paginate());
	}

	function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid structure type', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('structureType', $this->StructureType->read(null, $id));
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->StructureType->create();
			if ($this->StructureType->save($this->data)) {
				$this->Session->setFlash(__('The structure type has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The structure type could not be saved. Please, try again.', true));
			}
		}
		
		$parents = $this->StructureType->generatetreelist();
		$skins = $this->StructureType->Skin->find('list');
		$this->set(compact('parents', 'skins'));
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid structure type', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->StructureType->save($this->data)) {
				$this->Session->setFlash(__('The structure type has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The structure type could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->StructureType->read(null, $id);
		}
		$parents = $this->StructureType->generatetreelist();
		$skins = $this->StructureType->Skin->find('list');
		$this->set(compact('parents', 'skins'));
	}

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for structure type', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->StructureType->delete($id)) {
			$this->Session->setFlash(__('Structure type deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Structure type was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
