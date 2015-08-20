<?php
class Tile extends AppModel {
	var $name = 'Tile';
	var $actsAs = array(
		'XmlLink.XmlLinked'=>array('fields'=>array('x','y','zone_id','fertility','tile_type_id'),'internal'=>array('pathing_cache')),
		'Planned'=>array('equations'=>array('presence'=>array('cond'=>array('tile_type_id'=>2),'multi'=>-1))),
		'Node'=>array('usedField'=>'tile_type_id'),'EventTrigger','Invalided',
		'serialized'=>array('pathing_cache'),
	);
	
	var $chunkSize = 16;
	
	var $belongsTo = array(
		'TileType' => array(
			'className' => 'TileType',
			'foreignKey' => 'tile_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Zone' => array(
			'className' => 'Zone',
			'foreignKey' => 'zone_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	var $updateActions = array(
		'voidTiles' => array(
			'range' => 2,
			'event' => array(
				'original'=>array('or'=>array(
					null,
					array('tile_type_id !='=>1),
				)),
				'data.Tile.tile_type_id'=>1
			),
			'target' => array(
				'or' => array(
					null,
					array('tile_type_id'=>2)
				)
			)
		)
	);
	
	function beforeFind($queryData){
		/*'Cache' => array('patthing_cache'=>array(
			'association' => array(
				'className'=>'Event',
				'type'=>'event',
			)
		))*/
		//Cache::delete('Event','cacheBehavior');
		if(!$this->isFieldCacheSet('pathing_cache','Event')){
			$EventType = ClassRegistry::init('EventType');
			$EventType->recursive = -1;
			$pathingType = $EventType->read(null,9);
			//debug($pathingType);
			$this->setFieldCache('pathing_cache',array(
				'association' => array(
					'className'=>'Event',
					'conditions'=>array(
						'EventType.lft >=' => $pathingType['EventType']['lft'],
						'EventType.rght <=' =>  $pathingType['EventType']['rght']
					),
					'type'=>'event',
					'mode'=>'Aco',
				),
				'usedField' => 'tile_type_id'
			));
		}
		return parent::beforeFind($queryData);
	}
	
	function afterFind($results,$primary){
		$results = parent::afterFind($results,$primary);
		
		$results = $this->cachePathing($results);
			
		return $results;
	}
	
	/*function beforeSave($options) {
		$results = parent::beforeSave($options);
		
		//debug($this->data);
		
		
		
		return $results;
	}*/
	
	function afterSave($created){
		$results = parent::afterSave($created);
		
		$acts = array();
		$minRange = 0;
		App::import('Lib', 'SetMulti');
		foreach($this->updateActions as $name => $act){
			if(SetMulti::testCond($act['event'],$this)){
				$acts[$name] = $act;
				$minRange = max($minRange, $act['range']);
			}
		}
		//debug($acts);
		//debug($minRange);
		if(!empty($acts)){
			$pos = $this->getPos();
			//debug($pos);
			$this->updateTilesRect($pos['x']-$minRange*2,$pos['y']-$minRange*2,$pos['zone_id'],$minRange*4+1,$minRange*4+1,$acts);
			//return false;
		}
		
		if($created){
			if(empty($pos)){
				$pos = $this->getPos();
			}
			$this->checkChunkUpdater($pos['x'],$pos['y'],$pos['zone_id']);
		}
		
		return $results;
	}
	
	function myPos($source = null){
		$this->log(Debbuger::trace(array('depth'=>3)),'deprecated');
		$this->getPos($source);
	}
	
	function dist($tile1, $tile2){
		$pos1 = $this->getPos($tile1);
		$pos2 = $this->getPos($tile2);
		return sqrt(pow($pos1['x']-$pos2['x'],2)+pow($pos1['y']-$pos2['y'],2));
	}
	
	function getPos($source = null){
		if(is_null($source)){
			$source = $this;
		}
		$pos = null;
		if(!is_array($source) && is_numeric($source)){
			$data['id'] = $source;
		}else{
			$extractData = array(
				'id' => array('data.'.$this->alias.'.id','data.id',$this->alias.'.id','id'),
				'x' => array('data.'.$this->alias.'.x','data.x',$this->alias.'.x','x'),
				'y' => array('data.'.$this->alias.'.y','data.y',$this->alias.'.y','y'),
				'zone_id' => array('data.'.$this->alias.'.zone_id','data.zone_id',$this->alias.'.zone_id','zone_id')
			);
			App::import('Lib', 'SetMulti');
			$data = SetMulti::extractHierarchicMulti($extractData, $source);
		}
		//debug($data);
		if(isset($data['x']) && isset($data['x'])){
			$pos = $data;
			unset($pos['id']);
		}elseif(!empty($data['id'])){
			$tmp = $this->recursive;
			$this->recursive = -1;
			$pos = $this->find('first',array('fields'=>array('id','x','y','zone_id'),'conditions'=>array('id'=>$data['id'])));
			$pos = $pos[$this->alias];
			$this->recursive = $tmp;
		}
		//debug($pos);
		return $pos;
	}
	
	function parentNode() {
		if(empty($this->data[$this->alias]['id']) || $this->data[$this->alias]['id'] != $this->id){
			$this->read(array('id','tile_type_id'),$this->id);
		}
		if(!empty($this->data[$this->alias]['tile_type_id'])){
			return $this->TileType->myNodeRef($this->data[$this->alias]['tile_type_id']);
		}
		
		return $this->Node->buildPath('Tile',false);
	}
	
	function getRect($w, $h=null, $zone_id=null, $centerTile=null, $x=null, $y=null, $opt=array()){
		$passed = array_filter(compact($w, $h, $zone_id, $centerTile, $x, $y));
		if(is_array($w)){
			$opt = $w;
			unset($passed['w']);
		}
		if(is_array($x)){
			$opt = $x;
			unset($passed['x']);
		}
		$defOpt = array(
			'w'=>null, 
			'h'=>null, 
			'zone_id'=>null,
			'centerTile'=>null, 
			'x'=>null, 
			'y'=>null,
			'aliased'=>false,
		);
		$opt = array_merge($defOpt, $opt, $passed);
		
		$findOption = $this->rectQuery($opt);
		$tiles = $this->find('all',$findOption);
		if($opt['aliased']){
			$aliased = array();
			foreach($tiles as $key =>$val){
				if($opt['aliased'] === 'relative'){
					$aliased[($val[$this->alias]['x']-$realX).';'.($val[$this->alias]['y']-$realY)] = $val;
				}else{
					$aliased[($val[$this->alias]['x']).';'.($val[$this->alias]['y'])] = $val;
				}
			}
			$tiles = $aliased;
		}
		return $tiles;
	}
	
	function rectQuery($opt){
		$centerTile = $opt['centerTile'];
		if(is_null($centerTile) && is_null($opt['x'])  && is_null($opt['y']) && $this->id){
			$centerTile = $this->id;
		}
		$realX = is_null($opt['x'])?0:$opt['x'];
		$realY = is_null($opt['y'])?0:$opt['y'];
		if(!empty($centerTile)){
			if(is_numeric($centerTile)){
				$centerTile = $this->read(array('id','x','y'),$centerTile);
			}
			if(isset($centerTile[$this->alias])){
				$centerTile = $centerTile[$this->alias];
			}
			if(isset($centerTile['x']) && isset($centerTile['y']) ){
				$realX += $centerTile['x'];
				$realY += $centerTile['y'];
			}
		}
		$localOpt = array('w','h','centerTile','x','y','aliased');
		$findOption = array_diff_key($opt,array_flip($localOpt));
		$findOption['conditions'][$this->alias.'.x BETWEEN ? AND ?'] = array($realX, $realX+$opt['w']-1);
		$findOption['conditions'][$this->alias.'.y BETWEEN ? AND ?'] = array($realY, $realY+$opt['h']-1);
		$findOption['conditions']['zone_id'] = $opt['zone_id'];
		
		return $findOption;
	}
	
	function testForPathing($character=null,$tile=null){
		if(empty($tile)){
			$tile = $this->id;
		}
		if(!empty($tile) && is_numeric($tile)){
			if($this->data[$this->alias]['id'] == $tile){
				$tile = $this->data;
			}else{
				$tile = $this->find('first',array('conditions'=>array($this->alias.'.id'=>$tile)));
			}
		}
		if(!empty($tile) && empty($tile[$this->alias])){
			$tile = array($this->alias=>$tile);
		}
		//debug($tile[$this->alias]);
		return $tile[$this->alias]['pathing_cache']["walk"] == 1;
	}
	
	function cachePathing($results){
		$this->Behaviors->attach('Util');
		$uResults = array();
		$uResults = &$this->unifiedResultRef(&$results);
		foreach($uResults as &$tile){
			if(!empty($tile['Tile']) && array_key_exists('pathing_cache',$tile['Tile'])){
				if(is_null($tile['Tile']['pathing_cache'])){
					$id = $tile['Tile']['id'];
					if(!empty($tile['Node']['id'])){
						$node = $tile['Node']['id'];
					}else{
						$node = $this->myNodeRef($id);
					}
					if(!empty($node)){
						$pathing = array();
						$Event = ClassRegistry::init('Event');
						$Event->EventType->Behaviors->attach('Util');
						$Event->EventType->recursive = -1;
						$findOpt = array(
							'conditions'=>array(
								'EventType.rght-EventType.lft = 1'
							),
							'joins' => array(
								$Event->EventType->treeJointOpt(true,array(
									'conditions'=>array('ParentEventType.id'=>9)
								))
							)
						);
						$pathingTypes = $Event->EventType->find('list',$findOpt);
						//debug($pathingTypes);
						foreach($pathingTypes as $typeId => $alias){
							$res = $Event->dispatchEvent($typeId,39,$node,null,1);
							$pathing[$alias] = $res;
						}
						$tile['Tile']['pathing_cache'] = $pathing;
						$this->create();
						if(!$this->save(array('id'=>$id,'pathing_cache'=>$pathing))){
							debug('wtf');
						}
					}
				}
				$tile['Tile']['pathing'] = $tile['Tile']['pathing_cache'];
				//debug($tile);
			}
		}
		//debug($results);
		return $results;
	}
	function getChunkUpdaterPos($x,$y){
		return array(
			'left'=> (int)floor($x/$this->chunkSize)*$this->chunkSize,
			'top'=> (int)floor($y/$this->chunkSize)*$this->chunkSize,
			'x'=> (int)floor($x/$this->chunkSize)*$this->chunkSize-$this->chunkSize/2,
			'y'=> (int)floor($y/$this->chunkSize)*$this->chunkSize-$this->chunkSize/2,
			'right'=> (int)floor($x/$this->chunkSize+1)*$this->chunkSize-1,
			'bottom'=> (int)floor($y/$this->chunkSize+1)*$this->chunkSize-1,
		);
	}
	
	function getChunkUpdater($x,$y,$zone_id){
		$pos = $this->getChunkUpdaterPos($x,$y);
		$this->TimedEvent = ClassRegistry::init('TimedEvent');
		$findOpt = array(
			'conditions'=>array(
				'event_type_id'=>16,
				'x'=>$pos['x'],
				'y'=>$pos['y'],
				'zone_id'=>$zone_id
			)
		);
		$chunkUpdater = $this->TimedEvent->find('first',$findOpt);
		return $chunkUpdater;
	}
	
	function checkChunkUpdater($x,$y,$zone_id){
		$exist = $this->getChunkUpdater($x,$y,$zone_id);
		if(!empty($exist)){
			return $exist['TimedEvent']['id'];
		}
		$pos = $this->getChunkUpdaterPos($x,$y);
		$this->TimedEvent = ClassRegistry::init('TimedEvent');
		$format = $this->TimedEvent->getDataSource()->columns['datetime']['format'];
		$data =array(
			'time'=> date($format),
			'event_type_id'=>16,
			'x'=>$pos['x'],
			'y'=>$pos['y'],
			'zone_id'=>$zone_id,
			'range'=>$this->chunkSize/2,
			'data'=>$pos,
			'repeate'=> 10,
			'active' => 1,
			'context'=>"ChunkUpdater",
		);
		$this->TimedEvent->create();
		if($this->TimedEvent->save($data)){
			return $this->TimedEvent->id;
		}
		return null;
	}
	

	function updateTilesRect($x,$y,$zone_id,$w,$h,$actions = null){
		$tiles = $this->getRect(array('x'=>$x,'y'=>$y,'zone_id'=>$zone_id,'w'=>$w,'h'=>$h,'aliased'=>true));
		//debug($tiles);
		$modifs = array();
		for($xi = $x; $xi< $x+ $w; $xi++){
			for($yi = $y; $yi< $y+ $h; $yi++){
				$maxRange = max($xi - $x, $x + $w - $xi -1,$yi - $y, $y + $h - $yi -1);
				$tile = null;
				if(!empty($tiles[$xi.';'.$yi])){
					$tile = $tiles[$xi.';'.$yi];
				}
				$modif = $this->_updateTile($tile , $xi, $yi, $zone_id, $tiles, $maxRange, $actions);
				$modifs = SetMulti::merge2($modifs,$modif);
			}
		}
		$this->Behaviors->attach('Util');
		//debug($modifs);
		$this->bulkModify($modifs);
	}
	
	function _updateTile($data,$x,$y,$zone_id,$tiles,$maxRange,$actions = null){
		App::import('Lib', 'SetMulti');
		$modifs = array();
		$acts = $this->updateActions;
		if(!empty($actions)){
			$acts = array_intersect_key($acts,set::normalize($actions));
		}
		foreach($acts as $name => $act){
			if($act['range'] <= $maxRange){
				if(SetMulti::testCond($act['target'],$data)){
					if(empty($act['callback'])){
						$callBack = array($this,'update_'.$name);
					}else{
						$callBack = $act['callback'];
					}
					$modif = call_user_func($callBack ,$data,$x,$y,$zone_id,$tiles);
					$modifs = SetMulti::merge2($modifs,$modif);
				}
			}
		}
		return $modifs;
	}

	function update_voidTiles($data,$x,$y,$zone_id,$tiles){
		$act = $this->updateActions['voidTiles'];
		$range = $act['range'];
		$closeSolidTile = false;
		for($xi = $x-$range; $xi< $x+$range; $xi++){
			for($yi = $y-$range; $yi< $y+$range; $yi++){
				$tile = null;
				if(!empty($tiles[$xi.';'.$yi])){
					$tile = $tiles[$xi.';'.$yi];
					if($tile['Tile']['tile_type_id'] != 2){
						$closeSolidTile = true;
						break 2;
					}
				}
			}
		}
		$create = array();
		$delete = array();
		if($closeSolidTile){
			if(empty($data)){
				//create tile
				$create[] = array(
					'x' => $x,
					'y' => $y,
					'zone_id' => $zone_id,
					'active' => true,
					'tile_type_id' => 2
				);
			}
		}else{
			if(!empty($data)){
				//remove tile
				$delete[$data['Tile']['id']] = $data;
			}
		}
		return array('save' => $create, 'delete' => $delete);
	}
}


