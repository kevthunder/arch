<?php
	class XmlLinkedBehavior extends ModelBehavior {
		var $settings;
		var $defaultOptions = array(
			'tagName' => true,
			'find' => array('fields'=>array('id')),
			'outputDefine' => array(
				'field' => 'output_define',
			)
		);
		var $internalFindOpt = array('internal','tagName','mapping','sublinked');
		
		function setup(&$model, $settings) {
			$settings = (array)$settings;
			if(count(array_intersect_key($settings,array_flip(array('find','fields')))) == 0){
				$settings = array(
					'find' => array('fields'=>$settings)
				);
				if(!empty($settings['find']['fields']['internal'])){
					$settings['find']['internal'] = $settings['find']['fields']['internal'];
					unset($settings['find']['fields']['internal']);
				}
				//debug($settings);
			}
			if(!isset($settings['find']) && isset($settings['fields'])){
				$settings = array(
					'find' => $settings
				);
			}
			$this->settings[$model->alias] = Set::merge($this->defaultOptions, (array)$settings);
		}
		
		function tagName(&$model){
			if(!empty($model->tagName)){
				return $model->tagName;
			}
			if($this->settings[$model->alias]['tagName']){	
				if($this->settings[$model->alias]['tagName'] === true){
					return $model->alias;
				}else{
					return $this->settings[$model->alias]['tagName'];
				}
			}
			return null;
		}
		
		function extractFromXml(&$model, $xml){
			$tag = $model->tagName();
			
			if(!is_object($xml) || get_class($xml) != 'SimpleXMLElement'){
				try {
					$xml = new SimpleXMLElement($xml);
				} catch (Exception $e) {
				
				}
			}
			if(!empty($xml)){
				$res = array();
				foreach($xml->{$tag} as $item){
					$data = $model->xmlToData($item);
					if(!empty($data)){
						$res[] = $data;
					}
				}
				return $res;
			}
			return null;
		}
		
		function xmlToData(&$model, $xml){
			if(!is_object($xml) || get_class($xml) != 'SimpleXMLElement'){
				try {
					$xml = new SimpleXMLElement($xml);
				} catch (Exception $e) {
				
				}
			}
			if(!empty($xml)){
				$data = array();
				/*foreach($this->settings[$model->alias]['linkedFields'] as $key => $val){
					$data[$key] = (string)$xml[$val];
				}*/
				
				$data = $this->simpleXmlToArray($xml);
				//debug($data);
				
				
				if(empty($data['id']) || $data['id'] <= 0){
					$data['id'] = null;
				}
				return $data;
			}
			return null;
		}
		
		function simpleXmlToArray($xml){
			$data = array();
			foreach($xml->attributes() as $a => $b) {
				$data[$a] = (string)$b;
			}
			foreach($xml->children() as $a => $b){
				$data[Inflector::pluralize($b->getName())][] = $this->simpleXmlToArray($b);
			}
			return $data;
		}
		
		function linkRead(&$model, $findOptions = null){
			$finalFindOptions = $this->linkReadFindOptions($model,$findOptions);
			//debug($finalFindOptions);
			$options = array_intersect_key($finalFindOptions, array_flip($this->internalFindOpt));
			//App::import('Lib', 'SetMulti');
			//$finalFindOptions = SetMulti::excludeKeys($finalFindOptions,$this->internalFindOpt,true);
			$finalFindOptions = array_diff_key($finalFindOptions,array_flip($this->internalFindOpt));
			if(empty($finalFindOptions)){
				return false;
			}
			$model->recursive = -1;
			if(isset($finalFindOptions['contain']) && !$model->Behaviors->attached('Containable')){
			    $model->Behaviors->attach('Containable');
			}
			//debug($finalFindOptions);
			$res = $model->find('all',$finalFindOptions);
			$res = $this->_dataToLinkData($model,$res,$options);
			$res = $this->_afterLinkRead($model,$res,$options);
			
			//debug($res);
			return $res;
		}
		
		function dataToLinkData(&$model, $data, $options = array()){
			if(!Set::numeric(array_keys($data))){
				$data = array($data);
			}
			$opt = $this->linkReadFindOptions($model,$options);
			$data = $this->_dataToLinkData($model, $data, $opt);
			$data = $this->_restrictData($model, $data, $opt);
			return $data;
		}
		
		function _restrictData(&$model, $data, $options = array()){
			$opt = $this->linkReadFindOptions($model,$options);
			$intersect = array_merge($opt['fields'],array('items','internal'));
			$tag = $opt['tagName'];
			foreach($data[$tag] as $key => $r){
				debug($data);
				if($key !== 'internal'){
					$data[$tag][$key] = array_intersect_key($r,array_flip($intersect));
					if(!empty($r['items'])){
						foreach($r['items'] as $itag => $items){
							$iModelName = $items['internal']['alias'];
							if(!empty($model->{$iModelName})){
								$iModel = $model->{$iModelName};
							}else{
								$iModel = ClassRegistry::init($iModelName);
							}
							
							$r['items'] = $this->_restrictData($iModel, $data);
						}
					}
				}
			}
			//debug(data);
			return $data;
		}
		
		function _dataToLinkData(&$model, $data, $options = array()){
			$defOpt = array(
				'internal'=>array('contain'=>array()),
			);
			$opt = array_merge($defOpt,$options);
			if(empty($data)){
				return $data;
			}
			$final = array();
			$tag = $opt['tagName'];
			foreach($data as $r){
				if(isset($r[$model->alias])){
					$join = $r;
					unset($join[$model->alias]);
					$me = $r[$model->alias];
				}else{
					$me = $r; 
					$join = array_filter($me,'is_array');
				}
				foreach($me as $key => $val){
					$type = $model->getColumnType($key);
					if (in_array($type, array('datetime', 'timestamp', 'date', 'time'))) {
						$me[$key] = strtotime($val);
					}
				}
				if(!empty($options['mapping'])){
					$r = array_merge($me,$join);
					$r = $this->_mapFields($r,$options['mapping']);
					//debug($r);
					$me = array_diff_key($r,$join);
					$join = array_intersect_key($r,$join);
				}
				$me['items'] = $this->_extractSubItems($model, $join, $options);
				//if($me['id'] == 3) debug($me);
				$final[$tag][] = $me;
			}
			$final[$tag] = $this->_extractInternal($model, $final[$tag], $opt);
			$final[$tag]['internal']['alias'] = $model->alias;
			//debug($final);
			return $final;
		}
		
		function _afterLinkRead($model,$results, $options = array()){
			$tag = $options['tagName'];
			if(!empty($results[$tag])){
				foreach($results[$tag] as $key => $r){
					if($key !== 'internal' && !empty($r['items']) && !empty($options['sublinked'])){
						foreach($options['sublinked'] as $iModelName => $subOpt){
							if(!empty($r['items'][$iModelName])){
								if(!empty($model->{$iModelName})){
									$iModel = $model->{$iModelName};
								}else{
									$iModel = ClassRegistry::init($iModelName);
								}
								$results[$tag][$key]['items'] = $this->_afterLinkRead($iModel, $r['items'], $subOpt);
							}
						}
					}
				}
			}
			if($this->_hasMethod($model,'afterLinkRead')){
				$cRes = $model->afterLinkRead($results);
				if(!empty($cRes) && is_array($cRes)){
					$results = $cRes;
				}
			}
			return $results;
		}
		
		function _mapFields($data,$map){
			foreach($map as $key => $val){
				if(is_array($val)){
					if(empty($data[$key])){
						$data[$key] = array();
					}
					$data[$key] = $this->_mapFields((array)$data[$key],$val);
				}else{
					$data[$key] = Set::extract($val, $data);
				}
			}
			return $data;
		}
		
		function _extractInternal(&$model, $data, $opt = array()){
			$outputDefineOpt = $this->settings[$model->alias]['outputDefine'];
			$final = array();
			if(!empty($opt['internal'])){
				foreach($data as $r){
					$o_r = $r;
					//////////// Basic internals ////////////
					foreach($r as $key => $val){
						if(in_array($key,$opt['internal'])){
							$r['internal'][$key] = $r[$key];
							unset($r[$key]);
						}
					}
					
					//////////// Internals on relations ////////////
					if(!empty($r['items'])){
						foreach($tmp = $r['items'] as $key => $val){
							if(!empty($opt['internal']['contain'])){
								if(array_key_exists($key,$opt['internal']['contain']) || in_array($key,$opt['internal']['contain'],true)){
									$r['internal'][$key] = $val;
									unset($r['items'][$key]);
								}
							}
							if(array_key_exists($key,$opt['internal'])){
								$subOpt = $opt;
								$subOpt['internal'] = $opt['internal'][$key];
								$r['items'][$key] = $this->_extractInternal($model,$val,$subOpt);
							}
						}
					}
					
					//////////// Exceptions for output Definition ////////////
					if($outputDefineOpt && !empty($outputDefineOpt['field']) && !empty($o_r[$outputDefineOpt['field']])){
						$definition = (array)$o_r[$outputDefineOpt['field']];
						$r_data = $o_r;
						if(!empty($outputDefineOpt['exclude'])){
							$r_data = array_diff_key($r_data,array_flip($outputDefineOpt['exclude']));
						}
						foreach($definition as $key => $val){
							if(is_numeric($key)){
								$key = $val;
							}
							$r[$key]=Set::extract($val,$r_data);
						}
					}
					
					
					$final[] = $r;
				}
			}else{
				$final = $data;
			}
			return $final;
		}
		
		function _extractSubItems(&$model, $items, $options = array()){
			if(Set::numeric(array_keys($items))){
				$ordered_items = array();
				foreach($items as $items_un){
					foreach($items_un as $modelName=>$m_items){
						if(!Set::numeric(array_keys($m_items))){
							$m_items = array($m_items);
						}
						foreach($m_items as $item){
							$ordered_items[$modelName][] = $item;
						}
					}
				}
				$items = $ordered_items;
			}
			$items_final = array();
			//debug($items);
			foreach($items as $modelName=>$m_items){
				$items2 = array();
				if(!Set::numeric(array_keys($m_items))){
					if(!count(array_filter($m_items))){
						continue;
					}
					$m_items = array($m_items);
				}
				if(isset($options['sublinked'][$modelName])){
					if(!empty($model->{$modelName})){
						$cModel = $model->{$modelName};
					}else{
						$cModel = ClassRegistry::init($modelName);
					}
					//debug($modelName);
					$items_final[$modelName] = $this->_dataToLinkData($cModel,$m_items,$options['sublinked'][$modelName]);
					$items_final[$modelName] = $items_final[$modelName][$modelName];
				}else{
					foreach($m_items as $item){
						$item2 = array();
						foreach($item as $key=>$val){
							if($this->_isItem($val,$key)){
								$item2['items'][$key] = $val;
							}else{
								$item2[$key] = $val;
							}
						}
						if(!empty($item2['items'])){
							$item2['items'] = $this->_extractSubItems($item2['items']);
						}
						$items2[] = $item2;
					}
					$items_final[$modelName] = $items2;
				}
			}
			return $items_final;
		}
		
		function _isItem($data,$key = null){
			return is_array($data)
					&& (!empty($data['id']) || !empty($data[0]['id']))
					&& (
						is_null($key) 
						|| (
							!is_numeric($key) 
							&& ucfirst($key) == $key
						)
					);
		}
		
		function _hasMethod(&$model,$action){
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
		
		function linkReadFindOptions(&$model, $findOptions = null, $containMode = false){
			//////////// Normalize options ////////////
			if(is_null($findOptions)){
				if(!$containMode && !empty($model->id)){
					$findOptions = array('conditions'=>array($model->alias.'.id'=>$model->id));
				}else{
					$findOptions = array();
				}
			}else{
				$findOptions = (array)$findOptions;
				if(Set::numeric(array_keys($findOptions))){
					if(Set::numeric($findOptions)){
						$findOptions = array('conditions'=>array($model->alias.'.id'=>$findOptions));
					}else{
						$findOptions = array(
							'fields'=>$findOptions
						);
					}
				}else{
					$toArray = array('fields','conditions','contain');
					foreach($toArray as $val){
						if(!empty($findOptions[$val]) && !is_array($findOptions[$val]) ){
							$findOptions[$val] = array($findOptions[$val]);
						}
					}
				}
			}
			$def = $this->settings[$model->alias]['find'];
			$def['tagName'] = $model->tagName();
			$findOptions = Set::merge($def,$findOptions);
			if(!empty($findOptions['restrict'])){
				$findOptions['fields'] = array_diff($findOptions['fields'],(array)$findOptions['restrict']);
			}
			if(empty($findOptions['fields'])){
				return false;
			}
			if(!empty($findOptions['contain'])){
				$findOptions['contain'] = Set::normalize($findOptions['contain']);
			}
			
			$defaultInternal = Configure::read('xmlLink.defaultInternal');
			if(!empty($defaultInternal)){
				if(empty($findOptions['internal'])) $findOptions['internal'] = array();
				$findOptions['internal'] = Set::merge((array)$defaultInternal,(array)$findOptions['internal']);
			}
			
			//////////// Set mapping between bd and xml ////////////
			$fields = array();
			$mapping = array();
			foreach(Set::normalize($findOptions['fields']) as $key => $val){
				if(empty($val)){
					$fields[] = $key;
				}elseif(strpos($val,'.') !== false){
					$parts = explode('.',$val);
					$last = array_pop($parts);
					$cmodel = $model;
					$defUpTo = 0;
					if(!empty($findOptions['contain']) ) {
						while ($defUpTo+1<count($parts) && Set::check($findOptions['contain'], implode('.',array_slice($parts,0,$defUpTo+1)))) {
							$defUpTo++;
							//debug(implode('.',array_slice($parts,0,$defUpTo+1)));
						}
					}
					$fieldPath = 'contain.'.implode('.',$parts).'.fields';
					$addFields = array();
					if(Set::check($findOptions,$fieldPath)){
						$addFields = Set::extract($fieldPath,$findOptions);
					}
					$addFields[] = $last;
					if($defUpTo > 0){
						$internalPath = 'internal.'.implode('.',array_slice($parts,0,$defUpTo));
						$findOptions = Set::insert($findOptions,$internalPath, $parts[$defUpTo]);
					}else{
						$findOptions = Set::insert($findOptions,'internal.'.$fieldPath,$addFields);
					}
					$findOptions = Set::insert($findOptions,'contain.'.implode('.',$parts).'.fields',$addFields);
					$mapping[$key] = $val;
				}else{
					$fields[] = $val;
					$findOptions['internal'][] = $val;
					$mapping[$key] = $val;
				}
			}
			$findOptions['mapping'] = $mapping;
			
			
			//////////// Setup for output Definition field ////////////
			$outputDefineOpt = $this->settings[$model->alias]['outputDefine'];
			if($outputDefineOpt && !empty($outputDefineOpt['field']) && $model->hasField($outputDefineOpt['field'])){
				if(empty($outputDefineOpt['include'])){
					$needed = array_keys($model->schema());
				}else{
					$needed = $outputDefineOpt['include'];
				}
				$needed += array($outputDefineOpt['field']);
				if(!empty($outputDefineOpt['exclude'])){
					$needed = array_diff($needed,$outputDefineOpt['exclude']);
				}
				$needed = array_diff($needed,$fields);
				$needed = array_diff($needed,$findOptions['internal']);
				$findOptions['internal'] = array_merge($findOptions['internal'],$needed);
			}
			
			
			//////////// Set contains ////////////
			$fields += array('id');
			if(!empty($findOptions['internal'])){
				$ifields = $findOptions['internal'];
				unset($ifields['contain']);
				$fields = array_merge($fields,$ifields);
			}
			if(!empty($findOptions['internal']['contain'])){
				$findOptions['internal']['contain'] = Set::normalize($findOptions['internal']['contain']);
				foreach($findOptions['internal']['contain'] as $modelName => $opt){
					if(!empty($model->{$modelName}) && (empty($findOptions['contain']) || (empty($findOptions['contain'][$modelName]) && !in_array($modelName,$findOptions['contain'])))){
						$findOptions['contain'][$modelName] = $opt;
					}
				}
			}
			$findOptions['fields'] = $fields;
			if(!empty($findOptions['contain'])){
				$contain = $findOptions['contain'];
				$finalContain = array();
				foreach((array)$findOptions['contain'] as $modelName => $opt){
					if(is_numeric($modelName)){
						$modelName = $opt;
						$opt = null;
					}
					if(isset($model->$modelName)){
						$cModel = $model->$modelName;
					}else{
						$cModel = ClassRegistry::init($modelName);
					}
					if($cModel->Behaviors->attached('XmlLinked')){
						$subOpt = $cModel->linkReadFindOptions($opt,true);
						if(!empty($subOpt)){
							$findOptions['sublinked'][$cModel->alias] = $subOpt;
							/*if(!empty($subOpt['internal'])){
								$findOptions['internal'][$cModel->alias] = $subOpt['internal'];
								unset($subOpt['internal']);
							}*/		
							$subOpt = array_diff_key($subOpt, array_flip($this->internalFindOpt));
							if(!empty($subOpt['contain'])){
								$subOpt = array_merge($subOpt,$subOpt['contain']);
								unset($subOpt['contain']);
							}
						}
					}else{
						$subOpt = (array)$opt;
						if(empty($subOpt['fields'])){
							$subOpt['fields'] = array_keys($cModel->schema());
						}
						$findOptions['internal']['contain'][$modelName] = $subOpt;
					}
					if(!empty($subOpt)){
						if(!in_array('id', $subOpt['fields']) && !in_array($modelName.'.id', $subOpt['fields'])){
							$subOpt['fields'][] = $modelName.'.id';
						}
						$finalContain[$modelName] = $subOpt;
					}
				}
				$findOptions['contain'] = $finalContain;
			}
			//debug($findOptions);
			return $findOptions;
		}
		
		/*function _parseLinkedFieldsOpt(){
			$res = array();
			foreach((array)func_get_args() as $fields){
				foreach((array)$fields as $key => $val){
					if(!empty($val)){
						if(is_numeric($key)){
							$res[$val] = $val;
						}else{
							$res[$key] = $val;
						}
					}
				}
			}
			return $res;
		}*/
	}
?>