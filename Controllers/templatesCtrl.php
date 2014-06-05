<?php

class templatesCtrl {

	function procesarPlantilla_login($content,$band) {
		if ($band == 0)
			$content = str_replace("{{'sesion-expirada'}}", '<h2 class="form-horizontal-heading"> Su sesion ha expirado! </h2>', $content);
		else if ($band == 1)
			$content = str_replace("{{'sesion-expirada'}}", '<h2 class="form-horizontal-heading"> Usuario desconectado </h2>', $content);
		else if ($band == 2)
			$content = str_replace("{{'sesion-expirada'}}", '<h2 class="form-horizontal-heading"> Usuario o Contrasenha incorrecta </h2>', $content);
		else
			$content = str_replace("{{'sesion-expirada'}}", '',$content);
		return $content;
	}

	function procesarPlantilla_cicleview($content,$cicle_array,$non_workingarray,$ciclos) {
		//$content = str_replace("{{'ciclo'}}", $cicle_array['clave_ciclo'], $content);
		//$content = str_replace("{{'fechainicial'}}",date_format($cicle_array['inicio'],'d/m/Y'), $content);
		//$content = str_replace("{{'fechafinal'}}", date_format($cicle_array['fin'],'d/m/Y'), $content);
		$content = str_replace("{{'inicia-periodo'}}", '', $content);
		$content = str_replace("{{'termina-periodo'}}", '', $content);
		$content = str_replace("{{'fechainicial'}}", $cicle_array['inicio'], $content);
		$content = str_replace("{{'fechafinal'}}", $cicle_array['fin'], $content);
		$cad_ini_nonworking = "{{'inicia_nonworking'}}";
		$cad_fin_nonworking = "{{'termina_nonworking'}}";
		$pos_cad_ini_nonworking = strpos($content,$cad_ini_nonworking);
		$pos_cad_fin_nonworking = strpos($content,$cad_fin_nonworking) + strlen($cad_fin_nonworking);
		$sub_nonworking =  substr($content,$pos_cad_ini_nonworking,($pos_cad_fin_nonworking-$pos_cad_ini_nonworking));
		$nonworking = "";
		if (isset($non_workingarray)) {
			while (list($key,$date) = each($non_workingarray)) {
				$sub_nonworking_aux = $sub_nonworking;
				$sub_nonworking_aux = str_replace("{{'non_working'}}",$date, $sub_nonworking_aux);
				$nonworking = $nonworking . $sub_nonworking_aux;
			}

		}
		$content = str_replace($sub_nonworking,$nonworking,$content);
		$content = str_replace($cad_ini_nonworking, '',$content);
		$content = str_replace($cad_fin_nonworking, '',$content);
		$content = str_replace("{{'ciclo'}}", $this -> llena_select_ciclos($ciclos,$cicle_array['clave_ciclo']), $content);
		return $content;

	}

	function procesarPlantilla_cicleviewcontent($content,$cicle_array,$non_workingarray) {
		//$content = str_replace("{{'ciclo'}}", $cicle_array['clave_ciclo'], $content);
		//$content = str_replace("{{'fechainicial'}}",date_format($cicle_array['inicio'],'d/m/Y'), $content);
		//$content = str_replace("{{'fechafinal'}}", date_format($cicle_array['fin'],'d/m/Y'), $content);
		$content = str_replace("{{'fechainicial'}}", $cicle_array['inicio'], $content);
		$content = str_replace("{{'fechafinal'}}", $cicle_array['fin'], $content);
		$cad_ini_nonworking = "{{'inicia_nonworking'}}";
		$cad_fin_nonworking = "{{'termina_nonworking'}}";
		$pos_cad_ini_nonworking = strpos($content,$cad_ini_nonworking);
		$pos_cad_fin_nonworking = strpos($content,$cad_fin_nonworking) + strlen($cad_fin_nonworking);
		$sub_nonworking =  substr($content,$pos_cad_ini_nonworking,($pos_cad_fin_nonworking-$pos_cad_ini_nonworking));
		$nonworking = "";
		if (isset($non_workingarray)) {
			while (list($key,$date) = each($non_workingarray)) {
				$sub_nonworking_aux = $sub_nonworking;
				$sub_nonworking_aux = str_replace("{{'non_working'}}",$date, $sub_nonworking_aux);
				$nonworking = $nonworking . $sub_nonworking_aux;
			}

		}
		$content = str_replace($sub_nonworking,$nonworking,$content);
		$content = str_replace($cad_ini_nonworking, '',$content);
		$content = str_replace($cad_fin_nonworking, '',$content);

		$content = substr($content, strpos($content,"{{'inicia-periodo'}}"),strpos($content,"{{'termina-periodo'}}") + strlen("{{'termina-periodo'}}")-strpos($content,"{{'inicia-periodo'}}"));
		$content = str_replace("{{'inicia-periodo'}}", '', $content);
		$content = str_replace("{{'termina-periodo'}}", '', $content);
		$content = '<table class="table" id="periodo">' . $content . '</table>';
		return $content;
	}

