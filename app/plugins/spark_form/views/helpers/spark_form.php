<?php
//App::import('Helper','Form');

class SparkFormHelper  extends AppHelper /*extends FormHelper*/ {
	var $helpers = array('Html', 'Form'/*, 'Javascript'*/);
	
	function __construct(){
		App::import('Lib', 'SparkForm.SparkFormData'); 
		$this->specialValues = SparkFormData::specialValues();
	}
	
	function specialValues($fieldName, $options = array()){
		$this->setEntity($fieldName);
		$specialValues = array(
			'true' => true,
			'false' => false,
			'null' => null,
			'undefined' => null,
		);
		$spcFieldOpt = array(
			'label' => __('Special value',true),
			'options' => array_combine(array_keys($specialValues),array_keys($specialValues)),
			'empty' => __('none',true),
		);
		$hasSpecial = null;
		$value =  $this->value($fieldName);
		$view =& ClassRegistry::getObject('view');
		$entity = $view->entity();
		foreach($this->specialValues as $spc => $val){
			if($value === $val){
				$hasSpecial = $spc;
			}
		}
		if (
			!$hasSpecial && !empty($entity) && (
				empty($this->data) || !Set::check($this->data, join('.', $entity))
			)
		) {
			$hasSpecial = 'undefined';
		}
		if($hasSpecial){
			$options['value'] = '';
			$spcFieldOpt['value'] = $hasSpecial;
		}
		if(empty($options['after'])) $options['after'] = '';
		$options['after'] .= $this->Form->input($fieldName.'_spc', $spcFieldOpt);
		return $this->Form->input($fieldName, $options);
	}
	
	function multiple($fieldName, $options = array() ){
		//debug($options);
		$this->Html->script('/spark_form/js/multiple',array('inline'=>false));
		$this->Html->css('/spark_form/css/multiple',null,array('inline'=>false));
		if(!array_key_exists('fields',$options)){
			if(empty($options['type']) || $options['type'] != 'multiple'){
				$options['fields'] = array('__val__'=>$options);
			}else{
				$options['fields'] = array('val'=>array());
			}
		}
		$defOpt = array(
			'mode' => 'table',
			'id'=>$this->domId($fieldName),
			'elem' => null,
			'elemVars' => array('labels'),
			'toAttributes' => array('div'),
			'independantLabels' => false,
			'colClass' => true,
			'div'=>array(
				'class'=>array('MultipleInput'),
			),
			'model'=>array(
				'class'=>array('modelLine'),
				'fields'=>array('disabled'=>true),
			),
			'deleteField'=>array(
				'spc' => 'deleteInput',
				'type' => 'hidden',
			),
			'min' => 0, 
			'add' => array(
				'class' => 'btAdd',
				'label' => __('Add Row',true),
			),
			'delete' => array(
				'class' => 'btDelete',
				'label' => __('Delete',true),
				'colLabel' => false,
			),
			'mainContainer' => null,
			'toMainContainer' => array('id','min','max','nameprefix'),
			'dynamicKeys' => false,
		);
		$modeOpt = array(
			'table' => array(
				'elem' => 'multiple_table',
				'elemVars' => array('labels'),
				'toAttributes' => array('table','td','tdAction','trAction','div'),
				'independantLabels' => true,
				'mainContainer' => 'table',
				'table'=>array(
					'class'=>array('MultipleTable'),
					'cellspacing'=>0,
					'cellpadding'=>0,
				),
				'tr'=>array(
					'class'=>array('line'),
				),
				'trAction'=>array(
					'class'=>array('actionLine'),
				),
				'td'=>array(
				),
				'tdAction'=>array(
					'class'=>array('actionCell'),
				)
			)
		);
		//normalize and count
		$options['fields'] = Set::normalize($options['fields']);
		if(array_key_exists('__key__',$options)){
			$options['dynamicKeys'] = true;
		}
		
		$opt = array_merge($defOpt,$options);
		if(!empty($opt['mode']) && !empty($modeOpt[$opt['mode']])){
			$opt = array_merge($defOpt,$modeOpt[$opt['mode']],$options);
		}
		$nbColls = 1;
		
		$this->setEntity($fieldName);
		
		$fullFieldName = $fieldName;
		if($fullFieldName != ucfirst($fullFieldName) && substr($fullFieldName,0,strlen($this->model())+1) != $this->model().'.'){
			$fullFieldName = $this->model().'.'.$fullFieldName;
		}
		$opt['nameprefix'] = $this->_name($fullFieldName);
		
		$values = current($this->value());
		if(empty($values)){
			$values = array();
		}
		foreach($tmp = $opt['fields'] as $key => $field){
			if($field === false){
				unset($opt['fields'][$key]);
			}else{
				if(empty($field)){
					$field = array();
				}
				if(!is_array($field)){
					$field = array('type'=>$field);
				}
				$def = array(
					'div'=>false
				);
				$field = array_merge($def,$field);
				if(empty($field['type']) && $key == 'id'){
					$field['type'] = 'hidden';
				}
				if($opt['independantLabels'] && !array_key_exists('label',$field)){
					$field['label'] = $this->defaultLabelText($key);
				}
				
				if(!empty($field['class'])){
					$field['class'] = (array)$field['class'];
				}
				if($opt['colClass']){
					$field['class'][] = $this->domId($fullFieldName.'.'.$key);
				}
				$field['class'] = implode(' ',$field['class']);
				
				if(!empty($field['type']) && $field['type'] == 'hidden'){
				}else{
					$nbColls++;
				}
				$opt['fields'][$key] = $field;
			}
		}
		$hiddens = array();
		if($opt['independantLabels']){
			$labels = array();
			foreach($tmp = $opt['fields'] as $key => $field){
				if(empty($field['type']) || $field['type'] != 'hidden'){
					if(is_array($field['label'])){
						$field = $this->label_aposition($key,$field);
						$labels[] = $field['label']['text'];
					}else{
						$labels[] = $field['label'];
					}
					$field['label'] = false;
				}
				if($key == '__key__'){
					$field['name'] = false;
					$field['spc'] = 'rowKeyInput'; 
					$field['class'] = 'rowKeyInput'; 
				}
				$opt['fields'][$key] = $field;
			}
		}
		$lines = array();
		$nbRow = count($values);
		$nbRow = max($nbRow,$opt['min']);
		for ($i = -1; $i < $nbRow; $i++) {
			$line = array();
			$model = ($i == -1);
			$rowKey = $index = $i;
			if($model){
				$rowKey = $index = "---i---";
			}elseif($i < count($values)){
				$valuesKeys = array_keys($values);
				$rowKey = $valuesKeys[$i];
			}
			$trOpt = $opt['tr'];
			$trOpt['rowkey'] = $rowKey;
			if($model){
				$trOpt = Set::merge($trOpt,$opt['model']);
			}
			$line['tr'] = $this->_parseAttributes($trOpt,array('fields'));
			
			foreach($tmp = $opt['fields'] as $key => $field){
				if(!empty($trOpt['fields'])){
					$field = array_merge($trOpt['fields'],$field);
				}
				if($key == '__key__' && !$model){
					$field['value'] = $rowKey;
				}
				if($rowKey !== $index){
					$field['id'] = $this->domId($fullFieldName.'.'.$index.'.'.$key);
				}
				if(empty($field['type']) || $field['type'] != 'hidden'){
					if($key == '__val__'){
						$subFieldName = $fullFieldName.'.'.$rowKey;
					}else{
						$subFieldName = $fullFieldName.'.'.$rowKey.'.'.$key;
					}
					$line['inputs'][$subFieldName] = $field;
				}else{
					$hiddens[$key] = $field;
				}
			}
			foreach($hiddens as $key => $field){
				if(!empty($trOpt['fields'])){
					$field = array_merge($trOpt['fields'],$field);
				}
				if($key == 'id'){
					$optDelete = $opt['deleteField'];
					if(!empty($trOpt['fields'])){
						$optDelete = array_merge($trOpt['fields'],$optDelete);
					}
					if($rowKey !== $index ){
						$optDelete['id'] = $this->domId($fullFieldName.'.'.$index.'.'.$key);
					}
					$line['hidden'][$fieldName.'.'.$rowKey.'.delete'] = $optDelete;
					$field['spc'] = 'keyInput';
				}
				$line['hidden'][$fieldName.'.'.$rowKey.'.'.$key] = $field;
			}
			$lines[] = $line;
		}
		
		
		
		if(!empty($opt['mainContainer']) && !empty($opt['toMainContainer'])){
			$opt[$opt['mainContainer']] = array_merge($opt[$opt['mainContainer']],array_intersect_key($opt,array_flip($opt['toMainContainer'])));
		}
		
		
		$elemsAttr = array();
		foreach(Set::normalize($opt['toAttributes']) as $key => $val){
			if(array_key_exists($key,$opt) && $opt[$key] !== false){
				$elemsAttr[$key] = $this->_parseAttributes($opt[$key]);
			}else{
				$elemsAttr[$key] = false;
			}
		}
		
		$view =& ClassRegistry::getObject('view');
		$elemOpt = array('plugin'=>'spark_form','fieldName'=>$fieldName,'lines'=>$lines,'elemsAttr'=>$elemsAttr,'options'=>$opt);
		$elemOpt = array_merge($elemOpt,compact($opt['elemVars']));
		$html = $view->element($opt['elem'],$elemOpt);
		
		return $html;
	}
	
	
	////////////////////////// Other functions //////////////////////////
	
