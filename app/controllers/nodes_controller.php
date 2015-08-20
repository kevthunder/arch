<?php
class NodesController extends AppController {

	var $name = 'Nodes';
	
	function admin_fix() {
		set_time_limit(200);
		$this->Node->recover();
		$this->render(false);
	}
	
	function admin_test(){
		/*$this->NodeLinkType = ClassRegistry::init('NodeLinkType');
		$this->NodeLinkType->save(array('id'=>2,'exclude_models'=>'SkillInstance'));*/
		$this->render(false);
	}

	function admin_index() {
		$this->Node->recursive = 0;
		$this->set('nodes', $this->paginate());
	}

	function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid node', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('node', $this->Node->read(null, $id));
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->Node->create();
			if ($this->Node->save($this->data)) {
				$this->Session->setFlash(__('The node has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The node could not be saved. Please, try again.', true));
			}
		}
		$this->set('parents', $this->Node->find('list'));
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid node', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Node->save($this->data)) {
				$this->Session->setFlash(__('The node has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The node could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Node->read(null, $id);
		}
		$this->set('parents', $this->Node->nodeTreeList());
	}

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for node', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Node->delete($id)) {
			$this->Session->setFlash(__('Node deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Node was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
