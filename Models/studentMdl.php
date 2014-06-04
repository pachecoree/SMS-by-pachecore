<?php

class studentMdl {

	function __construct($driver) {
		$this -> db_driver = $driver;
		require('Models/stdMdl.php');
		$this -> std_obj = new stdMdl($driver);
	}
	
	function add_student($student) {

		#Gets the student info
		#Goes to the DB to add the student, and add the "Active" status
		#will return array if it was succesfull , false if it fails
		$userid =strtoupper($student['studentid']);
		$password = strtoupper($student['password']);
		$nombre = strtoupper($student['name']);
		$primer_a = strtoupper($student['first']);
		$segundo_a = strtoupper($student['second']);
		$correo = strtoupper($student['email']);
		$clave_carrera = strtoupper($student['career']);
		$nacimiento = $student['nacimiento'];
		if (isset($student['github']))
			$github = strtoupper($student['github']);
		else
			$github = "";
		if (isset($student['cellphone']))
			$celular = strtoupper($student['cellphone']);
		else
			$celular = "";
		if (isset($student['web']))
			$web = strtoupper($student['web']);
		else
			$web = "";


		#Check if Student ID is not already in DB
		$statement = "SELECT userid FROM users_student WHERE userid = '$userid'";
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
		$prepare = "INSERT INTO users_student VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
		if ($query = $this -> db_driver -> prepare($prepare)) {
			$query -> bind_param("ssssssssssisi",$userid,$password,$nombre,$primer_a,$segundo_a,
								 $nacimiento,$correo,$github,$celular,$web,$i=1,$clave_carrera,$i=1);
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

	function view_student_grades($codigo_alumno,$ciclo) { #
		#Gets the Student ID and Cicle
		#Goes to the DB to search for the Student, and gets all his active courses info
		#will return array if it found it, false if not.

		$return_array = array();

		#Check if Student is in the database
		#If it is, get his information
		$statement = "SELECT us.userid AS codigo, CONCAT( us.nombre,  ' ', us.primer_a,  ' ', us.segundo_a ) AS nombre, c.nombre AS carrera
					  FROM users_student AS us
					  JOIN Carrera AS c ON c.clave_carrera = us.clave_carrera
					  WHERE userid =  '$codigo_alumno'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result = $query -> store_result()) {
				if ($query = $result -> fetch_array(MYSQLI_ASSOC)) {
					$return_array['nombre'] = $query['nombre'];
					$return_array['carrera'] = $query['carrera'];
					$return_array['codigo'] = $query['codigo'];
				}
				else {
					return false;
				}
			}
			else {
				return false;
			}
			$result -> close();
		}

		#Get student grades
		$statement = "SELECT m.nombre as materia, l.calificacion as calificacion
					  FROM Lista AS l
					  JOIN Curso AS c ON c.clave_curso = l.clave_curso
					  JOIN Materia AS m ON m.clave_materia = c.clave_materia
					  WHERE l.codigo_alumno =  '$codigo_alumno'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result = $query -> store_result()) {
				while ($query = $result -> fetch_array(MYSQLI_ASSOC)) {
					$return_array['materia'][] = $query['materia'];
					$return_array['calificacion'][] = $query['calificacion'];
				}
			}
			else {
				return false;
			}
			$result -> close();
		}
		if (sizeof($return_array) > 0)
			return $return_array;
		else
			return false;
	}

	function view_student_courseDetails($clave_curso,$studentid) {
		$return_array = array();
		$statement = "SELECT c.codigo_alumno as codigo, r.actividad as actividad,
		r.porcentaje as porcentaje,c.calificacion as calificacion,((c.calificacion * r.porcentaje)/10) as puntos
	  	FROM Calificacion c JOIN Rubro as r ON c.clave_rubro = r.clave_rubro
		WHERE c.clave_curso ='$clave_curso' AND c.codigo_alumno = '$studentid'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result = $query -> store_result()) {
				while ($query = $result -> fetch_array(MYSQLI_ASSOC)) {
			 		$return_array[$query['codigo']]['actividad'][] = $query['actividad'];
			 		$return_array[$query['codigo']]['porcentaje'][] = $query['porcentaje'];
			 		$return_array[$query['codigo']]['puntos'][] = $query['puntos'];
			 		//$return_array[$query['codigo']]['calificacion'] = $query['calificacion'];
			 		$statement1 = "SELECT calificacion FROM Lista WHERE codigo_alumno = '".$query['codigo']."' AND clave_curso = '".$clave_curso."'";
			 		$query1 = $this -> db_driver;
			 		if ($query1 -> real_query($statement1)) {
			 			if ($result1 = $query1 -> store_result()) {
			 				if ($query1 = $result1 -> fetch_array(MYSQLI_ASSOC)) {
			 					$return_array[$query['codigo']]['calificacion'] = $query1['calificacion'];
			 				}
			 			}
			 		}
			  	}
			}
			$result -> close();
		}
		if (sizeof($return_array) > 0)
			return $return_array;
		else
			return false;
	}

	function view_student_course($codigo_alumno,$ciclo,$nrc) {
		#Gets the Student ID and Course
		#Goes to the DB to search for the Student, and gets all his course information (attendance and grades)
		#will return array if it found it, false if not.

		$return_array = array();

		#Check if Student is in the database
		#If it is, get his information
		/*$statement = "SELECT us.userid AS codigo, CONCAT( us.nombre,  ' ', us.primer_a,  ' ', us.segundo_a ) AS nombre, c.nombre AS carrera
					  FROM users_student AS us
					  JOIN Carrera AS c ON c.clave_carrera = us.clave_carrera
					  WHERE userid =  '$codigo_alumno'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result = $query -> store_result()) {
				if ($query = $result -> fetch_array(MYSQLI_ASSOC)) {
					$return_array['nombre'] = $query['nombre'];
					$return_array['carrera'] = $query['carrera'];
					$return_array['codigo'] = $query['codigo'];
				}
				else {
					return false;
				}
			}
			else {
				return false;
			}
			$result -> close();
		}*/


		#Check if Student is enrolled in the course and cicle given
		$statement = "SELECT clave_curso FROM Curso WHERE nrc = '$nrc' && clave_ciclo = '$ciclo'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result = $query -> store_result()) {
				if ($query = $result -> fetch_array(MYSQLI_ASSOC)) {
					$clave_curso = $query['clave_curso'];
				}
				else {
					return false;
				}
			}
			$result -> close();
		}
		else
			return false;


		#Get student attendance
		$statement = "SELECT asistencia,dia From Asistencia where clave_curso = '$clave_curso' AND codigo_alumno = '$codigo_alumno'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result())
				while ($query= $result -> fetch_array(MYSQLI_ASSOC)) {
					$return_array[$codigo_alumno]['dia'][] = $query['dia'];
					$return_array[$codigo_alumno]['asistencia'][] = $query['asistencia'];
				}
			$result -> close();
		}



		if (sizeof($return_array) > 0)
			return $return_array;
		else
			return false;
	}

	function modify_status($userid,$value) { 
		#Gets the Student ID
		#Goes to the DB to search for the Student, and gets all his information
		#Will change its status , to the one received
		#Return array if it was succesfully changed, or false if it failed

		#Look for student by userdi(codigo_alumno) in DB

		$statement = "SELECT userid FROM users_student WHERE userid = '$userid'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {	
			if ($result = $query -> store_result()) {
				if (!$query = $result -> fetch_array(MYSQLI_ASSOC)) {
					return false;
				}
			}
			$result -> close();
		}

		#Update Student Status
		$prepare = "UPDATE users_student SET clave_estado = ? WHERE userid = ?";
		if ($query = $this -> db_driver -> prepare($prepare)) {
			$query -> bind_param("is",$value,$userid);
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

	function update_studentinfo($studentid,$correo,$github,$celular,$web) {
		$prepare = "UPDATE users_student SET correo = ?, github= ?, celular = ?, web = ? WHERE userid = ?";
		if ($query = $this -> db_driver -> prepare($prepare)) {
			$query -> bind_param("sssss",$correo,$github,$celular,$web,$studentid);
			if ($query -> execute()) {
				return true;
			}
		}
		return false;
	}

	function delete_rubros($clave_curso) {
		$prepare = "DELETE FROM Rubro WHERE clave_curso = ?";
		if ($query = $this -> db_driver -> prepare($prepare)) {
			$query -> bind_param("s",$clave_curso);
			if ($query -> execute()) {
				return true;
			}
		}
		return false;
	}

}

?>