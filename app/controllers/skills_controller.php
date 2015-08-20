<?php
class SkillsController extends AppController {

	var $name = 'Skills';
	var $uses = array('Skill','Event');

	
	
	function test() {
		
		//$skills = $this->Skill->find('all');
		//debug($skills);
		$skill = $this->Skill->read(null,3);
		debug($skill);
		//debug($this->Skill->node());
		//$skill['Skill']['ui_behaviors']['base_class'] = 'CastedAbility';
		//$this->Skill->save($skill);
		
		//$event = $this->Event->read(null,38);
		//debug($event);
		/*
		$event['Event']['params'] = array(
			'model'=>'Tile',
			'foreign_key'=>'{{params.tile_id}}',
			'data'=>array('tile_type_id'=>1)
		);
		*/
		//$this->Event->save($event);
		
		$this->render(false);
	}
	
	function admin_index() {
		$this->Skill->recursive = 0;
		$this->set('skills', $this->paginate());
	}

	function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid skill', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('skill', $this->Skill->read(null, $id));
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->Skill->create();
			if ($this->Skill->save($this->data)) {
				$this->Skill->saveSimpleEvents($this->Skill->id,$this->data);
				$this->Skill->saveSkillProvider($this->data['Skill']['provider_node'],$this->Skill->id);
				$this->Session->setFlash(__('The skill has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The skill could not be saved. Please, try again.', true));
			}
		}
		$targetHandlers = $this->Skill->getTargetHandlers(true);
		$this->set('targetHandlers',$targetHandlers);
		$nodes = $this->Skill->Node->nodeTreeList();
		$this->set('nodes',$nodes);
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid skill', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Skill->save($this->data)) {
				$this->Skill->saveSimpleEvents($this->Skill->id,$this->data);
				$this->Skill->saveSkillProvider($this->data['Skill']['provider_node'],$this->Skill->id);
				$this->Session->setFlash(__('The skill has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The skill could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Skill->read(null, $id);
			$userEvent = $this->Skill->getSimpleUserEvent();
			if(!empty($userEvent)){
				//debug($userEvent);
				$this->data['Skill']['user_node'] = $userEvent['Event']['aro_id'];
				$this->data['Skill']['target_handler'] = $userEvent['Event']['function'];
			}
			$targetEvent = $this->Skill->getSimpleTargetEvent();
			if(!empty($targetEvent)){
				//debug($targetEvent);
				$this->data['Skill']['target_node'] = $targetEvent['Event']['aco_id'];
			}
			$provider = $this->Skill->getSkillProvider();
			if(!empty($provider)){
				//debug($provider);
				$this->data['Skill']['provider_node'] = $provider['NodeLink']['owner_node_id'];
			}
		}
		$targetHandlers = $this->Skill->getTargetHandlers(true);
		$this->set('targetHandlers',$targetHandlers);
		$nodes = $this->Skill->Node->nodeTreeList();
		$this->set('nodes',$nodes);
	}

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for skill', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Skill->delete($id)) {
			$this->Session->setFlash(__('Skill deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Skill was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
