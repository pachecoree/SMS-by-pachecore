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
			require('Controllers/templatesCtrl.php');
			$this -> templateCtrl = new templatesCtrl();
			require('Controllers/mailCtrl.php');
			$this -> emailCtrl = new mailCtrl();
		}

		function validate_user (){
			#Validate if userid is correct
			$table = 'users_';
			$table1 = '';
			$userid = $_POST['userid'];
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
				if ($usuario = $this -> mdl_obj -> get_user($userid,$_POST['password'],$table,$type)) {
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
					case 'changepwd':
						$session = $this -> validation -> active_session();
						if (sizeof($_POST) == 0 ){
							$header = file_get_contents('Views/Head.html');
							$footer = file_get_contents('Views/Footer.html');
							$content = file_get_contents('Views/change_password.html');
							$content = str_replace("{{'mensaje'}}", 'Ingrese Contrasenha', $content);
							$content = $this-> templateCtrl -> get_menu($content);
							echo $header . $content . $footer;
						}
						if (isset($_POST['password']) && isset($_POST['password1'])) {
							if ($session == false) {
								$header = file_get_contents('Views/Head.html');
								$footer = file_get_contents('Views/Footer.html');
								$content = file_get_contents('Views/login.html');
								$content = $this -> templateCtrl -> procesarPlantilla_login($content,-1);
								echo $header.$content.$footer;
								return;
							}
							$header = file_get_contents('Views/Head.html');
							$footer = file_get_contents('Views/Footer.html');
							$content = file_get_contents('Views/logincorrectly.html');
							$this -> mdl_obj -> updatePassword($_POST['password'],$_SESSION['userid'],$session);
							$content = str_replace("{{'sesion-expirada'}}", 'Contrasenha modificada !', $content);
							$content = $this-> templateCtrl -> get_menu($content);
							echo $header . $content . $footer;
						}
					break;

					case 'signin':
					session_start();
						if (isset($_POST['userid']) && isset($_POST['password'])) {
							if ($this -> session_started()) {
								$this -> errors -> session_active();
							}
							else {
								if ($usuario = $this -> validate_user()) {
									$this -> start_session($usuario['usuario'],$usuario['type'],$usuario['userid']);
									$header = file_get_contents('Views/Head.html');
									$footer = file_get_contents('Views/Footer.html');
									if ($this -> mdl_obj -> get_generica($usuario['userid'],$_SESSION['type'])) {
										$_SESSION['pass'] = $_POST['password'];
										$content = file_get_contents('Views/change_password.html');
										$content = str_replace("{{'mensaje'}}", 'Por razones de seguridad, Cambie su contrasenha', $content);
									}
									else
										$content = file_get_contents('Views/logincorrectly.html');
									//require('Views/loginCorrectly.php');
									$content = $this-> templateCtrl -> get_menu($content);
									echo $header . $content . $footer;
								}
								else {
									//$this -> errors -> error_login_data();
									$header = file_get_contents('Views/Head.html');
									$content = file_get_contents('Views/login.html');
									$footer = file_get_contents('Views/Footer.html');
									$content = $this-> templateCtrl -> procesarPlantilla_login($content,2);
									echo $header.$content.$footer;
								}
							}
						}
						else {
							$header = file_get_contents('Views/Head.html');
							$content = file_get_contents('Views/login.html');
							$footer = file_get_contents('Views/Footer.html');
							$content = $this-> templateCtrl -> procesarPlantilla_login($content,2);
							echo $header.$content.$footer;
						}
						break;

					case 'signout':
						session_start();
						$this -> end_session();
						$header = file_get_contents('Views/Head.html');
						$content = file_get_contents('Views/login.html');
						$footer = file_get_contents('Views/Footer.html');
						$content = $this-> templateCtrl -> procesarPlantilla_login($content,1);
						echo $header.$content.$footer;
                        break;
					
					default:
						#Activity  was not valid
						echo 'Activity ',$_GET['act'],' is not valid';
						break;
				}
			}
			else {
				#Activity was not input
				echo 'Activity not found';
				die();
			}
		}

	}
?>