		function procesarPlantilla_searchcourse($content,$ciclos) {

			$cadena = "";
			while ((list( ,$clave) = each($ciclos))) {
				$cadena .= '<option value="'.$clave.'">'.$clave.'</option>';
			}
			$content = str_replace("{{'ciclo'}}",$cadena, $content);
			
			return $content;
		}

		function procesarPlantilla_clonecourse($content,$ciclos) {
			

			$cadena = "";
			while ((list( ,$clave) = each($ciclos))) {
				$cadena .= '<option value="'.$clave.'">'.$clave.'</option>';
			}
			$content = str_replace("{{'ciclo'}}",$cadena, $content);
			
			return $content;
		}

		function procesarPlantilla_viewfields($content,$rubros) {
			$cad_ini_rubro = "{{'inicia-rubro'}}";
			$cad_fin_rubro = "{{'termina-rubro'}}";
			$pos_cad_ini_rubro = strpos($content,$cad_ini_rubro);
			$pos_cad_fin_rubro = strpos($content,$cad_fin_rubro) + strlen($cad_fin_rubro);
			$sub_rubro =  substr($content,$pos_cad_ini_rubro,($pos_cad_fin_rubro-$pos_cad_ini_rubro));
			$rubro = "";
			if (isset($rubros['actividad'])) {
				while ((list( ,$actividad) = each($rubros['actividad'])) && (list( ,$porcentaje) = each($rubros['porcentaje']))) {
					$sub_rubro_aux = $sub_rubro;
					$sub_rubro_aux = str_replace("{{'actividades'}}", $actividad, $sub_rubro_aux);
					$sub_rubro_aux = str_replace("{{'porcentaje'}}", $porcentaje, $sub_rubro_aux);
					$rubro .= $sub_rubro_aux;
				}
			}
			$content = str_replace($sub_rubro,$rubro,$content);
			$content = str_replace("{{'inicia-rubro'}}", '',$content);
			$content = str_replace("{{'termina-rubro'}}", '',$content);

			return $content;
		}

