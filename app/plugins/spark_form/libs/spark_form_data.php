<?php
class SparkFormData extends Object {
	//App::import('Lib', 'SparkForm.SparkFormData'); 
	function specialValues() {
		return array(
			'true' => true,
			'false' => false,
			'null' => null,
			'undefined' => null,
		);
	}
	
	function specialDeconstruct($data){
		if(is_array($data)){
			$specialValues = SparkFormData::specialValues();
			foreach($tmp = $data as $key => $val){
				if(preg_match('/^(.*)_spc$/',$key,$matches)){
					if($val == 'undefined'){
						unset($data[$matches[1]]);
					}elseif(array_key_exists($val,$specialValues)){
						$data[$matches[1]] = $specialValues[$val];
					}
					unset($data[$key]);
				}
			}
		}
		return $data;
	}
	
	
}
?>