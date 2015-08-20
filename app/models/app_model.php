<?php
class AppModel extends Model {

	var $lastAssociations;
	var $actsAs = array('Cache');
	
	
	function beforeFind($queryData){
		$results = parent::beforeFind($queryData);
		
		$this->lastAssociations = array('belongsTo'=>$this->belongsTo,'hasMany'=>$this->hasMany,'hasOne'=>$this->hasOne,'hasAndBelongsToMany'=>$this->hasAndBelongsToMany);
		
		return $results;
	}
	
	function afterFind($results,$primary){
		$results = parent::afterFind($results,$primary);
		if(!$primary){
			//debug('~'.$this->alias);
			$return = $this->behaviorsTrigger($this, 'assocAfterFind', array($results, $primary), array('modParams' => true));
			//debug($return);
			
			if ($return !== true) {
				$results = $return;
			}
		}else{
			//debug($this->alias);
		}
		
			
		return $results;
	}
	
	
	
	function behaviorsTrigger(&$model, $callback, $params = array(), $options = array()) {
		
		if (empty($this->Behaviors->_attached)) {
			return true;
		}
		$options = array_merge(array('break' => false, 'breakOn' => array(null, false), 'modParams' => false), $options);
		$count = count($this->Behaviors->_attached);

		for ($i = 0; $i < $count; $i++) {
			$name = $this->Behaviors->_attached[$i];
			if (in_array($name, $this->Behaviors->_disabled)) {
				continue;
			}
			if(method_exists($this->Behaviors->{$name},$callback)){
				$result = $this->Behaviors->{$name}->dispatchMethod($model, $callback, $params);
			}else{
				$result = false;
			}

			if ($options['break'] && ($result === $options['breakOn'] || (is_array($options['breakOn']) && in_array($result, $options['breakOn'], true)))) {
				return $result;
			} elseif ($options['modParams'] && is_array($result)) {
				$params[0] = $result;
			}
		}
		if ($options['modParams'] && isset($params[0])) {
			return $params[0];
		}
		return true;
	}
	
	///////////////////// query tracking /////////////////////
	static $queryTrack;
	static $findId;
	function _uniqueFindId() {
		$val = (int)self::$findId;
		self::$findId++;
		return $val;
	}
	function _trackQuery($type){
		if(empty(self::$queryTrack)){
			App::import('Lib', 'RecursiveTracking');
			self::$queryTrack = new RecursiveTracking();
		}
		self::$queryTrack->track(array('model'=>$this->alias,'id'=>$this->_uniqueFindId(),'type'=>$type));
	}
	function _endQueryTrack(){
		self::$queryTrack->endTrack();
	}
	function getQueryRef(){
		return self::$queryTrack->getData();
	}
	
	function find($conditions = null, $fields = array(), $order = null, $recursive = null) {
		$this->_trackQuery('find');
		$res = parent::find($conditions,$fields,$order,$recursive);
		$this->_endQueryTrack();
		return $res;
	}
}
?>