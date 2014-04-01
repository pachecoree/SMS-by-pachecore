<?php

#Show the Student Course grades and attendance
	echo 'Codigo Del Alumno: ',$course_info['codigo'],'</br>';
	echo 'Nombre: ',$course_info['nombre'],'</br>';
	echo 'Carrera: ',$course_info['carrera'],'</br>';
	if (isset($course_info['dia'])) {
		while ((list( ,$dia) = each($course_info['dia']))) {
			echo $dia,'&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
		}
		echo '<br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
		while ((list( ,$asistencia) = each($course_info['asistencia']))) {
			echo $asistencia,'&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
			echo '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
		}
	}
	else
		echo "Student not registered to a Course";

?>