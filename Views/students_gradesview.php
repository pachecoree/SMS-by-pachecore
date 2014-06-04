<?php

#Show the Student Courses and Grades
	echo 'Codigo Del Alumno: ',$grades_info['codigo'],'</br>';
	echo 'Nombre: ',$grades_info['nombre'],'</br>';
	echo 'Carrera: ',$grades_info['carrera'],'</br></br></br>';
	if (isset($grades_info['materia'])) {
		while ((list( ,$materia) = each($grades_info['materia'])) && (list( ,$calificacion) = each($grades_info['calificacion']))) {
			echo $materia,' | ',$calificacion,'</br>';
		}
	}
	else
		echo "Student not registered to a Course";
?>