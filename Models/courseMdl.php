<?php

class courseMdl {

	function __construct($driver) {
		$this -> db_driver = $driver;
	}

	function add_course($course_info) {
		#Go the the DB and add the course
		#It will return true if it is succesful or false if it fails
		#Variables to add into Curso table
		//$clave_curso;
	 	//$clave_ciclo;
	 	$nrc = $course_info['nrc'];
	 	$clave_materia = $course_info['subject'];
	 	$clave_maestro = $_SESSION['userid'];
	 	$seccion = $course_info['section'];

	 	#Get actual ciclo from DB
		$statement = "SELECT c.clave_ciclo as ciclo, ec.estado as estado FROM Ciclo as c JOIN Estado_ciclo as ec ON c.clave_estado = ec.clave_estado WHERE c.clave_estado =1";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result())
				if ( $query= $result -> fetch_array(MYSQLI_ASSOC)) {
					$course_info['cicle'] = $query['ciclo'];
				}
				else 
					return false;
			$result -> close();
		}

		$clave_ciclo = $course_info['cicle'];

		#Check if Subject ID is in Database
		$query = $this -> db_driver;
		$statement = "SELECT nombre FROM Materia WHERE clave_materia = '$clave_materia'";
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result())
				if ($query= $result -> fetch_array(MYSQLI_ASSOC)) {
					$course_info['subject_name'] = $query['nombre'];
				}
				else
					return false;
			$result -> close();
		}
		
		#Check if NRC is not already registered in a course on this cicle
		#Check if Section is not already registerd to a course on this cicle
		$query = $this -> db_driver;
		$statement = "SELECT nrc FROM Curso WHERE (clave_ciclo = '$clave_ciclo' AND nrc = '$nrc') OR (clave_ciclo = '$clave_ciclo' AND clave_materia = '$clave_materia' AND seccion = '$seccion')";
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result())
				if ($query= $result -> fetch_array(MYSQLI_ASSOC)) {
					return false;
				}
				else {
					$clave_curso = $nrc.$clave_ciclo;
				}
			$result -> close();
		}

		#Create Unique table for this course

		$prepare = "CREATE TABLE Curso_".$clave_curso."(codigo_alumno INT NOT NULL, calificacion DECIMAL) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8";
		if ($query = $this -> db_driver->prepare($prepare)) {
	    	if ($query->execute()) {
	    		$prepare = "ALTER TABLE Curso_".$clave_curso." ADD FOREIGN KEY (codigo_alumno) REFERENCES users_student (userid)
							ON DELETE NO ACTION ON UPDATE NO ACTION";
				if ($query = $this -> db_driver -> prepare($prepare)) {
					if (!($query -> execute())) {
						return false;
					}
				}
	    	}
	    	else {
	    		return false;
	    	} 
		}
		else
			return false;

		return $course_info;
	}

	function add_student_to_course($studentid,$courseid) {
		#Go to the DB and get student to be added
		#Will return false if the student was not found, or return full name if an error ocurred while adding
		#Return array with student information to confirm the it was added to a course
		$student = array ('studentid' => $studentid, "name" => "Carlos Mauricio Romero Pacheco");
		$student['subject'] = "Topicos Selectos de Computacion";
		$student['section'] = "D27";
 		return $student;
	}

	function view_course_attendance($courseid) {
		#Go to the DB and get all students in actual the course
		#Will return false if the course was not found, or return an array containing all students in
		#the course and the attendance list
		$course['subject'] = "Taller de Compiladores";
		$course['section'] = "D04";
		$course['nrc'] = "91780";
		$course['attendance'][] = array ("studentid" => 211213995, "name" => "Romero Pacheco Carlos Mauricio","attendance" => "* | * | / | * | / | * | * | *");
		$course['attendance'][] = array ("studentid" => 210519152, "name" => "Villanueva Venegas Neo Octavio","attendance" => "/ | / | / | * | / | * | * | *");
		return $course;
	}

	function view_course_grade($courseid) {
		#Go to the DB and get all students in actual the course
		#Will return false if the course was not found, or return an array containing all students in
		#the course with grades
		$course['subject'] = "Ingenieria de Software";
		$course['section'] = "D01";
		$course['nrc'] = "14580";
		$course['grade'][] = array ("studentid" => 211213995, "name" => "Romero Pacheco Carlos Mauricio","grade" => "9.1");
		$course['grade'][] = array ("studentid" => 210519152, "name" => "Villanueva Venegas Neo Octavio","grade" => "7.3");
		return $course;
	}

	function add_field_to_course($field_array) {
		#Go to the DB and get the course info
		#Validate percentage value is accepted by getting percentage total and adding the new one , and making sure its equal or less than 100
		#Then proceed to the insertion of the new field
		#Return the array if the field was succesfully added , or false if it failed
		$field_array['subject'] = "Topicos Selectos de Computacion I";
		return $field_array;
	}

	function add_grade_to_field($field_array) {
		#Go to the DB and get the course's and student's info
		#Add the grade to the field of the student's course
		#Return the array if the grade was succesfully captured , or false if it failed
		$field_array['subject'] = "Topicos Selectos de Computacion I";
		$field_array['name'] = 'Carlos Mauricio Romero Pacheco';
		return $field_array;
	}

	function add_sheet_to_course($sheet_array) {
		#Go to the DB and get the course's information
		#Add the sheet to the field  the course
		#Return the array if was added , or false if it failed
		$sheet_array['subject'] = 'Graficas por Computadoras';
		$sheet_array['section'] = 'D01';
		return $sheet_array;
	}

	function check_studentsid($studentids_array,$courseid) {
		$return_array = array();
		foreach ($studentids_array as $key => $studentid) {
			#Go to the DB ad check if student is in course
			#If found, add to return_array
			$return_array[] = $studentid;
		}
		return $return_array;
	}
}

?>