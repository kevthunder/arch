<?php
	echo $this->Form->input($prefix.'model', array('type'=>'text'));
	echo $this->Form->input($prefix.'key', array('type'=>'text'));
	echo $this->Form->input($prefix.'conditions', array('type'=>'text'));
	echo $this->Form->input($prefix.'updateAll', array('type'=>'checkbox'));
	echo $this->SparkForm->multiple($prefix.'data', array('fields'=>array(
		'__key__'=>array('type'=>'text'),
		'__val__'=>array('type'=>'text'),
	)));
?>
