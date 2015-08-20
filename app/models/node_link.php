<?php
class NodeLink extends AppModel {
	var $name = 'NodeLink';
	
	
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'OwnerNode' => array(
			'className' => 'Node',
			'foreignKey' => 'owner_node_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'OwnedNode' => array(
			'className' => 'Node',
			'foreignKey' => 'owned_node_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'NodeLinkType' => array(
			'className' => 'NodeLinkType',
			'foreignKey' => 'type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	function __construct(){
		parent::__construct();
		if(!isset($this->Node)){
			$this->Node = ClassRegistry::init('Node');
		}
	}
	
	function link($type, $ownerRef, $ownedRef, $opt = array()){
		$defaultOpt = array(
			'context'=> null,
			'dry' => false,
		);
		$opt = Set::merge($defaultOpt,$opt);
		if(/*empty($type) || */empty($ownerRef) || empty($ownedRef)){
			return false;
		}
		
		$ownerRef = $this->Node->fullRef($ownerRef);
		if(isset($ownerRef['id'])){
			$ownerNode = array('Node'=>$ownerRef);
		}else{
			$ownerNode = $this->Node->getNode($ownerRef,false);
		}
		if(empty($ownerNode)){
			debug('ack1');
			return false;
		}
		
		
		$ownedRef = $this->Node->fullRef($ownedRef);
		if(isset($ownedRef['id'])){
			$ownedNode = array('Node'=>$ownedRef);
		}else{
			$ownedNode = $this->Node->getNode($ownedRef,false);
		}
		if(empty($ownedNode)){
			debug('ack2');
			return false;
		}
		$optToData = array('context');
		$data = array(
			'type_id'=>$this->NodeLinkType->getTypeId($type),
			'active'=>1,
			'owner_node_id' => $ownerNode['Node']['id'],
			'owned_node_id' => $ownedNode['Node']['id']
		);
		$data = Set::merge($data,array_intersect_key($opt, array_flip($optToData)));
		//debug($data);
		if(!$opt['dry']){
			$this->create();
			$res = $this->save($data);
			if($res) {
				$res[$this->alias]['id'] = $this->id;
			}
			return $res;
		}else{
			return $data;
		}
	}
	
	function unlinkAll($type, $ownerRef, $ownedRef){
		$ownerRef = $this->Node->fullRef($ownerRef);
		$ownedRef = $this->Node->fullRef($ownedRef);
		if(/*empty($type) || */empty($ownerRef) || empty($ownedRef)){
			return false;
		}
		
		$ownerCond = array();
		forEach($ownerRef as $key => $val){
			$ownerCond['OwnerNode.'.$key] = $val;
		}
		$ownedCond = array();
		forEach($ownedRef as $key => $val){
			$ownedCond['OwnedNode.'.$key] = $val;
		}
		$query = array(
			'field'=>array('id'),
			'conditions'=>array(
				$this->alias.'.type_id'=>$this->NodeLinkType->getTypeId($type),
				$this->alias.'.active'=>1,
				$this->alias.'.owner_node_id = OwnerParent.id',
				$this->alias.'.owned_node_id = OwnedParent.id'
			),
			'joins' => array(
				array(
					'type'=>'inner',
					'table'=>$this->Node->useTable,
					'alias' =>'OwnerNode',
					'conditions' => $ownerCond
				),
				array(
					'type'=>'inner',
					'table'=>$this->Node->useTable,
					'alias' =>'OwnedNode',
					'conditions' => $ownedCond
				),
				array(
					'type'=>'inner',
					'table'=>$this->Node->useTable,
					'alias' =>'OwnerParent',
					'conditions' => array(
						'OwnerParent.lft >= OwnerNode.lft',
						'OwnerParent.rght <= OwnerNode.rght'
					)
				),
				array(
					'type'=>'inner',
					'table'=>$this->Node->useTable,
					'alias' =>'OwnedParent',
					'conditions' => array(
						'OwnedParent.lft >= OwnedNode.lft',
						'OwnedParent.rght <= OwnedNode.rght'
					)
				)
			)
		);
		$this->recursive = -1;
		$toDelete = $this->find('all',$query);
		if(is_array($toDelete)){
			if(!empty($toDelete)){
				foreach($toDelete as $i => $val){
					$toDelete[$i] = $val[$this->alias]['id'];
				}
				$bk = $this->belongsTo;
				$this->belongsTo = array();
				$res = $this->deleteAll(array('id'=>$toDelete));
				$this->belongsTo = $bk;
				return $res;
			}
			return true;
		}
		return false;
	}
	
	function getValidConditions($opt = array()){
		$defaultOpt = array(
			'time' => true,
			'alias' => true,
		);
		$opt = Set::merge($defaultOpt,$opt);
		if($opt['alias'] === true){
			$opt['alias'] = $this->alias;
		}
		$a = "";
		if(!empty($opt['alias'])){
			$a = $opt['alias']. ".";
		}
		$conditions = array(
			$a.'active' => 1
		);
		if(!empty($opt['time'])){
			if($opt['time'] === true){
				$opt['time'] = mktime();
			}
			if(is_numeric($opt['time'])){
				$format = $this->getDataSource()->columns['datetime']['format'];
				$opt['time'] = date($format,$opt['time']);
			}
			$conditions[] = array('or'=>array(
					$a.'start <=' => $opt['time'],
					$a.'start IS NULL'
				));
			$conditions[] = array('or'=>array(
					$a.'end >' => $opt['time'],
					$a.'end IS NULL'
				));
		}
		return $conditions;
	}
	
