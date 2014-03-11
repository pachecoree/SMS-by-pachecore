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
	}

	function validate_cicle($cicle) {
		#Check if format was correctly input
		$pattern = '/^[2][0-9]{3}[a-z]$/i';
		if (preg_match($pattern,$cicle) == 1) {
			return true;
		}
		#Cicle format is incorrect
		return false;
	}

	function run() {
		#Check if the activity was input
		if (isset($_GET['act'])) {
			switch ($_GET['act']) {
				case 'add':
					#Check if cicle exists
					if (isset($_GET['cicle'])) {
						#Validate if cicle is correct
						if ($this -> validate_cicle($_GET['cicle'])) {
							#The cicle format is correct
							#The cicle begin and end dates and the non working days will be selected
							
							#Send the data to the model
							if ($this -> cicleMdl -> add_cicle($_GET['cicle'])) {
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
							#Cicle format is incorrect
							$this -> errors -> not_valid_format($_GET['cicle'],'cicle');
						}
					}
					else {
						#Cicle was not input
						$this -> errors -> not_found_input('Cicle');
						return;
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