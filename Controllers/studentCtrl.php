<?php

class studentCtrl {

	function __construct() {
		#Create the errors object
		require('Views/errors.php');
		$this -> errors = new errors();
		#Create the validation object
		require('Controllers/validationCtrl.php');
		$this -> validation = new validationCtrl();
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
												$student['name'] = $_GET['name'].' '.$_GET['first'].' '.$_GET['second'];
												$student['studentid'] = $_GET['studentid'];
												$student['email'] = $_GET['email'];
												$student['career'] = 'Computacion';
												#Get the model
												require('Models/studentMdl.php');
												$mdl_object = new studentMdl();
												$student = $mdl_object -> add_student($student);
												if (is_array($student)) {
													#Get the View
													require('Views/studentview.php');
												}
												else {
													#Display the add error
													$this -> errors -> error_add_student($student['first'].' '.$student['second'].' '. $student['nombre']);
												}
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
										return;
									}
									else if ($value == 0) {
										#Change status to "INACTIVO"
										#Get the model
										require('Models/studentMdl.php');
										#Create Model object
										$mdl_obj = new studentMdl();
										#Send to the model
										$student_array = $mdl_obj -> modify_status($_GET['studentid'],"INACTIVO");
									}
									else if ($value == 1) {
										#Change status to "ACTIVO"
										#Get the model
										require('Models/studentMdl.php');
										#Create Model object
										$mdl_obj = new studentMdl();
										#Send to the model
										$student_array = $mdl_obj -> modify_status($_GET['studentid'],"ACTIVO");
									}
									if (is_array($student_array)) {
										#Get the view
										require('Views/modify_student_statusview.php');
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
					#Check if cicle exists
					if (isset($_GET['cicle'])) {
						#Validate if cicle is valid
						if ($this -> validation -> validate_cicle($_GET['cicle'])) {
							#Check if Student ID exists
							if (isset($_GET['studentid'])) {
								#Validate if Student ID is valid
								if ($this -> validation -> validate_sid($_GET['studentid'])) {
									#Check if Course ID exists
									if (isset($_GET['courseid'])) {
										if ($this -> validation -> validate_courseid($_GET['courseid'])) {
											#Get the model
											require('Models/studentMdl.php');
											$mdl_obj = new studentMdl();
											$course_info = $mdl_obj -> view_student_course($_GET['studentid'],$_GET['courseid']);
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
											#Course ID is not valid
											$this -> errors -> not_valid_input($_GET['courseid'],'Course ID');
										}
									}
									else {
										#Get the model
										require('Models/studentMdl.php');
										$mdl_obj = new studentMdl();
										$grades_info = $mdl_obj -> view_student_grades($_GET['studentid'],$_GET['cicle']);
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
									#Student ID is not valid
									$this -> errors -> not_valid_format($_GET['studentid'],'Student ID');
								}
							}
							else {
								#Student ID was not input
								$this -> errors -> not_found_input('Student ID');
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