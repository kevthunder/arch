<?php

class LocalEvent {

/**
 * 
 * 
 * @var string $name
 */
	protected $_name = null;

/**
 * 
 *
 * @var object
 */
	protected $_subject;

/**
 * 
 *
 * @var mixed $data
 */
	public $data = null;

/**
 * 
 *
 * @var mixed $result
 */
	public $result = null;

/**
 * 
 *
 * @var boolean
 */
	protected $_stopped = false;

/**
 * Constructor
 *
 *
 */
	public function __construct($name, $subject = null, $data = null) {
		$this->_name = $name;
		$this->data = $data;
		$this->_subject = $subject;
	}

/**
 * 
 *
 * @param string $attribute
 * @return mixed
 */
	public function __get($attribute) {
		if ($attribute === 'name' || $attribute === 'subject') {
			return $this->{$attribute}();
		}
	}

/**
 * 
 *
 * @return string
 */
	public function name() {
		return $this->_name;
	}

/**
 * 
 *
 * @return string
 */
	public function subject() {
		return $this->_subject;
	}

/**
 * 
 *
 * @return void
 */
	public function stopPropagation() {
		return $this->_stopped = true;
	}

/**
 * 
 *
 * @return boolean True if the event is stopped
 */
	public function isStopped() {
		return $this->_stopped;
	}

}
