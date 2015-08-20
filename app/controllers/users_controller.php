<?php
class UsersController extends AppController {

    var $name = 'Users';    
	var $helpers = array('Xml');
 
    /**
     *  The AuthComponent provides the needed functionality
     *  for login, so you can leave this function blank.
     */
    function login() {
    }
	function admin_login() {
	}

	
	function client_login($username = null,$password = null){
		$this->layout = 'xml/default';
		
		
		$warnings = array();
		if(!empty($_POST['username'])){
			$username = $_POST['username'];
		}
		if(!empty($_POST['password'])){
			$password = $_POST['password'];
		}
		if(!empty($username) && !empty($password)){
			if($this->Auth->login(array('username'=>$username,'password'=>$this->Auth->password($password)))){
				$warnings[] = 201;
			}else{
				$warnings[] = 401;
			}
		}else{
			$warnings[] = 400;
		}
		
		
		$this->set('warnings', $warnings);
		header ("content-type: text/xml");
	}

    function logout() {
        $this->redirect($this->Auth->logout());
    }
}
?>