		function procesarPlantilla_courseview($content,$info_curso,$rubros,$band_ciclo,$band_enrolled) {
			$cad_ini_horario = "{{'inicia-horario'}}";
			$cad_fin_horario = "{{'termina-horario'}}";
			$pos_cad_ini_horario = strpos($content,$cad_ini_horario);
			$pos_cad_fin_horario = strpos($content,$cad_fin_horario) + strlen($cad_fin_horario);
			$sub_horario =  substr($content,$pos_cad_ini_horario,($pos_cad_fin_horario-$pos_cad_ini_horario));
			$horario = "";
				while ((list( ,$inicio) = each($info_curso['inicio'])) && (list( ,$fin) = each($info_curso['fin']))
				   && (list( ,$dia) = each($info_curso['dia'])) && (list( ,$horas) = each($info_curso['horas']))) {
						$sub_horario_aux = $sub_horario;
						$sub_horario_aux = str_replace("{{'dia'}}", $dia, $sub_horario_aux);
						$sub_horario_aux = str_replace("{{'inicio'}}", $inicio, $sub_horario_aux);
						$sub_horario_aux = str_replace("{{'fin'}}", $fin, $sub_horario_aux);
						$sub_horario_aux = str_replace("{{'horas'}}", $horas, $sub_horario_aux);
						$horario = $horario . $sub_horario_aux;
				}

			$content = str_replace($sub_horario,$horario,$content);
			$content = str_replace("{{'ciclo'}}" , $info_curso['ciclo'],$content);
			$content = str_replace("{{'nrc'}}" , $info_curso['nrc'],$content);
			$content = str_replace("{{'seccion'}}" , $info_curso['seccion'],$content);
			$content = str_replace("{{'clave'}}" , $info_curso['clave_materia'],$content);
			$content = str_replace("{{'materia'}}" , $info_curso['materia'],$content);
			$content = str_replace("{{'maestro'}}" , $info_curso['maestro'],$content);
			$content = str_replace("{{'inicia-horario'}}", '',$content);
			$content = str_replace("{{'termina-horario'}}", '',$content);

			if ($band_ciclo) {
				if ($band_enrolled) {
					if (!isset($rubros['actividad'])) {
						$cadena = '
						<table class="table">
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td>
								<form method="post" action="index.php?ctrl=course&act=addfield" class="form-horizontal" role="form">
									<input type="hidden" name="nrc_curso" value="'.$info_curso['nrc'].'"/>
									<button class="btn btn-primary btn-block" type="submit">Agregar Rubros</button>
								</form>
							</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						';
					}
					else {
						$cadena = '
						<table class="table">
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td>
								<form method="post" action="index.php?ctrl=course&act=viewfields" class="form-horizontal" role="form">
									<input type="hidden" name="clave_curso" value="'.$info_curso['nrc'].$info_curso['ciclo'].'"/>
									<button class="btn btn-primary btn-block" type="submit">Ver Rubros</button>
								</form>
							</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						';
					}
				}
				else {
					$cadena = '
					<table class="table">
						<tr>
							<form method="post" action="index.php?ctrl=course&act=list" class="form-horizontal" role="form">
								<td>
									<button class="btn btn-primary btn-block" type="submit">Lista de Alumnos</button>
									<input type="hidden" name="ciclo" value="'.$info_curso['ciclo'].'"></input>
									<input type="hidden" name="nrc" value="'.$info_curso['nrc'].'"></input>
								</td>
							</form>
							<form method="post" action="index.php?ctrl=course&act=list_a" class="form-horizontal" role="form">
								<td>
									<input type="hidden" name="nrc" value="'.$info_curso['nrc'].'"></input>
									<input type="hidden" name="ciclo" value="'.$info_curso['ciclo'].'"></input>
									<button class="btn btn-primary btn-block" type="submit">Ver Asistencias</button>
								</td>
							</form>
							<form method="post" action="index.php?ctrl=course&act=list_c" class="form-horizontal" role="form">
								<td>
									<input type="hidden" name="nrc" value="'.$info_curso['nrc'].'"></input>
									<input type="hidden" name="ciclo" value="'.$info_curso['ciclo'].'"></input>
									<button class="btn btn-primary btn-block" type="submit">Ver Calificaciones</button>
								</td>
							</form>
					';
					if (isset($rubros['actividad'])) {
							$cadena .= '<form method="post" action="index.php?ctrl=course&act=viewfields" class="form-horizontal" role="form">
								<td>
									<input type="hidden" name="clave_curso" value="'.$info_curso['nrc'].$info_curso['ciclo'].'"/>
									<button class="btn btn-primary btn-block" type="submit">Ver Rubros</button>
								</td>
							</form>';
					}
				}
				$cadena .= '</tr></table>';
				$content = str_replace("{{'opciones-curso'}}",$cadena, $content);
			}
			else {
				$content = str_replace("{{'opciones-curso'}}",'',$content);
			}

			return $content;
		}

		function procesarPlantilla_viewstudent_courses($content,$ciclos,$ciclo) {
			$content = str_replace("{{'select_ciclo'}}",$this -> llena_select_ciclos($ciclos,$ciclo), $content);
			return $content;
		}

	function procesarPlantilla_capture_grade($content,$subject_array,$ciclo,$rubros,$students) {
		$content = str_replace("{{'materia'}}" ,$subject_array['materia'],$content);
		$content = str_replace("{{'nrc'}}" ,$subject_array['nrc'],$content);
		$content = str_replace("{{'clave_materia'}}" ,$subject_array['clave_materia'],$content);
		$content = str_replace("{{'seccion'}}", $subject_array['seccion'],$content);
		$cad_ini_horario = "{{'inicia-horario'}}";
		$cad_fin_horario = "{{'termina-horario'}}";
		$pos_cad_ini_horario = strpos($content,$cad_ini_horario);
		$pos_cad_fin_horario = strpos($content,$cad_fin_horario) + strlen($cad_fin_horario);
		$sub_horario =  substr($content,$pos_cad_ini_horario,($pos_cad_fin_horario-$pos_cad_ini_horario));
		$horario = "";
		while ((list( ,$inicio) = each($subject_array['inicio'])) && (list( ,$fin) = each($subject_array['fin']))
		   && (list( ,$dia) = each($subject_array['dia']))) {
			$sub_horario_aux = $sub_horario;
			$aux =$dia.'-'.$inicio.'-'.$fin;
			$sub_horario_aux = str_replace("{{'horario'}}",$aux, $sub_horario_aux);
			$horario = $horario . $sub_horario_aux;
		}
		$rubro = '';
		while ((list( ,$actividad) = each($rubros['actividad'])) && (list( ,$clave_rubro) = each($rubros['clave_rubro']))) {
			$rubro .= '<option value="'.$clave_rubro.'">'.$actividad.'</option>';
		}
		$content = str_replace("{{'rubros'}}", $rubro, $content);
		$codigos = '';
		while (list( ,$codigo) = each($students['codigo'])) {
			$codigos .= '<option value="'.$codigo.'">'.$codigo.'</option>';
		}
		$content = str_replace("{{'codigos'}}", $codigos, $content);
		$content = str_replace($sub_horario,$horario,$content);
		$content = str_replace($cad_ini_horario, '',$content);
		$content = str_replace($cad_fin_horario, '',$content);
		$content = str_replace("{{'nrc'}}", $subject_array['nrc'], $content);
		$content = str_replace("{{'ciclo'}}", $ciclo, $content);
		return $content;
	}

