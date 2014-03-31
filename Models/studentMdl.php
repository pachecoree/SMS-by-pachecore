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

		#Add Student to System
		$prepare = "INSERT INTO users_student VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
		if ($query = $this -> db_driver -> prepare($prepare)) {
			$query -> bind_param("sssssssssisi",$userid,$password,$nombre,$primer_a,$segundo_a,
								 $correo,$github,$celular,$web,$i=1,$clave_carrera,$i=1);
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

	function view_student_grades($studentid,$cicle) { #
		#Gets the Student ID and Cicle
		#Goes to the DB to search for the Student, and gets all his active courses info
		#will return array if it found it, false if not.
		$grades_info['name'] = "Carlos Mauricio Romero Pacheco";
		$grades_info['studentid'] = "211213995";
		$grades_info['career'] = "Computacion";
		$grades_info['grades'][] = array ("materia" => "Taller de Compiladores", "calificacion" => 86.4);
		$grades_info['grades'][]= array ("materia" => "Topicos Selectos de Computacion III","calificacion" => 81.2);
		$grades_info['grades'][] = array ("materia" => "Organizacion de Computadoras","calificacion" => 96);
		$grades_info['grades'][] = array ("materia" => "Programacion Logica y Funcional","calificacion" => 78.8);
		return $grades_info;
	}

	function view_student_course($studentid,$courseid) { #
		#Gets the Student ID and Course
		#Goes to the DB to search for the Student, and gets all his course information (attendance and grades)
		#will return array if it found it, false if not.
		$course_info['name'] = "Carlos Mauricio Romero Pacheco";
		$course_info['studentid'] = "211213995";
		$course_info['career'] = "Computacion";
		$course_info['grades'][] = array ("materia" => "Taller de Compiladores", "calificacion" => 86.4,"asistencia" => " * | * | * | * | * | * | * | * |");
		return $course_info;
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

}

?>