<?php

class validationCtrl {

	function __construct () {
	}

/*	function validate_date($date) {
		$pattern = '/^[0-9]{1,2}[-][0-9]{1,2}[-][0-9]{4}$/';
		if (preg_match($pattern, $date) == 1) {
			list($day, $month, $year) = explode("-", $date);
			$date = "$month/$day/$year";
			if (checkdate($month, $day, $year)) {
		 		return new DateTime($date);
		 	}
		 	else 
		 		return false;
		}
		return false;
	}*/

	function validate_date($date) {
		//$pattern = '/^[0-9]{1,2}[-][0-9]{1,2}[-][0-9]{4}$/';
		//if (preg_match($pattern, $date) == 1) {
			list($year, $month, $day) = explode("-", $date);
			$date = "$month/$day/$year";
			if (checkdate($month, $day, $year)) {
		 		return new DateTime($date);
		 	}
		 	else 
		 		return false;
		//}
		return false;
	}

	function validate_nonworking($date,$fechainicial,$fechafinal) {
		list($year,$month,$day) = explode("-",$date);
		$date = "$month/$day/$year";
		if (checkdate($month,$day, $year)) {
			$date = new DateTime($date);
			if ($date >= $fechainicial && $date <= $fechafinal) {
				return true;
			}
		}
		return false;
	}

	function validate_nrc($nrc) {
		#Check if format was correctly input
		$pattern = '/^[0-9]{5}$/';
		if (preg_match($pattern,$nrc) == 1) {
			return true;
		}	
		#NRC format is not valid
		return false;
	}

	function validate_cicle($cicle) {
		#Check if format was correctly input
		$pattern = '/^[2][0-9]{3}[AB]$/i';
		if (preg_match($pattern,$cicle) == 1) {
			return true;
		}
		#Cicle format is not valid
		return false;
	}

	function validate_section($section) {
		#Check if format was correctly input
		$pattern = '/^d[0-9]{2}$/i';
		if (preg_match($pattern, $section) == 1) {
			return true;
		}
		#Section format is not valid
		return false;
	}

	function validate_subject($subject) {
		#Check if format was correctly input
		return true; //mientras temporal
		$pattern = '/^[a-z]{1}[a-z0-9][0-9]{3}$/i';
		if (preg_match($pattern,$subject) == 1) {
			return true;
		}
		#Subject name is not valid
		return false;
	}

	function compare_dates($fechaini , $fechafin) {
		if ($fechaini < $fechafin) {
			return true;
		}
		return false;
	}

	function validate_grade($grade) {
		#Check if format was correctly input
		$pattern = '/(^[0-9]([.][0-9]){0,1}$)|^10$/';
		if (preg_match($pattern,$grade) == 1) {
			return true;
		}
		else if (strcmp(strtoupper($grade),'NP') == 0)
			return true;
		else if (strcmp(strtoupper($grade),'SD') == 0)
			return true;
		#Percetange value is not valid
		return false;
	}

	function validate_percentage($percentage) {
		#Check if format was correctly input
		$pattern = '/(^[0]{0,1}[1-9]$)|(^[1-9][0-9]$)|(^100$)/';
		if (preg_match($pattern,$percentage) == 1) {
			return true;
		}
		#Percetange value is not valid
		return false;
	}

	function validate_attendance($value) {
		if ($value == 1) {
			return 1;
		}
		if ($value == 0) {
			return 0;
		}
		#Not valid value
		return 2;
	}

	function validate_field($field) {
		#Check if format was correctly input
		$pattern = '/^[1-9 a-z]{1,}$/i';
		if (preg_match($pattern,$field) == 1) {
			return true;
		}
		#Field value is not valid
		return false;
	}

	function validate_phonenumber($phonenumber) {
		#Check if format was correctly input
		$pattern = '/^33([0-9]{2}){4}$/';
		if (preg_match($pattern,$phonenumber) == 1) {
			return true;
		}
		#Phonenumber is not valid
		return false;
	}

	function validate_name($name) {
		#Check if format was correctly input
		$pattern = '/^[a-z ]{1,}$/i';
		if (preg_match($pattern,$name) == 1) {
			return true;
		}
		#Student Name is not valid
		return false;
	}

