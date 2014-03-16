<?php

/**
@
@author : Carlos Romero
@Proyecto : SMS - Students Management System
@Version : 1.0v
@
*/

#Recieve login information
	#Check if the controller exists
	if (isset($_GET['ctrl'])) {
		switch ($_GET['ctrl']) {
			case 'login':
				#Load login controller
				require('Controllers/loginCtrl.php');
				$ctrl = new loginCtrl();
				#Validate login informatcion (userid, password)
				if ($ctrl -> validate_login_data()) {
					#Data was validated correctly

				}
				else {
					#Data wasn't validated
					echo 'Incorrect Userid or Password';
					}
				break;
			
			case 'cicle':
				#Load cicle controller
				require('Controllers/cicleCtrl.php');
				$ctrl = new cicleCtrl();
				$ctrl -> run();
				break;

			case 'course':
				#Load course controller
				require('Controllers/courseCtrl.php');
				$ctrl = new courseCtrl();
				$ctrl -> run();
				
				break;

			case 'student':
				#Load student controller
				require('Controllers/studentCtrl.php');
				$ctrl = new studentCtrl();
				$ctrl -> run();
				break;

			default:
				#Controller was not valid
				echo 'Controller ',$_GET['ctrl'],' is not valid';
				break;
		}
	}
	else {
		#Controller was not input
		echo 'The controller was not found';
	}
?>