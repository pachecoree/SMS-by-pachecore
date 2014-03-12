<?php

#Show the Student Course grades and attendance
	echo 'Codigo Del Alumno: ',$_GET['studentid'],'</br>';
	while (list($key,$student) = each($course_info)) {
		echo $student['materia'],' | ',$student['calificacion'],' | ',$student['asistencia'],'</br>';
	}

?>