	function defaultLabelText($fieldName, $options = null){
		if(!empty($options) && array_key_exists('label',$options) && $options['label'] !== true){
			if(!is_array($options['label'])){
				return $options['label'];
			}elseif(array_key_exists('text',$options['label'])){
				return $options['label']['text'];
			}
		}
		if (strpos($fieldName, '.') !== false) {
			$text = array_pop(explode('.', $fieldName));
		} else {
			$text = $fieldName;
		}
		if (substr($text, -3) == '_id') {
			$text = substr($text, 0, strlen($text) - 3);
		}
		$text = __(Inflector::humanize(Inflector::underscore($text)), true);
		
		return $text;
	}
	
	
	function labelFor($fieldName, $options){
		if (!isset($options['label']) || $options['label'] !== false) {
			$label = array();
			if (!empty($options['label'])){
				$label = $options['label'];
			}
			if (!is_array($label)) {
				$label = array('text'=>$label);
			}
			$labelText = null;
			if (isset($label['text'])) {
				$labelText = $label['text'];
				//unset($label['text']);
			}
			return $this->Form->label($fieldName, $labelText, $label);
		}
		return null;
	}
	
	
	function normalizeAttributesOpt($options, $exclude = null){
		if(array_key_exists('class',$options) && is_array($options['class'])){
			$options['class'] = implode(' ',$options['class']);
		}
		if(!empty($exclude)){
			$options = array_diff_key($options,array_flip($exclude));
		}
		return $options;
	}
	function _parseAttributes($options, $exclude = null, $insertBefore = ' ', $insertAfter = null){
		$options = $this->normalizeAttributesOpt($options);
		return parent::_parseAttributes($options, $exclude, $insertBefore, $insertAfter);
	}
	
}