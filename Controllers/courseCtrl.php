<?php

class courseCtrl {

	function __construct() {
		#Create errors object
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
				case 'create':
					#Check if cicle exists
					if (isset($_GET['cicle'])) {
						#Validate if cicle is correct
						if ($this -> validation -> validate_cicle($_GET['cicle'])) {
							#Check if the subject name exists
							if (isset($_GET['nombre'])) {
								#Validate if the subject name is valid
								if ($this -> validation -> validate_subject($_GET['nombre'])) {
									#Check if the NRC exists
									if (isset($_GET['nrc'])) {
										#Validate if the nrc is valid
										if ($this -> validation -> validate_nrc($_GET['nrc'])) {
											#Check if section exists
											if (isset($_GET['seccion'])) {
												#Validate if the section is correct
												if ($this -> validation -> validate_section($_GET['seccion'])) {
													#All the course data has been validated correctly 
													#Get the model
													require('Models/courseMdl.php');
													#Create the course array and set the values
													$course_array = array( "subject" => $_GET['nombre'],
															     		"cicle" => $_GET['cicle'],
																 	    "section" => $_GET['seccion'],
																		"nrc" => $_GET['nrc']);
													#Create the Model object
													$course_obj = new courseMdl();
													#Callback to the add function, sending the array created as a parameter
													if ($course_obj -> add_course($course_array)) {
														#Get the view
														require('Views/courseview.php');
													}
													else {
														#Error adding course
														$this -> errors -> error_add_course($_GET['nombre']);
													}
												}
												else {
													#Section is not valid
													$this -> errors -> not_valid_format($_GET['seccion'],'Section');
												}
											}
											else {
												#Section was not input
												$this -> errors -> not_found_input('Section');
											}
										}
										else {
											#NRC is not valid
											$this -> errors -> not_valid_format($_GET['nrc'],'NRC');
										}
									}
									else {
										#NRC was not input
										$this -> errors -> not_found_input('NRC');
									}
								}
								else {
									#Subject name is not valid
									$this -> errors -> not_valid_format($_GET['nombre'],'Subject Name');
								}
							}
							else {
								#Subject name was not input
								$this -> errors -> not_found_input('Subject Name');
							}
						}
						else {
							#Cicle format is incorrect
							$this -> errors -> not_valid_format($_GET['cicle'],'Cicle');
						}
					}
					else {
						#Cicle was not input
						$this -> errors -> not_found_input('Cicle');
					}
					break;
				
				case 'clone':
					#Check if cicle exists
					if (isset($_GET['cicle'])) {
						#Validate if cicle is correct
						if ($this -> validation -> validate_cicle($_GET['cicle'])) {
							#Check if the subject name exists
								if (isset($_GET['nrc'])) {
									#Validate if the nrc is valid
									if ($this -> validation -> validate_nrc($_GET['nrc'])) {
										#Check if section exists
										if (isset($_GET['seccion'])) {
											#Validate if the section is correct
											if ($this -> validation -> validate_section($_GET['seccion'])) {
												#All the course data has been validated correctly 
												#Get the model
												require('Models/courseMdl.php');
												#Create the course array and set the values
												$course_array = array( "cicle" => $_GET['cicle'],
															 	       "section" => $_GET['seccion'],
																	   "nrc" => $_GET['nrc']);
												#Create the Model object
												$course_obj = new courseMdl();
												#Callback to the add function, sending the array created as a parameter
												if ($course_obj -> add_course($course_array)) {
													#Get the view
													require('Views/courseview.php');
												}
												else {
													#Error adding course
													$this -> errors -> error_add_course($_GET['nombre']);
												}
											}
											else {
												#Section is not valid
												$this -> errors -> not_valid_format($_GET['seccion'],'Section');
											}
										}
										else {
											#Section was not input
											$this -> errors -> not_found_input('Section');
										}
									}
									else {
										#NRC is not valid
										$this -> errors -> not_valid_format($_GET['nrc'],'NRC');
									}
								}
								else {
									#NRC was not input
									$this -> errors -> not_found_input('NRC');
						}
					}
					else {
						#Cicle format is incorrect
						$this -> errors -> not_valid_format($_GET['cicle'],'Cicle');
					}
				}
				else {
					#Cicle was not input
					$this -> errors -> not_found_input('Cicle');
				}
					break;

				case 'list_a':
					#Check if course exists
					if (isset($_GET['courseid'])) {
						if ($this -> validation -> validate_courseid($_GET['courseid'])) {
							#Get the model
							require('Models/courseMdl.php');
							$mdl_obj = new courseMdl();
							#Callback to the view function
							$attendance_array = $mdl_obj -> view_course_attendance($_GET['courseid']);
							if (is_array($attendance_array)) {
								#Get the View
								require('Views/attendance_listview.php');
							}
							else {
								#The course was not found
								$this -> errors -> error_query_list($attendance_array);
							}
						}
						else {
							#Course ID is not valid
							$this -> errors -> not_valid_format($_GET['courseid'],'Course ID');
						}
					}
					else {
						#Course ID was not input
						$this -> errors -> not_found_input('Course ID');
					}
					break;

				case 'list_c':
					#Check if course exists
					if (isset($_GET['courseid'])) {
						if ($this -> validation -> validate_courseid($_GET['courseid'])) {
							#Get the model
							require('Models/courseMdl.php');
							$mdl_obj = new courseMdl();
							#Callback to the view function
							$grade_array = $mdl_obj -> view_course_grade($_GET['courseid']);
							if (is_array($grade_array)) {
								#Get the View
								require('Views/grade_listview.php');
							}
							else {
								#The course was not found
								$this -> errors -> error_query_list($grade_array);
							}
						}
						else {
							#Course ID is not valid
							$this -> errors -> not_valid_format($_GET['courseid'],'Course ID');
						}
					}
					else {
						#Course ID was not input
						$this -> errors -> not_found_input('Course ID');
					}
					break;

				case 'add':
					#Check if course exists
					if (isset($_GET['courseid'])) {
						#Validate Course
						if ($this -> validation -> validate_courseid($_GET['courseid'])) {
							#Check if Student ID exists
							if (isset($_GET['studentid'])) {
								#Validate Student ID
								if ($this -> validation -> validate_sid($_GET['studentid'])) {
									#Get the model
									require('Models/courseMdl.php');
									$mdl_obj = new courseMdl();
									#Send code to model
									$student = $mdl_obj -> add_student_to_course($_GET['studentid']);
									if (is_array($student)) {
										#Get the view
										require('Views/student_added_course.php');
									}
									else if (is_string($student)) {
										#Failed to add student to course
										$this -> errors -> error_add_student_course($student);
									}
									else {
										#Could not find student
										$this -> errors -> student_not_found($_GET['studentid']);
									}
								}
								else {
									#Student ID not valid
									$this -> errors -> not_valid_format($_GET['studentid'],'Student ID');
								}
							}
							else {
								#Student ID not input
								$this -> errors -> not_found_input("Student ID");
							}
						}
						else {
							#Course is not valid
							$this -> errors -> not_valid_format($_GET['courseid'],'Course ID');
						}
					}
					else {
						#Course was not input
						$this -> errors -> not_found_input('Course ID');
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