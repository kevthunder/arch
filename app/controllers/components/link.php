<?php
class LinkComponent extends Object {
	var $components = array('Session');
	
    var $linkedModels = array(
		'Tile'=>'tile',
		'Path'=>'path',
		'Structure'=>'structure',
		'Skin'=>'skin',
	);
	
	var $loadedItems = null;
	var $controller;
	var $startTime;
	var $disableCache = false;
	var $createdItems = array();
	
	function initialize(&$controller, $settings = array()) {
		$this->controller =& $controller;
		App::import('Lib', 'TimeUtil');
		$this->startTime = (string)TimeUtil::relTime();
	}

	function addLoadedItems($items){
		$this->load();
		foreach($items as $modelName=>$m_items){
			if(!Set::numeric(array_keys(SetMulti::excludeKeys($m_items,array('internal'))))){
				$m_items = array($m_items);
			}
			foreach($m_items as $item){
				if(!empty($item['id'])){
					$model = ClassRegistry::init($modelName);
					if($model->Behaviors->attached('Node')){
						if(!empty($item['internal']['Node'][0]['id'])){
							$nodeId = $item['internal']['Node'][0]['id'];
						}else{
							$node = $model->node($model->myNodeRef($item['id']),false,true);
							$nodeId = $node['Node']['id'];
						}
						if(!empty($nodeId)){
							if(isset($this->loadedItems[$nodeId])){
								unset($this->loadedItems[$nodeId]);//Make sure everything id in DESC Order
							}
							$this->loadedItems[$nodeId] = $this->startTime;
							if(!empty($item['items'])){
								$this->addLoadedItems($item['items']);
							}
						}
					}
				}
			}
		}
		//debug($this->loadedItems);
	}
	
	function beforeRender(&$controller){
		$this->save();
	}
	
	function beforeRedirect(&$controller, $url, $status=null, $exit=true){
		$this->save();
	}
	
	function save(){
		if(!is_null($this->loadedItems)){
			$this->Session->write('loadedItems',$this->loadedItems);
			//debug($this->loadedItems);
		}
	}
	
	function reset(){
		$this->loadedItems = array();
	}
	
	function load(){
		if(is_null($this->loadedItems)){
			$this->loadedItems = $this->Session->read('loadedItems');
		}
		if(empty($this->loadedItems)){
			$this->loadedItems = array();
		}else{
			//$this->loadedItems = array($this->loadedItems[0]);
		}
	}
	
	function getInvalidationData($nodes){
		$this->load();
		$opts = array(
			'inheritParent'=>false,
			'mode'=>'all',
			'fields' => Array(
				'id',
				'model',
				'foreign_key'
			),
			'conditions' => array(
				'model NOT' => null
			),
			'order'=>'model'
		);
		$NodeLink = ClassRegistry::init('NodeLink');
		$Invalidation = ClassRegistry::init('Invalidation');
		/*if(!empty($this->loadedItems)){
			//reset($this->loadedItems);
			//$minTime = current($this->loadedItems);
			//debug($minTime);
			$loadedCond = array();
			foreach($this->loadedItems as $node_id => $time){
				$loadedCond[] = array(
					$Invalidation->alias.'.node_id'=>$node_id,
					$Invalidation->alias.'.time >'=>$time
				);
			}
			$opts = set::merge($opts,array(
				'fields' => Array(
					"GROUP_CONCAT(IFNULL (".$Invalidation->alias.".field,'all')) AS Fields"
				),
				'joins' => array('1000-invalidation'=>array(
					'type'=>'left',
					'table'=>$Invalidation->useTable,
					'alias' =>$Invalidation->alias,
					'conditions' => array(
						$Invalidation->alias.'.node_id = AllNode.id',
						array('or'=>$loadedCond)
					)
				)),
				'conditions' => array(
					array('or'=>array(
							$Invalidation->alias.'.node_id IS NOT NULL',
							'not'=>array('AllNode.id'=>array_keys($this->loadedItems))
					))
				)
			));
		}*/
		//debug($opts);
		$allNodes = $NodeLink->getLinked($nodes,'invalidation',$opts);
		
		//debug($allNodes);
		if(!empty($allNodes)){
			/////////// Get invalidations /////////// 
			App::import('Lib', 'SetMulti');
			$allNodeId = $this->_getAllNodeIds($allNodes);
			$excludeCheck = array();
			$invalids = array();
			if(!$this->disableCache){
				$excludeCheck = array_intersect_key($this->loadedItems,array_flip($allNodeId));
				$bydate = SetMulti::flip($excludeCheck);
				//debug($bydate);
				$findOpt = array('fields'=>array($Invalidation->alias.'.id',$Invalidation->alias.'.node_id'));
				foreach($bydate as $time => $nids){
					$findOpt['conditions']['or'][] = array(
						$Invalidation->alias.'.node_id'=>$nids,
						$Invalidation->alias.'.time >'=>$time
					);
				}
				$invalids = $Invalidation->find('all',$findOpt);
				if(!empty($invalids)){
					$invalids = SetMulti::group($invalids,$Invalidation->alias.'.node_id');
				}
			}
			//debug($invalids);
			//debug($excludeCheck);
			
			/////////// remove unneeded /////////// 
			$toGet = array();
			
			foreach($allNodes as $node){
				$nid = $node['Node']['id'];
				if((!isset($excludeCheck[$nid]) || isset($invalids[$nid])) && ! empty($node['Node']['model'])){
					$model = $node['Node']['model'];
					if(empty($toGet[$model])){
						$toGet[$model] = array('ids'=>array(),'globalFields'=>'all','fields'=>array());
					}
					
					$id = $node['Node']['foreign_key'];
					$toGet[$model]['ids'][] = $id;
					$toGet[$model]['fields'][$id] = 'all';
				}
			}
			//debug($toGet);
			$aro = array($this->controller->User->myNodeRef($this->controller->user['User']['id']));
			//debug($aro);
			$allItems = array();
			foreach($toGet as $modelName => $opt){
				$model = ClassRegistry::init($modelName);
				$model->create();
				$findOptions = array(
					'conditions'=>array($model->alias.'.id'=>$opt['ids'])
				);
				if($opt['globalFields'] != 'all'){
					$findOptions['restrict'] = $opt['globalFields'];
				}
				//debug($findOptions);
				$items = $model->triggerAction('linkRead',array($findOptions),$aro);
				if(!empty($items)){
					$allItems = array_merge($allItems,$items);
				}
			}
			//debug($allItems);
			return $allItems;
		}
		return null;
	}
	function _getAllNodeIds($allNodes){
		//Too slow :  return Set::extract('/Node/id',$allNodes);
		$res = array();
		foreach($allNodes as &$node){
			$res[] = $node['Node']['id'];
		}
		return $res;
	}
}
?>
