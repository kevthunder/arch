<?php

class LastCallbacksBehavior extends ModelBehavior {
	function _tcheckPos($model){
		$attached = $model->Behaviors->attached();
		if(in_array('LastCallbacks',$attached) && $attached[count($attached)-1]!='LastCallbacks'){
			$model->Behaviors->detach("LastCallbacks");
			$model->Behaviors->attach("LastCallbacks");
			return false;
		}
		return true;
	}
	
	function _dispatchLast($model, $callback, $params = array ( ), $options = array ( )){
		array_shift($params);
		if($this->_tcheckPos($model)){
			$callback = 'last'.ucfirst($callback);
			if (empty($model->Behaviors->_attached)) {
				return true;
			}
			$options = array_merge(array('break' => false, 'breakOn' => array(null, false), 'modParams' => false), $options);
			$count = count($model->Behaviors->_attached);

			for ($i = 0; $i < $count; $i++) {
				$name = $model->Behaviors->_attached[$i];
				if (in_array($name, $model->Behaviors->_disabled)) {
					continue;
				}
				if(!method_exists($model->Behaviors->{$name},$callback)){
					continue;
				}
				$result = $model->Behaviors->{$name}->dispatchMethod($model, $callback, $params);
				

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
		return true;
	}
	
	function beforeValidate($model){
		return $this->_dispatchLast($model,'beforeValidate', func_get_args());
	}
    function beforeFind($model){
		return $this->_dispatchLast($model,'beforeFind', func_get_args());
	}
    function afterFind($model){
		return $this->_dispatchLast($model,'afterFind', func_get_args());
	}
    function beforeSave($model){
		return $this->_dispatchLast($model,'beforeSave', func_get_args());
	}
    function afterSave($model){
		return $this->_dispatchLast($model,'afterSave', func_get_args());
	}
    function beforeDelete($model){
		return $this->_dispatchLast($model,'beforeDelete', func_get_args());
	}
    function afterDelete($model){
		return $this->_dispatchLast($model,'afterDelete', func_get_args());
	}

}
