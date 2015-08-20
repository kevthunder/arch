<?php
	class EventTriggerBehavior extends ModelBehavior {
		var $settings;
		var $defaultOptions = array(
			'timedEvent' => array(),
			'fieldChangeEvent' => array(),
		);
		
		var $timedEventDefOpt = array(
			'autoCreate' => true,
			'autoTrigger' => true,
			'eventType' => null,
			'timeField' => null,
			'role' => 'aco',
			'source' => array(
				'owner_id' => 'Node.id',
			),
			'data' => array(
				'event_type_id' => null,
				'time' => null,
				'data' => null,
				'aro_id' => null,
				'aco_id' => null,
				'x' => null,
				'y' => null,
				'range' => 0,
				'repeate' => null,
				'repeate_count' => null,
				'retroactive' => null,
				'owner_id' => null,
				'active' => 1
			)
		);
		
		var $fieldChangeDefOpt = array(
			'field' => null,
			'eventType' => '23',
			'validate' => true,
		);
		
		var $createWatch = array();
		
		function setup(&$model, $settings) {
			$this->settings[$model->alias] = array_merge($this->defaultOptions, (array)$settings);
			
			$this->createWatch[$model->alias] = array();
			if(!empty($this->settings[$model->alias]['timedEvent'])){
				$this->settings[$model->alias]['timedEvent'] = $this->getTimedEventsBasicOpt(&$model, $this->settings[$model->alias]['timedEvent']);
				foreach($this->settings[$model->alias]['timedEvent'] as $alias => $opt){
					if(!empty($opt['timeField']) && $opt['autoCreate']){
						$this->createWatch[$model->alias][$opt['timeField']] = $alias;
					}
				}
			}
			
			if(!empty($this->settings[$model->alias]['fieldChangeEvent'])){
				$this->settings[$model->alias]['fieldChangeEvent'] = $this->getFieldChangeOpt($model,$this->settings[$model->alias]['fieldChangeEvent']);
			}
			
			if(!isset($model->Event)){
				$model->Event = ClassRegistry::init('Event'); 
			}
			if(!$model->Behaviors->attached('Node')){
				$model->Behaviors->attach('Node');
			}
		}
		
		function beforeValidate(&$model){
			
		}
		function beforeSave(&$model){
			/*$actors = array();
			if(!empty(&$model->data['Actors'])){
				$actors = array_merge($actors,(array)$model->data['Actors']);
			}
			if(dispatchEvent('save',$aros=null,$acos=null,$params=null)){
				
			}*/
		}
		
		function hasMethod(&$model,$action){
			if(method_exists ($model, $action)){
				return true;
			}
			$behaviorsMethods = $model->Behaviors->methods();
			foreach($behaviorsMethods  as $methods){
				if(in_array($action,$methods)){
					return true;
				}
			}
			return false;
		}
		
		function saveCreate(&$model,$data, $validate = true, $fieldList = array()){
			$model->create();
			unset($data[$model->primaryKey]);
			unset($data[$model->alias][$model->primaryKey]);
			return $model->save($data, $validate, $fieldList);
		}
		
		
		function resolveSaveAction(&$model,$options){
			if(empty($model->id) && empty($options['params'][0][$model->primaryKey]) && empty($options['params'][0][$model->alias][$model->primaryKey])){
				$options['action'] = 'saveCreate';
			}
			return $options;
		}
		
		function resolveAction(&$model,$options,$min = false){
			$defaultOptions = array(
				'action' => null,
				'method' => null,
				'params'=> null,
				'aros'=> null,
				'acos'=> null,
				'validationMethod'=>false,
				'resolveFunct'=>null,
				'messages'=>null,
			);
			$options = array_merge($defaultOptions,$options);
			if(empty($options['action'])){
				return null;
			}
			if(is_null($options['resolveFunct']) || $options['resolveFunct']===true){
				$options['resolveFunct'] = 'resolve'.Inflector::camelize($options['action']).'Action';
			}
			if($options['resolveFunct'] && $model->hasMethod($options['resolveFunct'])){
				$options = $model->{$options['resolveFunct']}($options);
			}
			if(is_null($options['method']) || $options['method']===true){
				$options['method'] = $options['action'] ;
			}
			if(is_null($options['acos']) || $options['acos']===true){
				if($model->id){
					$options['acos'] = $model->myNodeRef();
				}else{
					$options['acos'] = $model->parentNode();
				}
			}
			if(!is_array($options['messages'])){
				$options['messages'] = array();
			}
			if($min){
				unset($options['method']);
				unset($options['validationMethod']);
				unset($options['resolveFunct']);
				unset($options['messages']);
			}
			//debug($options);
			return $options;
		}
		
		function triggerAction(&$model,$action,$params=array(),$aros=null,$options=array()){
			if(is_array($action)){
				$options = $action;
			}else{
				$options = array_merge(array(
					'action' => $action,
					'params'=> $params,
					'aros'=> $aros,
				),$options);
			}
			$options = $this->resolveAction($model,$options);
			if(empty($options)){
				return null;
			}
			$local = array('method','validationMethod','resolveFunct');
			$eventOptions = array_diff_key($options,array_flip($local));
			$eventOptions['strict'] = true;
			$eventOptions['phase'] = 1;
			if($model->Event->dispatchEvent($eventOptions)){
				$res = true;
				if($options['method'] && $model->hasMethod($options['method'])){
					if(Set::numeric(array_keys($options['params']))){
						$res = call_user_func_array(array($model, $options['method']), $eventOptions['params']);
					}else{
						$res = call_user_func_array(array($model, $options['method']), array($eventOptions['params']));
					}
				}
				if(!$options['validationMethod'] || $res){
					$eventOptions['strict'] = false;
					$eventOptions['phase'] = 2;
					$model->Event->dispatchEvent($eventOptions);
				}
				$options['messages'] = $eventOptions['messages'];
				return $res;
			}else{
				$options['messages'][] = 403;
			}
			return null;
		}
		
		function getFieldChangeOpt(&$model,$options){
			if(is_array($options) && Set::numeric(array_keys($options))){
				$finalOpt = array();
				foreach($options as $opt){
					$opt = $this->getFieldChangeOpt($model,$opt);
					if(!empty($opt)){
						$finalOpt[] = $opt;
					}
				}
				return $finalOpt;
			}
			
			if(!is_array($options)){
				$options = array('field'=>$options);
			}
			$opt = array_merge($fieldChangeDefOpt,$options);
			return $opt;
		}
		
		function testChangeEvents($model,$phase=1,$data=null){
			if(is_null($data)){
				$data = $model->data;
			}
			if(empty($this->settings[$model->alias]['fieldChangeEvent'])){
				return true;
			}
			$changesOpt = $this->settings[$model->alias]['fieldChangeEvent'];
			$needBefore = array();
			$changedfield = array();
			foreach($changesOpt as $opt){
				if(($phase != 1 || $opt['validate']) && isset($data[$model->alias][$opt['field']])){
					$changedfield[$opt['field']] = $opt;
					if(empty($data[$model->alias]['originalData'][$opt['field']])){
						$needBefore[] = $opt['field'];
					}
				}
			}
			if(!empty($needBefore)){
				if(empty($data[$model->alias]['originalData'])) $data[$model->alias]['originalData'] = array();
				$before = $model->find('first',array('fields'=>array_merge(array($model->primaryKey),$needBefore),'conditions'=>array($model->primaryKey=>$model->id),'recursive'=>-1));
				$data[$model->alias]['originalData'] = array_merge($before[$model->alias]);
			}
			$toTrigger = array();
			$diff = array();
			foreach($changedfield as $field => $opt){
				if($data[$model->alias][$opt['field']] != $data[$model->alias]['originalData'][$opt['field']]){
					$toTrigger[] = $opt["eventType"];
					if(is_numeric($data[$model->alias][$opt['field']])){
						$diff[$field] = $data[$model->alias][$opt['field']]-$data[$model->alias]['originalData'][$opt['field']];
					}else{
						$diff[$field] = $data[$model->alias][$opt['field']];
					}
				}
			}
			foreach($toTrigger as $eventType){
				$eventOpt = array(
					'name' => $eventType,
					'phase' => $phase,
					'strict' => ($phase == 1),
					'params' => array(
						'data' => $data[$model->alias],
						'originalData' => $data[$model->alias]['originalData'],
						'diff' => $diff,
					),
				);
				unset($eventOpt['params']['data']['originalData']);
				$res = $model->Event->dispatchEvent($eventOpt);
				if($phase == 1 && !$res){
					return false;
				}
			}
			return true;
		}
		
		function getTimedEventsBasicOpt(&$model,$timedEvents){
			$def = array(
				'autoCreate' => $this->timedEventDefOpt['autoCreate'],
			);
			$finalOpt = array();
			foreach($timedEvents as $alias => $opt){
				if(!is_array($opt)){
					$opt = array('eventType'=>$opt);
				}
				if(!empty($opt['alias'])){
					$alias = $opt['alias'];
				}elseif(is_numeric($alias) && !empty($opt['timeField'])){	
					$alias = $opt['timeField'];
				}
				if(empty($opt['timeField']) && !is_numeric($alias)){
					$opt['timeField'] = $alias;
				}
				$opt = array_merge($def,$opt);
				$finalOpt[$alias] = $opt;
			}
			return $finalOpt;
		}
		
		function getTimedEventOptions(&$model,$entry = null,$options = array()){
			$defOpt = $this->timedEventDefOpt;
			unset($defOpt['data']);
			$opt = Set::merge($defOpt,$options);
			if(empty($opt['data'])) $opt['data'] = array();
			$opt['data'] = array_merge(array_intersect_key($opt,$this->timedEventDefOpt['data']),$opt['data']);
			
			
			if(!empty($opt['timeField'])){
				$opt['source']['time'][] = $model->alias.'.'.$opt['timeField'];
			}
			if(!empty($opt['eventType'])){
				$event_type = $opt['eventType'];
				if(!is_numeric($event_type)){
					$event_type = $model->Event->EventType->find('first',array('field'=>'id','conditions'=>array('name'=>$event_type)));
					if(!empty($event_type)) $event_type = $event_type['EventType']['id'];
				}
				$opt['data']['event_type_id'] = $event_type;
			}
			if(!empty($opt['role'])){
				$opt['source'][$opt['role'].'_id'] = 'Node.id';
			}
			
			if(!empty($opt['source'])){
				App::import('Lib', 'SetMulti');
				$opt['data'] = array_merge($opt['data'],SetMulti::extractHierarchicMulti($opt['source'],$entry));
			}
			
			if(empty($opt['data']['x']) || empty($opt['data']['y'])){
				$model->Behaviors->attach('Util');
				if($model->hasMethod('getPos')){
					$pos = $model->getPos($entry);
					if(!empty($pos)){
						$opt['data'] = array_merge($pos,$opt['data']);
					}
				}
			}
			
			$opt['data'] = array_merge($this->timedEventDefOpt['data'],$opt['data']);
			
			if(!empty($opt['data']['event_type_id']) && !empty($opt['data']['time']) && !empty($opt['data']['owner_id'])){
				return $opt;
			}
			
			return null;
		}
		
		
		function afterSave(&$model, $created) {
			$toUpdate = array_intersect_key($this->createWatch[$model->alias],array_filter($model->data[$model->alias]));
			//if($model->alias == 'SkillInstance'){
			//	debug($model->data);
			//}
			if(!empty($toUpdate)){
				$entry = $model->find('first',array('conditions'=>array($model->alias.'.id'=>$model->id),'recursive'=>0));
				foreach($toUpdate as $alias => $opt){
					$res = $model->getTimedEvent($alias,$entry);
					if(!empty($res)){
						$model->data["TimedEvent"][$alias] = $res;
					}
				}
			}
		}
		
		
		function getTimedEvent(&$model, $eventAlias, $entry = null,$options = array()){
			$defOpt = array(
				'create' => true,
			);
			if(is_null($entry)){
				$entry = $this->id;
			}
			if(is_numeric($entry)){
				$entry = $this->find('first',array('conditions'=>array($this->alias.'.id'=>$entry),'recursive'=>0));
			}
			if(!empty($entry)){
				$savedOpt = array();
				if(!empty($this->settings[$model->alias]['timedEvent'][$eventAlias])){
					$savedOpt = $this->settings[$model->alias]['timedEvent'][$eventAlias];
				}
				$opt = $this->getTimedEventOptions($model, $entry, array_merge($defOpt,$savedOpt,$options));
			}
			if(!empty($opt)){
				$this->TimedEvent = ClassRegistry::init('TimedEvent');
				$timedEvent = $this->TimedEvent->find('first',array('conditions'=>array('event_type_id' => $opt['data']['event_type_id'],'owner_id' => $opt['data']['owner_id'])));
				if(empty($timedEvent) && $opt['create']){
					$data = $opt['data'];
					$this->TimedEvent->create();
					if($this->TimedEvent->save($data)){
						$timedEvent = array('TimedEvent'=>$data);
						$timedEvent['TimedEvent']['id'] = $this->TimedEvent->id;
						
						$model->Behaviors->attach('Util');
						if($model->hasMethod('timedEventCreated')){
							$model->timedEventCreated($timedEvent,$entry);
						}
						
						if($opt['autoTrigger']){
							$this->TimedEvent->checkEvents($timedEvent);
						}
					}else{
						return false;
					}
				}
				
				
				return $timedEvent;
			}
			return null;
		}
		
		
	}
?>