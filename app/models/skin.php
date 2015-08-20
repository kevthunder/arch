<?php
class Skin extends AppModel {
	var $name = 'Skin';
	var $displayField = 'title';
	
	
	var $actsAs = array(
		'XmlLink.XmlLinked'=>array('title','class_name')
	);
}
