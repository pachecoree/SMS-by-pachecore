<?php

#Show the Student Course grades and attendance
	echo 'Codigo Del Alumno: ',$course_info['studentid'],'</br>';
	echo 'Nombre: ',$course_info['name'],'</br>';
	echo 'Carrera: ',$course_info['career'],'</br>';
	while (list($key,$student) = each($course_info['grades'])) {
		echo $student['materia'],' | ',$student['calificacion'],' | ',$student['asistencia'],'</br>';
	}

?>