		function procesarPlantilla_teacherview($content,$teacher) {
			$content = str_replace("{{'codigo'}}", $teacher['codigo'],$content);
			$content = str_replace("{{'estado'}}", $teacher['estado'],$content);
			$content = str_replace("{{'nombre'}}", $teacher['nombre'],$content);
			$content = str_replace("{{'correo'}}", $teacher['correo'],$content);
			$content = str_replace("{{'celular'}}", $teacher['celular'],$content);
			return $content;
		}

		function procesarPlantilla_studentview($content,$student) {
			$content = str_replace("{{'codigo'}}", $student['codigo'],$content);
			$content = str_replace("{{'estado'}}", $student['estado'],$content);
			$content = str_replace("{{'nombre'}}", $student['nombre'],$content);
			$content = str_replace("{{'carrera'}}", $student['carrera'],$content);
			$content = str_replace("{{'correo'}}", $student['correo'],$content);
			$content = str_replace("{{'celular'}}", $student['celular'],$content);
			$content = str_replace("{{'web'}}", $student['web'],$content);
			$content = str_replace("{{'github'}}", $student['github'],$content);
			$content = str_replace("{{'fecha'}}", $student['fecha_nac'],$content);
			return $content;
		}

		function procesarPlantilla_grade_listview($content,$grade_array,$subject_array,$band_ciclo,$ciclo) {
		$cad_ini_rubro = "{{'inicia-rubro'}}";
		$cad_fin_rubro = "{{'termina-rubro'}}";
		$cad_ini_calificacion = "{{'inicia-calificacion'}}";
		$cad_fin_calificacion = "{{'termina-calificacion'}}";

		$pos_cad_ini_rubro = strpos($content,$cad_ini_rubro);
		$pos_cad_fin_rubro = strpos($content,$cad_fin_rubro) + strlen($cad_fin_rubro);
		$sub_rubros =  substr($content,$pos_cad_ini_rubro,($pos_cad_fin_rubro-$pos_cad_ini_rubro));
		$rubros = "";

		$pos_cad_ini_calificacion = strpos($content,$cad_ini_calificacion);
		$pos_cad_fin_calificacion = strpos($content,$cad_fin_calificacion) + strlen($cad_fin_calificacion);
		$sub_calificacion =  substr($content,$pos_cad_ini_calificacion,($pos_cad_fin_calificacion-$pos_cad_ini_calificacion));
		$calificacion = "";

		foreach ($grade_array as $key => $value) {
			while ((list( , $actividad) = each($grade_array[$key]['actividad'])) && (list( , $porcentaje) = each($grade_array[$key]['porcentaje']))) {
				$sub_rubros_aux = $sub_rubros;
				$sub_rubros_aux = str_replace("{{'rubro'}}", $actividad, $sub_rubros_aux);
				$sub_rubros_aux = str_replace("{{'porcentaje'}}", $porcentaje, $sub_rubros_aux);
				$rubros = $rubros . $sub_rubros_aux;
			}
			$sub_rubros_aux = $sub_rubros;
			$sub_rubros_aux = str_replace("{{'rubro'}}-{{'porcentaje'}}", 'calificacion', $sub_rubros_aux);
			$rubros = $rubros . $sub_rubros_aux;
			break;
		}
		foreach ($grade_array as $key => $value) {
			$calificacion = $calificacion . '</tr>';
			$sub_calificacion_aux = $sub_calificacion;
			$sub_calificacion_aux = str_replace("{{'calificacion'}}", $key, $sub_calificacion_aux);
			$calificacion = $calificacion . $sub_calificacion_aux;
			while (list( , $puntos) = each($grade_array[$key]['puntos'])) {
				$sub_calificacion_aux = $sub_calificacion;
				$sub_calificacion_aux = str_replace("{{'calificacion'}}",round($puntos,2), $sub_calificacion_aux);
				$calificacion = $calificacion . $sub_calificacion_aux;
			}
			$sub_calificacion_aux = $sub_calificacion;
			$sub_calificacion_aux = str_replace("{{'calificacion'}}",$grade_array[$key]['calificacion'], $sub_calificacion_aux);
			$calificacion = $calificacion . $sub_calificacion_aux;
			$calificacion = $calificacion . '</tr>';

		}
		$content = str_replace($sub_rubros, $rubros, $content);
		$content = str_replace($cad_ini_rubro, '', $content);
		$content = str_replace($cad_fin_rubro, '', $content);
		$content = str_replace($sub_calificacion, $calificacion,$content);
		$content = str_replace($cad_ini_calificacion,'',$content);
		$content = str_replace($cad_fin_calificacion,'',$content);
		$content = str_replace("{{'materia'}}" ,$subject_array['materia'],$content);
		$content = str_replace("{{'nrc'}}" ,$subject_array['nrc'],$content);
		$content = str_replace("{{'clave_materia'}}" ,$subject_array['clave_materia'],$content);
		$content = str_replace("{{'seccion'}}", $subject_array['seccion'],$content);
		$cad_ini_horario = "{{'inicia-horario'}}";
		$cad_fin_horario = "{{'termina-horario'}}";
		$pos_cad_ini_horario = strpos($content,$cad_ini_horario);
		$pos_cad_fin_horario = strpos($content,$cad_fin_horario) + strlen($cad_fin_horario);
		$sub_horario =  substr($content,$pos_cad_ini_horario,($pos_cad_fin_horario-$pos_cad_ini_horario));
		$horario = "";
		while ((list( ,$inicio) = each($subject_array['inicio'])) && (list( ,$fin) = each($subject_array['fin']))
		   && (list( ,$dia) = each($subject_array['dia']))) {
			$sub_horario_aux = $sub_horario;
			$aux =$dia.'-'.$inicio.'-'.$fin;
			$sub_horario_aux = str_replace("{{'horario'}}",$aux, $sub_horario_aux);
			$horario = $horario . $sub_horario_aux;
		}
		$boton = '';
		if ($band_ciclo && $_SESSION['type'] != 1){
			$boton = '<button class="btn btn-primary btn-block" type="submit">Capturar Calificaciones</button>';
		}
		$content = str_replace("{{'boton'}}", $boton,$content);
		$content = str_replace("{{'nrc'}}", $subject_array['nrc'], $content);
		$content = str_replace("{{'ciclo'}}", $ciclo, $content);
		$content = str_replace($sub_horario,$horario,$content);
		$content = str_replace($cad_ini_horario, '',$content);
		$content = str_replace($cad_fin_horario, '',$content);
		return $content;
	}


