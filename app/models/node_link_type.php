<?php
class NodeLinkType extends AppModel {
	var $name = 'NodeLinkType';
	var $displayField = 'name';
	
	var $cache = array();
	
	var $actsAs = array(
		'serialized'=>array('exclude_models')
	);
	
	function getOpt($name){
		if(!empty($this->cache[$name])){
			return $this->cache[$name];
		}
		$extract = array(
			'typeId' => 'id',
			'inheritParent' => 'inherit_parent',
			'recursive_links' => 'recursive_links',
			'globalLinks' => 'global_links',
			'excludeModels' => 'exclude_models'
		);
		$findOpt = array('conditions'=>array(),'recursive'=>-1);
		if(is_numeric($name)){
			$findOpt['conditions']['id'] = $name;
		}else{
			$findOpt['conditions']['name'] = $name;
		}
		$type = $this->find('first',$findOpt);
		App::import('Lib', 'SetMulti');
		$opt = SetMulti::extractHierarchicMulti($extract,$type[$this->alias],array('extractNull' => false));
		$this->cache[$type[$this->alias]['name']] = $opt;
		$this->cache[$type[$this->alias]['id']] = $opt;
		//debug($opt);
		return $opt;
	}
	
	function getTypeId($name){
		if(is_numeric($name)){
			return $name;
		}else{
			$opt = $this->getOpt($name);
			return $opt['typeId'];
		}
	}
}
