<?php

class stdMdl {
	
	function __construct($driver) {
		$this -> db_driver = $driver;
	}

	function get_cicle() {
		$statement = "SELECT clave_ciclo as ciclo FROM Ciclo WHERE clave_estado =1";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result())
				if (! $query= $result -> fetch_array(MYSQLI_ASSOC))
					return false;	
			$result -> close();
		}
		return $query['ciclo'];
	}

	function get_all_cicles() {
		$cicles = array();
		$statement = "SELECT clave_ciclo FROM Ciclo ORDER BY clave_ciclo DESC";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result()) {
				while ($query= $result -> fetch_array(MYSQLI_ASSOC)) {
					$cicles[] = $query['clave_ciclo'];
				}
			}
			else {
				return false;
			}
			$result -> close();
		}
		return $cicles;
	}

	function get_teacher($clave_maestro) {
		$query = $this -> db_driver;
		$statement = "SELECT nombre,primer_a,segundo_a FROM users_teacher WHERE userid = '$clave_maestro'";
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result())
				if (!$query= $result -> fetch_array(MYSQLI_ASSOC)) {
					return false;
				}
			$result -> close();
		}
		return $clave_maestro;
	}

	function get_cicle_days($ciclo,$clave_curso) {
		#Get inicio, fin and dias_libres of this cicle
		$statement = "SELECT c.inicio as inicio,c.fin as fin,dl.dia as dia
				      FROM Ciclo as c JOIN Dia_libre as dl ON dl.clave_ciclo = c.clave_ciclo WHERE c.clave_ciclo = '$ciclo'";
		$query = $this -> db_driver;
		$dias_ciclo[] = array();
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result()) {
				while ($query= $result -> fetch_array(MYSQLI_ASSOC)) {
					$dias_ciclo['inicio'] = $query['inicio'];
					$dias_ciclo['fin'] = $query['fin'];
					$dias_ciclo['libres'][] = $query['dia'];
				}
			}
			$result -> close();
		}
		#Get the course's days (Days of the week course will be imparted)
		$statement = "SELECT clave_dia FROM Dias_curso WHERE clave_curso = '$clave_curso'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result = $query -> store_result()) {
				while ($query = $result -> fetch_array(MYSQLI_ASSOC)) {
					$dias_ciclo['dias'][] = $query['clave_dia'];
				}
			}
			$result -> close();
		}


		return $dias_ciclo;
	}

	function create_ev_sheet($clave_curso,$clave_rubro,$nocol) {
		if ($nocol > 0) {
			$columnas = "";
			for ($i=1 ; $i <= $nocol ; $i++) {
				$columnas = $columnas. 'col' . $i . ' CHAR(25) NULL';
					if($i != $nocol)
						$columnas = $columnas. ' , ';
			}
			$tabla = "CREATE TABLE hoja_evaluacion_".$clave_curso.'_'.$clave_rubro." (codigo_alumno CHAR(9) NOT NULL, $columnas, promedio DECIMAL NULL, 
					  INDEX fk_hoja_evaluacion_".$clave_curso.'_'.$clave_rubro."1 (codigo_alumno ASC), CONSTRAINT fk_hoja_evaluacion_".$clave_curso.'_'.$clave_rubro."1 
					  FOREIGN KEY (codigo_alumno) REFERENCES users_student (userid) ON DELETE
					  NO ACTION ON UPDATE NO ACTION) ENGINE = MyISAM DEFAULT CHARACTER SET = utf8";//ENGINE = InnoDB DEFAULT CHARACTER SET = utf8";
			#Create evaluation sheet
			if (!$this -> db_driver ->query($tabla))
				return false;
		}
		return true;
	}

	function obtener_dia() {
		$statement = "SELECT NOW() as hora";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result = $query -> store_result()) {
				if ($query = $result -> fetch_array(MYSQLI_ASSOC)) {
					$hora = $query['hora'];
				}
			}
		}
		$result -> close();
		$hora = new DateTime($hora);
		$hora = date_format($hora,'m/d/Y');
		return $hora;
	}

	function obtener_curso($nrc,$ciclo) {
		$query = $this -> db_driver;
		$return_array = array();

		$clave_curso = $nrc . $ciclo;
		$statement = "SELECT c.nrc as nrc, c.clave_ciclo as ciclo, m.clave_materia as clave_materia,m.nombre as materia,
					  a.nombre as academia,c.seccion as seccion, CONCAT(t.nombre,' ',t.primer_a,' ',t.segundo_a) as maestro
					  FROM Curso as c
					  JOIN users_teacher as t ON c.clave_maestro = t.userid
				 	  JOIN Materia as m ON m.clave_materia = c.clave_materia
					  JOIN Academia as a ON m.clave_academia = a.clave_academia WHERE c.clave_curso = '$clave_curso'";
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result()) {
				if ($query= $result -> fetch_array(MYSQLI_ASSOC)) {
					#Copy query results into return array
					$return_array['nrc'] = $query['nrc'];
					$return_array['ciclo'] = $query['ciclo'];
					$return_array['materia'] = $query['materia'];
					$return_array['clave_materia'] = $query['clave_materia'];
					$return_array['seccion'] = $query['seccion'];
					$return_array['maestro'] = $query['maestro'];
					#Get Course Schedule
					$statement = "SELECT d.dia as dia, i.hora_inicio as inicio, f.hora_fin as fin,
								 dc.horas as horas
								 FROM Dias_curso as dc
								 JOIN Inicio as i ON i.clave_inicio = dc.clave_inicio
							  	 JOIN Fin as f ON f.clave_fin = dc.clave_fin
								 JOIN Dias as d ON d.clave_dia = dc.clave_dia
								 WHERE dc.clave_curso = ?";
					if ($query = $this -> db_driver->prepare($statement)) {
						$query -> bind_param('s',$clave_curso);
						$query->execute();
						$query->bind_result($dia,$inicio,$fin,$horas);
							while ($query->fetch()) {
							$return_array['dia'][] = $dia;
							$return_array['inicio'][] = $inicio;
							$return_array['fin'][] = $fin;
							$return_array['horas'][] = $horas;
						}
					}
				}
			}
		$result -> close();
		}
	return $return_array;
	}


	function crear_curso_datos($clave_curso) {
		$return_array = array();
		$query = $this -> db_driver;
		$statement = "SELECT c.nrc as nrc, c.clave_ciclo as ciclo, m.clave_materia as clave_materia,m.nombre as materia,
					  a.nombre as academia,c.seccion as seccion, CONCAT(t.nombre,' ',t.primer_a,' ',t.segundo_a) as maestro
					  FROM Curso as c
					  JOIN users_teacher as t ON c.clave_maestro = t.userid
				 	  JOIN Materia as m ON m.clave_materia = c.clave_materia
					  JOIN Academia as a ON m.clave_academia = a.clave_academia WHERE c.clave_curso = '$clave_curso'";
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result()) {
				if ($query= $result -> fetch_array(MYSQLI_ASSOC)) {
					#Copy query results into return array
					$return_array['nrc'] = $query['nrc'];
					$return_array['ciclo'] = $query['ciclo'];
					$return_array['materia'] = $query['materia'];
					$return_array['clave_materia'] = $query['clave_materia'];
					$return_array['seccion'] = $query['seccion'];
					$return_array['maestro'] = $query['maestro'];
					#Get Course Schedule
					$statement = "SELECT d.dia as dia, i.hora_inicio as inicio, f.hora_fin as fin,
								 dc.horas as horas
								 FROM Dias_curso as dc
								 JOIN Inicio as i ON i.clave_inicio = dc.clave_inicio
							  	 JOIN Fin as f ON f.clave_fin = dc.clave_fin
								 JOIN Dias as d ON d.clave_dia = dc.clave_dia
								 WHERE dc.clave_curso = ?";
					if ($query = $this -> db_driver->prepare($statement)) {
						$query -> bind_param('s',$clave_curso);
						$query->execute();
						$query->bind_result($dia,$inicio,$fin,$horas);
							while ($query->fetch()) {
							$return_array['dia'][] = $dia;
							$return_array['inicio'][] = $inicio;
							$return_array['fin'][] = $fin;
							$return_array['horas'][] = $horas;
						}
					}
					else {
						return false;
					}
				}
				else return false;
			}
		}
		$result -> close();
	return $return_array;
	}

	function maestro_curso($clave_curso,$clave_maestro) {
		$statement = "SELECT * FROM Curso WHERE clave_maestro = '$clave_maestro' AND clave_curso = '$clave_curso'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result = $query -> store_result()) {
				if ($query = $result -> fetch_array(MYSQLI_ASSOC)) {
					return true;
				}
			}
		}
		$result -> close();
		return false;
	}

	function buscar_alumno($userid) {
		$info_alumno = array();

		$statement = "SELECT us.userid as codigo,us.fecha_nac as fecha_nac, CONCAT(us.nombre,' ',us.primer_a,' ',us.segundo_a) as nombre, us.correo as correo,
					us.celular as celular,us.web as web,us.github as github,eu.estado as estado, ca.nombre as carrera,
					us.clave_generica as generica
					FROM users_student as us
					JOIN Estado_usuario as eu ON eu.clave_estado = us.clave_estado
					JOIN Carrera as ca ON ca.clave_carrera = us.clave_carrera
					WHERE userid = '$userid'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result = $query -> store_result()) {
				if ($query = $result -> fetch_array(MYSQLI_ASSOC)) {
					$info_alumno['codigo'] = $query['codigo'];
					$info_alumno['nombre'] = $query['nombre'];
					$info_alumno['estado'] = $query['estado'];
					$info_alumno['correo'] = $query['correo'];
					$info_alumno['carrera'] = $query['carrera'];
					$info_alumno['fecha_nac'] = $query['fecha_nac'];
					if ($query['generica'] == 1) {
						$info_alumno['generica'] = $query['generica'];
					}
					if (strcmp($query['github'], "") != 0 )
						$info_alumno['github'] = $query['github'];
					else $info_alumno['github'] = "";
					if (strcmp($query['celular'], "") != 0 )
						$info_alumno['celular'] = $query['celular'];
					else $info_alumno['celular'] = "";
					if (strcmp($query['web'], "") != 0 )
						$info_alumno['web'] = $query['web'];
					else $info_alumno['web'] = "";
				}
				else
					return false;
			}
			$result -> close();
		}

		return $info_alumno;
	}

	function get_carreras() {
		$statement = "SELECT * FROM Carrera";
		$query = $this -> db_driver;
		$carrera = array();
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result()) {
				while ($query= $result -> fetch_array(MYSQLI_ASSOC)) {
					$carrera['clave_carrera'][] = $query['clave_carrera'];
					$carrera['carrera'][] = $query['nombre'];
				}
			}
			$result -> close();
		}
		return $carrera;
	}

	function get_student($studentid) {
		$statement = "SELECT *,us.nombre as usnombre,c.nombre as cnombre,eu.estado FROM users_student as us
					  JOIN Estado_usuario as eu on eu.clave_estado = us.clave_estado
					  JOIN Carrera as c on c.clave_carrera = us.clave_carrera
					  WHERE us.userid = ".$studentid;
		$query = $this -> db_driver;
		$student = array();
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result()) {
				if ($query= $result -> fetch_array(MYSQLI_ASSOC)) {
					$student['codigo'] = $query['userid'];
					$student['nombre'] = $query['usnombre'].' '.$query['primer_a'].' '.$query['segundo_a'];
					$student['fecha'] = $query['fecha_nac'];
					$student['correo'] = $query['correo'];
					$student['github'] = $query['github'];
					$student['celular'] = $query['celular'];
					$student['web'] = $query['web'];
					$student['estado'] = $query['estado'];
					$student['carrera'] = $query['cnombre'];
					$student['fecha_nac'] = $query['fecha_nac'];
				}
				else {
					return false;
				}
			}
			$result -> close();
		}
		return $student;
	}

	function get_cicleinfo($ciclo) {
		$cicle = array();
		if ($ciclo === null) {
			$statement = "SELECT clave_ciclo,inicio,fin FROM Ciclo
			          WHERE clave_estado = 1";
		}
		else {
			$statement = "SELECT clave_ciclo,inicio,fin FROM Ciclo
			          WHERE clave_ciclo = '$ciclo'";
		}
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result()) {
				if ($query= $result -> fetch_array(MYSQLI_ASSOC)) {
					$cicle['clave_ciclo'] = $query['clave_ciclo'];
					$cicle['inicio'] = $query['inicio'];
					$cicle['fin'] = $query['fin'];
				}
				else {
					return false;
				}
			}
			$result -> close();
		}
		return $cicle;
	}

	function get_nonworking($ciclo) {
		$nonworking = array();
		$statement = "SELECT dia FROM Dia_libre
			          WHERE clave_ciclo = '$ciclo'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result()) {
				while ($query= $result -> fetch_array(MYSQLI_ASSOC)) {
					$nonworking[] = $query['dia'];
				}
			}
			else {
				return false;
			}
			$result -> close();
		}
		return $nonworking;
	}

	function get_all_materias() {
		$materias = array();
		$statement = "SELECT clave_materia,nombre FROM Materia ORDER BY clave_materia";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result()) {
				while ($query= $result -> fetch_array(MYSQLI_ASSOC)) {
					$materias['clave_materia'][] = $query['clave_materia'];
					$materias['nombre'][] = $query['nombre'];
				}
			}
			else {
				return false;
			}
			$result -> close();
		}
		return $materias;
	}

	function get_all_teachers() {
		$teachers = array();
		$statement = "SELECT userid,CONCAT(nombre,' ',primer_a,' ',segundo_a) as nombre FROM users_teacher ORDER BY nombre";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result()) {
				while ($query= $result -> fetch_array(MYSQLI_ASSOC)) {
					$teachers['clave'][] = $query['userid'];
					$teachers['nombre'][] = $query['nombre'];
				}
			}
			else {
				return false;
			}
			$result -> close();
		}
		return $teachers;
	}

	function get_all_dias() {
		$dias = array();
		$statement = "SELECT * FROM Dias";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result()) {
				while ($query= $result -> fetch_array(MYSQLI_ASSOC)) {
					$dias['clave'][] = $query['clave_dia'];
					$dias['dia'][] = $query['dia'];
				}
			}
			else {
				return false;
			}
			$result -> close();
		}
		return $dias;
	}

	function get_all_horas() {
		$horas = array();
		$statement = "SELECT * FROM Inicio";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result()) {
				while ($query= $result -> fetch_array(MYSQLI_ASSOC)) {
					$horas['clave'][] = $query['clave_inicio'];
					$horas['hora'][] = $query['hora_inicio'];
				}
			}
			else {
				return false;
			}
			$result -> close();
		}
		return $horas;
	}

	function search_studentbynombre($nombre) {
		$students = array();
		$statement = "SELECT us.userid as codigo,CONCAT(us.nombre,' ',us.primer_a,' ',us.segundo_a) as nombre,c.nombre as carrera FROM users_student as us
					  JOIN Carrera as c on us.clave_carrera = c.clave_carrera
		              where (SELECT CONCAT( us.nombre,  ' ', us.primer_a,  ' ', us.segundo_a )) LIKE  '%".$nombre."%'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result()) {
				while ($query= $result -> fetch_array(MYSQLI_ASSOC)) {
					$students['codigo'][] = $query['codigo'];
					$students['nombre'][] = $query['nombre'];
					$students['carrera'][] = $query['carrera'];
				}
			}
			else {
				return false;
			}
			$result -> close();
		}
		return $students;
	}

	function search_studentbycodigo($codigo) {
		$students = array();
		$statement = "SELECT us.userid as codigo,CONCAT(us.nombre,' ',us.primer_a,' ',us.segundo_a) as nombre,c.nombre as carrera FROM users_student as us
					  JOIN Carrera as c on c.clave_carrera = us.clave_carrera
		              where userid=".$codigo;
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result =$query -> store_result()) {
				while ($query= $result -> fetch_array(MYSQLI_ASSOC)) {
					$students['codigo'][] = $query['codigo'];
					$students['nombre'][] = $query['nombre'];
					$students['carrera'][] = $query['carrera'];
				}
			}
			else {
				return false;
			}
			$result -> close();
		}
		return $students;
	}

	function check_if_course_empty($clave_curso) {
		$statement = "SELECT * FROM Lista WHERE clave_curso = '$clave_curso'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result = $query -> store_result())
				if ($query = $result -> fetch_array()) {
					#a student was found on course, so adding field will fail
					return false;
				}
			$result -> close();
		}
		return true;
	}

	function get_rubros($clave_curso) {
		$statement = "SELECT clave_rubro,actividad,porcentaje FROM Rubro WHERE clave_curso = '$clave_curso'";
		$query = $this -> db_driver;
		$rubros = array();
		if ($query -> real_query($statement)) {
			if ($result = $query -> store_result()) {
				while ($query = $result -> fetch_array(MYSQLI_ASSOC)) {
					$rubros['actividad'][] = $query['actividad'];
					$rubros['clave_rubro'][] = $query['clave_rubro'];
					$rubros['porcentaje'][] = $query['porcentaje'];
				}
			}
			$result -> close();
		}
		return $rubros;
	}

	function get_nrcs($clave_ciclo,$teacher_id) {
		$statement = "SELECT nrc FROM Curso WHERE clave_ciclo = '$clave_ciclo' AND clave_maestro = '$teacher_id'";
		$query = $this -> db_driver;
		$nrc = array();
		if ($query -> real_query($statement)) {
			if ($result = $query -> store_result()) {
				while ($query = $result -> fetch_array(MYSQLI_ASSOC)) {
					$nrc[] = $query['nrc'];
				}
			}
			$result -> close();
		}
		return $nrc;
	}

	function get_nrcs_cicle($clave_ciclo,$userid) {
		$statement = "SELECT nrc FROM Curso WHERE clave_ciclo = '$clave_ciclo'";
		$query = $this -> db_driver;
		$nrc = array();
		if ($query -> real_query($statement)) {
			if ($result = $query -> store_result()) {
				while ($query = $result -> fetch_array(MYSQLI_ASSOC)) {
					//$nrc[] = $query['nrc'];
					$clave_curso = $query['nrc'].$clave_ciclo;
					$statement1 = "SELECT * FROM Lista WHERE clave_curso = '$clave_curso' AND codigo_alumno = '$userid'";
					$query1 = $this -> db_driver;
					if ($query1 -> real_query($statement1)) {
						if ($result1 = $query1 -> store_result()) {
							if (!$query1 = $result1 -> fetch_array(MYSQLI_ASSOC)) {
								$rub = $this-> get_rubros($clave_curso);
								if (isset($rub['actividad']))
									$nrc[] = $query['nrc'];
							}
						}
					}
					$result1 -> close();
				}
			}
			$result -> close();
		}
		return $nrc;
	}

	function get_cicles_student($studentid) {
		$ciclos = array();
		$statement = "SELECT ciclo_registro FROM users_student WHERE userid >= '$studentid'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result = $query -> store_result()) {
				if ($query = $result -> fetch_array(MYSQLI_ASSOC)) {
					$clave_ciclo = $query['ciclo_registro'];
				}
			}
			$result -> close();
		}
		$statement = "SELECT clave_ciclo FROM Ciclo WHERE clave_ciclo >= '$clave_ciclo' ORDER BY clave_ciclo DESC";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result = $query -> store_result()) {
				while ($query = $result -> fetch_array(MYSQLI_ASSOC)) {
					$ciclos[] = $query['clave_ciclo'];
				}
			}
			$result -> close();
		}
		return $ciclos;
	}

	function get_studentCourses($ciclo,$studentid) {
		$cursos = array();
		$statement = "SELECT m.nombre,c.nrc,c.seccion,c.clave_materia,l.calificacion  FROM Lista as l 
					JOIN Curso as c on c.clave_curso = l.clave_curso
					JOIN Materia as m on m.clave_materia = c.clave_materia
					WHERE c.clave_ciclo = '$ciclo' AND l.codigo_alumno = '$studentid'";
		$query = $this -> db_driver;
		if ($query -> real_query($statement)) {
			if ($result = $query -> store_result()) {
				while ($query = $result -> fetch_array(MYSQLI_ASSOC)) {
					$cursos['nombre'][] = $query['nombre'];
					$cursos['nrc'][] = $query['nrc'];
					$cursos['seccion'][] = $query['seccion'];
					$cursos['clave'][] = $query['clave_materia'];
					$cursos['calificacion'][] = $query['calificacion'];		
				}
			}
			$result -> close();
		}
		return $cursos;
	}

}
?>