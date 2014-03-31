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
					  NO ACTION ON UPDATE NO ACTION) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8";
			#Create evaluation sheet
			if (!$this -> db_driver ->query($tabla))
				return false;
		}
		return true;
	}

	function crear_curso_datos($return_array,$clave_curso) {
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
				}
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

		$statement = "SELECT us.userid as codigo, CONCAT(us.nombre,' ',us.primer_a,' ',us.segundo_a) as nombre, us.correo as correo,
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
					if ($query['generica'] == 1) {
						$info_alumno['generica'] = $query['generica'];
					}
					if (strcmp($query['github'], "") != 0 )
						$info_alumno['github'] = $query['github'];
					if (strcmp($query['celular'], "") != 0 )
						$info_alumno['celular'] = $query['celular'];
					if (strcmp($query['web'], "") != 0 )
						$info_alumno['web'] = $query['web'];
				}
				else
					return false;
			}
			$result -> close();
		}

		return $info_alumno;
	}
}
?>