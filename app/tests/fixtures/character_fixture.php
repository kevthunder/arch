<?php
/* Character Fixture generated on: 2011-06-26 23:30:29 : 1309131029 */
class CharacterFixture extends CakeTestFixture {
	var $name = 'Character';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'tile_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'hp' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'total_hp' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'name' => 'Lorem ipsum dolor sit amet',
			'tile_id' => 1,
			'hp' => 'Lorem ipsum dolor sit amet',
			'total_hp' => 'Lorem ipsum dolor sit amet',
			'active' => 1,
			'created' => '2011-06-26 23:30:29',
			'modified' => '2011-06-26 23:30:29'
		),
	);
}
