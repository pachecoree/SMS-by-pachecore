<?php

class courseCtrl {

	function __construct($driver) {
		#Create errors object
		require('Views/errors.php');
		$this -> errors = new errors();
		#Create the validation object
		require('Controllers/validationCtrl.php');
		$this -> validation = new validationCtrl();
		#Create Model object
		require('Models/courseMdl.php');
		$this -> mdl_obj = new courseMdl($driver);
	}

	function run() {
			#Check if the activity was input
			if (isset($_GET['act'])) {
				switch ($_GET['act']) {
					case 'create':
					$session = $this -> validation -> active_session();
					if ($session >= 2) {
						if ($session == 3) {
							if (isset($_GET['teacher_id'])) {
								if ($this -> validation -> validate_userid($_GET['teacher_id']) == 2) {
									$teacher_id = $_GET['teacher_id'];	
								}
								else {
									$this -> errors -> not_valid_userid('Teacher');
									die();
								}
							}
							else {
								$this -> errors -> not_found_input('Teacher ID');
								die();
							}
						}
						else
							$teacher_id = $_SESSION['userid'];
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
													if ((is_array($_GET['days']) && is_array($_GET['hours']) && is_array($_GET['schedule'])) && ($this -> validation -> validate_schedule($_GET['days'],$_GET['hours'],$_GET['schedule']))) {
														#Create the course array and set the values
														$course_array = array( "subject" => strtoupper($_GET['subject']),
																		 	   "teacher_id" => strtoupper($teacher_id), 
																		 	   "section" => strtoupper($_GET['section']),
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
														#Callback to the add function, sending the array created as a parameter
														if (is_array($course_array = $this -> mdl_obj -> add_course($course_array))) {
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
													$this -> errors -> not_found_input('Schedule');
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
					else if ($session == false) {
						$this -> errors -> not_logged_in();
					}
					else {
						$this -> errors -> not_valid_usertype();
					}
					break;
				
				case 'clone':
					$session = $this -> validation -> active_session();
					if ($session >= 2) {
						if ($session == 3) {
							if (isset($_GET['teacher_id'])) {
								if ($this -> validation -> validate_userid($_GET['teacher_id']) == 2) {
									$teacher_id = $_GET['teacher_id'];	
								}
								else {
									$this -> errors -> not_valid_userid('Teacher');
									die();
								}
							}
							else {
								$this -> errors -> not_found_input('Teacher ID');
								die();
							}
						}
						else
							$teacher_id = $_SESSION['userid'];
						#Check if cicle_c exists
						if (isset($_GET['cicle_c'])) {
							#Validate if cicle_c is correct
							if ($this -> validation -> validate_cicle($_GET['cicle_c'])) {
								#Check if the NRC exists
									if (isset($_GET['nrc'])) {
										#Validate if the nrc is valid
										if ($this -> validation -> validate_nrc($_GET['nrc'])) {
											#Check if section exists
											if (isset($_GET['section'])) {
												#Validate if the section is correct
												if ($this -> validation -> validate_section($_GET['section'])) {
													#Check if nrc_c exists
													if (isset($_GET['nrc_c'])) {
														#Validate if nrc_c is correct
														if ($this -> validation -> validate_nrc($_GET['nrc_c'])) {
															#Check if subject exists
															if (isset($_GET['subject'])) {
																#Validate subject
																if ($this -> validation -> validate_subject($_GET['subject'])) {
																	#Create the course array and set the values
																	$course_array = array( "ciclo_anterior" =>strtoupper($_GET['cicle_c']),
																				 	       "seccion" => strtoupper($_GET['section']),
																				 	       "clave_materia" => strtoupper($_GET['subject']),
																						   "nrc_anterior" => $_GET['nrc_c'],
																						   "clave_maestro" => strtoupper($teacher_id),
																						   "nrc" => $_GET['nrc']);

																	#Callback to the add function, sending the array created as a parameter
																	$course_array = $this -> mdl_obj -> clone_course($course_array);
																	if (is_array($course_array)) {
																		#Get the view
																		require('Views/courseview.php');
																	}
																	else {
																		#Error adding course
																		$this -> errors -> error_add_course($_GET['subject']);
																	}
																}
																else {
																	#Subject is not valid
																	$this -> errors -> not_valid_format($_GET['subject'],'Subject ID');
																}
															}
															else {
																#Subject was not input
																$this -> errors -> not_found_input('Subject ID');
															}
														}
														else {
															#NRC_c is not valid
															$this -> errors -> not_valid_format($_GET['nrc_c'],'NRC_c');
														}
													}
													else {
														#NRC_c was not input
														$this -> errors -> not_found_input('NRC_c');
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
									#Cicle_cc format is incorrect
									$this -> errors -> not_valid_format($_GET['cicle_c'],'Cicle');
								}
							}
							else {
								#Cicle_c was not input
								$this -> errors -> not_found_input('Cicle');
							}
						}
						else if ($session == false) {
							$this -> errors -> not_logged_in();
						}
						else {
							$this -> errors -> not_valid_usertype();
						}
					break;

				case 'list_a':
					#Check if session is active
					$session = $this -> validation -> active_session();
					#Check account privileges
					if ($session >= 2) {
						#User is allowed to execute action
					#Check if course exists
					if (isset($_GET['nrc'])) {
						if ($this -> validation -> validate_courseid($_GET['nrc'])) {
							#Callback to the view function
							$attendance_array = $this -> mdl_obj -> view_course_attendance($_GET['nrc']);
							if (is_array($attendance_array)) {
								#Get the View
								require('Views/attendance_listview.php');
							}
							else {
								#The course was not found
								$this -> errors -> error_query_list($_GET['nrc']);
							}
						}
						else {
							#Course ID is not valid
							$this -> errors -> not_valid_format($_GET['nrc'],'NRC');
						}
					}
					else {
						#Course ID was not input
						$this -> errors -> not_found_input('NRC');
					}
					}
					else if ($session == false) {
						$this -> errors -> not_logged_in();
					}
					else {
						$this -> errors -> not_valid_usertype();
					}
					break;

				case 'list_c':
					#Check if session is active
					$session = $this -> validation -> active_session();
					#Check account privileges
					if ($session >= 2) {
						#User is allowed to execute action
					#Check if course exists
					if (isset($_GET['courseid'])) {
						if ($this -> validation -> validate_courseid($_GET['courseid'])) {
							#Callback to the view function
							$grade_array = $this -> mdl_obj -> view_course_grade($_GET['courseid']);
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
					}
					else if ($session == false) {
						$this -> errors -> not_logged_in();
					}
					else {
						$this -> errors -> not_valid_usertype();
					}
					break;

				case 'addstudent':
					$session = $this -> validation -> active_session();
					if ($session >= 2) {
						if ($session == 3) {
							if (isset($_GET['teacher_id'])) {
								if ($this -> validation -> validate_userid($_GET['teacher_id']) == 2) {
									$teacher_id = $_GET['teacher_id'];	
								}
								else {
									$this -> errors -> not_valid_userid('Teacher');
									die();
								}
							}
							else {
								$this -> errors -> not_found_input('Teacher ID');
								die();
							}
						}
						else
							$teacher_id = $_SESSION['userid'];
						#Check if NRC exists
							if (isset($_GET['nrc'])) {
								#Validate NRC
								if ($this -> validation -> validate_nrc($_GET['nrc'])) {
									#Check if Student ID exists
									if (isset($_GET['studentid'])) {
										#Validate Student ID
										if ($this -> validation -> validate_sid($_GET['studentid'])) {
											#Send code to model
											$student =$this -> mdl_obj -> add_student_to_course($_GET['studentid'],$_GET['nrc'],$teacher_id);
											if (is_array($student)) {
												#Get the view
												require('Views/student_added_course.php');
											}
											else if ($student == 1) {
												#Failed to add student to course
												$this -> errors -> error_add_student_course($_GET['studentid']);
											}
											else if ($student == 2) {
												#Student is already in course
												$this -> errors -> student_in_course($_GET['studentid']);
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
									#NRC is not valid
									$this -> errors -> not_valid_format($_GET['nrc'],'NRC');
								}
							}
							else {
								#NRC was not input
								$this -> errors -> not_found_input('NRC');
							}
						}
						else if ($session == false) {
							$this -> errors -> not_logged_in();
						}
						else {
							$this -> errors -> not_valid_usertype();
						}
					break;

				case 'addfield':
					#Check if session is active
					$session = $this -> validation -> active_session();
					#Check account privileges
					if ($session >= 2) {
						if ($session == 3) {
							if (isset($_GET['teacher_id'])) {
								if ($this -> validation -> validate_userid($_GET['teacher_id']) == 2) {
									$teacher_id = $_GET['teacher_id'];	
								}
								else {
									$this -> errors -> not_valid_userid('Teacher');
									die();
								}
							}
							else {
								$this -> errors -> not_found_input('Teacher ID');
								die();
							}
						}
						else
							$teacher_id = $_SESSION['userid'];
						#User is allowed to execute action
						#Check if NRC exists
						if (isset($_GET['nrc'])) {
							#Check if NRC is valid
							if ($this -> validation -> validate_nrc($_GET['nrc'])) {
								#Check Field exists
								if (isset($_GET['field'])) {
									#Check if Field is valid
									if ($this -> validation -> validate_field($_GET['field'])) {
										#Check if percentage exists
										if (isset($_GET['percentage'])) {
											#Check if percentage is valid
											if ($this -> validation -> validate_percentage($_GET['percentage'])) {
												#Check if nocol exists
												if (isset($_GET['nocol'])) {
													#Check if nocol is valid
													if ($this -> validation -> validate_nocol($_GET['nocol'])) {
														#Create array to send to the add field function
														$field_array = array ("nrc" => $_GET['nrc'],
																			  "field" => $_GET['field'],
																			  "percentage" => $_GET['percentage'],
																			  "nocol" => $_GET['nocol'],
																			  "teacher_id" => $teacher_id);
														$field_array = $this -> mdl_obj -> add_field_to_course($field_array);
														if (is_array($field_array)) {
															#Field was added to Course
															#Get the view
															require('Views/field_added_courseview.php');
														}
														else {
															#Could not add field to course
															$this -> errors ->  error_add_field($_GET['field']);
														}
													}
													else {
														#Nocol is not valid
														$this -> errors -> not_valid_format($_GET['nocol'],'Number of Columns');
													}
												}
												else {
													#Nocol was not input
													$this -> errors -> not_found_input('Number of Columns');
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
								#NRC  is not valid
								$this -> errors -> not_valid_format($_GET['nrc'],'NRC');
							}
						}
						else {
							#NRC was not input
							$this -> errors -> not_found_input('NRC');
						}
					}
					else if ($session == false) {
						$this -> errors -> not_logged_in();
					}
					else {
						$this -> errors -> not_valid_usertype();
					}
 					break;

 				case 'addsheet':
					#Disabling module
					$this -> errors -> module_disabled();
					die();
					#Check if session is active
					$session = $this -> validation -> active_session();
					#Check account privileges
					if ($session == 2) {
						#User is allowed to execute action
	 					#Check if Course ID exists
	 					if (isset($_GET['courseid'])) {
	 						#Validate Course ID
	 						if ($this -> validation -> validate_courseid($_GET['courseid'])) {
	 							#Check if Field exists
	 							if (isset($_GET['field'])) {
	 								#Validate Field
	 								if ($this -> validation -> validate_field($_GET['field'])) {
	 									#Create array and send it to Model
	 									$sheet_array = array("field" => $_GET['field'], "courseid" => $_GET['courseid']);
	 									$sheet_array = $this -> mdl_obj -> add_sheet_to_course($sheet_array);
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
					}
					else if ($session == false) {
						$this -> errors -> not_logged_in();
					}
					else {
						$this -> errors -> not_valid_usertype();
					}
 					break;

 				case 'attendance':
					#Check if session is active
					$session = $this -> validation -> active_session();
					#Check account privileges
					if ($session >= 2) {
						if ($session == 3) {
							if (isset($_GET['teacher_id'])) {
								if ($this -> validation -> validate_userid($_GET['teacher_id']) == 2) {
									$teacher_id = $_GET['teacher_id'];	
								}
								else {
									$this -> errors -> not_valid_userid('Teacher');
									die();
								}
							}
							else {
								$this -> errors -> not_found_input('Teacher ID');
								die();
							}
						}
						else
							$teacher_id = $_SESSION['userid'];
						#User is allowed to execute action
	 					#Check if NRC exists
	 					if (isset($_GET['nrc'])) {
	 						#Validate NRC
	 						if ($this -> validation -> validate_courseid($_GET['nrc'])) {
	 							#Check if Value exists
	 							if (isset($_GET['value'])) {
	 								#Validate value
	 								$value = $this -> validation -> validate_attendance($_GET['value']);
	 								if ($value != 2) {
	 									if (isset($_GET['day'])) {
	 										$date = $this -> validation -> validate_date($_GET['day']);
	 										if (!is_bool($date)) {
			 									#Check if Student ID exists, can be more than one
			 									if (isset($_GET['studentid'])) {
			 										#Validated Student's ID
			 										$studentsid_aray = array();
			 										foreach ($_GET['studentid'] as $key => $SID) {
			 											if ($this -> validation -> validate_sid($SID))
			 												$studentsid_aray[] = $SID;
			 										}
			 										#Found Students
			 										$studentsid_aray = $this -> mdl_obj -> check_studentsid($studentsid_aray,$_GET['nrc'],$value,date_format($date,'Y-m-d'),$teacher_id);
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
	 											#Date is not valid
	 											$this -> errors -> not_valid_date();
	 										}
	 									}
	 									else {
	 										#Days was not input
	 										$this -> errors -> not_found_input('Day');
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
	 							$this -> errors ->not_valid_format($_GET['nrc'],'NRC');
	 						}
	 					}
	 					else {
	 						#Course ID was not input
	 						$this -> errors -> not_found_input('NRC');
	 					}
					}
					else if ($session == false) {
						$this -> errors -> not_logged_in();
					}
					else {
						$this -> errors -> not_valid_usertype();
					}
 					break;	

 				case 'capture':
					#Check if session is active
					$session = $this -> validation -> active_session();
					#Check account privileges
					if ($session >= 2) {
						#User is allowed to execute action
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
	 													#Create array to send to the add field function
														$field_array = array ("studentid" => $_GET['studentid'],
																			  "field" => $_GET['field'],
																			  "grade" => $_GET['grade']);
														$field_array = $this -> mdl_obj -> add_grade_to_field($field_array);
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
					}
					else if ($session == false) {
						$this -> errors -> not_logged_in();
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