	function procesarPlantilla_course_listview($content,$students_array,$subject_array) {
		$content = str_replace("{{'materia'}}" ,$subject_array['materia'],$content);
		$content = str_replace("{{'nrc'}}" ,$subject_array['nrc'],$content);
		$content = str_replace("{{'clave_materia'}}" ,$subject_array['clave_materia'],$content);
		$content = str_replace("{{'seccion'}}", $subject_array['seccion'],$content);
		$cad_ini_horario = "{{'inicia-horario'}}";
		$cad_fin_horario = "{{'termina-horario'}}";
		$pos_cad_ini_horario = strpos($content,$cad_ini_horario);
		$pos_cad_fin_horario = strpos($content,$cad_fin_horario) + strlen($cad_fin_horario);
		$sub_horario =  substr($content,$pos_cad_ini_horario,($pos_cad_fin_horario-$pos_cad_ini_horario));
		$horario = "";
		while ((list( ,$inicio) = each($subject_array['inicio'])) && (list( ,$fin) = each($subject_array['fin']))
		   && (list( ,$dia) = each($subject_array['dia']))) {
			$sub_horario_aux = $sub_horario;
			$aux =$dia.'-'.$inicio.'-'.$fin;
			$sub_horario_aux = str_replace("{{'horario'}}",$aux, $sub_horario_aux);
			$horario = $horario . $sub_horario_aux;
		}
		$content = str_replace($sub_horario,$horario,$content);
		$content = str_replace($cad_ini_horario, '',$content);
		$content = str_replace($cad_fin_horario, '',$content);
		$cadena = '';
		if(sizeof($students_array) > 0) {
			while((list( ,$nolista) = each($students_array['nolista'])) && (list( ,$carrera) = each($students_array['carrera'])) &&
				   (list( ,$nombre) = each($students_array['nombre'])) && (list( ,$codigo) = each($students_array['codigo']))) {
				$cadena .= '<tr>';
				$cadena .= '
						<td>'.$nolista.'</td>
						<td>'.$codigo.'</td>
						<td>'.$nombre.'</td>
						<td>'.$carrera.'</td>';
				$cadena .= '</tr>';
			}
		}

		$content = str_replace("{{'lista_alumnos'}}", $cadena, $content);
		return $content;
	}


