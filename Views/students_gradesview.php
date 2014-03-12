<?php

#Show the Student Courses and Grades
	echo 'Codigo Del Alumno: ',$_GET['studentid'],'</br>';
	while (list($key,$student) = each($grades_info)) {
		echo $student['materia'],' | ',$student['calificacion'],'</br>';
	}

?>