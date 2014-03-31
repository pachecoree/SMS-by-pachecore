<?php

class courseMdl {

	function __construct($driver) {
		$this -> db_driver = $driver;
		require('Models/stdMdl.php');
		$this -> std_obj = new stdMdl($driver);
	}

	function add_course($course_info) {
		#Go the the DB and add the course
		#It will return true if it is succesful or false if it fails
		#Variables to add into Curso table
	 	$nrc = $course_info['nrc'];
	 	$clave_materia = $course_info['subject'];
	 	$clave_maestro = $course_info['teacher_id'];
	 	$seccion = $course_info['section'];
	 	#Get actual ciclo from DB
	 	$clave_ciclo = $this -> std_obj -> get_cicle();
	 	#If the returned value from get_cicle is false, return false to mark error
	 	if ($clave_ciclo == false) 
	 		return false;

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
		
		#Check if NRC is not already registered in a course on this cicle and
		#Check if Section is not already registerd to a subject on this cicle
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

		#If the session was started by an administrator 
		#Check if the Teacher ID exists in database
		if ($_SESSION['type'] == 3)
			$clave_maestro = $this -> std_obj -> get_teacher($clave_maestro);
		#if the returned value from get_teacher is false, return false to mark error
		if ($clave_maestro == false)
			return false;
		#IF returned value is not false, teacher id exists

		#Everything is set up to add the Course to the Data Base
		#First we add the course to 'Curso' table
		#Create array where we will return all informtation
		$return_array = array();
		$prepare = "INSERT INTO Curso VALUES(?,?,?,?,?,?)";
		if ($query = $this -> db_driver->prepare($prepare)) {
			$query -> bind_param("ssssss",$clave_curso,$clave_ciclo,$nrc,$clave_materia,$clave_maestro,$seccion);
	    	if ($query->execute()) {
	    		while ((list( ,$horario) = each($course_info['schedule'])) && (list( ,$dias) = each($course_info['days'])) && (list( ,$horas) = each($course_info['hours']))) {
	    			$prepare = "INSERT INTO Dias_curso VALUES(?,?,?,?,?)";
	    			$fin = $horario + ( $horas-1);
	    			if ($query = $this -> db_driver -> prepare($prepare)) {
	    				$query -> bind_param("siiii",$clave_curso,$dias,$horas,$horario,$fin);
	    				if (!$query -> execute()) {
	    					return false;
	    				}
	    			}
	    			else {
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

		#Get values to create course_info array with all information from database
	    #
		$return_array = $this -> std_obj -> crear_curso_datos($return_array,$clave_curso);
		if ($return_array == false)
			return false;

		return $return_array;
	}



	function add_student_to_course($studentid,$nrc,$teacher_id) {
		#Go to the DB and get student to be added
		#Will return false if the student was not found, or return full name if an error ocurred while adding
		#Return array with student information to confirm the it was added to a course
	 	
	 	#Get Student information
		$statement = "SELECT CONCAT(nombre,' ',primer_a,' ',segundo_a) as nombre FROM users_student WHERE userid = '$studentid'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result())
				if ( $query= $result -> fetch_array(MYSQLI_ASSOC)) {
					$student_info['nombre'] = $query['nombre'];
					$student_info['codigo'] = $studentid;
				}
				else 
					return false;
			$result -> close();
		}


	 	#Get actual ciclo from DB
	 	$ciclo = $this -> std_obj -> get_cicle();
	 	#If the returned value from get_cicle is false, return false to mark error
	 	if ($ciclo == false) 
	 		return false;
	 	else
	 		$student_info['clave_ciclo'] = $ciclo;
	 	#Get_cicle ,returned actual cicle
		$clave_ciclo = $student_info['clave_ciclo'];

		#Check if NRC is on a Course this cicle, to get CourseID
		$statement = "SELECT c.nrc as nrc,m.nombre as materia,c.seccion as seccion,c.clave_materia as clave_materia
					  FROM Curso as c 
					  JOIN Materia as m ON c.clave_materia = m.clave_materia
					  WHERE clave_ciclo = '$ciclo' AND nrc = '$nrc'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result())
				if ($query= $result -> fetch_array(MYSQLI_ASSOC)) {
					$student_info['clave_curso'] = $clave_curso = $nrc.$ciclo;
					$student_info['seccion'] = $query['seccion'];
					$student_info['materia'] = $query['materia'];
					$student_info['clave_materia'] = $query['clave_materia'];
					$student_info['nrc'] = $query['nrc'];
				}
				else {
					return 1;
				}
			$result -> close();
		}

		#Check if Teacher is imparting that course
	    #IF false , teacher is not link to that course
		if (!($this -> std_obj -> maestro_curso($clave_curso,$teacher_id)))
			return false;

		#Check if Student is in the course
		$statement = "SELECT * FROM Lista WHERE clave_curso = '$clave_curso' AND codigo_alumno='$studentid'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result())
				if ($query= $result -> fetch_array(MYSQLI_ASSOC)) {
					return 2;
				}
			$result -> close();
		}


		#Proceed to add student to Course
		#Add him to all course tables
		#ADD to Lista Table
		$prepare = "INSERT INTO Lista VALUES (?,?,?)";
		if ($query = $this -> db_driver -> prepare($prepare)) {
			$query -> bind_param("ssd",$clave_curso,$studentid,$i=0.0);
			if (!$query -> execute()) {
				return 1;
			}
		}

		#Add to Asistencia Table
		#Create the fechasCtrl object to get the array containing all dates for this course
		require('Controllers/fechasCtrl.php');
		$fechas_obj = new fechasCtrl();
		$dias_ciclo = $this -> std_obj -> get_cicle_days($ciclo,$clave_curso);
		#IF return value is false ,we will return false to mark error
		if ($dias_ciclo == false) {
			return false;
		}
		#If return value is true, we now have both beginning and ending date of the cicle and the non working days
		#And the days the cuorse will be imparted
		#Lets generate the array that will contain all days of this course
		$fechas = $fechas_obj -> listar_fechas($dias_ciclo['inicio'],$dias_ciclo['fin'],$dias_ciclo['dias'],$dias_ciclo['libres']);
		#Now we will add each to Asistencia linking studendt , Clave_curso and putting a true value as asistencia on each day
		foreach ($fechas as $value) {
			$prepare = "INSERT INTO Asistencia VALUES (?,?,?,?)";	
			if ($query = $this -> db_driver -> prepare($prepare)) {
				$query -> bind_param("ssss",$clave_curso,$studentid,$value,$i=1);
				$query -> execute();
			}
		}

		#For Each 'Rubro' we will add the student in Calificacion table
		$statement = "SELECT clave_rubro,numero_columnas FROM Rubro WHERE clave_curso = '$clave_curso'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result()) {
				#If there are any Rubro for this course, student will be added to the Calificacion table
				#Linking it to each Rubro
				while ($query= $result -> fetch_array(MYSQLI_ASSOC)) {
					//
					$id_rubro = (int)$query['clave_rubro'];
					$numero_columnas = (int) $query['numero_columnas'];
					$prepare = "INSERT INTO Calificacion (clave_curso,clave_rubro,codigo_alumno,calificacion) VALUES (?,?,?,?)";
					if ($insert = $this -> db_driver -> prepare($prepare)) {
						$insert -> bind_param("sisi",$clave_curso,$id_rubro,$studentid,$i=0);
						$insert -> execute();
					}
					if ($numero_columnas > 0) {
						#Add the student to the Extra Evalutation sheet
						$tabla = 'hoja_evaluacion_'.$clave_curso.'_'.$id_rubro;
						$prepare_ev = "INSERT INTO $tabla (codigo_alumno) VALUES (?)";
						if ($insert_ev = $this -> db_driver -> prepare($prepare_ev)) {
							$insert_ev -> bind_param("s",$studentid);
							$insert_ev -> execute();
						}
					}//
				}
			}
			$result -> close();
		}
		return $student_info;
	}


	function view_course_attendance($nrc) {
		#Go to the DB and get all students in actual the course
		#Will return false if the course was not found, or return an array containing all students in
		#the course and the attendance list
		$return_array = array();

	 	#Get actual ciclo from DB
	 	$clave_ciclo = $this -> std_obj -> get_cicle();
	 	#If the returned value from get_cicle is false, return false to mark error
	 	if ($clave_ciclo == false) 
	 		return false;

		#Check if NRC exists , and if it does compare teacher_id
		$statement = "SELECT clave_maestro FROM Curso where nrc = '$nrc' AND ciclo_actual = '$clave_ciclo'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result())
				if ($query= $result -> fetch_array(MYSQLI_ASSOC)) {
					if ($_SESSION['type'] == 2 && !(strcmp($_SESSION['userid'], $query['clave_maestro']))) {
						return false;
					}
				}
				else {
					return false;
				}
			$result -> close();
		}

	 	$clave_curso = $nrc.$clave_ciclo;
		$statement = "select codigo_alumno,asistencia,dia From Asistencia where clave_curso = '$clave_curso'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result())
				while ($query= $result -> fetch_array(MYSQLI_ASSOC)) {
					$return_array[$query['codigo_alumno']]['dia'][] = $query['dia'];
					$return_array[$query['codigo_alumno']]['asistencia'][] = $query['asistencia'];
				}
			$result -> close();
		}

		if (sizeof($return_array) > 0)
			return $return_array;
		else
			return false;
	}

	function view_course_grade($nrc) {
		#Go to the DB and get all students in actual the course
		#Will return false if the course was not found, or return an array containing all students in
		#the course with grades
		$return_array = array();

	 	#Get actual ciclo from DB
	 	$clave_ciclo = $this -> std_obj -> get_cicle();
	 	#If the returned value from get_cicle is false, return false to mark error
	 	if ($clave_ciclo == false) 
	 		return false;

		#Check if NRC exists , and if it does compare teacher_id
		$statement = "SELECT clave_maestro FROM Curso where nrc = '$nrc' AND ciclo_actual = '$clave_ciclo'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result())
				if ($query= $result -> fetch_array(MYSQLI_ASSOC)) {
					if ($_SESSION['type'] == 2 && !(strcmp($_SESSION['userid'], $query['clave_maestro']))) {
						return false;
					}
				}
				else {
					return false;
				}
			$result -> close();
		}

		$clave_curso = $nrc.$clave_ciclo;
		$statement = "SELECT c.codigo_alumno as codigo, r.actividad as actividad,
		r.porcentaje as porcentaje, ((c.calificacion * r.porcentaje)/10) as puntos, l.calificacion as calificacion
	  	FROM Calificacion c JOIN Rubro as r ON c.clave_rubro = r.clave_rubro
		JOIN Lista as l ON l.codigo_alumno = c.codigo_alumno WHERE c.clave_curso = '$clave_curso'";

		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result = $query -> store_result()) {
				while ($query = $result -> fetch_array(MYSQLI_ASSOC)) {
			 		$return_array[$query['codigo']]['actividad'][] = $query['actividad'];
			 		$return_array[$query['codigo']]['porcentaje'][] = $query['porcentaje'];
			 		$return_array[$query['codigo']]['puntos'][] = $query['puntos'];
			 		$return_array[$query['codigo']]['calificacion'] = $query['calificacion'];
			  	}
			}
			$result -> close();
		}
		if (sizeof($return_array) > 0)
			return $return_array;
		else
			return false;
	}

	function add_field_to_course($field_array) {
		#Go to the DB and get the course info
		#Validate percentage value is accepted by getting percentage total and adding the new one , and making sure its equal or less than 100
		#Then proceed to the insertion of the new field
		#Return the array if the field was succesfully added , or false if it failed

		#Array to be returned
		$return_array = array();

		$nocol = $return_array['nocol'] = $field_array['nocol'];
		$clave_maestro = $field_array['teacher_id'];
		$nrc = $field_array['nrc'];
		$porcentaje = $return_array['porcentaje'] = $field_array['percentage'];
		$actividad = $return_array['actividad'] = $field_array['field'];
		#If the session was started by an administrator 
		#Check if the Teacher ID exists in database
		if ($_SESSION['type'] == 3)
			$clave_maestro = $this -> std_obj -> get_teacher($clave_maestro);
		#if the returned value from get_teacher is false, return false to mark error
		if ($clave_maestro == false)
			return false;
		#IF returned value is not false, teacher id exists

		#Get actual ciclo from DB
	 	$ciclo = $this -> std_obj -> get_cicle();
	 	#If the returned value from get_cicle is false, return false to mark error
	 	if ($ciclo == false)
	 		return false;

		#Check if NRC is on a Course this cicle, to get CourseID
		$statement = "SELECT c.nrc as nrc,m.nombre as materia,c.seccion as seccion,c.clave_materia as clave_materia
					  FROM Curso as c 
					  JOIN Materia as m ON c.clave_materia = m.clave_materia
					  WHERE clave_ciclo = '$ciclo' AND nrc = '$nrc'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result())
				if ($query= $result -> fetch_array(MYSQLI_ASSOC)) {
					$clave_curso = $nrc.$ciclo;
					$return_array['clave_materia'] = $query['clave_materia'];
					$return_array['materia'] = $query['materia'];
				}
				else {
					return false;
				}
			$result -> close();
		}

		#Check if Teacher is imparting that course
	    #IF false , teacher is not link to that course
		if (!($this -> std_obj -> maestro_curso($clave_curso,$clave_maestro)))
			return false;

		#The system restricts adding fields to course while there are students enrolled on it
		#We check if there any students enrolled
		$statement = "SELECT * FROM lista WHERE clave_curso = '$clave_curso'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result = $query -> store_result())
				if ($query = $result -> fetch_array()) {
					#a student was found on course, so adding field will fail
					return false;
				}
			$result -> close();
		}

		#Get the actual percentage of fields in the course
		$statement = "SELECT SUM(porcentaje) as porcentaje FROM Rubro WHERE clave_curso = '$clave_curso'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result = $query -> store_result()) {
				if ($query = $result -> fetch_array(MYSQLI_ASSOC)) {
					$porcentaje_total = $query['porcentaje'];
				}
				else
					$porcentaje_total = 0;
			}
			$result -> close();
		}
		#IF porcentaje_total plus the field to be added percentege exceeds 100, return false to mark error
		if (($porcentaje_total + $porcentaje ) > 100) {
			return false;
		}

		#Everything es properly validated, now proceed to add field to course
		$prepare = "INSERT INTO Rubro (clave_curso,actividad,porcentaje,numero_columnas) VALUES (?,?,?,?)";
		if ($query = $this -> db_driver -> prepare($prepare)) {
			$query -> bind_param("ssii",$clave_curso,$actividad,$porcentaje,$nocol);
			if ($query -> execute()) {
				#Get the Autoincrement key from the inserted row to create evaluation sheet (if columns are 1 or higher)
				$clave_rubro = $query -> insert_id;
			}
			else
				return false;
		}

		#Generate the different columns for evaluation sheet
		#IF it returns false, we will return false to mark error
		if (!$this -> std_obj -> create_ev_sheet($clave_curso,$clave_rubro,$nocol))
			return false;
		#Evaluation Sheets were created

		return $return_array;
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

	function check_studentsid($studentids_array,$nrc,$asistencia,$dia,$teacher_id) { #Attendance
		$return_array = array();

		#Get actual ciclo from DB
	 	$ciclo = $this -> std_obj -> get_cicle();
	 	#If the returned value from get_cicle is false, return false to mark error
	 	if ($ciclo == false)
	 		return false;

	 	#Check if NRC is a course this cicle , and check if teacher is giving that course
		$statement = "SELECT nrc FROM Curso WHERE nrc = '$nrc' AND clave_maestro = '$teacher_id'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result = $query -> store_result())
				if (!$query = $result -> fetch_array(MYSQLI_ASSOC)) {
					return false;
				}
			$result -> close();
		}

	 	$clave_curso = $nrc.$ciclo;

	 	#Update Asistencia values for each Student received
		foreach ($studentids_array as $key => $codigo_alumno) {
			#Go to the DB ad check if student is in course
			#If found, add to return_array
			#Update Student Status
			$prepare = "UPDATE Asistencia SET asistencia = ? WHERE codigo_alumno = ? AND dia = ? AND clave_curso = ?";
			if ($query = $this -> db_driver -> prepare($prepare)) {
				$query -> bind_param("isss",$asistencia,$codigo_alumno,$dia,$clave_curso);
				$query -> execute();
				if ($query -> affected_rows > 0) {
					$return_array[] = $codigo_alumno;
				}
			}
		}
		return $return_array;
	}

	function clone_course($course_info) {

		$ciclo_anterior = $course_info['ciclo_anterior'];
		$nrc_anterior = $course_info['nrc_anterior'];
		$nrc = $course_info['nrc'];
		$clave_materia = $course_info['clave_materia'];
		$clave_maestro = $course_info['clave_maestro'];
		$seccion = $course_info['seccion'];
		$return_array = array();
		#Look for ciclo_anterior in DB to check if it exists
		$statement = "SELECT clave_ciclo FROM Ciclo WHERE clave_ciclo = '$ciclo_anterior'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result = $query -> store_result())
				if (!$query = $result -> fetch_array(MYSQLI_ASSOC)) {
					return false;
				}
			$result -> close();
		}

		#Check if NRC_c (from past course) exists as a course in ciclo_anterior
		$statement = "SELECT nrc FROM Curso WHERE clave_ciclo = '$ciclo_anterior' AND nrc = '$nrc_anterior'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result = $query -> store_result())
				if (!$query = $result -> fetch_array(MYSQLI_ASSOC)) {
					return false;
				}
			$result -> close();
		}

	 	#Get actual ciclo from DB
	 	$clave_ciclo = $this -> std_obj -> get_cicle();
	 	#If the returned value from get_cicle is false, return false to mark error
	 	if ($clave_ciclo == false) 
	 		return false;

		#Create Curso ID
		$clave_curso = $nrc.$clave_ciclo;	 
		#Create Curso ID (anterior)
		$curso_anterior = $nrc_anterior.$ciclo_anterior;

		#If the session was started by an administrator 
		#Check if the Teacher ID exists in database
		if ($_SESSION['type'] == 3)
			$clave_maestro = $this -> std_obj -> get_teacher($clave_maestro);
		#if the returned value from get_teacher is false, return false to mark error
		if ($clave_maestro == false)
			return false;
		#IF returned value is not false, teacher id exists

		#Check if Teacher is imparting that course
	    #IF false , teacher is not link to that course
		if (!($this -> std_obj -> maestro_curso($clave_curso,$clave_maestro)))
			return false;

		#Check if NRC is not already registered in a course on this cicle and
		#Check if Section is not already registerd to a subject on this cicle
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


		#Insert Curso
		$prepare = "INSERT INTO Curso Values(?,?,?,?,?,?)";
		if ($query = $this -> db_driver -> prepare($prepare)) {
			$query -> bind_param("ssssss",$clave_curso,$clave_ciclo,$nrc,$clave_materia,$clave_maestro,$seccion);
			if (!$query -> execute()) {
				return false;
			}
		}
		#Clone the course Schedule
		$prepare = "INSERT INTO Dias_curso (clave_curso,clave_dia,horas,clave_inicio,clave_fin) 
					SELECT (SELECT clave_curso FROM Curso WHERE nrc = ? AND clave_ciclo = ?) AS clave_curso,
					clave_dia,horas,clave_inicio,clave_fin FROM Dias_curso
					WHERE clave_curso = ?";
		if ($query = $this -> db_driver -> prepare($prepare)) {
			$query -> bind_param("sss",$nrc,$clave_ciclo,$curso_anterior);
			if (!$query -> execute()) {
				return false;
			}
		}

		#Clone Fields
		$prepare = "INSERT INTO Rubro (clave_curso,actividad,porcentaje,numero_columnas)
				    SELECT (SELECT clave_curso FROM Curso WHERE nrc = ? AND clave_ciclo = ?) AS clave_curso,actividad,porcentaje,
				    numero_columnas FROM Rubro WHERE clave_curso = ?";
		if ($query = $this -> db_driver -> prepare($prepare)) {
			$query -> bind_param("sss",$nrc,$clave_ciclo,$curso_anterior);
			if (!$query -> execute()) {
				return false;
			}
		}
		#Fields Were cloned
		#Now create ev_sheet for each field that has columns
		$statement = "SELECT clave_rubro,numero_columnas FROM Rubro WHERE clave_curso = '$clave_curso'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result = $query -> store_result()) {
				while ($query = $result -> fetch_array(MYSQLI_ASSOC)) {
					#Generate the different columns for evaluation sheet
					#IF it returns false, we will return false to mark error
					$this -> std_obj -> create_ev_sheet($clave_curso,$query['clave_rubro'],$query['numero_columnas']);
				}
				#Evaluation Sheets were created
			}
			$result -> close();
		}

		#Get values to create course_info array with all information from database
	    #
		$return_array = $this -> std_obj -> crear_curso_datos($return_array,$clave_curso);
		if ($return_array == false)
			return false;

		return $return_array;
	}
}

?>