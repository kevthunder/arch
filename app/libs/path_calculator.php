<?php
App::import('Lib', 'PathFinder');
class PathCalculator extends Object {
	//App::import('Lib', 'PathCalculator');
	
	//////////// public variables ////////////
	var $fromTile = null;
	var $toTile = null;
	var $range = 0;
	var $character = null;
	var $calculing = false;
	var $calculed = false;
	var $success = null;
	
	//////////// private variables ////////////
	var $_buffer = array();
	var $_finalFinder;
	var $_bestFinders = array();
	var $_maxIteration = 200;
	var $_steps = array();
	var $tileCache = array();
	var $Tile;

	//////////// Constructor ////////////
	function __construct($character=null,$toTile=null,$range=0,$fromTile=null) {
		$this->Tile = ClassRegistry::init('Tile');
		if(!empty($toTile)){
			$this->setToTile($toTile);
		}
		if(!empty($fromTile)){
			$this->setFromTile($fromTile);
		}
		if(!empty($character)){
			$this->setCharacter($character);
		}
		$this->$range = $range;
		//debug($this);
	}
	
	//////////// Public functions ////////////
	function setToTile($val){
		$this->toTile = $this->_getTile($val);
	}
	function setFromTile($val){
		$this->fromTile = $this->_getTile($val);
	}
	function setCharacter($character){
		if(is_numeric($character)){
			$this->Character = ClassRegistry::init('Character');
			$character = $this->Character->find('first',array('conditions'=>array('Character.id'=>$character),'recursive'=>(empty($this->fromTile)?0:-1)));
		}
		if(empty($character['Character'])){
			$character = array('Character'=>$character);
		}
		if(empty($this->fromTile)){
			if(!empty($character['Tile']['id'])){
				$this->setFromTile($character['Tile']);
			}elseif(!empty($character['Character']['tile_id'])){
				$this->setFromTile($character['Character']['tile_id']);
			}
		}
		$this->character = $character;
	}
	
	function calculate(){
		if($this->calculed == false){
			$this->calculing = true;
			$startFinder = new PathFinder($this,$this->fromTile,null);
			$startFinder->applyForBest();
			$i = 0;
			while(count($this->_buffer) && $i < $this->_maxIteration){
				$pathFinder = $this->removeFromBufferAt(0);
				$pathFinder->calculate();
				$i++;
			}
			$this->success = $i < $this->_maxIteration;
			if(!$this->success){
				debug('busted');
			}
			$this->calculed = true;
		}
	}
	public function GetTilesAt($x,$y){
		if(empty($this->tileCache[$x.';'.$y])){
			$this->loadChunk($x,$y);
		}
		if(!empty($this->tileCache[$x.';'.$y])){
			return $this->tileCache[$x.';'.$y];
		}
		return null;
	}
	public function testTileForPathing($tile){
		return $tile['Tile']['pathing_cache']["walk"] == 1;
	}
	
	public function toData(){
		if($this->success){
			$steps = array();
			foreach ($this->_steps as $step) {
				$steps[] = array(
					'entry_point_x' => $step->entryPoint['x'],
					'entry_point_y' => $step->entryPoint['y'],
					'exit_point_x' => $step->exitPoint['x'],
					'exit_point_y' => $step->exitPoint['y'],
					'tile_id' => $step->getTileId(),
					'length' => $step->getPathLength()
				);
			}
			return array('Path'=>array(
				'character_id' => $this->character['Character']['id'],
				'start_tile_id' => $this->fromTile['Tile']['id'],
				'end_tile_id' => $this->toTile['Tile']['id'],
				'steps' => $steps
			));
		}
		return null;
	}
	//////////// internal functions ////////////
	function loadChunk($x,$y){
		//debug($x.';'.$y.' => '.(floor($x/8)*8).';'.(floor($y/8)*8));
		$res = $this->Tile->getRect(array(
			'x'=>floor($x/8)*8,
			'y'=>floor($y/8)*8,
			'w'=>8,
			'h'=>8,
			'aliased'=>true
		));
		$this->tileCache = array_merge($this->tileCache,$res);
	}
	function setBestFinder($pathFinder){
		$tileId = $pathFinder->getTileId();
		if($this->calculing && $pathFinder && (!array_key_exists($tileId,$this->_bestFinders) || $this->_bestFinders[$tileId]->getPathLength() > $pathFinder->getPathLength())){
			$old = null;
			if(array_key_exists($tileId,$this->_bestFinders)){
				$old = $this->_bestFinders[$tileId];
			}
			$this->_bestFinders[$tileId] = $pathFinder;
			if($old){
			   $old->destroy();
			   unset($old);
			}
			if($this->isDestReached($pathFinder)){
				$this->setFinalFinder($pathFinder);
			}else{
				$this->addToBuffer($pathFinder);
			}
			return true;
		}
		return false;
	}
	function getBestFinderAt($tileId){
		if($this->calculed){
			for($i=0; $i < count($this->_steps); $i++){
				if($this->_steps[$i]->getTileId == $tileId){
					return $this->_steps[$i];
				}
			}
		}
		return $this->_bestFinders[$tileId];
	}
	
	
	//////////// Private functions ////////////
	protected function _getTile($tile){
		if(is_numeric($tile)){
			$tile = $this->Tile->find('first',array('conditions'=>array('Tile.id'=>$tile),'recursive'=>-1));
		}
		if(empty($tile['Tile'])){
			$tile = array('Tile'=>$tile);
		}
		$this->loadChunk($tile['Tile']['x'],$tile['Tile']['y']);
		return $tile;
	}
	
	function addToBuffer($pathFinder){
		$pos = 0;
		//debug( count($this->_buffer) );
		while($pos<count($this->_buffer) && $this->_buffer[$pos]->getIndicativeRemaining() < $pathFinder->getIndicativeRemaining() ){
			$pos++;
		}
		array_splice($this->_buffer,$pos,0,array($pathFinder));
		$pathFinder->addEventListener(array($this,'bufferedDestroyedHandler'),'destroyed');

		
	}
	function removeFromBuffer($pathFinder){
		$this->removeFromBufferAt(array_search($pathFinder,$this->_buffer,true));
	}
	function removeFromBufferAt($pos){
		if($pos !== false){
			$pathFinder = $this->_buffer[$pos];
			array_splice($this->_buffer,$pos,1);
			//debug($this->_buffer);
			$pathFinder->removeEventListener(array($this,'bufferedDestroyedHandler'),'destroyed');
			return $pathFinder;
		}
		return null;
	}
	function ClearBuffer(){
		$this->calculing = false;
		$this->_buffer = array();
	}
	
	function isDestReached($pathFinder){
		$tile = $pathFinder->curTile;
		if($this->range == 0){
			return ($tile['Tile']['id'] == $this->toTile['Tile']['id']);
		}else{
			return $this->Tile->dist($tile,$this->toTile) <= $this->range;
		}
		return false;
	}
	
	function setFinalFinder($pathFinder){
		$this->ClearBuffer();
		$this->_finalFinder = $pathFinder;
		$backFinder = $pathFinder;
		//trace('================');
		while($backFinder!=null){
			array_unshift($this->_steps,$backFinder);
			if($backFinder->getLastFinder() != null){
				$backFinder->getLastFinder()->SetNexFinder($backFinder);
			}
			//debug($backFinder->curTile['Tile']['x'].';'.$backFinder->curTile['Tile']['y']);
			$backFinder = $backFinder->getLastFinder();
		}
	}
	//////////// Event Handlers functions ////////////
	function bufferedDestroyedHandler($e){
		$this->removeFromBuffer($e->subject);
	}
	
}