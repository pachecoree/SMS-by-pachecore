<?php

 class teacherCtrl {

	function __construct($driver) {
		#Create errors object
		require('Views/errors.php');
		$this -> errors = new errors();
		#Create the validation object
		require('Controllers/validationCtrl.php');
		$this -> validation = new validationCtrl();
		#Create Model object
		require('Models/teacherMdl.php');
		$this -> teacher_mdl = new teacherMdl($driver);
		require('Controllers/templatesCtrl.php');
		$this -> templateCtrl = new templatesCtrl();
		require('Controllers/mailCtrl.php');
		$this -> emailCtrl = new mailCtrl();
	} 	

	function genera_password($length = 5) {
	    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@';
	    $rdmpass = '';
	    for ($i = 0; $i < $length; $i++) {
	        $rdmpass .= $caracteres[rand(0, strlen($caracteres) - 1)];
	    }
	    return $rdmpass;
	}

	function run() {
		if (isset($_GET['act'])) {
			switch ($_GET['act']) {
				case 'add':
					$session = $this -> validation -> active_session();
					if ($session == 3) {
					#Create student array
					$teacher = array();
						#Check if the name is valid
						if (isset($_POST['first']) && isset($_POST['second']) && isset($_POST['name'])) {
							#Validate full name
							if ($this -> validation -> validate_name(($_POST['name'].' '.$_POST['first'].' '.$_POST['second']))) {
								#Check if e-mail exists
								if (isset($_POST['email'])) {
									#Validate e-mail
									if ($this -> validation -> validate_email($_POST['email'])) {
										#Check if Student ID exists
										if (isset($_POST['teacherid'])) {
											#Validate Student ID
											if ($this -> validation -> validate_tid($_POST['teacherid'])) {
												if (isset($_POST['cellphone'])) {
													if ($this -> validation -> validate_phonenumber($_POST['cellphone'])) {
															#Fill Student array
															$teacher['password'] = $this -> genera_password();
															$teacher['name'] = $_POST['name'];
															$teacher['first'] = $_POST['first'];
															$teacher['second'] = $_POST['second'];
															$teacher['teacherid'] = $_POST['teacherid'];
															$teacher['email'] = $_POST['email'];
															$teacher['cellphone'] = $_POST['cellphone'];
															$teacher = $this -> teacher_mdl -> std_obj -> add_teacher($teacher);
															if (is_array($teacher)) {
																#Get the View
																$footer = file_get_contents('Views/Footer.html');
																$header = file_get_contents('Views/Head.html');
																$content = file_get_contents('Views/teacherview.html');
																$content = $this -> templateCtrl -> get_menu($content);
																$content = $this -> templateCtrl -> procesarPlantilla_teacherview($content,$teacher);
																echo $header .$content.$footer;
															}
															else {
																#Display the add error
																$this -> errors -> error_add_student($_POST['first'].' '.$_POST['second'].' '. $_POST['name']);
															}
													}
													else {
														#Career not valid
														$this -> errors -> not_valid_format($_POST['cellphone'],"Cellphone");
													}
												}
												else {
													#Career not input
													$this -> errors -> not_found_input("cellphone");
												}
											}
											else {
												#Student id not valid
												$this -> errors -> not_valid_format($_POST['teacherid'],"Teacher ID");
											}
										}
										else {
											#Student ID not input
											$this -> errors -> not_found_input('Teacher ID');
										}
									}
									else {
										#E-mail is not valid
										$this -> errors -> not_valid_format($_POST['email'],"E-mail");
									}
								}
								else {
									#Correo was not input
									$this -> errors -> not_found_input('E-mail');
								}
							}
							else {
								#Full name is not valid
								$this -> errors -> not_valid_format(($_POST['name'].' '.$_POST['first'].' '.$_POST['second']),'Nombre Completo');
							}
						}
						else {
							#Name was not input
							$this -> errors -> not_found_input('Full Name');
						}
					}
					else if ($session == false) {
						$header = file_get_contents('Views/Head.html');
						$footer = file_get_contents('Views/Footer.html');
						$content = file_get_contents('Views/login.html');
						$content = $this -> templateCtrl -> procesarPlantilla_login($content,-1);
						echo $header.$content.$footer;
					}
					else {
						$this -> errors -> not_valid_usertype();
					}
					break;

				case 'new':
					$session = $this -> validation -> active_session();
					if ($session >= 3) {
						$footer = file_get_contents('Views/Footer.html');
						$header = file_get_contents('Views/Head.html');
						$content = file_get_contents('Views/addteacher.html');
						$content = $this -> templateCtrl -> get_menu($content);
						echo $header . $content . $footer;
					}
					else if ($session == false) {
						$header = file_get_contents('Views/Head.html');
						$footer = file_get_contents('Views/Footer.html');
						$content = file_get_contents('Views/login.html');
						$content = $this -> templateCtrl -> procesarPlantilla_login($content,-1);
						echo $header.$content.$footer;
					}
					else {
						$this -> errors -> not_valid_usertype();
					}
					break;


				default:
					#Activity is not valid
					$this -> errors -> not_valid_input($_GET['act'],'Activity');
					break;
			}
		}
		else {
			#Act was not input
			$this -> errors -> not_found_input('Activity');
		}
	}

 }

?>