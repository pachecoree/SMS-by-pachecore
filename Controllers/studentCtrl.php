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
					#Create student array
					$student = array();
					$bandera_datos = 0;
					#Check if the name is valid
					if (isset($_GET['paterno']) && isset($_GET['materno']) && isset($_GET['nombre'])) {
						#Validate full name
						if ($this -> validation -> validate_name(($_GET['nombre'].' '.$_GET['paterno'].' '.$_GET['materno']))) {
							#Check if e-mail exists
							if (isset($_GET['correo'])) {
								#Validate e-mail
								if ($this -> validation -> validate_email($_GET['correo'])) {
									#Check if Student ID exists
									if (isset($_GET['codigo'])) {
										#Validate Student ID
										if ($this -> validation -> validate_sid($_GET['codigo'])) {
											#Check if Phonenumber exists
											if (isset($_GET['celular'])) {
												#Validate Phonenumber
												if ($this -> validation -> validate_phonenumber($_GET['celular'])) {
													$student['celular'] = $_GET['celular'];
												}
												else {
													#Phonenumber not valid
													$bandera_datos = 1;
													$this -> errors -> not_valid_format($_GET['celular'],"Phone Number");
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
													$student['webpage'] = $_GET['web'];
												}
												else {
													#Web Page not valid
													$bandera_datos = 1;
													$this -> errors -> not_valid_format($_GET['web'],"Web Page");
												}
											}
											#All data has been validated
											if ($bandera_datos == 0) {
												#Get the model
												require('Models/studentMdl.php');
												$mdl_object = new studentMdl();
												if (is_array($mdl_object -> add_student($student))) {
													#Get the View
													require('Views/studentview.php');
												}
												else {
													#Display the add error
													$this -> errors -> error_add_student($student['paterno'].' '.$student['materno'].' '. $student['nombre']);
												}
											}
										}
										else {
											#Student id not valid
											$this -> errors -> not_valid_format($_GET['codigo'],"Student ID");
										}
									}
									else {
										#Student ID not input
										$this -> errors -> not_found_input('Student ID');
									}
								}
								else {
									#E-mail is not valid
									$this -> errors -> not_valid_format($_GET['correo'],"E-mail");
								}
							}
							else {
								#Correo was not input
								$this -> errors -> not_found_input('E-mail');
							}
						}
						else {
							#Full name is not valid
							$this -> errors -> not_valid_format(($_GET['nombre'].' '.$_GET['paterno'].' '.$_GET['materno']),'Nombre Completo');
						}
					}
					else {
						#Name was not input
						$this -> errors -> not_found_input('Full Name');
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