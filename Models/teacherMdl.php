<?php

class teacherMdl {
	
	function __construct($driver) {
		$this -> db_driver = $driver;
		require('Models/stdMdl.php');
		$this -> std_obj = new stdMdl($driver);
	}

function add_student($student) {
		$ciclo = $this -> std_obj -> get_cicle();
		#Gets the student info
		#Goes to the DB to add the student, and add the "Active" status
		#will return array if it was succesfull , false if it fails
		$userid =strtoupper($teacher['studentid']);
		$password = strtoupper($teacher['password']);
		$nombre = strtoupper($teacher['name']);
		$primer_a = strtoupper($teacher['first']);
		$segundo_a = strtoupper($teacher['second']);
		$correo = strtoupper($teacher['email']);
		$celular = strtoupper($teacher['cellphone']);

		#Check if Student ID is not already in DB
		$statement = "SELECT userid FROM users_teacher WHERE userid = '$userid'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result = $query -> store_result()) {
				if ($query = $result -> fetch_array(MYSQLI_ASSOC)) {
					return false;
				}
			}
			$result -> close();
		}
		else
			return false;

		#Add Student to System
		$prepare = "INSERT INTO users_teacher VALUES (?,?,?,?,?,?,?,?,?,?)";
		if ($query = $this -> db_driver -> prepare($prepare)) {
			$query -> bind_param("sssssssiis",$userid,$password,$nombre,$primer_a,$segundo_a,
								 $correo,$celular,$i=1,$i=1,$ciclo);
			if (!$query -> execute()) {
				return false;
			}
		}

		#Get values to create  array with all user information from database
	    #
		$return_array = $this -> std_obj -> buscar_alumno($userid);
		if ($return_array == false)
			return false;
		

		return $return_array;
	}


}
?>