	function validate_ciclestatus($status) {
		if (strcmp(strtolower($status), 'actual') == 0) {
			return 0;
		} 
		return 1;
	}

    function validate_studentstatus($status) {
		if ($status == 0) {
			return 0;
		} 
		if ($status == 1) {
			return 1;
		} 
		return 2;
	}

	function validate_userid($userid) {
		#Check if format was correctly input
		$pattern = '/^[0-9d][0-9]{8}$/i';
		if (preg_match($pattern,$userid) == 1) {
			return 1;
		}
		$pattern = '/^[0-9d][0-9]{5,7}$/i';
		if (preg_match($pattern,$userid) == 1) {
			return 2;
		}
		$pattern = '/^[0-9d][0-9]{4}$/i';
		if (preg_match($pattern,$userid) == 1) {
			return 3;
		}
		#Student ID is not valid
		return false;
	}

	function validate_tid($tid) {
		#Check if format was correctly input
		$pattern = '/^[0-9d][0-9]{5,7}$/i';
		if (preg_match($pattern,$tid) == 1) {
			return true;
		}
		#Teacher ID is not valid
		return false;
	}

	function validate_sid($sid) {
		#Check if format was correctly input
		$pattern = '/^[0-9d][0-9]{8}$/i';
		if (preg_match($pattern,$sid) == 1) {
			return true;
		}
		#Student ID is not valid
		return false;
	}

	function validate_web($web) {
		#Check if format was correctly input
		$pattern = '/^[a-z0-9]{1,}([.][a-z0-9]{1,}){1,}([\/.][a-z0-9]{0,}){0,}$/i';
		if (preg_match($pattern,$web) == 1) {
			return true;
		}
		#Student ID is not valid
		return false;
	}

	function validate_email($email) {
		#Check if format was correctly input
		$pattern = '/^([a-z0-9_-]{1,}([.][a-z0-9_-]{1,}){0,}[a-z0-9_-]{0,}){1,}[@][a-z0-9]{1,}([.]([a-z0-9]){1,}){1,}$/i';
		if (preg_match($pattern,$email) == 1) {
			return true;
		}
		#Email is not valid
		return false;
	}
	

	function validate_github($github) {
		#Check if format was correctly input
		$pattern = '/^[a-z1-9][a-z1-9-]{1,}$/';
		if (preg_match($pattern,$github) == 1) {
			return true;
		}
		#github Account is not valid
		return false;
	}

	function validate_career($career) {
		if ($career > 0 && $career <= 14) {
			return true;
		}
		return false;
	}

	function validate_courseid($courseid) {
		#Check if format was correctly input
		$pattern = '/^[0-9]{1,6}$/';
		if (preg_match($pattern,$courseid) == 1) {
			return true;
		}
		return false;
	}

	function validate_nocol($nocol) {
		#Check if number of columns is between 0 and 10
		if ($nocol >= 0 && $nocol <=10)
			return true;
		#Number of columns is not valid
		return false;
	}

	function validate_schedule($day,$hours,$schedule) {
		$dias_clase = array();
		while ((list( ,$horario) = each($schedule)) && (list( ,$dias) = each($day)) && (list( ,$horas) = each($hours))) {
			foreach ($dias_clase as $value) {
				if ($value == $dias) {
					return false;
				}
			}
			if (($dias < 1) || ($dias > 6)) {
				return false;
			}
			if (($horas < 1) || ($horas > 6)) {
				return false;
			}
			if (($horario < 1) || ($horario >15))  {
				return false;
			}
			if (($horario+($horas-1)) > 15) {
				return false;
			}
			$dias_clase[] = $dias;
		}
		return true;
	}

	function active_session() {
		#Will handle 3 numerical status
		#3 - Logged and the account type has admin rights
		#2 - Logged and the account type has teacher rights
		#1 - Logged and the account type has student rights
		#Will return false if session is not active
		session_start();
		if (isset($_SESSION['started'])) {
			if ($_SESSION['type'] == 3) {
				return 3;
			}
			else if ($_SESSION['type'] == 2) {
				return 2;
			}
			else if ($_SESSION['type'] == 1) {
				return 1;
			}
		}
		else
			return false;
	}
}


?>