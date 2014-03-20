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
		var_dump($_SESSION);
		echo '</br>';
		session_start();
		var_dump($_SESSION);
		echo '</br>';
		if (isset($_GET['act'])) {
			switch ($_GET['act']) {
				case 'create':
					#Check if cicle exists
					if (isset($_GET['cicle'])) {
						#Validate if cicle is correct
						if ($this -> validation -> validate_cicle($_GET['cicle'])) {
							#Check if the subject name exists
							if (isset($_GET['subject'])) {
								#Validate if the subject name is valid
								if ($this -> validation -> validate_subject($_GET['subject'])) {
									#Check if the NRC exists
									if (isset($_GET['nrc'])) {
										#Validate if the nrc is valid
										if ($this -> validation -> validate_nrc($_GET['nrc'])) {
											#Check if section exists
											if (isset($_GET['section'])) {
												#Validate if the section is correct
												if ($this -> validation -> validate_section($_GET['section'])) {
													#Check if the course schedule is correct
													#Check if course days and hours exists
													if (isset($_GET['days']) && isset($_GET['hours']) && isset($_GET['schedule'])) {
														if ($this -> validation -> validate_schedule($_GET['days'],$_GET['hours'],$_GET['schedule'])) {
															#All the course data has been validated correctly 
															#Get the model
															require('Models/courseMdl.php');
															#Create the course array and set the values
															$course_array = array( "subject" => $_GET['subject'],
																		     		"cicle" => $_GET['cicle'],
																			 	    "section" => $_GET['section'],
																					"nrc" => $_GET['nrc']);
															#Separate days elements and add them to course array
															foreach ($_GET['days'] as $key => $value) {
																$aux_array[] = $value;
															}
															$course_array['days'] = $aux_array;
															$aux_array = array();
															#Separate hours elements and add them to course array
															foreach ($_GET['hours'] as $key => $value) {
																$aux_array[] = $value;
															}
															$course_array['hours'] = $aux_array;
															$aux_array = array();
															#Separate schedule elements and add them to course array
															foreach ($_GET['schedule'] as $key => $value) {
																$aux_array[] = $value;
															}
															$course_array['schedule'] = $aux_array;
															#Create the Model object
															$course_obj = new courseMdl();
															#Callback to the add function, sending the array created as a parameter
															if ($course_obj -> add_course($course_array)) {
																#Get the view
																require('Views/courseview.php');
															}
															else {
																#Error adding course
																$this -> errors -> error_add_course($_GET['subject']);
															}
														}
														else {
															#Class schedule is incorrect
															$this -> errors -> not_valid_schedule();
														}
													}
													else {
														#Class schedule is incorrect
														$this -> errors -> not_valid_schedule();
													}
												}
												else {
													#Section is not valid
													$this -> errors -> not_valid_format($_GET['section'],'Section');
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
									$this -> errors -> not_valid_format($_GET['subject'],'Subject Name');
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
										if (isset($_GET['section'])) {
											#Validate if the section is correct
											if ($this -> validation -> validate_section($_GET['section'])) {
												#All the course data has been validated correctly 
												#Get the model
												require('Models/courseMdl.php');
												#Create the course array and set the values
												$course_array = array( "cicle" => $_GET['cicle'],
															 	       "section" => $_GET['section'],
																	   "nrc" => $_GET['nrc']);
												$course_array['subject'] = 'Matematicas II';
												$course_array['days'] = array('1','3','5');
												$course_array['hours'] = array('2','2','1');
												$course_array['schedule'] = array('1100:1255','1100:1255','1100:1155');
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
												$this -> errors -> not_valid_format($_GET['section'],'Section');
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

				case 'addstudent':
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
									$student = $mdl_obj -> add_student_to_course($_GET['studentid'],$_GET['courseid']);
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

				case 'addfield':
					#Check if course exists
					if (isset($_GET['courseid'])) {
						#Check if Course ID is valid
						if ($this -> validation -> validate_courseid($_GET['courseid'])) {
							#Check Field exists
							if (isset($_GET['field'])) {
								#Check if Field is valid
								if ($this -> validation -> validate_field($_GET['field'])) {
									#Check if percentage exists
									if (isset($_GET['percentage'])) {
										#Check if percentage is valid
										if ($this -> validation -> validate_percentage($_GET['percentage'])) {
											#Get the model
											require('Models/courseMdl.php');
											#Create the model object
											$mdl_obj = new courseMdl();
											#Create array to send to the add field function
											$field_array = array ("courseid" => $_GET['courseid'],
																  "field" => $_GET['field'],
																  "percentage" => $_GET['percentage']);
											$field_array = $mdl_obj -> add_field_to_course($field_array);
											if (is_array($field_array)) {
												#Field was added to Course
												#Get the view
												require('Views/field_added_courseview.php');
											}
											else {
												#Could not add field to course
												$this -> errors ->  error_add_field($_GET['courseid']);
											}
										}
										else {
											#Percetange is not valid
											$this -> errors -> not_valid_format($_GET['percentage'],'percentage');
										}
									}
									else {
										#Percentage was not input
										$this -> errors -> not_found_input('percentage');
									}
								}
								else {
									#Field is not valid
									$this -> errors -> not_valid_format($_GET['field'],'Field');
								}
							}
							else {
								#Field was not input
								$this -> errors -> not_found_input('Field');
							}
						}
						else {
							#Course ID is not valid
							$this -> errors -> not_valid_format($_GET['courseid'],'Course ID');
						}
					}
					else {
						#Course was not input
						$this -> errors -> not_found_input('Course ID');
					}
 					break;

 				case 'addsheet':
 					#Check if Course ID exists
 					if (isset($_GET['courseid'])) {
 						#Validate Course ID
 						if ($this -> validation -> validate_courseid($_GET['courseid'])) {
 							#Check if Field exists
 							if (isset($_GET['field'])) {
 								#Validate Field
 								if ($this -> validation -> validate_field($_GET['field'])) {
 									#Get the Model
 									require('Models/courseMdl.php');
 									#Create the model object
 									$mdl_obj = new courseMdl();
 									#Create array and send it to Model
 									$sheet_array = array("field" => $_GET['field'], "courseid" => $_GET['courseid']);
 									$sheet_array = $mdl_obj -> add_sheet_to_course($sheet_array);
 									if (is_array($sheet_array)) {
 										#Get the view
 										require('Views/add_sheet_course.php');
 									}
 									else {
 										#Failed to add sheet
 										$this -> errors -> error_add_sheet();
 									}
 								}
 								else {
 									#Field is not valid
 									$this -> errors -> not_valid_format($_GET['field'],'Field');
 								}
 							}
 							else {
 								#Field was not input
			 					$this -> errors -> not_found_input('Field');
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

 				case 'attendance':
 					#Check if Course ID exists
 					if (isset($_GET['courseid'])) {
 						#Validate Course ID
 						if ($this -> validation -> validate_courseid($_GET['courseid'])) {
 							#Check if Value exists
 							if (isset($_GET['value'])) {
 								#Validate value
 								if ($this -> validation -> validate_attendance($_GET['value']) != 2) {
 									$value = $this -> validation -> validate_attendance($_GET['value']);
 									#Check if Student ID exists, can be more than one
 									if (isset($_GET['studentid'])) {
 										#Get the model
 										require('Models/courseMdl.php');
 										#Create the Model object
 										$mdl_obj = new courseMdl();
 										#Validated Student's ID
 										$studentsid_aray = array();
 										foreach ($_GET['studentid'] as $key => $SID) {
 											if ($this -> validation -> validate_sid($SID))
 												$studentsid_aray[] = $SID;
 										}
 										#Found Students
 										$studentsid_aray = $mdl_obj -> check_studentsid($studentsid_aray,$_GET['courseid']);
 										if (sizeof($studentsid_aray) == 0) {
 											#No students found from student's ID
 											$this -> errors -> notstudents_att();
 											return;
 										}
 										if ($value == 1) {
 											require('Views/attendance_putview.php');
 										}
 										else if ($value == 0) {
 											require('Views/attendance_removeview.php');
 										}
 										return;
 									}
 									else {
 										#Students ID where not input
 										$this -> errors -> not_found_input('Student(s) ID');
 									}
 								}
 								else {
 									#Value is not valid
 									$this -> errors -> not_valid_format($_GET['value'],'Value');
 								}
 							}	
 							else {
 								#Value was not input
 								$this -> errors -> not_found_input('Value');
 							}
 						}
 						else {
 							#Course ID is not valid
 							$this -> errors ->not_valid_format($_GET['courseid'],'Course ID');
 						}
 					}
 					else {
 						#Course ID was not input
 						$this -> errors -> not_found_input('Course ID');
 					}
 					break;	

 				case 'capture':
 					#Check if Student ID exists
 					if (isset($_GET['studentid'])) {
 						#Validate Student ID
 						if ($this -> validation -> validate_sid($_GET['studentid'])) {
 							#Check if field exists
 							if (isset($_GET['field'])) {
 								#Validate Field
 								if ($this -> validation -> validate_field($_GET['field'])) {
 									#Check if grade exists
 									if (isset($_GET['grade'])) {
 										#Validate grade
 										if ($this -> validation -> validate_grade($_GET['grade'])) {
 											#Check if Course ID exists
 											if (isset($_GET['courseid'])) {
 												#Validate if Course ID is correct
 												if ($this -> validation -> validate_courseid($_GET['courseid'])) {
 													#Get the model
 													require('Models/courseMdl.php');
 													#Create the model object
 													$mdl_obj = new courseMdl();
 													#Create array to send to the add field function
													$field_array = array ("studentid" => $_GET['studentid'],
																		  "field" => $_GET['field'],
																		  "grade" => $_GET['grade']);
													$field_array = $mdl_obj -> add_grade_to_field($field_array);
													if (is_array($field_array)) {
														#Get the view
														require('Views/capture_gradeview.php');
													}
													else {
														#Failed to capture grade
														$this -> errors -> error_capture_grade();
													}
 												}
 												else {
 													#Course ID is not valid
 													$this -> errors -> not_valid_format($_GET['courseid'],'Course ID');
 												}
 											}
 											else {
 												#Course ID was not iput
 												$this -> errors -> not_found_input('Course ID');
 											}
 										}
 										else {
 											#Grade is not valid
 											$this -> errors -> not_valid_format($_GET['grade'],'Grade');
 										}
 									}
 									else {
 										#Grade was not input
 										$this -> errors -> not_found_input('Grade');
 									}
 								}
 								else {
 									#Field is not valid
 									$this -> errors -> not_valid_format($_GET['field'],'Field');
 								}
 							}
 							else {
 								#Field was not input
 								$this -> errors -> not_found_input('Field');
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