<?php
class TilesController extends AppController {

	var $name = 'Tiles';
	var $helpers = array('Xml');

	function index() {
		$this->Tile->recursive = 0;
		$this->set('tiles', $this->paginate());
	}

	function xml() {
		$this->layout = 'xml/default';
		$this->Tile->recursive = -1;
		$tiles = $this->Tile->find('all');
		$this->set('tiles', $tiles);
		
		header ("content-type: text/xml");
	}
	
	
	
	function admin_test() {
		
		/*$limits = $this->Tile->find('first',array('fields'=>array('MAX(x) as max_x','MAX(y) as max_y','MIN(x) as min_x','MIN(y) as min_y'),'conditions'=>array()));
		$limits = $limits[0];
		debug($limits);
		
		$l = $this->Tile->chunkSize;
		for ($x = $limits['min_x']-$l; $x < $limits['max_x']+$l; $x+=$l) {
			for ($y = $limits['min_y']-$l; $y < $limits['max_y']+$l; $y+=$l) {
				debug($this->Tile->checkChunkUpdater($x,$y,1));
			}
		}*/
		
		//$this->Tile->updateTilesRect(-40,-40, 1, 80,80);
		/*$res = array();
		$res = Set::insert($res, 'ttt', 44);
		debug($res);
		
		App::import('Lib', 'SetMulti');
		$cond = array(
			'foo.bar' => 'lol',
			'foo.bas !=' => 'lol',
			'foo.cool >' => 3,
		);
		$data = array(
			'foo'=> array(
				'bar' => 'lol',
				'bas' => 'boom',
				'cool' => 22
			)
		);
		var_dump(SetMulti::testCond($cond ,$data));*/
		
		/*App::import('Lib', 'Operations');
		$op = Operations::parseStringOperation('test !=',array('mode'=>'left','type'=>'bool','sepPattern'=>'\h+'));
		var_dump($op);
		$op['val'] = 'test';
		var_dump(Operations::applyOperation($op));*/
		
		/*$tiles = $this->Tile->find('all');
		foreach($tiles as $tile){
			$this->Tile->id = $tile['Tile']['id'];
			$this->Tile->updateNode();
		}*/
		
		//$tile = $this->Tile->read(null,15);
		//debug($tile);
		$tiles = $this->Tile->find('list',array('conditions'=>array('x'=>1),'recursive'=>-1));
		debug($tiles);
		//$tile = $this->Tile->read(null,8);
		//debug($tile);
		//debug($this->Tile->Node->getNodes($tile['Node']['id'],array('mode'=>'list','dry'=>false)));
		//debug($this->Tile->Node->getNodeIds($this->Tile->myNodeRef(),array('dry'=>false)));
		//$NodeLink = ClassRegistry::init('NodeLink');
		//debug($NodeLink->getLinked($tile['Node']['id'],'invalidation',array('mode'=>'all')));
		
		//$this->autoRender = false;
		//$this->Tile->Behaviors->attach('Invalided');
		//debug($this->Tile->invalidateEntry(1));
		
		//$Invalidation = ClassRegistry::init('Invalidation');
		//debug($Invalidation->save(array('node_id'=>28,'fields'=>array('test','test2'))));
		
		//$NodeLink = ClassRegistry::init('NodeLink');
		//$NodeLink->recover();
		//debug($this->Tile->Node->buildPath('Jean/Mike',true));
		//debug($NodeLink->getLinked('Mike','test'));
		//$this->autoRender = false;
		//debug($this->Tile->Node->fullRef(array('Tile'=>'76')));
		//debug($this->Tile->Node->fullRef(17));
		//debug($this->Tile->Node->node('Steeve/Bob/Luc/Erik/Simon/Marc',true,false));
		//debug($this->Tile->Node->buildPath('Model/Tile',true));
		//debug($this->Tile->Node->fullRef('Steeve/Bob/Luc/Erik'));//  /Erik/Simon/Marc
		//debug($this->Tile->triggerAction('test'));
		
		$debugMsgs = ob_get_contents();
		$this->set('debugMsgs',$debugMsgs);
		ob_end_clean();
		
		
		$this->render(false);
	}
	/*
	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid tile', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('tile', $this->Tile->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Tile->create();
			if ($this->Tile->save($this->data)) {
				$this->Session->setFlash(__('The tile has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The tile could not be saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid tile', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Tile->save($this->data)) {
				$this->Session->setFlash(__('The tile has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The tile could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Tile->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for tile', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Tile->delete($id)) {
			$this->Session->setFlash(__('Tile deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Tile was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
	*/
	function admin_index() {
		$this->Tile->recursive = 0;
		$this->set('tiles', $this->paginate());
	}
	function admin_map($x=-16,$y=-16,$w=32,$h=32,$zone_id=1){
		$tiles = $this->Tile->getRect(array(
			'x'=>$x,
			'y'=>$y,
			'w'=>$w,
			'h'=>$h,
			'zone_id'=>$zone_id,
			'aliased'=>true
		));
		$this->set('tiles', $tiles);
		$this->set('x', $x);
		$this->set('y', $y);
		$this->set('w', $w);
		$this->set('h', $h);
	}
	
	function admin_fix($id = null) {
		$findOpt = array(
			'conditions'=>array(
			),
			'joins' => array(
				array(
					'alias' => 'Tile2',
					'table'=>'tiles',
					'type' => 'INNER',
					'conditions' => array(
						'Tile2.x = Tile.x',
						'Tile2.y = Tile.y',
						'Tile2.id > Tile.id' 
					)
				)
			)
		);
		$tiles = $this->Tile->find('all',$findOpt);
		debug($tiles);
		$this->render(false);
	}

	function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid tile', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('tile', $this->Tile->read(null, $id));
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->Tile->create();
			if ($this->Tile->save($this->data)) {
				$this->Session->setFlash(__('The tile has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The tile could not be saved. Please, try again.', true));
			}
		}
		
		$this->set('tileTypes',$this->Tile->TileType->find('list'));
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid tile', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Tile->save($this->data)) {
				$this->Session->setFlash(__('The tile has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The tile could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Tile->read(null, $id);
		}
		$this->set('tileTypes',$this->Tile->TileType->find('list'));
	}

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for tile', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Tile->delete($id)) {
			$this->Session->setFlash(__('Tile deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Tile was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
