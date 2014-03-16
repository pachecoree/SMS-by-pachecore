<?php

#Show the Student Courses and Grades
	echo 'Codigo Del Alumno: ',$grades_info['studentid'],'</br>';
	echo 'Nombre: ',$grades_info['name'],'</br>';
	echo 'Carrera: ',$grades_info['career'],'</br>';
	while (list($key,$student) = each($grades_info['grades'])) {
		echo $student['materia'],' | ',$student['calificacion'],'</br>';
	}

?>