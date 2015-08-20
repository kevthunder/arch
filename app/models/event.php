<?php
class Event extends AppModel {
	var $name = 'Event';
	
	var $defaultHandlerPath = 'handler';
	var $specialHandlers = array(
		'true'=>true,
		'false'=>false,
		'none'=>null
	);
	
	var $actsAs = array(
		'serialized'=>array('conditions','params'=>array('manualSave'=>true)),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Aro' => array(
			'className' => 'Node',
			'foreignKey' => 'aro_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Aco' => array(
			'className' => 'Node',
			'foreignKey' => 'aco_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Owner' => array(
			'className' => 'Node',
			'foreignKey' => 'owner_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'EventType' => array(
			'className' => 'EventType',
			'foreignKey' => 'event_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	function beforeSave(){
		if(!empty($this->data[$this->alias]['params']) && !empty($this->data[$this->alias]['handler']) && !empty($this->data[$this->alias]['function'])){
			
			App::import('Lib', 'ClassCollection'); 
			$Handler = ClassCollection::getObject("handler",$this->data[$this->alias]['handler']);
			$function = $this->data[$this->alias]['function'];
			if($Handler && method_exists($Handler,$function.'_deconstruct')){
				$this->data[$this->alias]['params'] = $Handler->{$function.'_deconstruct'}($this->data[$this->alias]['params']);
			}
		}
		if(isset($this->data[$this->alias]['params'])){
			$this->data[$this->alias]['params'] = $this->serializeFunct($this->data[$this->alias]['params']);
		}
		return true;
	}
	
	function afterSave($created){
		$results = parent::afterSave($created);
		
		if(!in_array($this->data['Event']['event_type_id'],array(15,22))){
			$eventType = $created?22:15;
			$eventOption = array(
				'name' => $eventType,
				'aros'=> $this->data['Event']['aro_id'],
				'acos'=> $this->data['Event']['aco_id'],
				'bindedEvent'=> $this->unserialize($this->data),
				'phase'=> 2,
				'strict'=>false,
			);
			//debug($eventOption);
			$this->dispatchEvent($eventOption);
		}
		
		return $results;
	}
	
	function specialHandlersList(){
		$specialNames = array_keys($this->specialHandlers);
		$specialNames = array_map(array("Inflector","humanize"),$specialNames);
		$specials = array_combine(array_keys($this->specialHandlers),$specialNames);
		return $specials;
	}
	function handlersList($includeSpecial = true){
		App::import('Lib', 'ClassCollection'); 
		$handlers = ClassCollection::getList("handler",true);
		if($includeSpecial){
			$specials = $this->specialHandlersList();
			$handlers = array_merge($specials,$handlers);
		}
		return $handlers;
	}
	function handlersFunctionList($handler){
		if(array_key_exists($handler,$this->specialHandlers)) return false;
		App::import('Lib', 'ClassCollection'); 
		$class = ClassCollection::getClass("handler",$handler);
		if($class){
			$ignorePaterns = array(
				'/^_/',
				'/_form$/',
				'/^(?P<parent>.*)_log$/',
				'/_deconstruct$/',
			);
			$methods = get_class_methods($class);
			$fromParent = get_class_methods("handler");
			$methods = array_diff($methods,$fromParent);
			$list = array();
			foreach($methods as $method){
				foreach($ignorePaterns as $patern){
					if(preg_match($patern,$method,$matches)){
						if(!empty($matches['parent'])){
							$method = $matches['parent'];
							continue 1;
						}
						continue 2;
					}
				}
				$list[$method] = Inflector::humanize(Inflector::underscore($method));
			}
		}
		return $list;
	}
	
	function getHandlerInputs($handlerAlias,$function) {
		if(!empty($handlerAlias)){
			App::import('Lib', 'ClassCollection'); 
			$Handler = ClassCollection::getObject("handler",$handlerAlias);
			if($Handler && method_exists($Handler,$function.'_form')){
				$inputs = $Handler->{$function.'_form'}();
				return $inputs;
			}
		}
		return null;
	}
	
	function getHandler($alias){
		App::import('Lib', 'ClassCollection'); 
		return ClassCollection::getObject("handler",$alias);
		/*
		//get class name
		$p = strrpos('.',$alias);
		if($p===false){
			$name = $alias;
		}else{
			$name = substr($alias,$p+1);
		}
		$name = ucfirst($name).'Handler';
		
		//init
		if($exitent = ClassRegistry::getObject($name)){
			return $exitent;
		}else{
			$path = str_replace('.',DS,Inflector::underscore($alias));
			//if($p===false){
			//	$path = APP  'libs' . DS . $this->defaultHandlerPath . DS . $path;
			//}
			$path .= '_handler.php';
			if(!class_exists('Handler')){
				App::import('Lib', 'Handler', array('file' =>$this->defaultHandlerPath. DS .'handler.php'));
			}
			if(!class_exists($name)){
				if(!App::import('Lib', $name, array('file' =>$path))){
					debug($name.' not found');
				}
			}
			$created = null;
			//debug($name);
			//debug($path);
			if(class_exists($name)) {
				$created = new $name();
			}
			if($created){
				ClassRegistry::addObject($name, $created);
			}
			return $created;
		}
		*/
	}
	
	
	function dispatchEventPhases(&$eventName,$aros=null,$acos=null,$params=null,&$options=array()){
			if(is_array($eventName)){
				$options = &$eventName;
			}else{
				$options = array_merge($options,array(
					'name' => $eventName,
					'aros'=> $aros,
					'acos'=> $acos,
					'params'=> $params
				));
			}
			$options['phase'] = 1;
			//var_dump($options);
			if($this->dispatchEvent($options)){
				$options['phase'] = 2;
				$options['strict'] = false;
				$res = $this->dispatchEvent($options);
				return $res;
			}else{
				$options['messages'][] = 403;
			}
			return null;
	}
	
	
	function dispatchEvent(&$eventName,$aros=null,$acos=null,$params=null,$phase=2,&$options=array()){
		if(is_array($eventName)){
			$options = &$eventName;
		}else{
			$options = array_merge($options,array(
				'name' => $eventName,
				'aros'=> $aros,
				'acos'=> $acos,
				'params'=> $params,
				'phase'=> $phase
			));
		}
		$defaultOptions = array(
			'name' => '',
			'aros'=> null,
			'acos'=> null,
			'params'=> null,
			'phase'=> 2,
			'subEvent'=>array(),
			'return'=>true,
			'returnError'=>false,
			'strict'=>true,
		);
		if(empty($options['name']) && !empty($options['action'])){
			$options['name'] = $options['action'];
		}
		$options = array_merge($defaultOptions,$options);
		if(empty($options['name'])){
			return $options['returnError'];
		}
		$this->recursive = -1;
		if(!isset($this->Node)){
			$this->Node = ClassRegistry::init('Node'); 
		}
		if(empty($options['all_aros'])){
			$options['all_aros'] = $this->Node->appendParents($options['aros']); 
		}
		if(empty($options['all_acos'])){
			$options['all_acos'] = $this->Node->appendParents($options['acos']); 
		}
		if(empty($options['all_types'])){
			$options['all_types'] = $this->EventType->getSupTypes($options['name']);
		}
		
		$listeners = array();
		$res = $this->_dispatchEvent($options,$listeners);
		//debug($options['name'].' phase '.$options['phase']);
		//debug(var_export(SetMulti::filterNot($options,'is_object',-1),true));
		$this->_eventLog($listeners,$options,$res);
		
		return $res;
	}
	
	
	function _dispatchEvent(&$options,&$listeners = null){
		$findOpt = array(
			'conditions'=>array(
				'active'=>1,
				'event_type_id'=>array_keys($options['all_types']),
				array('or'=>array('phase'=>$options['phase'],'phase IS NULL')),
				array('or'=>array('aro_id'=>$options['all_aros'],'aro_id IS NULL')),
				array('or'=>array('aco_id'=>$options['all_acos'],'aco_id IS NULL')),
			),
			'order'=> array("`handler`='false' DESC")
		);
		//debug($findOpt);
		$listeners = $this->find('all', $findOpt);
		debug($options['name'].' phase '.$options['phase']);
		//debug($listeners);
		
		if(!empty($listeners)){
			$result = false;
			foreach($listeners as $listener){
				$curRes = null;
				if(isset($this->specialHandlers[$listener['Event']['handler']])){
					$curRes = $this->specialHandlers[$listener['Event']['handler']];
				}else{
					$Handler = $this->getHandler($listener['Event']['handler']);
					if($Handler && method_exists($Handler,$listener['Event']['function'])){
						$funct = $listener['Event']['function'];
						if(empty($funct)){
							$funct = $options['name'];
						}
						$params = (array)$listener['Event']['params'];
						$params = $this->_parseParams($options,$params);
						//$params = array_merge((array)$options['params'],$params);
						$curRes = $Handler->{$funct}($options,$params);
					}
				}
				$options['firedListener'][$listener['Event']['id']] = $curRes;
				if(!is_null($curRes)){
					if($curRes === false) {
						return $options['returnError'];
					}else{
						$result = true;
					}
				}
			}
			if(!$result && $options['strict']){
				return $options['returnError'];
			}
		}elseif($options['strict']){
			return $options['returnError'];
		}
		if(!empty($options['subEvent']) && is_array($options['subEvent'])){
			/*if(!Set::Numeric(array_keys($options['subEvent']))){
				$options['subEvent'] = array($options['subEvent']);
			}
			//$inherit = array();
			foreach($options['subEvent'] as &$subEvent){
				//$subEvent = array_merge($subEvent,array_intersect_key($options,array_flip($inherit)));
				if(empty($subEvent['all_aros']) && empty($subEvent['aros'])){
					$subEvent['all_aros'] = $options['all_aros'];
				}
				$subEvent['parentEvent'] = $options;
				$subEvent['phase'] = $options['phase'];
				$subEvent['strict'] = $options['strict'];
				$res = $this->dispatchEvent($subEvent);
				if($res === false){
					return $options['returnError'];
				}
			}*/
			$res = $this->_subEvents($options['subEvent'],$options);
			$success = $res['success'];
			unset($res['success']);
			$options['subEvent'] = $res;
			if($res === false){
				return $options['returnError'];
			}
		}
		//debug(SetMulti::filterNot($options,'is_object',-1));
		return $options['return'];
	}
	
	function _parseParams($options,$params){
		$pattern = "/^{{([\w.-_]*)}}$/";
		$matches = array();
		foreach($params as $key => $val){
			if(is_string($val) && preg_match($pattern, $val, $matches)){
				//debug($matches);
				$params[$key] = Set::extract($matches[1],$options);
			}elseif(is_array($val)){
				$params[$key] = $this->_parseParams($options,$val);
			}
		}
		return $params;
	}
	
	function _eventLog($listeners,&$options,$result){
		$loggedListeners = array();
		//debug($listeners);
		//debug(SetMulti::filterNot($options,'is_object',-1));
		foreach($listeners as $listener){
			if($listener['Event']['log']){
				$loggedListeners[] = $listener;
			}
		}
		if(!empty($loggedListeners) || !empty($options['timed_event_id'])){
			$this->TimedEvent = ClassRegistry::init('TimedEvent');
			App::import('Lib', 'SetMulti');
			$format = $this->getDataSource()->columns['datetime']['format'];
			$log = count($loggedListeners);
			$data = array(
				'event_type_id' => reset(array_keys($options['all_types'])),
				'time' => date($format),
				'aro_id' => !empty($options['all_aros'])?end($options['all_aros']):null,
				'aco_id' => !empty($options['all_acos'])?end($options['all_acos']):null,
				'final_data' => SetMulti::filterNot($options,'is_object',-1),
				'context' => 'log',
			);
			if($options['phase']==1){
				$data['success'] = (bool)$result;
			}else{
				$data['success'] = 1;
				$data['result'] = $result;
			}
			if(!empty($options['timed_event_id'])){
				$data['id'] = $options['timed_event_id'];
				$exclude = array('event_type_id','time','aro_id','aco_id','context');
				$data = array_diff_key($data,array_flip($exclude));
			}else{
				$data['active'] = 0;
			}
			foreach($loggedListeners as $listener){
				//debug($listener['Event']['function'].'_log');
				$Handler = $this->getHandler($listener['Event']['handler']);
				if($Handler && method_exists($Handler,$listener['Event']['function'].'_log')){
					$params = (array)$listener['Event']['params'];
					$params = $this->_parseParams($options,$params);
					$res = $Handler->{$listener['Event']['function'].'_log'}($data,$params);
					if($res === false){
						$log--;
					}elseif(is_array($res)){
						$data = $res;
					}
				}
			}
			if($log || !empty($options['timed_event_id'])){
				$this->TimedEvent->create();
				$this->TimedEvent->save($data);
				if(empty($options['timed_event_id'])){
					$options['timed_event_id'] = $this->TimedEvent->id;
				}
			}
		}
	}
	
	function _subEvents($subEvents,$parentEvent){
		$localOpt = array('min','max','valid');
		$mod = array(
			'and' => array(),
			'or' => array(
				'min' => 1,
			)
		);
		//debug(var_export(SetMulti::filterNot($subEvents,'is_object',-1),true));
		if(!$this->_hasSubEvents($subEvents,$localOpt,$mod)){
			$subEvents = array($subEvents);
		}
		$opt = array_intersect_key($subEvents,array_flip($localOpt));
		$subEventsOnly = array_diff_key($subEvents,array_flip($localOpt));
		foreach($tmp = $subEventsOnly as $key => $subEvent){
			if(array_key_exists('valid',$subEvent) && !$subEvent['valid']){
				unset($subEventsOnly[$key]);
			}
		}
		$total = count($subEventsOnly);
		if($total > 0){
			$defOpt = array(
				'min' => $total,
				'max' => $total,
			);
			$opt = array_merge($defOpt,$opt);
			$valids = 0;
			$i = 0;
			foreach($subEventsOnly as $key => $subEvent){
				if(!empty($subEvent['filterPhase']) && $subEvent['filterPhase'] != $parentEvent['phase']){
					continue;
				}
				$isMod = in_array($key,array_keys($mod), true);
				$hasSub = $this->_hasSubEvents($subEvent,$localOpt,$mod);
				if($isMod || $hasSub){
					if(!$hasSub){
						$subEvent = array($subEvent);
					}
					if($isMod){
						$subEvent = array_merge($mod[$key],$subEvent);
					}
					$res = false;
					$res = $this->_subEvents($subEvent,$parentEvent);
					if($res !== false){
						$subEvent = $res;
					}
				}else{
					//$subEvent = array_merge($subEvent,array_intersect_key($options,array_flip($inherit)));
					if(empty($subEvent['all_aros']) && empty($subEvent['aros'])){
						$subEvent['all_aros'] = $parentEvent['all_aros'];
					}
					$subEvent['parentEvent'] = $parentEvent;
					$subEvent['phase'] = $parentEvent['phase'];
					$subEvent['strict'] = $parentEvent['strict'];
					$res = $this->dispatchEvent($subEvent);
					unset($subEvent['parentEvent']);
					unset($subEvent['phase']);
					unset($subEvent['strict']);
				}
				$subEvent['valid'] = ($res !== false);
				if($subEvent['valid']){
					$valids++;
				}
				if($valids > $opt['max']){
					$subEvent['valid'] = false;
				}
				$subEvents[$key] = $subEvent;
				if($total - $i - 1 + $valids < $opt['min']){
					$subEvents['success'] = false;
					return $subEvents;
				}
				$i++;
			}
		}
		$subEvents['success'] = true;
		return $subEvents;
	}
	
	function _hasSubEvents($event,$localOpt,$mod){
		$keys = array_keys($event);
		$modKeys = array_intersect($keys,array_keys($mod));
		$nonOptKeys = array_diff($keys,array_merge($localOpt,array_keys($mod)));
		return (count($nonOptKeys) == 0 && count($modKeys)>0) || Set::Numeric($nonOptKeys);
	}
	
	function debugEventStack($eventOptions,$return=false){
		$out = $eventOptions['name'];
		while(!empty($eventOptions['parentEvent'])){
			$eventOptions = $eventOptions['parentEvent'];
			$out .= '=>'.$eventOptions['name'];
		}
		if($return){
			return $out;
		}else{
			if (Configure::read() > 0) {
				$calledFrom = debug_backtrace();
				echo '<strong>' . substr(str_replace(ROOT, '', $calledFrom[1]['file']), 1) . '</strong>';
				echo ' (line <strong>' . $calledFrom[1]['line'] . '</strong>)';
				echo "\n<pre class=\"cake-debug\">\n";
				$out = print_r($out, true);
				$out = str_replace('<', '&lt;', str_replace('>', '&gt;', $out));
				echo $out . "\n</pre>\n";
			}
		}
	}
	
	function reverseEventAssociation($foreignClass,$assoc, $data, $options){
		$defOpt = array(
			'errorReturn'=>null,
			'mode'=>'all',
		);
		$opt = Set::merge($defOpt,$options);
		if(empty($assoc['mode'])){
			return $opt['errorReturn'];
		}
		//debug($assoc);
		//debug($data);
		$cond = array();
		$this->Behaviors->attach('Util');
		$filtered = $this->testApplicableCond($assoc['conditions'], $data, null, &$cond);
		//debug($filtered);
		//debug($cond);
		if(empty($filtered)){
			return null;
		}
		if(!empty($cond)){
			$localOpt = array('dependant','className','type','name','mode');
			$findOpt = array_diff_key($assoc,array_flip($localOpt));
			$filtered = $this->find('all',$findOpt);
		}
		if(empty($filtered)){
			return null;
		}
		$ids = $this->extractIds($filtered);
		$foreignModel = ClassRegistry::init($foreignClass);
		if(empty($foreignModel) || !$foreignModel->Behaviors->attached('Node')){
			return $opt['errorReturn'];
		}
		$foreignModel->recursive = -1;
		$findOpt = array(
			'joins' => array(
				$foreignModel->nodesJoint(),
				$foreignModel->Node->parentsJointOpt(),
				array(
					'table' => $this->useTable,
					'alias' => $this->alias,
					'type' => 'inner',
					'conditions' => array(
						'Parent.id = '.$this->alias.'.'.Inflector::underscore($assoc['mode']).'_id',
						$this->alias.'.id' => $ids
					)
				)
			),
		);
		//debug($findOpt);
		$items = $foreignModel->find($opt['mode'],$findOpt);
		//debug($items);
		return $items;
		
		return $opt['errorReturn'];
	}
}
