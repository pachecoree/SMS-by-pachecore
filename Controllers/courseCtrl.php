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
				case 'add':
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