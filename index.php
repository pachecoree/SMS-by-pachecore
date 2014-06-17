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
		require('Models/database_config.inc');
		$db_driver = new mysqli($conex,$user,$pass,$db,$port);
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
				$ctrl = new cicleCtrl($db_driver);
				$ctrl -> run();
				break;

			case 'course':
				#Load course controller
				require('Controllers/courseCtrl.php');
				$ctrl = new courseCtrl($db_driver);
				$ctrl -> run();
				
				break;

			case 'student':
				#Load student controller
				require('Controllers/studentCtrl.php');
				$ctrl = new studentCtrl($db_driver);
				$ctrl -> run();
				break;

			case 'teacher':
				#Load student controller
				require('Controllers/teacherCtrl.php');
				$ctrl = new teacherCtrl($db_driver);
				$ctrl -> run();
				break;

			default:
				#Controller was not valid
				echo 'Controller ',$_GET['ctrl'],' is not valid';
				break;
		}
	}
	else {
		require('Controllers/validationCtrl.php');
		require('Controllers/templatesCtrl.php');
		require('Controllers/mailCtrl.php');
		$templateCtrl = new templatesCtrl();
		$emailCtrl = new mailCtrl();
		$validation = new validationCtrl();
		$header = file_get_contents('Views/Head.html');
		$footer = file_get_contents('Views/Footer.html');
		
		if (!$validation->active_session()) {
			$content = file_get_contents('Views/login.html');
			$content = $templateCtrl -> procesarPlantilla_login($content,-1);
		}
		else {
			$content = file_get_contents('Views/logincorrectly.html');
			$content = str_replace("{{'nombre'}}", $_SESSION['user'], $content);
			$content = $templateCtrl -> get_menu($content);
		}
		echo $header.$content.$footer;
		#Controller was not input
		//echo 'The controller was not found';
	}
?>
