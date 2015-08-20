<?php
class Invalidation extends AppModel {
	var $name = 'Invalidation';
	var $displayField = 'modified';
	
	var $timeout = '-2 days';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Node' => array(
			'className' => 'Node',
			'foreignKey' => 'node_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	function cleanOld(){
		App::import('Lib', 'TimeUtil'); 
		$this->recursive = -1;
		$minTime = TimeUtil::realToRelTime(strtotime($this->timeout));
		$this->deleteAll(array('time < ' => $minTime));
	}
	
	/*function beforeSave($options){
		if(isset($this->data[$this->alias])){
			$data = &$this->data[$this->alias];
		}else{
			$data = &$this->data;
		}
		if(empty($this->id) && empty($data['id'])){
			$this->recursive = -1;
			$minTime = strtotime($this->timeout);
			$this->deleteAll(array('modified < ' => $minTime));
			if(empty($data['node_id'])){
				return false;
			}
			$same = $this->find('first',array('conditions'=>array('node_id ' => $data['node_id'])));
			if(!empty($same)){
				$same = $same[$this->alias];
				debug($same);
				$this->id = $data['id'] = $same['id'];
				if(empty($data['fields']) || empty($same['fields'])){
					$data['fields'] = null;
				}else{
					$data['fields'] = array_flip(array_flip(array_merge((array)$data['fields'],$same['fields'])));
				}
				$this->save($data);
				
				$this->data = array();//will still return true but wont insert anything (Cakephp 1.3.9)
				//return false;
			}
		}
		
		if(isset($data['fields'])){
			$data['fields'] = implode(',',(array)$data['fields']);
		}
		
		return true;
	}*/
	
	/*function afterFind($results, $primary){
		foreach($results as &$res){
			if(isset($res[$this->alias])){
				$data = &$res[$this->alias];
			}else{
				$data = &$res;
			}
			
			if(isset($data['fields']) && !is_null($data['fields']) && !is_array($data['fields'])){
				$data['fields'] = explode(',',$data['fields']);
			}
		}
		return $results;
	}*/
}