	function procesarPlantilla_attendance_listview($content,$attendance_array,$subject_array,$hoy,$clave_curso,$band_ciclo,$ciclo) {
		$content = str_replace("{{'materia'}}" ,$subject_array['materia'],$content);
		$content = str_replace("{{'nrc'}}" ,$subject_array['nrc'],$content);
		$content = str_replace("{{'clave_materia'}}" ,$subject_array['clave_materia'],$content);
		$content = str_replace("{{'seccion'}}", $subject_array['seccion'],$content);
		$cad_ini_horario = "{{'inicia-horario'}}";
		$cad_fin_horario = "{{'termina-horario'}}";
		$pos_cad_ini_horario = strpos($content,$cad_ini_horario);
		$pos_cad_fin_horario = strpos($content,$cad_fin_horario) + strlen($cad_fin_horario);
		$sub_horario =  substr($content,$pos_cad_ini_horario,($pos_cad_fin_horario-$pos_cad_ini_horario));
		$horario = "";
		while ((list( ,$inicio) = each($subject_array['inicio'])) && (list( ,$fin) = each($subject_array['fin']))
		   && (list( ,$dia) = each($subject_array['dia']))) {
			$sub_horario_aux = $sub_horario;
			$aux =$dia.'-'.$inicio.'-'.$fin;
			$sub_horario_aux = str_replace("{{'horario'}}",$aux, $sub_horario_aux);
			$horario = $horario . $sub_horario_aux;
		}
		$content = str_replace($sub_horario,$horario,$content);
		$content = str_replace($cad_ini_horario, '',$content);
		$content = str_replace($cad_fin_horario, '',$content);

		$cadena = '';
		if(sizeof($attendance_array) > 0) {
			$arreglo_dias = array();
			list(,, $anio) = explode("/", $hoy);
			$cadena = '<tr><td>Codigo</td>';
			foreach ($attendance_array as $key => $value) {
				while (list( , $dia) = each($attendance_array[$key]['dia'])) {
					list($year, $month, $day) = explode("-", $dia);
					$dia = "$month/$day";
					$arreglo_dias[] = $dia.'/'.$anio;
					$cadena .= '<td>'.$dia.'</td>';
				}
				$cadena .= '<td>&nbsp&nbsp&nbspPtj</td>';
				break;
			}
			$cadena .= '</tr>';
			foreach ($attendance_array as $key => $value) {
				$cadena .= '<tr><td class="filterable-cell">'.$key.'</td>';
				$contador = 0;
				$promedio = 0;
				while (list( , $asistencia) = each($attendance_array[$key]['asistencia'])) {
					$checkbox = '<input onclick="apilar_asistencia(this)" id="'.$key.'_'.$arreglo_dias[$contador].'"type="checkbox" value="'.$key.'" name="'.$arreglo_dias[$contador].'"';
					if ($asistencia == 1) {
						$checkbox .= 'checked="checked">';
					}
					else {
						$checkbox .= '>';
					}
					$contador++;
					$cadena .= '<td>'.$checkbox.'</td>';
					if ($asistencia == 1)
						$promedio++;
				}
				$cadena .= '<td>%'.round(($promedio/$contador)*100,2).'</td></tr>';
			}
		}
		$boton = '';
		if ($band_ciclo && $_SESSION['type'] != 1) {
			$boton = '<button class="btn btn-primary btn-block" type="submit">Guardar</button>';
		}
		$content = str_replace("{{'boton'}}", $boton,$content);
		$content = str_replace("{{'clave_curso'}}", $clave_curso, $content);
		$content = str_replace("{{'nrc'}}", $subject_array['nrc'], $content);
		$content = str_replace("{{'ciclo'}}", $ciclo, $content);
		$content = str_replace("{{'asistencias'}}", $cadena, $content);
		return $content;
	}

