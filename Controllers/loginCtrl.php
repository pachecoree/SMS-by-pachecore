<?php

	class loginCtrl {
		#constructor function
		function __construct($driver) {
			#get the Model create loginMdl object 
			require('Models/loginMdl.php');
			$this -> mdl_obj = new loginMdl($driver);
			require('Views/errors.php');
			$this -> errors = new errors();
			require('Controllers/validationCtrl.php');
			$this -> validation = new validationCtrl();
		}

		function validate_user (){
			#Validate if userid is correct
			$table = 'users_';
			$table1 = '';
			$userid = $_GET['userid'];
			$value = $this -> validation -> validate_userid($userid);
			if (!($value == false)) {
				if ($value == 1) {
					$table1 = 'student';
					$type = 1;
				}
				elseif ($value == 2) {
					$table1 = 'teacher';
					$type = 2;
				}
				elseif ($value == 3) {
					$table1 = 'admin';
					$type = 3;
				}
				$table = $table.$table1;
				if ($usuario = $this -> mdl_obj -> get_user($userid,$_GET['password'],$table,$type)) {
					#Userid is correct
					return $usuario;
				}
			}
			return false;
		}


		function session_started() {
			if (isset($_SESSION['started'])) {
				return true;
			}
			return false;
		}

		function start_session($user,$type,$userid) {
			$_SESSION['started'] = true;
			$_SESSION['user'] = $user;
			$_SESSION['userid'] = $userid;
			$_SESSION['type'] = $type;
		}

		function end_session() {
			session_unset();
			session_destroy();	
			setcookie(session_name(), '', time()- 3600);
		}

		function run() {
			if (isset($_GET['act'])) {
				switch ($_GET['act']) {
					case 'signin':
					session_start();
						if (isset($_GET['userid']) && isset($_GET['password'])) {
							if ($this -> session_started()) {
								$this -> errors -> session_active();
							}
							else {
								if ($usuario = $this -> validate_user()) {
									$this -> start_session($usuario['usuario'],$usuario['type'],$usuario['userid']);
									require('Views/loginCorrectly.php');
								}
								else {
									$this -> errors -> error_login_data();
								}
							}
						}
						else {
							$this -> errors -> not_found_input('User ID or Password');
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