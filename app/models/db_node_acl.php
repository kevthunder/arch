<?php

/**
 * Load Model and Node Model
 */
App::import('Model', 'Node');

/**
 * ACL Node
 *
 *
 * @package       cake
 * @subpackage    cake.cake.libs.model
 */
class AclNode extends Node {

	function node($ref = null, $fullPath = true, $incomplete = false) {
		return $this->getNode($ref, $fullPath, $incomplete);
	}
}

/**
 * Access Control Object
 *
 * @package       cake
 * @subpackage    cake.cake.libs.model
 */
class Aco extends AclNode {

/**
 * Model name
 *
 * @var string
 * @access public
 */
	var $name = 'Aco';

/**
 * Binds to ARO nodes through permissions settings
 *
 * @var array
 * @access public
 */
	var $hasAndBelongsToMany = array('Aro' => array('with' => 'Permission'));
}

/**
 * Action for Access Control Object
 *
 * @package       cake
 * @subpackage    cake.cake.libs.model
 */
class AcoAction extends AppModel {

/**
 * Model name
 *
 * @var string
 * @access public
 */
	var $name = 'AcoAction';

/**
 * ACO Actions belong to ACOs
 *
 * @var array
 * @access public
 */
	var $belongsTo = array('Aco');
}

/**
 * Access Request Object
 *
 * @package       cake
 * @subpackage    cake.cake.libs.model
 */
class Aro extends AclNode {

/**
 * Model name
 *
 * @var string
 * @access public
 */
	var $name = 'Aro';

/**
 * AROs are linked to ACOs by means of Permission
 *
 * @var array
 * @access public
 */
	var $hasAndBelongsToMany = array('Aco' => array('with' => 'Permission'));
}

/**
 * Permissions linking AROs with ACOs
 *
 * @package       cake
 * @subpackage    cake.cake.libs.model
 */
class Permission extends AppModel {

/**
 * Model name
 *
 * @var string
 * @access public
 */
	var $name = 'Permission';

/**
 * Explicitly disable in-memory query caching
 *
 * @var boolean
 * @access public
 */
	var $cacheQueries = false;

/**
 * Override default table name
 *
 * @var string
 * @access public
 */
	var $useTable = 'aros_acos';

/**
 * Permissions link AROs with ACOs
 *
 * @var array
 * @access public
 */
	var $belongsTo = array('Aro', 'Aco');

/**
 * No behaviors for this model
 *
 * @var array
 * @access public
 */
	var $actsAs = null;

/**
 * Constructor, used to tell this model to use the
 * database configured for ACL
 */
	function __construct() {
		$config = Configure::read('Acl.database');
		if (!empty($config)) {
			$this->useDbConfig = $config;
		}
		parent::__construct();
	}
}
