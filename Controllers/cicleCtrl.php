<?php

class cicleCtrl {

	function __construct() {
		#Constructor
		require('Models/cicleMdl.php');
		#Create Model object
		$this -> cicleMdl = new cicleMdl();
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
						#Check if cicle exists
						if (isset($_GET['cicle'])) {
							#Validate if cicle is correct
							if ($this -> validation -> validate_cicle($_GET['cicle'])) {
								#The cicle format is correct
								#Check if both start and end of cicle dates exists
								if (isset($_GET['begindate']) && isset($_GET['enddate'])) {
									#Validate if dates are correct
									$fechainicial = $this -> validation -> validate_date($_GET['begindate']);
									$fechafinal = $this -> validation -> validate_date($_GET['enddate']);
									if (!($this -> validation -> compare_dates($fechainicial,$fechafinal))) {
										$this -> errors -> date_range_fail();
										return;
									}

									if ( (!is_bool($fechainicial)) && (!is_bool($fechafinal))) {
										#Check if non working days exists
										if (isset($_GET['nonworking'])) {
											$non_workingarray = array();
											if (is_array($_GET['nonworking'])) {
												while (list($key,$date) = each($_GET['nonworking'])) {
													$date = $this->validation -> validate_date($date);
													if (!is_bool($date)) {
														$non_workingarray[] = $date;
													}
													else {
														$this -> validation -> day_not_valid($date);
														return;
													}
												}
											}
											else {
												$date = $this -> validation -> validate_date($_GET['nonworking']);
												if (!is_bool($date)) {
												$non_workingarray[] = $date;
												}
												else {
													$this -> validation -> day_not_valid($date);
													return;
												}
											}
										}
										#Send the data to the model
										$ciclo = $_GET['cicle'];
										if ($this -> cicleMdl -> add_cicle($ciclo)) {
											#Cicle created correctly
											require('Views/cicleview.php');
										}
										else {
											#Cicle creation failed
											$errors -> error_add_cicle($_GET['cicle']); 
										}
										return;
									}
									else {
										#At least one date is not valid
										$this -> errors -> not_valid_date();
									}
								}
								else {
									#At least one date is missing
									$this -> errors -> not_found_input('Cicle Date(s)');
								}
							}
							else {
								#Cicle format is incorrect
								$this -> errors -> not_valid_format($_GET['cicle'],'cicle');
							}
						}
						else {
							#Cicle was not input
							$this -> errors -> not_found_input('Cicle');
							return;
						}
					}
					else if ($session == false) {
						$this -> errors -> not_logged_in();
					}
					else {
						$this -> errors -> not_valid_usertype();
					}
					break;
		
			case 'modify':
					$session = $this -> validation -> active_session();
					if ($session >= 3) {
						#Check if cicle exists
						if (isset($_GET['cicle'])) {
							#Validate cicle
							if ($this -> validation -> validate_cicle($_GET['cicle'])) {
								#Check if status modifier exists
								if (isset($_GET['status'])) {
									#Validate status
									$value = $this -> validation ->  validate_ciclestatus($_GET['status']);
									if ($value == 0) {
										#Send data to the model
										$return_value = $this -> cicleMdl -> modify_status($_GET['cicle'],$_GET['status']);
										#Create the array
										$status_array = array("status" => $_GET['status'],
															  "cicle" => $_GET['cicle']);
										if ($return_value) {
											#Get the View
											require('Views/modify_cicle_statusview.php');
										}
										else {
											$this -> errors -> not_modify_cicle($_GET['cicle']);
										}
									}
									else {
										#Status modifier is not valid
										$this -> errors -> not_valid_format($_GET['status'],'Status');
										return;
									}
								}
								else {
									#Status Modifier was not input
									$this -> errors -> not_found_input('Status');
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