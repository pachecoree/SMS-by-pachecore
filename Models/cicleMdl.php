<?php 

class cicleMdl {
	#Validate that the input cicle is correct
	
	function __construct($driver) {
		$this -> db_driver = $driver;
	}

	function add_cicle($cicle_array,$nonworking) {
		$cicle_return = false;
		#Recieves the cicle information to be added in an array
		#Return status true if it was correctly created or false if it failed
		//Update Clave_estado to make it 0  so that only the new cicle gets "Actual"
		$prepare_up = "UPDATE Ciclo SET clave_estado = ? where clave_ciclo <> ?";
		$query_up = $this -> db_driver -> prepare($prepare_up);
		$query_up -> bind_param("is",$i = 0,$cicle_array['clave_ciclo']);

		//Insert into table Ciclo
		$prepare_ins = "INSERT INTO Ciclo VALUES(?,?,?,?)";
		$query = $this -> db_driver ->prepare($prepare_ins);
		$query->bind_param("sssi", strtoupper($cicle_array['clave_ciclo']),date_format($cicle_array['inicio'],'Y/m/d'),date_format($cicle_array['fin'],'Y/m/d'),$i = 1);
		if ($query->execute()) {
			$cicle_return = true;
			$query_up -> execute();
		}
		unset($query);
		//add not working days into 'Dia_libre' table
		if (isset($nonworking) && ($cicle_return == true)) {
			while (list($key,$dia) = each($nonworking)) {
				$prepare_ins = "INSERT INTO Dia_libre Values(?,?)";
				$query = $this -> db_driver -> prepare($prepare_ins);
				$query-> bind_param("ss",strtoupper($cicle_array['clave_ciclo']),date_format($dia,'Y/m/d'));
				$query->execute();
			}
		}
		return $cicle_return;
	}

	function modify_status($cicle,$status) {
		#Recieves the cicle information
		#Go to the DB and look for the cicle and modify its status, and modify actual Active cicle to "Pasado"
		#Return true if it was succesful or False if it failed
		return true;
	}

}


?>