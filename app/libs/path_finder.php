<?php
App::import('Lib', 'LocalEventDispatcher');
class PathFinder extends LocalEventDispatcher {
	//////////// public variables ////////////
	var $lastTile;
	var $curTile;
	var $nexFinder;
	var $entryPoint;
	var $exitPoint = array('x'=>0.5,'y'=>0.5);
	var $prevExitPoint;
	var $dependencies;
	
	//////////// private variables ////////////
	var $_calculator;
	var $_cachedIndicativeRemaining;
	var $_cachedLastPathLength;
	var $_cachedLastFinder;
	
	
	//////////// Constructor ////////////
	function __construct($calculator,$curTile,$lastTile,$entryPoint = null) {
		parent::__construct();
		$this->_calculator = $calculator;
		$this->lastTile = $lastTile;
		$this->curTile = $curTile;
		if($entryPoint == null){
			$this->entryPoint = $this->gessJointPointTo();
		}else{
			$this->entryPoint = $entryPoint;
		}
	}
	
	//////////// Public functions ////////////
	function getTopTile(){
		return $this->_calculator->getTilesAt($this->curTile['Tile']['x'],$this->curTile['Tile']['y']-1);
	}
	function getRightTile(){
		return $this->_calculator->getTilesAt($this->curTile['Tile']['x']+1,$this->curTile['Tile']['y']);
	}
	function getBottomTile(){
		return $this->_calculator->getTilesAt($this->curTile['Tile']['x'],$this->curTile['Tile']['y']+1);
	}
	function getLeftTile(){
		return $this->_calculator->getTilesAt($this->curTile['Tile']['x']-1,$this->curTile['Tile']['y']);
	}
	public function getSegmentLength(){
		return $this->segmentLengthTo($this->exitPoint);
	}
	public function getPathLength(){
		return $this->_cachedLastPathLength + $this->getSegmentLength();
	}
	public function getTileId(){
		return $this->curTile['Tile']['id'];
	}
	
	function setNexFinder($val){
		if($this->nexFinder != $val){
			$this->nexFinder = $val;
			$oldExit = $this->exitPoint;
			if($this->nexFinder == null){
				$this->exitPoint = array('x'=>0.5,'y'=>0.5);
			}else{
				$this->nexFinder->setLastTile($this->curTile);
				if($this->nexFinder->prevExitPoint != null){
					$this->exitPoint = $this->nexFinder->prevExitPoint;
				}
			}
			if(!$oldExit['x'] == $this->exitPoint['x'] && $oldExit['y'] == $this->exitPoint['y']){
				$this->updateCache();
			}
		}
	}
	function getIndicativeRemaining(){
		if(!$this->_cachedIndicativeRemaining){
			$dist = $this->_calculator->Tile->dist($this->curTile,$this->_calculator->toTile);
			$this->_cachedIndicativeRemaining = $dist + $this->getPathLength()*0.7;
		}
		return $this->_cachedIndicativeRemaining;
	}
	
	
	function calculate(){
		$adjacentTiles = array(
			$this->getTopTile(),
			$this->getRightTile(),
			$this->getBottomTile(),
			$this->getLeftTile()
		);
		foreach($adjacentTiles as $t){
			//if(empty($this->lastTile['tile'])){
			//	debug($this->lastTile);
			//}
			if($t && (empty($this->lastTile) || $t['Tile']['id'] != $this->lastTile['Tile']['id']) && $this->_calculator->testTileForPathing($t)){
				$pathFinder = new PathFinder($this->_calculator,$t,$this->curTile);
				$pathFinder->applyForBest();
			}
		}
	}

	function pathLengthTo($exitPoint){
		return $this->_cachedLastPathLength + $this->segmentLengthTo($exitPoint);
	}
	function segmentLengthTo($exitPoint){
		if(!isset($exitPoint['y'])){
			$haha = lol;
			debug($exitPoint);
			exit();
		}
		return sqrt(pow($this->entryPoint['x']-$exitPoint['x'],2)+pow($this->entryPoint['y']-$exitPoint['y'],2));
	}
	
	function applyForBest(){
		$this->updateCache();
		if($this->_calculator->setBestFinder($this)){
		}
	}
	
	function setLastTile($val){
		if($this->lastTile['Tile']['id'] != $val['Tile']['id']){
			$this->lastTile = $val;
			$this->updateCache();
		}
	}
	
	public function destroy(){
		$this->setLastCachedFinder(null);
		$this->dispatchEvent('destroyed');
	}
	//////////// Private functions ////////////
	
	function getLastFinder(){
		if($this->lastTile){
			return $this->_calculator->getBestFinderAt($this->lastTile['Tile']['id']);
		}
		return null;
	}
	
	protected function updateCache(){
		$l = $this->getLastFinder();
		if($l != null){
			$this->setLastCachedFinder($l);
			//debug($l->getTopTile());
			$lastPathLength = $l->pathLengthTo($this->prevExitPoint);
			if($this->_cachedLastPathLength != $l->getPathLength()){
				$this->_cachedLastPathLength = $l->getPathLength();
				//dispatchEvent(new Event(PathFinder.UPDATED));
			}
		}
	}
	
	protected function setLastCachedFinder($pathFinder){
		if($this->_cachedLastFinder != $pathFinder){
			if($this->_cachedLastFinder != null){
				$this->_cachedLastFinder->removeEventListener(array($this,'lastFinderUpdateHandler'),'updated');
				$this->_cachedLastFinder->removeEventListener(array($this,'lastFinderUpdateHandler'),'destroyed');
			}
			$this->_cachedLastFinder = $pathFinder;
			if($this->_cachedLastFinder != null){
				$this->prevExitPoint = $this->_cachedLastFinder->gessJointPointTo($this->curTile);
				//debug($this->prevExitPoint);
				$this->_cachedLastFinder->addEventListener(array($this,'lastFinderUpdateHandler'),'updated');
				$this->_cachedLastFinder->addEventListener(array($this,'lastFinderUpdateHandler'),'destroyed');
			}
		}
	}
	
	function gessJointPointTo($target = null){
		if($target == null){
			$target = $this->lastTile;
		}
		if($this->curTile != null && $target != null){
			$topTile = $this->getTopTile();
			$rightTile = $this->getRightTile();
			$bottomTile = $this->getBottomTile();
			$leftTile = $this->getLeftTile();
			if($target['Tile']['id'] == $topTile['Tile']['id']){
				return array('x'=>0.5,'y'=>0);
			}else if($target['Tile']['id'] == $rightTile['Tile']['id']){
				return array('x'=>1,'y'=>0.5);
			}else if($target['Tile']['id'] == $bottomTile['Tile']['id']){
				return array('x'=>0.5,'y'=>1);
			}else if($target['Tile']['id'] == $leftTile['Tile']['id']){
				return array('x'=>0,'y'=>0.5);
			}
		}else{
			return array('x'=>0.5,'y'=>0.5);
		}
	}
	
	
	//////////// Event Handlers functions ////////////
	function lastFinderUpdateHandler($e){
		$this->updateCache();
	}
}