<?php
	echo $this->Form->input($prefix.'mode', array('type'=>'select','options'=>$modes));
	echo $this->SparkForm->multiple($prefix.'output_define', array('fields'=>array(
		'__key__'=>array('type'=>'text'),
		'__val__'=>array('type'=>'text'),
	)));
?>