	function getLinked($items,$linkType,$opt = array()){
		$localOpt = array('mode','time','finalNodes','filter','inheritParent');
		$defaultOpt = array(
			'typeId' => null,
			'mode' => 'all',
			'recusive' => -1,
			'inheritParent' => true,
			'globalLinks' => true,
			'time' => true,
			'finalNodes' => array(),
			'excludeModels' => false,
			'filter' => false
		);
		$opt = Set::merge($defaultOpt,$this->NodeLinkType->getOpt($linkType),$opt);
		if(empty($opt['fields'])){
			$fields = $this->Node->getFields(false);
			$opt['fields'] = $fields;
		}
		//debug($opt);
		
		$getNodesFields = $opt['fields'];
		if(!empty($opt['filter'])){
			$ds = $this->Node->getDataSource();
			$this->Node->virtualFields['filter'] = $ds->conditions($opt['filter'], true, false, $this->Node);
			$getNodesFields = array_merge(array('filter'),$getNodesFields);
		}
		//debug($items);
		$allNodes = $this->Node->getNodes($items,array('fields'=>$getNodesFields));
		//debug($allNodes);
		if(!empty($opt['filter'])){
			unset($this->Node->virtualFields['filter']);
		}
		
		$nodeIds = $this->_getAllNodeIds($allNodes);
		
		
		$tcheckNodes = array_diff($nodeIds,$opt['finalNodes']);
		if(!empty($tcheckNodes)){
			//////// new hopefully faster ////////
			if($opt['inheritParent']) {
				$all = $this->Node->appendParents($tcheckNodes);
			}else{
				$all = $tcheckNodes;
			}
			
			$this->Behaviors->attach('Util');
			$validConditions = $this->getValidConditions(array('time'=>$opt['time'],'alias' =>false));
			$query = array(
				'conditions' => array(),
				'group' => array($this->Node->alias.'.id')
			);
			if($opt['inheritParent']) {
				$join =  array(
					'type'=>'inner',
					'table'=>$this->Node->useTable,
					'alias' => 'DirectNode',
					'conditions' => array(
						$this->Node->alias.'.lft >= DirectNode.lft',
						$this->Node->alias.'.rght <= DirectNode.rght'
					)
				);
				if(!empty($opt['excludeModels'])){
					$join['conditions']['not']['DirectNode.model'] = $opt['excludeModels'];
				}
				$query['joins'][] = $join;
			}
			if(!empty($opt['excludeModels'])){
				$query['conditions']['not'][$this->Node->alias.'.model'] = $opt['excludeModels'];
			}
			$joinCond = array($this->alias.'.type_id' => $opt['typeId']);
			if($opt['globalLinks']){
				$joinCond[] = $this->alias.'.type_id IS NULL';
				$joinCond = array('or'=>$joinCond);
			}
			$query['joins'][] = array(
				'type'=>'inner',
				'table'=>$this->useTable,
				'alias' => $this->alias,
				'conditions' => array(
					$joinCond,
					$this->addAliasToCond($validConditions,$this->alias),
					$this->alias.'.owned_node_id = '.($opt['inheritParent']?'DirectNode':$this->Node->alias).'.id',
					$this->alias.'.owner_node_id' => $all
				)
			);
			
			$queryOpt = array_diff_key($opt, array_flip($localOpt));
			
			if(!empty($queryOpt['conditions'])){
				$queryOpt['conditions'] = $this->addAliasToCond($queryOpt['conditions'],$this->Node->alias);
			}
			if(!empty($queryOpt['order'])){
				$queryOpt['order'] = $this->addAliasToCond((array)$queryOpt['order'],$this->Node->alias);
			}
			$query['fields'] = array();
			if(!empty($opt['filter'])){
				$filter_field = '('. $ds->conditions($this->addAliasToCond($opt['filter'], $this->Node->alias), true, false, $this->Node).') as filter';
				$query['fields'] = array_merge(array($filter_field),$query['fields']);
			}
			$query = Set::merge($queryOpt,$query);
			
			$foundNodes = $this->Node->find('all',$query);
			//debug($foundNodes);
			
			if(!empty($foundNodes)){
				foreach($foundNodes as &$node){
					if(!empty($opt['filter'])){
						$node['Node']['filter'] = $node[0]['filter'];
					}
					if(is_array($node) && isset($node['Node'])){
						$node = $node['Node'];
					}
				}
				$newOpt = $opt;
				$newOpt['finalNodes'] = array_merge($newOpt['finalNodes'],$nodeIds);
				$foundNodes = $this->getLinked($foundNodes,$linkType,$newOpt);
				$allNodes = array_merge($allNodes,$foundNodes);
			}
		}
		if(!empty($opt['filter'])){
			$filteredNodes = array();
			//debug($allNodes);
			foreach($allNodes as $node){
				if(!is_array($node) || !isset($node['Node']['filter']) || $node['Node']['filter']){
					if(is_array($node)){
						unset($node['Node']['filter']);
					}
					$filteredNodes[] = $node;
				}
			}
			$allNodes = $filteredNodes;
		}
		if($opt['mode'] == 'list'){
			foreach($allNodes as &$node){
				if(is_array($node) && isset($node['Node']['id'])){
					$node = $node['Node']['id'];
				}
			}
		}
		return $allNodes;
	}
	
	function _getAllNodeIds($allNodes){
		//Too slow :  return $this->Node->getNodeIds($allNodes);
		$res = array();
		foreach($allNodes as &$node){
			$res[] = $node['Node']['id'];
		}
		return $res;
	}
}
?>