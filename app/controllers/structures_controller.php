<?php
class StructuresController extends AppController {

	var $name = 'Structures';

	
	function admin_test() {
		//$this->Structure->StructureType->autoInheritFetch(false);
		//$struct = $this->Structure->StructureType->read(null,2);
		//debug($struct);
		//$this->Structure->StructureType->save(array('pathing'=>array('1x1'=>10)));
		//$struct['StructureType']['skin_id'] = null;
		//$struct['StructureType']['ui_behaviors']['boom'] = 'pow';
		//$this->Structure->StructureType->save($struct);
		//$this->Structure->StructureType->save(array('title'=>'tree','active'=>1));
		//$this->Structure->StructureType->deleteAll(true,true,true);
		//debug($structure = $this->Structure->linkRead(array('consitions'=>array('id'=>1))));
		
		/*$variant = $type = $this->Structure->StructureType->read(null,2);
		do{
			$type = $variant;
			$variant = $this->Structure->StructureType->find('first',array(
				'conditions'=>array(
					'lft >'=>$type['StructureType']['lft'],
					'rght <'=>$type['StructureType']['rght'],
					'variant'=>1
				),
				'order'=>'RAND()'
			));
		}while(!empty($variant));
		debug($type);*/
		
		/*$struct = $this->Structure->read(null,8);
		$this->Structure->save();
		debug($struct);*/
		
		
		/*$data = array(
			'active' => 1,
			'structure_type_id' => 5,
			'tile_id' => 159
		);
		$this->Structure->create();
		$this->Structure->save($data);*/
		
		/*$structs = $this->Structure->find('all');
		foreach ($structs as $struct) {
			$this->Structure->id = $struct['Structure']['id'];
			$this->Structure->updateNode();
			//$this->Structure->updatePathing($struct['Structure']['id']);
		}*/
		//$this->Structure->save($struct);
		
		$this->render(false);
	}
	
	
	function admin_index() {
		$this->Structure->recursive = 0;
		$this->set('structures', $this->paginate());
	}

	function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid structure', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('structure', $this->Structure->read(null, $id));
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->Structure->create();
			if ($this->Structure->save($this->data)) {
				$this->Session->setFlash(__('The structure has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The structure could not be saved. Please, try again.', true));
			}
		}
		$tiles = $this->Structure->Tile->find('list');
		$structureTypes = $this->Structure->StructureType->find('list');
		$skins = $this->Structure->Skin->find('list');
		$this->set(compact('tiles', 'structureTypes', 'skins'));
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid structure', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Structure->save($this->data)) {
				$this->Session->setFlash(__('The structure has been saved', true));
				//$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The structure could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Structure->read(null, $id);
		}
		$tiles = $this->Structure->Tile->find('list');
		$structureTypes = $this->Structure->StructureType->find('list');
		$skins = $this->Structure->Skin->find('list');
		$this->set(compact('tiles', 'structureTypes', 'skins'));
	}

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for structure', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Structure->delete($id)) {
			$this->Session->setFlash(__('Structure deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Structure was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
