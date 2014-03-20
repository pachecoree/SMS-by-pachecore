<?php

	class loginCtrl {
		#constructor function
		function __construct() {
			#get the Model create loginMdl object 
			require('Models/loginMdl.php');
			$this -> mdl_obj = new loginMdl();
			require('Views/errors.php');
			$this -> errors = new errors();
		}

		function validate_userid (){
			#Check if userid was input 
			if (isset($_GET['userid'])) {
				#Validate if userid is correct
				if ($this -> mdl_obj -> get_userid(strtolower($_GET['userid']))) {
					#Userid is correct
					return true;
				}
			}
			else {
				#Userid was not input
				echo 'Could not find UserID in this context</br>';
			}
			return false;
		}

		function validate_password() {
			#Check if password was input
			if (isset($_GET['password'])) {
				#Validate if password is correct
				if ($this -> mdl_obj -> get_password($_GET['password'])) {
					#Password is correct
					return true;
				}
			}
			else {
				#Password was not input
				echo 'Could not find Password</br>';
			}
			return false;
		}

		function session_started() {
			if (isset($_SESSION['userid'])) {
				return true;
			}
			return false;
		}

		function start_session($userid) {
			//session_start();
			//$_SESSION['started'] = true;
			$_SESSION['userid'] = $userid;
		}

		function end_session() {
			session_unset();
			session_destroy();	
			setcookie(session_name(), '', time()-3600);
		}

		function validate_login_data() {
			#Callback to validate functions
			if ($this -> validate_userid()) {
				if ($this -> validate_password()) {
					#Show view
					return true;
				}
			}
			return false;
		}

		function run() {
			if (isset($_GET['act'])) {
				switch ($_GET['act']) {
					case 'signin':
					session_start();
						if ($this -> session_started()) {
							$this -> errors -> session_active();
						}
						else {
							if ($this -> validate_login_data()) {
								$this -> start_session($_GET['userid']);
								require('Views/loginCorrectly.php');
							}
							else {
								echo 'Incorrect Userid or Password';
							}
						}
						break;

					case 'signout':
						session_start();
						$this -> end_session();
						echo 'Signed out';
					
					default:
						//
						break;
				}
			}
			else {
				//
			}
		}

	}
?>