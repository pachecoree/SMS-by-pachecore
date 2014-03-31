<?


class fechasCtrl {
	
	/*function validate_date($date) {
		$pattern = '/^[0-9]{1,2}[\/][0-9]{1,2}[\/][0-9]{4}$/';
		if (preg_match($pattern, $date) == 1) {
			list($day, $month, $year) = explode("/", $date);
			$date = "$month/$day/$year";
			if (checkdate($month, $day, $year)) {
		 		$date = new DateTime($date);
		 		return date_format($fecha,'D d-m-Y');
		 	}
		 	else 
		 		return false;
		}
		return false;
	}*/

	function regresa_contador($fecha) {
		if (preg_match('/^Mon/i',(date_format($fecha,'D d-m-Y'))) == 1)
			return 1;
		if (preg_match('/^Tue/i', (date_format($fecha,'D d-m-Y'))) == 1)
			return 2;
		if (preg_match('/^Wed/i', (date_format($fecha,'D d-m-Y'))) == 1)
			return 3;
		if (preg_match('/^Thu/i', (date_format($fecha,'D d-m-Y'))) == 1)
			return 4;
		if (preg_match('/^Fri/i', (date_format($fecha,'D d-m-Y'))) == 1)
			return 5;
		if (preg_match('/^Sat/i', (date_format($fecha,'D d-m-Y'))) == 1)
			return 6;
		if (preg_match('/^Sun/i', (date_format($fecha,'D d-m-Y'))) == 1)
			return 7;
	}

	function listar_fechas($fechainicial, $fechafinal, $dias,$libres) {
		$dias_libre = array();
		$arreglo_dias = array();
		foreach ($libres as $dia_libre) {
			//list($anio,$mes,$dia) = explode("/",$dia_libre);
			//$dia_libre = "$anio/$mes/$dia";
			$dia_libre = new DateTime($dia_libre);
			$dias_libre[] =  date_format($dia_libre, 'Y-m-d');
		}
		//list($day, $month, $year) = explode("/", $fechainicial);
	 	//$fechaini = "$year/$month/$day";
	 	$fechainicial = new DateTime($fechainicial);
	 	//list($day, $month, $year) = explode("/", $fechafinal);
	 	//$fechafin = "$year/$month/$day";
	 	$fechafinal = new DateTime($fechafinal);
	 	$fechafinal = $fechafinal -> modify('+1 day');
		 while( $fechainicial != $fechafinal) {
		 	$contador = $this -> regresa_contador($fechainicial);
		 	if (in_array($contador, $dias)) {
		 		$f = date_format($fechainicial, 'Y-m-d');
		 		if (!(in_array($f, $dias_libre))) {
		 		$arreglo_dias[] = $f;
		 		}
		 	}
		$fechainicial = ($fechainicial ->modify('+1 day'));
		}
		return $arreglo_dias;
	}



}


?>