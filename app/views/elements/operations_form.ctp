<?php
	echo $this->Form->input($prefix.'model', array('type'=>'text'));
	echo $this->Form->input($prefix.'key', array('type'=>'text'));
	echo $this->Form->input($prefix.'conditions', array('type'=>'text'));
	echo $this->SparkForm->multiple($prefix.'operations', array('fields'=>array(
		'field'=>array('type'=>'text'),
		'operator'=>array('type'=>'select','options'=>$operators),
		'value'=>array('type'=>'text'),
	)));
?>