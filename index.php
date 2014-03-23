<?php

/**
@
@author : Carlos Romero
@Proyecto : SMS - Students Management System
@Version : 1.0v
@
*/

	#Check if the controller exists
	if (isset($_GET['ctrl'])) {
		//require('Models/database_config.inc');
		$db_driver = new mysqli('localhost','root','tortas','SMS');
		if ($db_driver -> connect_error) {
			die("Error al conectar a la base de datos");
		}
		switch ($_GET['ctrl']) {
			case 'login':
				#Load login controller
				require('Controllers/loginCtrl.php');
				$ctrl = new loginCtrl($db_driver);
				$ctrl -> run();
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