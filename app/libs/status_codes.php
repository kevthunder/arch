<?php
class StatusCodes extends Object {

	var $codes = array(
		//Status
		200 => array("msg" => "OK"),
		201 => array("msg" => "Loggin succesful"),
		204 => array("msg" => "No content"),
		//Warnings
		300 => array("msg" => "Not logged"),
		301 => array("msg" => "Notting happened"),
		302 => array("msg" => "Test"),
		303 => array("msg" => "No character selected"),
		//Errors
		400 => array("msg" => "Insufficient credential, please enter username and password."),
		401 => array("msg" => "Failed to login, make sure username and password are correct."),
		402 => array("msg" => "Internal Error"),
		403 => array("msg" => "Forbidden"),
		404 => array("msg" => "Handler not found"),
		405 => array("msg" => "Action not found"),
		406 => array("msg" => "Missing attribute"),
		407 => array("msg" => "Invalide attribute"),
		409 => array("msg" => "Bad Request"),
		431 => array("msg" => "Out of range"),
		441 => array("msg" => "Target not found"),
	);
	
	//$_this =& StatusCodes::getInstance();
	function &getInstance() {
		static $instance = array();
		if (!$instance) {
			$instance[0] =& new StatusCodes();
		}
		return $instance[0];
	}
	
	function getCode($no){
		$_this =& StatusCodes::getInstance();
		if(!empty($_this->codes[$no])){
			return array_merge(array("no"=>$no),$_this->codes[$no]);
		}else{
			return false;
		}
	}
}
?>