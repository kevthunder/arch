<?php
class CharactersController extends AppController {

	var $name = 'Characters';
	var $helpers = array('Xml');
	var $uses = array('Character','User','Event');

	
	
	function admin_test() {
	
		//$this->Character->getGetAiUpdater(4);
		
		
		//$this->Event = ClassRegistry::init('Event');
		//$opt = array(
		//	'acos'=>1052,
		//	'action'=>26,
		//);
		//$this->Event->dispatchEvent($opt);
		
		
		//$Path = ClassRegistry::init('Path');
		//debug($Path->find('all'));
	
	
		$this->Character->id = 2;
		//debug($this->Character->node(null,false,true));
		$this->Character->save(array('hp'=>50));
		$this->Character->save(array('hp'=>0));
		
		//debug($this->Character->read(null,1));
		
		/*$oldData = array (
			  'Character' => 
			  array (
				'id' => '1',
				'name' => 'Prime',
				'character_type_id' => NULL,
				'skin_id' => NULL,
				'tile_id' => '215',
				'hp' => '50',
				'total_hp' => '198',
				'speed' => '50',
				'user_id' => '1',
				'presence' => NULL,
				'active' => '1',
				'created' => NULL,
				'modified' => '2012-08-29 18:23:38',
				'synced' => '2012-08-29 18:23:38',
			  ),
			  'Tile' => 
			  array (
				'id' => '215',
				'x' => '-8',
				'y' => '-10',
				'tile_type_id' => '1',
				'top_id' => NULL,
				'right_id' => NULL,
				'bottom_id' => NULL,
				'left_id' => NULL,
				'presence' => NULL,
				'fertility' => NULL,
				'pathing_cache' => 
				array (
				  'walk' => true,
				  'build' => true,
				),
				'active' => '1',
				'created' => '2012-03-12 22:22:36',
				'modified' => '2012-08-22 19:58:32',
				'synced' => '2012-08-22 19:58:32',
				'pathing' => 
				array (
				  'walk' => true,
				  'build' => true,
				),
			  ),
			  'User' => 
			  array (
				'id' => '1',
				'username' => 'kevthunder',
				'password' => '8bece149abc3b3dd893b65a140a96adb9d92a665',
				'controlled_character_id' => '1',
			  ),
			  'CharacterType' => 
			  array (
				'id' => NULL,
				'title' => NULL,
				'desc' => NULL,
				'complete_type' => NULL,
				'skin_id' => NULL,
				'total_hp' => NULL,
				'total_hp_multi' => NULL,
				'speed' => NULL,
				'speed_multi' => NULL,
				'needed_presence' => NULL,
				'lft' => NULL,
				'rght' => NULL,
				'parent_id' => NULL,
				'active' => NULL,
				'created' => NULL,
				'modified' => NULL,
			  ),
			  'Skin' => 
			  array (
				'id' => NULL,
				'title' => NULL,
				'class_name' => NULL,
				'active' => NULL,
				'created' => NULL,
				'modified' => NULL,
			  ),
			  'Node' => 
			  array (
				'id' => '40',
				'parent_id' => '39',
				'model' => 'Character',
				'foreign_key' => '1',
				'alias' => NULL,
				'lft' => '27',
				'rght' => '28',
			  ),
			  'Item' => 
			  array (
				0 => 
				array (
				  'id' => '1',
				  'title' => NULL,
				  'skin_id' => NULL,
				  'tile_id' => NULL,
				  'character_id' => '1',
				  'item_type_id' => '1',
				  'strength' => NULL,
				  'active' => '1',
				  'created' => '2012-02-13 23:18:09',
				  'modified' => '2012-02-13 23:18:09',
				),
			  ),
			  'CharacterSubtype' => 
			  array (
			  ),
		);
		
		debug( $this->Character->updateDataNow($oldData) );*/
		
		//debug($this->Character->savePlanning(array('tile_id'=>'2'),strtotime("+1 min"),array('dry'=>true)));
		//$this->Character->updateNow();
		//$this->Character->set($this->Character->find('first'));
		//$this->Character->savePlanning(array('hp'=>'+20','total_hp'=>array('operator'=>'substract',1)),strtotime("+1 min"));
		$this->render(false);
	}
	
	function my_characters() {
		$this->layout = 'xml/default';
		
		
		$warnings = array();
		
		//debug($this->user);
		if(!empty($this->user)){
			$this->Character->recursive = -1;
			$characters = $this->Character->find('all',array('conditions'=>array('user_id'=>$this->user['User']['id'])));
			$this->set('characters', $characters);
		}else{
			$warnings[] = 300;
		}
		$this->set('warnings', $warnings);
		header ("content-type: text/xml");
		//header ("content-type: text/plain");
	}
	
	function select($character_id = null) {
		//$this->layout = 'xml/default';
		
		$warnings = array();
		if(!empty($character_id)){
			if(!empty($this->user)){
				$userRef = $this->User->myNodeRef($this->user['User']['id']);
				if(!$this->Character->Behaviors->attached('NodeLinked')){
					$this->Character->Behaviors->attach('NodeLinked');
				}
				$character = $this->Character->read(null,$character_id);
				$allowed = false;
				if($character['Character']['user_id'] == $this->user['User']['id']){
					$allowed = true;
				}else{
					//possibly use an event for validation
				}
				if($allowed){
					if(
						$this->Character->NodeLink->unlinkAll(null,$userRef,'Character')
						&& $this->Character->linkTo(null,$userRef,$character_id,'owned')
					){
						$warnings[] = 200;
					}else{
						$warnings[] = 402;
					}
				}else{
					$warnings[] = 403;
				}
			}else{
				$warnings[] = 300;
			}
		}
		if(empty($warnings)){
			$warnings[] = 301;
		}
		$this->set('warnings', $warnings);
		
		$this->render('/inputs/response');
	}
	
	
	function admin_index() {
		$this->Character->recursive = 0;
		$this->set('characters', $this->paginate());
	}

	function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid character', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('character', $this->Character->read(null, $id));
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->Character->create();
			if ($this->Character->save($this->data)) {
				$this->Session->setFlash(__('The character has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The character could not be saved. Please, try again.', true));
			}
		}
		$tiles = $this->Character->Tile->find('list');
		$users = $this->Character->User->find('list');
		$this->set(compact('tiles', 'users'));
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid character', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Character->save($this->data)) {
				$this->Session->setFlash(__('The character has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The character could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Character->read(null, $id);
		}
		$tiles = $this->Character->Tile->find('list');
		$users = $this->Character->User->find('list');
		$this->set(compact('tiles', 'users'));
	}

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for character', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Character->delete($id)) {
			$this->Session->setFlash(__('Character deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Character was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
