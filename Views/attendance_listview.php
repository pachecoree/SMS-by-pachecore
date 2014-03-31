<?php 

#Show Attendance list of the course
	echo '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
	foreach ($attendance_array as $key => $value) {
		while (list( , $dia) = each($attendance_array[$key]['dia'])) {
			echo $dia,'&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
		}
		echo '&nbsp&nbsp&nbspporcentaje';
		break;
	}
	echo '</br></br>';
	foreach ($attendance_array as $key => $value) {
		echo $key;
		$contador = 0;
		$promedio = 0;
		echo '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
		while (list( , $asistencia) = each($attendance_array[$key]['asistencia'])) {
			$contador++;
			echo $asistencia,'&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
			if ($asistencia == 1)
				$promedio++;
		}
		echo '%',round(($promedio/$contador)*100,2);
		echo '</br>';
	}
?>