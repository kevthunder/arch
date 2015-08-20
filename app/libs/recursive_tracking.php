<?php
class RecursiveTracking extends Object {

	var $stack;
	
	function track($data){
		$this->stack[]=$data;
	}
	
	function setData($data){
		if(!empty($this->stack)){
			array_pop($this->stack);
			$this->stack[]=$data;
		}
	}
	
	function getData(){
		return end($this->stack);
	}
	
	function endTrack(){
		if(!empty($this->stack)){
			return array_pop($this->stack);
		}
	}
}
?>