<?php
class Operations extends Object {

	// App::import('Lib', 'Operations');
	

	var $opDefOpt = array(
		'function' => true,
		'named' => array(
			0 => array('subject'),
			1 => array('val')
		),
		'type' => null,
		'sepPattern' => '\h*',
		'dataPattern' => '.+',
		'patternSingle' => '/^{op}$/',
		'patternLeft' => '/^(?P<subject>{data}){sep}{op}$/',
		'patternRight' => '/^{op}{sep}(?P<val>{data})$/',
		'patternFull' => '/^(?P<subject>{data}){sep}{op}{sep}(?P<val>{data})$/',
	);
	var $operators = array(
		'add' => array(
			'name' => 'add',
			'type' => 'numeric',
			'opPattern' => '\+',
			'dataPattern' => '[0-9]*,?[0-9]+',
			'alias' => array('+=','+')
		),
		'substract' => array(
			'name' => 'substract',
			'type' => 'numeric',
			'opPattern' => '--?',
			'strictPattern' => '--',
			'dataPattern' => '[0-9]*,?[0-9]+',
			'alias' => array('-=','-')
		),
		'addPrc' => array(
			'name' => 'addPrc',
			'type' => 'numeric',
			'opPattern' => '\+',
			'dataPattern' => '[0-9]*,?[0-9]+',
			'patternSingle' => '/^{op}%$/',
			'patternLeft' => '/^(?P<subject>{data}){sep}{op}%$/',
			'patternRight' => '/^{op}{sep}(?P<val>{data})%$/',
			'patternFull' => '/^(?P<subject>{data}){sep}{op}{sep}(?P<val>{data})%$/',
			'alias' => array('+%')
		),
		'substractPrc' => array(
			'name' => 'substractPrc',
			'type' => 'numeric',
			'opPattern' => '--?',
			'dataPattern' => '[0-9]*,?[0-9]+',
			'patternSingle' => '/^{op}%$/',
			'patternLeft' => '/^(?P<subject>{data}){sep}{op}%$/',
			'patternRight' => '/^{op}{sep}(?P<val>{data})%$/',
			'patternFull' => '/^(?P<subject>{data}){sep}{op}{sep}(?P<val>{data})%$/',
			'alias' => array('-%')
		),
		'multiply' => array(
			'name' => 'multiply',
			'type' => 'numeric',
			'opPattern' => '\*',
			'dataPattern' => '[0-9]*,?[0-9]+',
			'alias' => array('*=','*')
		),
		'different' => array(
			'name' => 'different',
			'type' => 'bool',
			'opPattern' => '(!=|<>)',
			'alias' => array('!=','diff','<>')
		),
		'differentAbs' => array(
			'name' => 'differentAbs',
			'type' => 'bool',
			'opPattern' => '!==',
			'alias' => array('!==')
		),
		'greaterEq' => array(
			'name' => 'greaterEq',
			'type' => 'bool',
			'opPattern' => '>=',
			'alias' => array('>=','gte')
		),
		'greater' => array(
			'name' => 'greater',
			'type' => 'bool',
			'opPattern' => '>',
			'alias' => array('>','gt')
		),
		'lesserEq' => array(
			'name' => 'lesserEq',
			'type' => 'bool',
			'opPattern' => '<=',
			'alias' => array('<=','lte')
		),
		'lesser' => array(
			'name' => 'lesser',
			'type' => 'bool',
			'opPattern' => '<',
			'alias' => array('<','lt')
		),
		'equalAbs' => array(
			'name' => 'equalAbs',
			'type' => 'bool',
			'opPattern' => '===',
			'alias' => array('===')
		),
		'equal' => array(
			'name' => 'equal',
			'type' => 'bool',
			'opPattern' => '==?',
			'alias' => array('=','eq','==')
		),
		'concat' => array(
			'name' => 'concat',
			'type' => 'string',
			'opPattern' => '\.',
			'alias' => array('.=','.')
		),
	);

	
	//$_this =& Operations::getInstance();
	function &getInstance() {
		static $instance = array();
		if (!$instance) {
			$instance[0] =& new Operations();
		}
		return $instance[0];
	}
	
	function _op_add($val,$val2){
		return $val + $val2;
	}
	function _op_substract($val,$val2){
		return $val - $val2;
	}
	function _op_multiply($val,$val2){
		return $val * $val2;
	}
	function _op_concat($val,$val2){
		return $val . $val2;
	}
	function _op_greater($val,$val2){
		return $val > $val2;
	}
	function _op_greaterEq($val,$val2){
		return $val >= $val2;
	}
	function _op_lesser($val,$val2){
		return $val < $val2;
	}
	function _op_lesserEq($val,$val2){
		return $val <= $val2;
	}
	function _op_equal($val,$val2){
		return $val == $val2;
	}
	function _op_equalAbs($val,$val2){
		return $val === $val2;
	}
	function _op_different($val,$val2){
		return $val != $val2;
	}
	function _op_differentAbs($val,$val2){
		return $val !== $val2;
	}
	
