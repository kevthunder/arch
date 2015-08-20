<?php
class LinkAction extends Object {
    var $warnings = array();
	var $params = array();
	var $requestNo;
	var $requestXml;
	var $content = null;
	var $controller;
	var $uses;
	var $data = array();
	var $items = array();
	var $defaultAro = array();
	var $_inited = false;
	var $responseKeys = array(
		'requestNo' => 'no',
		'content',
		'warnings',
		'data',
		'items'
	);
	
	function init(){
		if(!$this->_inited){
			if(!empty($this->uses)){
				foreach((array)$this->uses as $modelName){
					$model = ClassRegistry::init($modelName);
					if($model){
						$this->{$model->name} = $model;
					}
				}
			}
			$this->_inited = true;
		}
	}
	
	function addMsg($code){
		if(is_array($code)){
			foreach($code as $c){
				$this->addMsg($c);
			}
		}else{
			$this->warnings[] = $code;
		}
	}
	function addItems($items){
		//debug($items);
		if(!empty($items)){
			foreach($items as $modelName=>$model){
				App::import('Lib', 'SetMulti');
				if(!Set::numeric(array_keys(SetMulti::excludeKeys($model,array('internal'))))){
					$model = array($model);
				}
				if(array_key_exists('internal',$model)){
					if(empty($this->items[$modelName]['internal'])){
						$this->items[$modelName]['internal'] = array();
					}
					$this->items[$modelName]['internal'] = array_merge($this->items[$modelName]['internal'],$model['internal']);
					unset($model['internal']);
				}
				foreach($model as $item){
					$this->items[$modelName][] = $item;
				}
			}
			$this->controller->Link->addLoadedItems($items);
		}
	}
	function getCreatedItem($oldId){
		if(!empty($this->controller->Link->createdItems[$oldId])){
			return $this->controller->Link->createdItems[$oldId];
		}
		return null;
	}
	function set($key,$val=null){
		if(!is_array($key)){
			$key = array($key=>$val);
		}
		$this->data = array_merge($this->data,$key);
	}
	
	function execute($requestXml){
		$this->init();
		if(is_string($requestXml)){
			$dom = new DomDocument;
			$dom->preserveWhiteSpace = FALSE;
			if($dom->loadXML($xml)){
				$requestXml = $dom->firstChild;
			}
		}
		//is_string() 
		$this->warnings = array();
		if(is_object($requestXml) && get_class($requestXml) == "DOMElement"){
			$action = $requestXml->getAttribute('action');
			$this->requestNo = $requestXml->getAttribute('no');
			$this->reset();
			$this->requestXml = $requestXml->ownerDocument->saveXML($requestXml); 
			foreach($requestXml->attributes as $attr){
				$this->params[$attr->name] = $attr->value;
			}
			//debug($this->params);
			if(method_exists($this,$action)){
				$this->content = $this->$action();
				return $this->responseData();
			}else{
				return $this->invalidRequest($requestXml,405);
			}
		}else{
			return $this->invalidRequest($requestXml);
		}
	}
	
	function reset(){
		$this->params = array();
		$this->data = array();
		$this->items = array();
		$this->element = null;
		$this->requestXml = null;
	}
	
	function responseData($source = null){
		$data = array();
		if(is_null($source)){
			$source = $this;
		}
		foreach($this->responseKeys as $skey =>$tkey){
			if(is_numeric($skey)){
				$skey = $tkey;
			}
			if(is_object($source)){
				if(isset($source->$skey)){
					$data[$tkey] = $source->$skey;
				}
			}else{
				if(isset($source[$skey])){
					$data[$tkey] = $source[$skey];
				}
			}
		}
		//debug($data);
		return $data;
	}
	
	function invalidRequest($requestXml, $error = 409){
		$data = array();
		$requestNo = null;
		if(is_object($requestXml) && get_class($requestXml) == "DOMElement"){
			$requestNo = $requestXml->getAttribute('no');
		}
		if(!empty($requestNo)){
			$data['no'] = $requestNo;
		}
		$data['warnings'] = array($error);
		return $data;
	}
}
?>
