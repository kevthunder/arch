<?php
class UtilBehavior extends ModelBehavior {
	//$model->Behaviors->attach('Util');
	
	function unifiedResultRef(&$model, &$results, $alias = null){ //Deprecated
		if(is_null($alias)){
			$alias = $model->alias;
		}
		if(!Set::numeric(array_keys($results))){
			if(isset($results[$alias])){
				$uResults = array(&$results);
			}else{
				$uResults = array($alias=>&$results);
			}
		}else{
			$uResults = array();
			foreach($results as $key => &$val){
				$uResults[$key] = &$val;
			}
		}
		return $uResults;
	}
	
	// Simple data formating :
	//				$model->Behaviors->attach('Util');
	//				$data = $model->unifiedResult($data);
	// data formating and revert :  
	//				$model->Behaviors->attach('Util');
	//				list($data, $oldFormat) = $model->unifiedResult($data, null, null, true);
	//				...
	//				$data = $model->unifiedResult($data, null, $oldFormat);
	function unifiedResult($model, $results, $alias = null, $format = array(), $retrunOldFormat = false){
		if(empty($results)){
			return $results;
		}
		$defFormat = array(
			'multiple' => true,
			'named' => true,
			'idOnly' => false,
		);
		$format = array_merge($defFormat,(array)$format);
		if(is_null($alias)){
			$alias = $model->alias;
		}
		$oldFormat = $this->getResultFormat($model,$results,$alias);
		if(!$oldFormat['multiple']){
			$results = array($results);
		}
		$uResults = array();
		foreach($results as $key => &$val){
			if(!$oldFormat['named']){
				$val = array($alias=>$val);
			}
			if($oldFormat['idOnly']){
				$val[$alias] = array('id'=>$val[$alias]);
			}
			// here all the formatings are applied
			if($format['idOnly']){
				$val[$alias] = $val[$alias]['id'];
			}
			if(!$format['named']){
				$val = $val[$alias];
			}
			$uResults[$key] = $val;
		}
		if(!$format['multiple']){
			$uResults = $uResults[0];
		}
		if($retrunOldFormat){
			return array($uResults,$oldFormat);
		}
		return $uResults;
	}
	function getResultFormat($model,&$results,$alias = null){
		if(is_null($alias)){
			$alias = $model->alias;
		}
		$oldFormat = array();
		$oldFormat['multiple'] = is_array($results) && Set::numeric(array_keys($results));
		if(!$oldFormat['multiple']){
			$val = $results;
		}else{
			$val = reset($results);
		}
		$oldFormat['named'] = !empty($val) && (is_array($val) && isset($val[$alias]) || (!empty($model->{key($val)}) && $model->{key($val)} instanceof Model));
		if(!$oldFormat['named']){
			$val = array($alias=>$val);
		}
		$oldFormat['idOnly'] = !empty($val[$alias]) && !is_array($val[$alias]) && is_numeric($val[$alias]);
		return $oldFormat;
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
	
	function bulkModify(&$model, $modif, $returnAdded = false){
		$addedKeys = array();
		if(!count(array_intersect(array_keys($modif),array('save','delete')))){
			$modif = array('save'=>$modif);
		}
		//////// save modif and added ////////
		if(!empty($modif['save'])){
			foreach($modif['save'] as $key => $save){
				$model->create();
				if(!$model->save($save)){
					return false;
				}else{
					if($returnAdded && empty($save['id']) && empty($save[$model->alias]['id'])){
						$addedKeys[$key] = $model->id;
					}
				}
			}
		}
		//////// delete ////////
		if(!empty($modif['delete'])){
			$deleteIds = $model->extractIds($modif['delete']);
			$model->deleteAll(array($model->alias.'.id'=>$deleteIds));
		}
		if($returnAdded){
			return $addedKeys;
		}else{
			return true;
		}
	}
	
	function getMinimalData(&$model, $fields, $data = null, $findOptions = array(), $priorityRes = true){
		if(is_null($data)){
			$data = $model->data;
		}
		//debug($data);
		
		//////// get fields and contain ////////
		$contain = array();
		$findFields = array();
		foreach($fields as &$field){
			if(substr($field,0,strlen($model->alias)+1) == $model->alias.'.'){
				$findFields[] = $field;
				$field = $model->alias.'.'.$field;
			}elseif( ($pos = strrpos($field,'.')) == false){
				$findFields[] = $model->alias.'.'.$field;
			}else{
				$path = substr($field,0,$pos);
				$depth = count(explode('.',$path));
				$f = substr($field,$pos+1);
				if($depth>1){
					$contain = Set::insert($contain,$path.'.fields',array('id',$f));
				}else{
					$contain = Set::insert($contain,$path,array());
					if(!in_array($path .'.id',$findFields)){
						$findFields[] = $path .'.id';
					}
					$findFields[] = $field;
				}
			}
		}
		if(!in_array($model->alias .'.id',$findFields)){
			$findFields[] = $model->alias .'.id';
		}
		
		//////// normalize Data ////////
		list($data, $oldFormat) = $model->unifiedResult($data, null, null, true);
		App::import('Lib', 'SetMulti');
		$data = SetMulti::group($data,$model->alias .'.id',array('singleArray'=>false));
		//debug($data);
		
		//////// get ids ////////
		$ids = array(); 
		foreach($data as $entry){
			foreach($fields as $field){
				if(!Set::check ($entry, $field)){
					$ids[] = $entry[$model->alias]['id'];
					continue 2;
				}
			}
		}
		
		//////// query ////////
		$findOpt = array(
			'fields' => $findFields,
			'conditions'=>array($model->alias.'.id'=>$ids),
			'contain' => $contain
		);
		$findOpt = array_merge($findOpt, $findOptions);
		//debug($findOpt);
		$completion = $model->find('all',$findOpt);
		//debug($completion);
		
		//////// merge data ////////
		foreach($completion as $c){
			$id = $c[$model->alias]['id'];
			if($priorityRes){
				$data[$id] = Set::merge($data[$id],$c);
			}else{
				$data[$id] = Set::merge($c,$data[$id]);
			}
		}
		$data = array_values($data);
		$data = $this->unifiedResult($model,$data,null,$oldFormat);
		//debug($data);
		return $data;
	}
	
	function getOriginalData(&$model, $fields, $options = array()){
		$defOpt = array(
			'fields' => true,
			'id' => null,
			'data' => null,
			'singleField' => false,
		);
		if(!is_array($options) && is_numeric($options)){
			$options = array('id'=>$options);
		}
		if(is_numeric($fields)){
			$options = array('id'=>$fields);
		}elseif(is_array($fields) && array_key_exists('fields',$fields)){
			$options = $fields;
		}elseif(is_array($fields)){
			$options['fields'] = $fields;
		}else{
			$options['fields'] = array($fields);
			$options['singleField'] = true;
		}
		
		$opt = Set::merge($defOpt,$options);
		if(empty($opt['data'])){
			$opt['data'] = $model->data;
			if(empty($opt['id'])){
				$opt['id'] = $model->id;
			}
		}
		if(!empty($opt['data'][$model->alias]['originalData']) && (empty($opt['id']) || ( !empty($opt['data'][$model->alias]['originalData']['id']) && $opt['data'][$model->alias]['originalData']['id'] == $opt['id'] ) ) ){
			$originalData = $opt['data'][$model->alias]['originalData'];
		}else{
			$originalData = array();
		}
		$originalData['id'] = $opt['id'];
		$originalData = $this->getMinimalData($model,$opt['fields'], $originalData);
		if($opt['singleField']){
			return $originalData[$opt['fields'][0]];
		}else{
			return $originalData;
		}
	}
	
	
	function treeJointOpt(&$model, $parents = true, $options = null){
		$defOpt = array(
			'alias'=>$model->alias,
			'table'=>$model->useTable,
			'type'=>'inner',
			'parentAlias'=>($parents?'Parent':'Child').$model->alias,
			'conditions'=>array(),
		);
		$opt = Set::merge($defOpt,$options);
		return array(
			'type'=>$opt['type'],
			'table'=>$opt['table'],
			'alias' => $opt['parentAlias'],
			'conditions' => array_merge($opt['conditions'],array(
				$opt['alias'].'.lft '.($parents?'>=':'<=').' '.$opt['parentAlias'].'.lft',
				$opt['alias'].'.rght '.($parents?'<=':'>=').' '.$opt['parentAlias'].'.rght'
			))
		);
	}
	
	function addAliasToCond(&$model, $cond, $alias = null){
		if(is_null($alias)){
			$alias = $model->alias;
		}
		$newCond = array();
		$keyWords = array('or','not');
		$pattern = "/^[a-z]+(?!\.)/";
		foreach($cond as $key => $val){
			if(!is_numeric($key)){
				if(preg_match($pattern, $key, $matches,PREG_OFFSET_CAPTURE)){
					$surplus = 0;
					foreach($matches as $match){
						if(!in_array($match[0],$keyWords)){
							$key = substr_replace($key, $alias.'.', $match[1]+$surplus, 0);
							$surplus += strlen($alias.'.');
						}
					}
				}
			}elseif(!is_array($val)){
				if(preg_match($pattern, $val, $matches,PREG_OFFSET_CAPTURE)){
					$surplus = 0;
					foreach($matches as $match){
						if(!in_array($match[0],$keyWords)){
							$val = substr_replace($val, $alias.'.', $match[1]+$surplus, 0);
							$surplus += strlen($alias.'.');
						}
					}
				}
			}
			if(is_array($val)){
				$newCond[$key] = $this->addAliasToCond($model, $val, $alias);
			}else{
				$newCond[$key] = $val;
			}
		}
		return $newCond;
	}
	function extractIds(&$model, $arr, $alias = null){
		if(is_null($alias)){
			$alias = $model->alias;
		}
		$ids = array();
		if(!empty($arr)){
			foreach((array)$arr as $item){
				if(is_array($item)){
					if(isset($item['id'])){
						$ids[] = $item['id'];
					}elseif(isset($item[$alias]['id'])){
						$ids[] = $item[$alias]['id'];
					}
				}else{
					$ids[] = $item;
				}
			}
		}
		return $ids;
	}
	
	function dataToContainedRelations(&$model, $data = null, $alias = null){
		if(is_null($alias)){
			$alias = $model->alias;
		}
		if(is_null($data)){
			$data = $model->data;
		}
		App::import('Lib', 'SetMulti');
		if(is_array($data) && !SetMulti::isAssoc($data)){
			$res = array();
			foreach($data as $d){
				$res[$alias][] = $model->dataToContainedRelations($d,$alias);
			}
			return $res;
		}
		if(isset($data[$alias])){
			$relation = $data;
			unset($relation[$alias]);
			return array_merge($relation,$data[$alias]);
		}
		return $data;
	}
	
	function testApplicableCond(&$model, $cond, $data = null, $alias = null, &$unfiltered = null){
		if(is_null($alias)){
			$alias = $model->alias;
		}
		if(is_null($data)){
			$data = $model->data;
		}
		$filtered = $data;
		$unfiltered = $cond;
		//$cond['lol']=3;
		App::import('Lib', 'Operations');
		foreach($cond as $condkey => $condval){
			$find = false;
			$matches = array();
			//debug($key);
			if(is_numeric($condkey)){
			}elseif(preg_match('/^((?:([A-Z][A-Za-z0-9_-]*).)?[A-Za-z0-9_-]*)\s*(.*)\s*$/',$condkey, $matches)){
				//debug($matches);
				list($tmp, $path, $model, $operator) = $matches;
				if(empty($model)){
					$path = $alias.'.'.$path;
				}
				$ex = Set::extract('{n}.'.$path,$filtered);
				$ex = array_combine(array_keys($filtered),$ex);
				//debug($condval);
				//debug($ex);
				foreach($ex as $key => $val){
					if(!is_null($val)){
						$res = false;
						if(empty($operator)){
							$res = $val == $condval;
						}else{
							$res = Operations::simpleOperation($val,$operator,$condval);
						}
						if($res === false){
							unset($filtered[$key]);
						}
						if(is_bool($res)){
							$find = true;
						}
					}
				}
			}
			if($find){
				unset($unfiltered[$condkey]);
			}
		}
		return $filtered;
	}
}
?>