	function get_menu($content) {
		$menu = '<a href="index.php" class="btn btn-info"> Inicio</a> ';
		if ($_SESSION['type'] == 1) {
			$menu.= ' <div class="btn-group">
					  <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown"> Cursos <span class="caret"></span></button>
					  <ul class="dropdown-menu">
					  <li><a href="index.php?ctrl=student&act=list">Ver Cursos</a></li>
					  <li><a href="">MENU 3</a></li>
					  </ul></div>';
			$menu.= ' <div class="btn-group">
					  <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown"> Alumno <span class="caret"></span></button>
					  <ul class="dropdown-menu">
					  <li><a href="?ctrl=student&act=viewinfo">Mis Datos</a></li>
					  <li><a href="?ctrl=student&act=modifyinfo">Actualizar mi Informacion</a></li>
					  <li><a href="index.php?act=changepwd&ctrl=login">Cambiar Contrasenha</a></li>
					  </ul></div>';
		}
		else if ($_SESSION['type'] == 2) {
			$menu.= ' <div class="btn-group">
					  <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown"> Cursos <span class="caret"></span></button>
					  <ul class="dropdown-menu">
					  <li><a href="?ctrl=course&act=search">Buscar Curso</a></li>
					  <li><a href="?ctrl=course&act=new">Crear Curso</a></li>
					  <li><a href="?ctrl=course&act=clone">Clonar Curso</a></li>
					  </ul></div>';
			$menu.= ' <div class="btn-group">
					  <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown"> Profesor <span class="caret"></span></button>
					  <ul class="dropdown-menu">
					  <li><a href="index.php?act=changepwd&ctrl=login">Cambiar Contrasenha</a></li>
					  <li><a href="">MENU 2</a></li>
					  <li><a href="">MENU 3</a></li>
					  </ul></div>';
			$menu.= ' <div class="btn-group">
					  <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown"> Alumno <span class="caret"></span></button>
					  <ul class="dropdown-menu">
					  <li><a href="?ctrl=student&act=search">Buscar Alumno</a></li>
					  </ul></div>';
		}
		else if ($_SESSION['type'] == 3) {
			$menu.= ' <div class="btn-group">
					  <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown"> Ciclo <span class="caret"></span></button>
					  <ul class="dropdown-menu">
					  <li><a href="?ctrl=cicle&act=new">Crear Ciclo</a></li>
					  <li><a href="?ctrl=cicle&act=view_cicle">Ver Ciclo</a></li>
					  <li><a href="">MENU 3</a></li>
					  </ul></div>';
			$menu.= ' <div class="btn-group">
					  <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown"> Cursos <span class="caret"></span></button>
					  <ul class="dropdown-menu">
					  <li><a href="?ctrl=course&act=search">Buscar Curso</a></li>
					  <li><a href="?ctrl=course&act=new">Crear Curso</a></li>
					  <li><a href="?ctrl=course&act=clone">Clonar Curso</a></li>
					  </ul></div>';
			$menu.= ' <div class="btn-group">
					  <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown"> Profesor <span class="caret"></span></button>
					  <ul class="dropdown-menu">
					  <li><a href="?ctrl=teacher&act=new">Agregar Maestro</a></li>
					  <li><a href="">MENU 2</a></li>
					  <li><a href="">MENU 3</a></li>
					  </ul></div>';
			$menu.= ' <div class="btn-group">
					  <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown"> Alumno <span class="caret"></span></button>
					  <ul class="dropdown-menu">
					  <li><a href="?ctrl=student&act=new">Agregar Alumno</a></li>
					  <li><a href="?ctrl=student&act=search">Buscar Alumno</a></li>
					  <li><a href="">MENU 3</a></li>
					  </ul></div>';
			$menu.= ' <div class="btn-group">
					  <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown"> Admin <span class="caret"></span></button>
					  <ul class="dropdown-menu">
					  <li><a href="index.php?act=changepwd&ctrl=login">Cambiar Contrasenha</a></li>
					  </ul></div>';
		}
		$menu .= ' <a href="index.php?ctrl=login&act=signout" class="btn btn-default btn-info"> Desconectar</a>';
		$content = str_replace("{{'menu'}}", $menu, $content);
		return $content;
	}

	function llena_select_materias($materias) {
		$cadena = "";
		while ((list( ,$clave) = each($materias['clave_materia'])) && (list( ,$materia) = each($materias['nombre']))) {
			$cadena .= '<option value="'.$clave.'">'.$materia.'</option>';
		}
		return $cadena;
	}

