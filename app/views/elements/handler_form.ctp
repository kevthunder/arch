<?php 
if(!empty($paramInputs)){
	foreach ($paramInputs as $inputName => $inputOpt) { 
		$defOpt = array('type'=>'text');
		$inputOpt = array_merge($defOpt,$inputOpt);
		if($inputOpt['type'] == 'html'){
			echo $inputOpt['val'];
		}elseif($inputOpt['type'] == 'element'){
			$elemOpt = array(
				'prefix' => 'Event.params.',
			);
			if(!empty($this->data['Event']['params'])){
				$elemOpt['data'] = $this->data['Event']['params'];
			}
			if(!empty($inputOpt['options'])){
				$elemOpt = array_merge($elemOpt,$inputOpt['options']);
			}
			echo $this->element($inputOpt['val'],$elemOpt);
		}else{
			echo $this->Form->input('Event.params.'.$inputName,$inputOpt);
		}
	}
} 
?>