	function applyOperation($op, $val=null, $parseString = false){
		//debug($op);
		$_this =& Operations::getInstance();
		if($parseString && is_string($op)){
			$op = $_this->parseStringOperation($op,array(
				'mode' => 'right',
				'error' => '(val)'
			));
			//debug($op);
		}
		$opOpt = $_this->getOperator($op);
		if($opOpt){
			App::import('Lib', 'SetMulti'); 
			//debug($opOpt);
			if(empty($op['subject'])){
				$op['subject'] = $val;
			}
			$params = SetMulti::extractHierarchicMulti($opOpt['named'], $op);
			//debug($params);
			$directParams = SetMulti::pregFilterKey('/^[0-9]+$/',$op);
			//debug($directParams);
			$params = $directParams + $params;
			//debug($params);
			if((isset($opOpt['function']) && $opOpt['function'] === true) ){
				$funct = array($_this,'_op_'.$opOpt['name']);
			}elseif(!empty($opOpt['function'])){
				$funct = $opOpt['function'];
			}
			if(!empty($funct) && is_callable($funct)){
				return call_user_func_array($funct,$params);
			}
		}
		return null;
	}
	
	function simpleOperation($val1,$op,$val2){
		$_this =& Operations::getInstance();
		$opt = array('operator' => $op, 'val' => $val2);
		return $_this->applyOperation($opt,$val1);
	}
	
	function applyOperations($data,$preData,$parseString = false){
		$_this =& Operations::getInstance();
		foreach($cp = $data as $key => $val){
			if(is_array($val) && !empty($val['operator'])){
				if(isset($preData[$key])){
					$res = $_this->applyOperation($val,$preData[$key]);
					if(is_null($res)){
						unset($data[$key]);
					}else{
						$data[$key] = $res;
					}
				}else{
					unset($data[$key]);
				}
			}
		}
		return $data;
	}
	
	function getOperator($opt){
		$_this =& Operations::getInstance();
		if(!is_array($opt)){
			$opt = array('operator' => $opt);
		}
		$op = null;
		if(isset($_this->operators[$opt['operator']])){
			$op = $_this->operators[$opt['operator']];
		}else{
			foreach($_this->operators as $oper){
				if(!empty($oper['alias']) && in_array($opt['operator'],$oper['alias'])){
					$op = $oper;
					break;
				}
			}
		}
		if($op){
			$op = Set::merge($_this->opDefOpt,$op);
		}
		return $op;
	}
	
	function parseStringOperations($data,$options = array()){
		$defOpt = array(
			'mode' => 'right',
			'error' => '(val)'
		);
		$opt = Set::merge($defOpt,$options);
		$_this =& Operations::getInstance();
		foreach($data as $key => $val){
			foreach($_this->operators as $op){
				$data[$key] = $_this->parseStringOperation($val,$opt);
			}
		}
		return $data;
	}
	
	function parseStringOperation($val,$options = array()){
		if(is_numeric($val)){
			return $val;
		}
		$_this =& Operations::getInstance();
		$localOpt = array('type','error');
		$defOpt = array(
			'mode' => 'full',
			'error' => false
		);
		$opt = array_merge($defOpt,$options);
		App::import('Lib', 'SetMulti'); 
		foreach($_this->operators as $op){
			$op = Set::merge($_this->opDefOpt, $op, SetMulti::excludeKeys($opt,$localOpt));
			if(empty($opt['type']) || in_array($op['type'],(array)$opt['type'])){
				$pattern = $_this->getPattern($op);
				//debug($pattern);
				if(!empty($pattern)){
					if(preg_match($pattern,(string)$val,$res)){
						//debug($res);
						$oper = array('operator' => $op['name']);
						array_shift($res);
						foreach($res as $k => $r){
							if(!is_numeric($k)){
								$oper[$k] = $r;
							}
						}
						return $oper;
					}
				}
			}
		}
		if($opt['error'] == '(val)'){
			return $val;
		}else{
			return $opt['error'];
		}
	}
	
	function getPattern($options){
		$defOpt = array(
			'mode' => 'full'
		);
		$opt = Set::merge($defOpt,$options);
		$n = 'pattern'.ucfirst($opt['mode']);
		if(!empty($opt[$n])){
			$pattern = $opt[$n];
			foreach($opt as $key => $val){
				if(preg_match('/^(\w+)Pattern$/',$key,$res)){
					$pattern = str_replace('{'.$res[1].'}', $val, $pattern);
				}
			}
			return $pattern;
		}
		return null;
	}
}
?>