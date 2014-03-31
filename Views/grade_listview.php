<?php 

#Show Attendance list of the course
	echo '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
	foreach ($grade_array as $key => $value) {
		while ((list( , $actividad) = each($grade_array[$key]['actividad'])) &&
			  (list( , $porcentaje) = each($grade_array[$key]['porcentaje']))) {
			echo $actividad,'-',$porcentaje,'%&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
		}
		echo 'calificacion';
		break;
	}
	echo '</br>';

	foreach ($grade_array as $key => $value) {
		echo '</br>',$key;
		echo '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
		while (list( , $puntos) = each($grade_array[$key]['puntos'])) {
			echo round($puntos,2),'&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
		}
		echo $grade_array[$key]['calificacion'];

	}
?>