	function llena_select_carreras($carreras) {
		$cadena = "";
		while ((list( ,$clave) = each($carreras['clave_carrera'])) && (list( ,$materia) = each($carreras['carrera']))) {
			$cadena .= '<option value="'.$clave.'">'.$materia.'</option>';
		}
		return $cadena;
	}

	function llena_select_ciclos($ciclos,$ciclo) {
		$cadena = "";
		while ((list( ,$clave) = each($ciclos))) {
			if ($ciclo == $clave) $cadena .= '<option value="'.$clave.'" selected="selected">'.$clave.'</option>';
			else $cadena .= '<option value="'.$clave.'">'.$clave.'</option>';
		}
		return $cadena;
	}

	function llena_select_nrc($nrc) {
		$cadena = "";
		while ((list( ,$clave) = each($nrc))) {
			$cadena .= '<option value="'.$clave.'">'.$clave.'</option>';
		}
		return $cadena;
	}

	function llena_select_maestros($teachers) {
		$cadena = "";
		if (sizeof($teachers['clave']) == 1 ) {
			while ((list( ,$clave) = each($teachers['clave'])) && (list( ,$nombre) = each($teachers['nombre'])))
				$cadena .= $nombre.' <input type="hidden" id="selmaestro" name="teacher_id" value="'.$clave.'" />';
		}
		else {
			$cadena .= '<select onchange="maestro_select(this); get_nrc();" id="selmaestro" name="teacher_id" type="text" class="form-control">';
		while ((list( ,$clave) = each($teachers['clave'])) && (list( ,$nombre) = each($teachers['nombre']))) {
			$cadena .= '<option value="'.$clave.'">'.$nombre.'</option>';
			}
			$cadena .= '</select>';
			$cadena .= '</td><td><input onKeyUp="maestro_input(this)" onblur="maestro_input(this)" id="txtmaestro" type="text" class="form-control" maxlength="8"/>';
		}
		return $cadena;
	}	

	function llena_select_horas($horas) {
		$cadena = "";
		while ((list( ,$clave) = each($horas['clave'])) && (list( ,$hora) = each($horas['hora']))) {
			$cadena .= '<option value="'.$clave.'">'.$hora.'</option>';
		}
		return $cadena;
	}

	function llena_select_dias($dias) {
		$cadena = "";
		while ((list( ,$clave) = each($dias['clave'])) && (list( ,$dia) = each($dias['dia']))) {
			$cadena .= '<option value="'.$clave.'">'.$dia.'</option>';
		}
		return $cadena;
	}

	function get_studentmenu($session,$estado,$studentid) {
		$cadena = '<table class="table" id="opciones_estudiante"><tr>';
		if ($session == 3) {
			if ($estado == 'ACTIVO') {
				$cadena .= '
					<td>
						<form method="post" action="" class="form-horizontal" role="form">
							<button onclick="popup_curso()" class="btn btn-primary" type="button">Agregar a Curso</button>
						</form>
					</td>
					<td>
						<form method="post" action="index.php?ctrl=student&act=modifystatus" class="form-horizontal" role="form">
							<button class="btn btn-primary" type="submit">Dar de Baja</button>
							<input type="hidden" name="value" value="0"></input>
							<input type="hidden" name="studentid" value="'.$studentid.'"></input>
						</form>
					</td>
					</tr></table>
					<table class="table" id="agregar_curso"><tr>
					<table class="table" id="info_curso"><tr>
				';
			}
			else {
				$cadena .= '
					<td>
						<form method="post" action="index.php?ctrl=student&act=modifystatus" class="form-horizontal" role="form">
							<button class="btn btn-primary" type="submit">Dar de Alta</button>
							<input type="hidden" name="value" value="1"></input>
							<input type="hidden" name="studentid" value="'.$studentid.'"></input>
						</form>
					</td>
				';
			}
		}
		else if ($session == 3) {
			$cadena .= '
				<td>
					<form method="post" action="" class="form-horizontal" role="form">
						<button onclick="popup_curso()" class="btn btn-primary" type="button">Agregar a Curso</button>
					</form>
				</td>';
		}
		$cadena .= '</tr></table>';
		return $cadena;
	}

}

function mostrar_curso($curso) {
	$cadena = '';
	if (sizeof($curso) > 0) {
		$cadena = '
			<tr>
				<td></td>
			</tr>
		';
	}

	return $cadena;
}
?>