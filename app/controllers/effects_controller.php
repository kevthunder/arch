<?php
class EffectsController extends AppController {

	var $name = 'Effects';

	function admin_test($id = null){
		$res = $this->Effect->linkRead(array('conditions'=>array('Effect.id'=>$id)));
		debug($res);
		$this->render(false);
	}
	
	function admin_index() {
		$this->Effect->recursive = 0;
		$this->set('aliases', $this->Effect->aliases);
		$this->set('effects', $this->paginate());
	}

	function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid effect', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('effect', $this->Effect->read(null, $id));
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->Effect->create();
			if ($this->Effect->save($this->data)) {
				$this->Session->setFlash(__('The effect has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The effect could not be saved. Please, try again.', true));
			}
		}
		$skills = $this->Effect->Skill->find('list');
		$skins = $this->Effect->Skin->find('list');
		$eventTypes = $this->Effect->EventType->find('list');
		$this->set('aliases', $this->Effect->aliases);
		$this->set(compact('skills', 'skins', 'eventTypes'));
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid effect', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Effect->save($this->data)) {
				$this->Session->setFlash(__('The effect has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The effect could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Effect->read(null, $id);
		}
		$skills = $this->Effect->Skill->find('list');
		$skins = $this->Effect->Skin->find('list');
		$eventTypes = $this->Effect->EventType->find('list');
		$this->set('aliases', $this->Effect->aliases);
		$this->set(compact('skills', 'skins', 'eventTypes'));
	}

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for effect', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Effect->delete($id)) {
			$this->Session->setFlash(__('Effect deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Effect was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
