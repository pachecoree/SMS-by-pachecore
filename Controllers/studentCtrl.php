<?php

class studentCtrl {

	function __construct($driver) {
		#Create the errors object
		require('Views/errors.php');
		$this -> errors = new errors();
		#Create the validation object
		require('Controllers/validationCtrl.php');
		$this -> validation = new validationCtrl();
		require('Models/studentMdl.php');
		$this -> obj_mdl = new studentMdl($driver);
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
		#Check if the activity was input
		if (isset($_GET['act'])) {
			switch ($_GET['act']) {
				case 'add':
					$session = $this -> validation -> active_session();
					if ($session >= 3) {
					#Create student array
					$student = array();
					#Flags is to mark down errors on optional fields
					$bandera_datos = 0;
					#Check if the name is valid
					if (isset($_GET['first']) && isset($_GET['second']) && isset($_GET['name'])) {
						#Validate full name
						if ($this -> validation -> validate_name(($_GET['name'].' '.$_GET['first'].' '.$_GET['second']))) {
							#Check if e-mail exists
							if (isset($_GET['email'])) {
								#Validate e-mail
								if ($this -> validation -> validate_email($_GET['email'])) {
									#Check if Student ID exists
									if (isset($_GET['studentid'])) {
										#Validate Student ID
										if ($this -> validation -> validate_sid($_GET['studentid'])) {
											if (isset($_GET['career'])) {
												if ($this -> validation -> validate_career($_GET['career'])) {
													#Check if Phonenumber exists
													if (isset($_GET['cellphone'])) {
														#Validate Phonenumber
														if ($this -> validation -> validate_phonenumber($_GET['cellphone'])) {
															$student['cellphone'] = $_GET['cellphone'];
														}
														else {
															#Phonenumber not valid
															$bandera_datos = 1;
															$this -> errors -> not_valid_format($_GET['cellphone'],"Phone Number");
														}
													}
													#Check if Github account exists
													if (isset($_GET['github'])) {
														#Check if Github account is valid
														if ($this -> validation -> validate_github($_GET['github'])) {
															$student['github'] = $_GET['github'];
														}
														else {
															#Github not valid
															$bandera_datos =1;
															$this -> errors -> not_valid_format($_GET['github'],"Github Account");
														}
													}
													#Check if Web Page exists
													if (isset($_GET['web'])) {
														#Validate Web Page
														if ($this -> validation -> validate_web($_GET['web'])) {
															$student['web'] = $_GET['web'];
														}
														else {
															#Web Page not valid
															$bandera_datos = 1;
															$this -> errors -> not_valid_format($_GET['web'],"Web Page");
														}
													}
													#All data has been validated
													if ($bandera_datos == 0) {
														#Fill Student array
														$student['password'] = $this -> genera_password();
														$student['name'] = $_GET['name'];
														$student['first'] = $_GET['first'];
														$student['second'] = $_GET['second'];
														$student['studentid'] = $_GET['studentid'];
														$student['email'] = $_GET['email'];
														$student['career'] = $_GET['career'];
														$student = $this -> obj_mdl -> add_student($student);
														if (is_array($student)) {
															#Set action
															$student['action'] = "Added";
															#Get the View
															require('Views/studentview.php');
														}
														else {
															#Display the add error
															$this -> errors -> error_add_student($_GET['first'].' '.$_GET['second'].' '. $_GET['name']);
														}
													}
												}
												else {
													#Career not valid
													$this -> errors -> not_valid_format($_GET['career'],"Career");
												}
											}
											else {
												#Career not input
												$this -> errors -> not_found_input("Career");
											}
										}
										else {
											#Student id not valid
											$this -> errors -> not_valid_format($_GET['studentid'],"Student ID");
										}
									}
									else {
										#Student ID not input
										$this -> errors -> not_found_input('Student ID');
									}
								}
								else {
									#E-mail is not valid
									$this -> errors -> not_valid_format($_GET['email'],"E-mail");
								}
							}
							else {
								#Correo was not input
								$this -> errors -> not_found_input('E-mail');
							}
						}
						else {
							#Full name is not valid
							$this -> errors -> not_valid_format(($_GET['name'].' '.$_GET['first'].' '.$_GET['second']),'Nombre Completo');
						}
					}
					else {
						#Name was not input
						$this -> errors -> not_found_input('Full Name');
					}
					}
					else if ($session == false) {
						$this -> errors -> not_logged_in();
					}
					else {
						$this -> errors -> not_valid_usertype();
					}
					break;
				
				case 'modifystatus' :
					#Check if session is active
					$session = $this -> validation -> active_session();
					#Check account privileges
					if ($session >= 3) {
						#User is allowed to execute action
						#Check if student ID exists
						if (isset($_GET['studentid'])) {
							#Validate Student ID
							if ($this -> validation -> validate_sid($_GET['studentid'])) {
								#Check if value exists
								if (isset($_GET['value'])) {
									#Validate value
									$value = $this -> validation -> validate_studentstatus($_GET['value']);
									if ($value == 2) {
										$this -> errors -> not_valid_format($_GET['value'],'Status Value');
										die();
									}
									$student = $this -> obj_mdl -> modify_status(strtoupper($_GET['studentid']),$value);
									if (is_array($student)) {
										#Set action
										$student['action'] = "Status Modified";
										#Get the view
										require('Views/studentview.php');
										return;
									}
									else {
										#Could not modify Student Status
										$this -> errors -> not_modify_student_status($_GET['studentid']);
									}
								}
								else {
									#Status Value was not input
									$this -> errors -> not_found_input('Status Value');
								}
							}
							else {
								#Student ID is not valid
								$this -> errors -> not_valid_format($_GET['studentid'],'Student ID');
							}
						}
						else {
							#Student ID was not input
							$this -> errors -> not_found_input('Student ID');
						}
					}
					else if ($session == false) {
						#Session is not started
						$this -> errors -> not_logged_in();
					}
					else {
						#User is not allowed to execute action
						$this -> errors -> not_valid_usertype();
					}
					break;

				case 'list':
					#Check if session is active
					$session = $this -> validation -> active_session();
					#Check account privileges
					if ($session >= 1) {
						if ($session > 1) {
							if (isset($_GET['studentid'])) {
								if ($this -> validation -> validate_userid($_GET['studentid']) == 1) {
									$student_id = $_GET['studentid'];	
								}
								else {
									$this -> errors -> not_valid_userid('Student');
									die();
								}
							}
							else {
								$this -> errors -> not_found_input('Student ID');
								die();
							}
						}
						else
							$student_id = $_SESSION['userid'];
						#User is allowed to execute action
					#Check if cicle exists
					if (isset($_GET['cicle'])) {
						#Validate if cicle is valid
						if ($this -> validation -> validate_cicle($_GET['cicle'])) {
									#Check if NRC exists
									if (isset($_GET['nrc'])) {
										if ($this -> validation -> validate_nrc($_GET['nrc'])) {
											$course_info = $this -> obj_mdl -> view_student_course($_GET['studentid'],$_GET['cicle'],$_GET['nrc']);
											if (is_array($course_info)) {
												#Get the View
												require('Views/student_courseview.php');
											}
											else {
												#Could not find student
												$this -> errors -> student_not_found($_GET['studentid']);
											}
										}
										else {
											#Course NRC is not valid
											$this -> errors -> not_valid_input($_GET['nrc'],'NRC');
										}
									}
									else {
										#Get the model
										$grades_info = $this -> obj_mdl -> view_student_grades($_GET['studentid'],$_GET['cicle']);
										if (is_array($grades_info)) {
											#Get the View
											require('Views/students_gradesview.php');
										}
										else {
											#Could not find student
											$this -> errors -> student_not_found($_GET['studentid']);
										}
									}
								}
								else {
									#Cicle is not valid
									$this -> errors -> not_valid_format($_GET['cicle'],'cicle');
								}
							}
							else {
								#Cicle was not input
								$this -> errors -> not_found_input('Cicle');
							}
						}
						else if ($session == false) {
							#Session is not started
							$this -> errors -> not_logged_in();
						}
						else {
							#User is not allowed to execute action
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
			$this -> errors -> not_found_input('Activity');
		}
	}
}



?>